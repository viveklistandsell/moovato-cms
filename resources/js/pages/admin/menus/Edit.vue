<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import {
    ChevronDown,
    ChevronUp,
    Plus,
    PlusCircle,
    Search,
} from 'lucide-vue-next';
import { computed, provide, ref, watch } from 'vue';
import draggable from 'vuedraggable';
import Heading from '@/components/Heading.vue';
import { type LocaleOption } from '@/components/common/LocaleTabs.vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent } from '@/components/ui/card';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { setBreadcrumbs } from '@/composables/common/useBreadcrumbs';
import { useT } from '@/composables/useT';
import MenuTreeNode from './MenuTreeNode.vue';

const t = useT();
import {
    MENU_TREE_CONTEXT,
    type ItemDraft,
    type LinkType,
    type MenuItemRow,
    type MenuTreeContext,
    type TranslationRow,
    type TreeNode,
} from './tree-types';

type Option = {
    id: number;
    title: string;
    translations: Record<string, string>;
};

const props = defineProps<{
    menu: { id: number; key: string; name: string; is_active: boolean };
    items: MenuItemRow[];
    languages: LocaleOption[];
    pageOptions: Option[];
    categoryOptions: Option[];
}>();

setBreadcrumbs(() => [
    { title: t('sidebar.dashboard'), href: '/dashboard' },
    { title: t('sidebar.navigation_management'), href: '/admin/menus' },
    {
        title: props.menu.key === 'footer'
            ? t('sidebar.footer_menu')
            : t('sidebar.header_menu'),
        href: `/admin/menus/${props.menu.id}/edit`,
    },
]);
// Breadcrumbs set dynamically above via setBreadcrumbs().
defineOptions({});

const languagesRef = computed<LocaleOption[]>(() => props.languages);

const defaultLang = computed<string>(
    () =>
        props.languages.find((l) => l.is_default)?.code ??
        props.languages[0]?.code ??
        'de',
);

// ============================================================
// Tree (right column)
// ============================================================
const tree = ref<TreeNode[]>([]);
const expandedItemIds = ref<Set<number>>(new Set());
// Per-item active language tab (each card remembers its own selection).
const itemActiveLang = ref<Record<number, string>>({});
// Local per-item edit buffer — committed to the server on Save.
const itemDrafts = ref<Record<number, ItemDraft>>({});

function activeLangFor(id: number): string {
    return itemActiveLang.value[id] ?? defaultLang.value;
}

function setActiveLangFor(id: number, code: string): void {
    itemActiveLang.value = { ...itemActiveLang.value, [id]: code };
}

function buildTree(flat: MenuItemRow[]): TreeNode[] {
    const map = new Map<number, TreeNode>();
    const roots: TreeNode[] = [];
    for (const it of flat) map.set(it.id, { ...it, children: [] });
    for (const it of flat) {
        const node = map.get(it.id);
        if (!node) continue;
        if (it.parent_id === null) {
            roots.push(node);
        } else {
            const parent = map.get(it.parent_id);
            if (parent) parent.children.push(node);
            else roots.push(node);
        }
    }
    return roots;
}

watch(
    () => props.items,
    (val) => {
        tree.value = buildTree(val);
        // Seed/refresh drafts so unrelated changes don't blow away pending edits.
        for (const it of val) {
            if (!itemDrafts.value[it.id]) {
                itemDrafts.value[it.id] = {
                    translations: cloneTranslations(it),
                    open_in_new_tab: it.open_in_new_tab,
                    css_class: it.css_class ?? '',
                    is_active: it.is_active,
                };
            }
        }
    },
    { immediate: true },
);

function cloneTranslations(it: MenuItemRow): Record<string, TranslationRow> {
    const out: Record<string, TranslationRow> = {};
    for (const lang of props.languages) {
        const existing = it.translations[lang.code];
        out[lang.code] = existing
            ? { label: existing.label, link_url: existing.link_url }
            : { label: '', link_url: null };
    }
    return out;
}

function flattenForReorder(
    nodes: TreeNode[],
    parentId: number | null,
    out: Array<{ id: number; parent_id: number | null; sort_order: number }>,
): void {
    nodes.forEach((node, index) => {
        out.push({ id: node.id, parent_id: parentId, sort_order: index + 1 });
        if (node.children.length > 0) flattenForReorder(node.children, node.id, out);
    });
}

function syncOrder(): void {
    const flat: Array<{ id: number; parent_id: number | null; sort_order: number }> = [];
    flattenForReorder(tree.value, null, flat);
    router.post(
        `/admin/menus/${props.menu.key}/items/reorder`,
        { items: flat },
        { preserveScroll: true, preserveState: true, only: ['items'] },
    );
}

function toggleExpanded(id: number): void {
    if (expandedItemIds.value.has(id)) expandedItemIds.value.delete(id);
    else expandedItemIds.value.add(id);
    expandedItemIds.value = new Set(expandedItemIds.value);
}

function saveItem(node: TreeNode): void {
    const draft = itemDrafts.value[node.id];
    if (!draft) return;
    const translationsArray = props.languages.map((lang) => ({
        lang: lang.code,
        label: draft.translations[lang.code]?.label ?? '',
        link_url: draft.translations[lang.code]?.link_url || null,
    }));
    router.put(
        `/admin/menus/${props.menu.key}/items/${node.id}`,
        {
            parent_id: node.parent_id,
            link_type: node.link_type,
            link_id: node.link_id,
            open_in_new_tab: draft.open_in_new_tab,
            css_class: draft.css_class || null,
            is_active: draft.is_active,
            translations: translationsArray,
        },
        { preserveScroll: true },
    );
}

function destroyItem(node: TreeNode): void {
    const label =
        node.translations[defaultLang.value]?.label ??
        node.linked_title ??
        `Item #${node.id}`;
    if (!confirm(`Delete "${label}"? Its children will also be deleted.`)) return;
    router.delete(`/admin/menus/${props.menu.key}/items/${node.id}`, {
        preserveScroll: true,
        onSuccess: () => {
            delete itemDrafts.value[node.id];
            expandedItemIds.value.delete(node.id);
        },
    });
}

// Provide a single context object that every MenuTreeNode (at any depth)
// injects. Avoids prop-drilling shared state through arbitrarily deep trees.
const treeContext: MenuTreeContext = {
    languages: languagesRef,
    defaultLang,
    itemDrafts,
    expandedItemIds,
    itemActiveLang,
    activeLangFor,
    setActiveLangFor,
    toggleExpanded,
    syncOrder,
    saveItem,
    destroyItem,
};
provide(MENU_TREE_CONTEXT, treeContext);

// ============================================================
// Left column — Add menu items
// ============================================================
type SourceKey = 'pages' | 'categories' | 'url' | 'home';
const openSources = ref<Record<SourceKey, boolean>>({
    pages: true,
    categories: true,
    url: false,
    home: false,
});

function toggleSource(key: SourceKey): void {
    openSources.value[key] = !openSources.value[key];
}

// Selection state for the bulk-add sidebar.
const selectedPageIds = ref<Set<number>>(new Set());
const selectedCategoryIds = ref<Set<number>>(new Set());
const pageSearch = ref('');
const categorySearch = ref('');
const customUrl = ref('');
const customLabel = ref('');

const filteredPages = computed(() => {
    const q = pageSearch.value.trim().toLowerCase();
    if (q === '') return props.pageOptions;
    return props.pageOptions.filter((p) => p.title.toLowerCase().includes(q));
});
const filteredCategories = computed(() => {
    const q = categorySearch.value.trim().toLowerCase();
    if (q === '') return props.categoryOptions;
    return props.categoryOptions.filter((c) => c.title.toLowerCase().includes(q));
});

function togglePageSelection(id: number): void {
    if (selectedPageIds.value.has(id)) selectedPageIds.value.delete(id);
    else selectedPageIds.value.add(id);
    selectedPageIds.value = new Set(selectedPageIds.value);
}
function toggleCategorySelection(id: number): void {
    if (selectedCategoryIds.value.has(id)) selectedCategoryIds.value.delete(id);
    else selectedCategoryIds.value.add(id);
    selectedCategoryIds.value = new Set(selectedCategoryIds.value);
}

// Build translation rows from a picked entity's per-lang title; fall back
// to the canonical title (and finally to the entity name) when a language
// is missing a translation so every item ships with a non-empty label.
function translationsFromEntity(
    entity: Option,
): Array<{ lang: string; label: string; link_url: null }> {
    return props.languages.map((lang) => ({
        lang: lang.code,
        label: entity.translations[lang.code] ?? entity.title,
        link_url: null,
    }));
}

function addSelectedPages(): void {
    if (selectedPageIds.value.size === 0) return;
    const items = Array.from(selectedPageIds.value)
        .map((id) => props.pageOptions.find((p) => p.id === id))
        .filter((p): p is Option => p !== undefined)
        .map((p) => ({
            link_type: 'page' as const,
            link_id: p.id,
            translations: translationsFromEntity(p),
        }));
    bulkAdd(items);
    selectedPageIds.value = new Set();
}

function addSelectedCategories(): void {
    if (selectedCategoryIds.value.size === 0) return;
    const items = Array.from(selectedCategoryIds.value)
        .map((id) => props.categoryOptions.find((c) => c.id === id))
        .filter((c): c is Option => c !== undefined)
        .map((c) => ({
            link_type: 'category' as const,
            link_id: c.id,
            translations: translationsFromEntity(c),
        }));
    bulkAdd(items);
    selectedCategoryIds.value = new Set();
}

function addCustomLink(): void {
    const url = customUrl.value.trim();
    const label = customLabel.value.trim();
    if (url === '' || label === '') return;
    bulkAdd([
        {
            link_type: 'url' as const,
            link_id: null,
            translations: props.languages.map((lang) => ({
                lang: lang.code,
                label,
                link_url: url,
            })),
        },
    ]);
    customUrl.value = '';
    customLabel.value = '';
}

function addHomeItem(): void {
    bulkAdd([
        {
            link_type: 'home' as const,
            link_id: null,
            translations: props.languages.map((lang) => ({
                lang: lang.code,
                label: lang.code === 'de' ? 'Startseite' : 'Home',
                link_url: null,
            })),
        },
    ]);
}

function bulkAdd(
    items: Array<{
        link_type: LinkType;
        link_id: number | null;
        translations: Array<{ lang: string; label: string; link_url: string | null }>;
    }>,
): void {
    if (items.length === 0) return;
    router.post(
        `/admin/menus/${props.menu.key}/items/bulk-add`,
        { items },
        { preserveScroll: true },
    );
}
</script>

<template>
    <Head :title="`${menu.name} — ${t('sidebar.navigation_management')}`" />

    <div class="flex flex-col gap-6 p-4">
        <Heading
            :title="menu.name"
            :description="t('menus.description')"
        />

        <div class="grid grid-cols-1 gap-4 lg:grid-cols-[340px_1fr]">
            <!-- ============================================ -->
            <!-- LEFT: Source picker                          -->
            <!-- ============================================ -->
            <div class="flex flex-col gap-3">
                <!-- Pages -->
                <Card>
                    <CardContent class="p-0">
                        <div class="flex items-center justify-between border-b">
                            <button
                                type="button"
                                class="flex flex-1 items-center justify-between px-4 py-3 text-sm font-semibold hover:bg-muted/50"
                                @click="toggleSource('pages')"
                            >
                                {{ t('menus.source_pages') }}
                                <component
                                    :is="openSources.pages ? ChevronUp : ChevronDown"
                                    class="size-4"
                                />
                            </button>
                            <Link
                                href="/admin/pages/create"
                                class="mr-2 inline-flex items-center gap-1 rounded px-2 py-1 text-[11px] font-medium text-primary hover:bg-primary/10"
                                :title="t('menus.create_page_title')"
                            >
                                <PlusCircle class="size-3.5" />
                                {{ t('menus.new') }}
                            </Link>
                        </div>
                        <div v-if="openSources.pages" class="p-3">
                            <div class="relative mb-2">
                                <Search
                                    class="pointer-events-none absolute left-2 top-1/2 size-3.5 -translate-y-1/2 text-muted-foreground"
                                />
                                <Input
                                    v-model="pageSearch"
                                    type="search"
                                    :placeholder="t('menus.search_pages')"
                                    class="h-8 pl-7 text-xs"
                                />
                            </div>
                            <div class="max-h-56 overflow-y-auto rounded border bg-muted/20">
                                <div
                                    v-if="filteredPages.length === 0"
                                    class="px-3 py-6 text-center text-xs text-muted-foreground"
                                >
                                    {{ t('menus.no_pages_found') }}
                                </div>
                                <label
                                    v-for="page in filteredPages"
                                    :key="page.id"
                                    class="flex cursor-pointer items-center gap-2 border-b px-3 py-1.5 text-xs last:border-b-0 hover:bg-muted/50"
                                >
                                    <Checkbox
                                        :model-value="selectedPageIds.has(page.id)"
                                        @update:model-value="togglePageSelection(page.id)"
                                    />
                                    <span class="truncate">{{ page.title }}</span>
                                </label>
                            </div>
                            <div class="mt-2 flex items-center justify-between">
                                <span class="text-[11px] text-muted-foreground">
                                    {{ t('menus.selected_count', { count: selectedPageIds.size }) }}
                                </span>
                                <Button
                                    type="button"
                                    size="sm"
                                    :disabled="selectedPageIds.size === 0"
                                    @click="addSelectedPages"
                                >
                                    <Plus class="size-3.5" />
                                    {{ t('menus.add_to_menu') }}
                                </Button>
                            </div>
                        </div>
                    </CardContent>
                </Card>

                <!-- Categories -->
                <Card>
                    <CardContent class="p-0">
                        <div class="flex items-center justify-between border-b">
                            <button
                                type="button"
                                class="flex flex-1 items-center justify-between px-4 py-3 text-sm font-semibold hover:bg-muted/50"
                                @click="toggleSource('categories')"
                            >
                                {{ t('menus.source_categories') }}
                                <component
                                    :is="openSources.categories ? ChevronUp : ChevronDown"
                                    class="size-4"
                                />
                            </button>
                            <Link
                                href="/admin/pages/categories/create"
                                class="mr-2 inline-flex items-center gap-1 rounded px-2 py-1 text-[11px] font-medium text-primary hover:bg-primary/10"
                                :title="t('menus.create_category_title')"
                            >
                                <PlusCircle class="size-3.5" />
                                {{ t('menus.new') }}
                            </Link>
                        </div>
                        <div v-if="openSources.categories" class="p-3">
                            <div class="relative mb-2">
                                <Search
                                    class="pointer-events-none absolute left-2 top-1/2 size-3.5 -translate-y-1/2 text-muted-foreground"
                                />
                                <Input
                                    v-model="categorySearch"
                                    type="search"
                                    :placeholder="t('menus.search_categories')"
                                    class="h-8 pl-7 text-xs"
                                />
                            </div>
                            <div class="max-h-56 overflow-y-auto rounded border bg-muted/20">
                                <div
                                    v-if="filteredCategories.length === 0"
                                    class="px-3 py-6 text-center text-xs text-muted-foreground"
                                >
                                    {{ t('menus.no_categories_found') }}
                                </div>
                                <label
                                    v-for="cat in filteredCategories"
                                    :key="cat.id"
                                    class="flex cursor-pointer items-center gap-2 border-b px-3 py-1.5 text-xs last:border-b-0 hover:bg-muted/50"
                                >
                                    <Checkbox
                                        :model-value="selectedCategoryIds.has(cat.id)"
                                        @update:model-value="toggleCategorySelection(cat.id)"
                                    />
                                    <span class="truncate">{{ cat.title }}</span>
                                </label>
                            </div>
                            <p class="mt-2 text-[11px] text-muted-foreground">
                                {{ t('menus.categories_hint') }}
                            </p>
                            <div class="mt-2 flex items-center justify-between">
                                <span class="text-[11px] text-muted-foreground">
                                    {{ t('menus.selected_count', { count: selectedCategoryIds.size }) }}
                                </span>
                                <Button
                                    type="button"
                                    size="sm"
                                    :disabled="selectedCategoryIds.size === 0"
                                    @click="addSelectedCategories"
                                >
                                    <Plus class="size-3.5" />
                                    {{ t('menus.add_to_menu') }}
                                </Button>
                            </div>
                        </div>
                    </CardContent>
                </Card>

                <!-- Custom Link -->
                <Card>
                    <CardContent class="p-0">
                        <button
                            type="button"
                            class="flex w-full items-center justify-between border-b px-4 py-3 text-sm font-semibold hover:bg-muted/50"
                            @click="toggleSource('url')"
                        >
                            {{ t('menus.source_custom_link') }}
                            <component
                                :is="openSources.url ? ChevronUp : ChevronDown"
                                class="size-4"
                            />
                        </button>
                        <div v-if="openSources.url" class="space-y-2 p-3">
                            <div class="space-y-1">
                                <Label for="custom-url" class="text-xs">{{ t('menus.url_label') }}</Label>
                                <Input
                                    id="custom-url"
                                    v-model="customUrl"
                                    type="url"
                                    :placeholder="t('menus.url_placeholder')"
                                    class="h-8 text-xs"
                                />
                            </div>
                            <div class="space-y-1">
                                <Label for="custom-label" class="text-xs">{{ t('menus.link_text_label') }}</Label>
                                <Input
                                    id="custom-label"
                                    v-model="customLabel"
                                    :placeholder="t('menus.link_text_placeholder')"
                                    class="h-8 text-xs"
                                />
                            </div>
                            <p class="text-[11px] text-muted-foreground">
                                {{ t('menus.custom_link_hint') }}
                            </p>
                            <div class="flex justify-end">
                                <Button
                                    type="button"
                                    size="sm"
                                    :disabled="!customUrl.trim() || !customLabel.trim()"
                                    @click="addCustomLink"
                                >
                                    <Plus class="size-3.5" />
                                    {{ t('menus.add_to_menu') }}
                                </Button>
                            </div>
                        </div>
                    </CardContent>
                </Card>

                <!-- Home — hidden for the footer menu since a footer doesn't
                     need a Home link (admins were accidentally adding it). -->
                <Card v-if="menu.key !== 'footer'">
                    <CardContent class="p-0">
                        <button
                            type="button"
                            class="flex w-full items-center justify-between border-b px-4 py-3 text-sm font-semibold hover:bg-muted/50"
                            @click="toggleSource('home')"
                        >
                            {{ t('menus.source_home') }}
                            <component
                                :is="openSources.home ? ChevronUp : ChevronDown"
                                class="size-4"
                            />
                        </button>
                        <div v-if="openSources.home" class="p-3">
                            <p class="mb-2 text-[11px] text-muted-foreground">
                                {{ t('menus.home_hint') }}
                            </p>
                            <div class="flex justify-end">
                                <Button type="button" size="sm" @click="addHomeItem">
                                    <Plus class="size-3.5" />
                                    {{ t('menus.add_home') }}
                                </Button>
                            </div>
                        </div>
                    </CardContent>
                </Card>
            </div>

            <!-- ============================================ -->
            <!-- RIGHT: Menu structure                         -->
            <!-- ============================================ -->
            <Card>
                <CardContent class="space-y-1 p-4">
                    <div class="mb-3 flex items-center justify-between">
                        <h2 class="text-sm font-semibold">{{ t('menus.menu_structure') }}</h2>
                        <span class="text-[11px] text-muted-foreground">
                            {{ t('menus.menu_structure_hint') }}
                        </span>
                    </div>

                    <div
                        v-if="tree.length === 0"
                        class="rounded-md border border-dashed py-12 text-center text-sm text-muted-foreground"
                    >
                        {{ t('menus.empty_menu_hint') }}
                    </div>

                    <draggable
                        v-else
                        :model-value="tree"
                        item-key="id"
                        handle=".drag-handle"
                        group="menu"
                        :animation="150"
                        :empty-insert-threshold="20"
                        ghost-class="opacity-40"
                        @update:model-value="
                            (v: TreeNode[]) => {
                                tree = v;
                                syncOrder();
                            }
                        "
                    >
                        <template #item="{ element: node }: { element: TreeNode }">
                            <MenuTreeNode :node="node" :depth="0" />
                        </template>
                    </draggable>
                </CardContent>
            </Card>
        </div>
    </div>
</template>
