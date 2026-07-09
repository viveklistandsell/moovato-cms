<script setup lang="ts">
import {
    ChevronDown,
    ChevronUp,
    ExternalLink,
    FolderTree,
    GripVertical,
    Home,
    Link as LinkIcon,
    Trash2,
} from 'lucide-vue-next';
import { computed, inject } from 'vue';
import draggable from 'vuedraggable';
import LocaleTabs, { type LocaleOption } from '@/components/common/LocaleTabs.vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { useT } from '@/composables/useT';
import type { MenuTreeContext, TreeNode, LinkType } from './tree-types';
import { MENU_TREE_CONTEXT } from './tree-types';

const t = useT();

// Explicit name so the recursive <MenuTreeNode> reference resolves even if
// the component is imported under an alias.
defineOptions({ name: 'MenuTreeNode' });

const props = defineProps<{
    node: TreeNode;
    depth?: number;
}>();

const ctx = inject(MENU_TREE_CONTEXT) as MenuTreeContext;

const depth = computed<number>(() => props.depth ?? 0);

const indentStyle = computed<string>(() =>
    depth.value === 0 ? '' : `margin-left: ${Math.min(depth.value, 6) * 1.5}rem`,
);

const isExpanded = computed<boolean>(() => ctx.expandedItemIds.value.has(props.node.id));

function iconFor(type: LinkType) {
    return type === 'home'
        ? Home
        : type === 'page'
          ? LinkIcon
          : type === 'category'
            ? FolderTree
            : ExternalLink;
}

function typeLabel(type: LinkType): string {
    return type === 'home'
        ? t('menus.type_home')
        : type === 'page'
          ? t('menus.type_page')
          : type === 'category'
            ? t('menus.type_category')
            : t('menus.type_custom_link');
}

function labelOf(node: TreeNode): string {
    return (
        node.translations[ctx.defaultLang.value]?.label ??
        node.linked_title ??
        t('menus.item_default_label', { id: node.id })
    );
}

function onChildrenUpdate(newChildren: TreeNode[]): void {
    props.node.children = newChildren;
    ctx.syncOrder();
}

// Convenience pass-throughs to the shared context.
const draft = computed(() => ctx.itemDrafts.value[props.node.id]);
const langs = computed<LocaleOption[]>(() => ctx.languages.value);

// The "mega" token in css_class turns a top-level dropdown into a full-width
// mega menu on the frontend. Managed via a checkbox so editors don't have to
// remember the magic class name.
const MEGA_CLASS = 'mega';

function hasMegaClass(cls: string | null | undefined): boolean {
    return (cls ?? '').split(/\s+/).includes(MEGA_CLASS);
}

function setMegaClass(on: boolean): void {
    const d = draft.value;
    if (!d) {
        return;
    }
    const tokens = (d.css_class ?? '')
        .split(/\s+/)
        .filter((token) => token !== '' && token !== MEGA_CLASS);
    if (on) {
        tokens.push(MEGA_CLASS);
    }
    d.css_class = tokens.join(' ');
}
</script>

<template>
    <div class="mb-2" :style="indentStyle">
        <!-- Collapsed header row -->
        <div
            class="flex items-center gap-2 rounded-md border bg-card px-2 py-2 transition-colors"
            :class="{
                'opacity-50': !node.is_active,
                'border-primary/60': isExpanded,
            }"
        >
            <button
                type="button"
                class="drag-handle cursor-grab text-muted-foreground hover:text-foreground"
                :title="t('menus.drag_hint')"
            >
                <GripVertical class="size-4" />
            </button>
            <component
                :is="iconFor(node.link_type)"
                class="size-4 text-muted-foreground"
            />
            <button
                type="button"
                class="flex flex-1 items-center gap-2 truncate text-left"
                @click="ctx.toggleExpanded(node.id)"
            >
                <span class="truncate text-sm font-medium">
                    {{ labelOf(node) }}
                </span>
                <Badge
                    class="rounded-sm bg-muted px-1.5 py-0 text-[10px] font-normal text-muted-foreground"
                >
                    {{ typeLabel(node.link_type) }}
                </Badge>
                <span
                    v-if="node.linked_title"
                    class="truncate text-[11px] text-muted-foreground"
                    :title="node.linked_title"
                >
                    → {{ node.linked_title }}
                </span>
            </button>
            <button
                type="button"
                class="text-muted-foreground hover:text-foreground"
                :title="isExpanded ? t('menus.collapse') : t('menus.expand')"
                @click="ctx.toggleExpanded(node.id)"
            >
                <component
                    :is="isExpanded ? ChevronUp : ChevronDown"
                    class="size-4"
                />
            </button>
        </div>

        <!-- Expanded inline editor -->
        <div
            v-if="isExpanded && draft"
            class="mt-1 rounded-md border bg-muted/20 p-3"
        >
            <LocaleTabs
                :model-value="ctx.activeLangFor(node.id)"
                :languages="langs"
                @update:model-value="(v) => ctx.setActiveLangFor(node.id, v)"
            >
                <template #default="{ code }">
                    <div class="space-y-3 pt-3">
                        <div class="space-y-1">
                            <Label :for="`label-${node.id}-${code}`" class="text-xs">
                                {{ t('menus.navigation_label', { code }) }}
                            </Label>
                            <Input
                                :id="`label-${node.id}-${code}`"
                                v-model="draft.translations[code].label"
                                class="h-8 text-xs"
                            />
                        </div>
                        <div v-if="node.link_type === 'url'" class="space-y-1">
                            <Label :for="`url-${node.id}-${code}`" class="text-xs">
                                {{ t('menus.url_for_code', { code }) }}
                            </Label>
                            <Input
                                :id="`url-${node.id}-${code}`"
                                :model-value="draft.translations[code].link_url ?? ''"
                                class="h-8 text-xs"
                                @update:model-value="
                                    (v: string | number) =>
                                        (draft.translations[code].link_url =
                                            String(v) || null)
                                "
                            />
                        </div>
                    </div>
                </template>
            </LocaleTabs>

            <div class="mt-3 grid grid-cols-2 gap-3 border-t pt-3">
                <div class="space-y-1">
                    <Label :for="`css-${node.id}`" class="text-xs">{{ t('menus.css_class') }}</Label>
                    <Input
                        :id="`css-${node.id}`"
                        v-model="draft.css_class"
                        class="h-8 text-xs"
                    />
                </div>
                <div class="space-y-2 pt-5">
                    <label class="flex cursor-pointer items-center gap-2 text-xs">
                        <Checkbox
                            :model-value="draft.open_in_new_tab"
                            @update:model-value="
                                (v) => (draft.open_in_new_tab = v === true)
                            "
                        />
                        {{ t('menus.open_in_new_tab_short') }}
                    </label>
                    <label class="flex cursor-pointer items-center gap-2 text-xs">
                        <Checkbox
                            :model-value="draft.is_active"
                            @update:model-value="
                                (v) => (draft.is_active = v === true)
                            "
                        />
                        {{ t('menus.active') }}
                    </label>
                    <label
                        v-if="depth === 0 && ctx.allowMega"
                        class="flex cursor-pointer items-center gap-2 text-xs"
                    >
                        <Checkbox
                            :model-value="hasMegaClass(draft.css_class)"
                            @update:model-value="(v) => setMegaClass(v === true)"
                        />
                        {{ t('menus.megamenu') }}
                    </label>
                </div>
            </div>

            <div class="mt-3 flex items-center justify-between border-t pt-3">
                <Button
                    type="button"
                    variant="ghost"
                    size="sm"
                    class="text-rose-600 hover:bg-rose-100 hover:text-rose-700 dark:hover:bg-rose-900/40"
                    @click="ctx.destroyItem(node)"
                >
                    <Trash2 class="size-3.5" />
                    {{ t('menus.remove') }}
                </Button>
                <Button type="button" size="sm" @click="ctx.saveItem(node)">
                    {{ t('menus.save_changes') }}
                </Button>
            </div>
        </div>

        <!-- Children (always rendered as a droppable list so users can drop a
             new child into an empty parent — sortablejs needs a real container
             to accept a drop) -->
        <div class="ml-6 mt-2">
            <draggable
                :model-value="node.children"
                item-key="id"
                handle=".drag-handle"
                group="menu"
                :animation="150"
                :empty-insert-threshold="20"
                ghost-class="opacity-40"
                @update:model-value="onChildrenUpdate"
            >
                <template #item="{ element: child }: { element: TreeNode }">
                    <MenuTreeNode :node="child" :depth="depth + 1" />
                </template>
                <template #footer>
                    <div
                        v-if="node.children.length === 0 && isExpanded"
                        class="rounded-md border border-dashed py-2 text-center text-[11px] text-muted-foreground"
                    >
                        {{ t('menus.nest_under', { name: labelOf(node) }) }}
                    </div>
                </template>
            </draggable>
        </div>
    </div>
</template>
