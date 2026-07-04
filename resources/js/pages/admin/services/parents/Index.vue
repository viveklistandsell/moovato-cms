<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import { Pencil, Plus, Star, Trash2, X } from 'lucide-vue-next';
import { computed } from 'vue';
import Heading from '@/components/Heading.vue';
import BulkActions, {
    type BulkAction,
} from '@/components/common/BulkActions.vue';
import Pagination, {
    type PaginationMeta,
} from '@/components/common/Pagination.vue';
import PerPageSelect from '@/components/common/PerPageSelect.vue';
import SearchInput from '@/components/common/SearchInput.vue';
import SortableColumn from '@/components/common/SortableColumn.vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import {
    Card,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import { Checkbox } from '@/components/ui/checkbox';
import { setBreadcrumbs } from '@/composables/common/useBreadcrumbs';
import { useRowSelection } from '@/composables/common/useRowSelection';
import { useTableQuery } from '@/composables/common/useTableQuery';
import { useAdminLanguage, useFormatDate } from '@/composables/useAdminLocale';
import { useT } from '@/composables/useT';
import { getIcon } from '@/lib/iconMap';

const formatDate = useFormatDate();
const t = useT();
const adminLang = useAdminLanguage();

setBreadcrumbs(() => [
    { title: t('sidebar.dashboard'), href: '/dashboard' },
    { title: t('sidebar.services_management'), href: '/admin/services/parent-categories' },
    { title: t('sidebar.service_parent_categories'), href: '/admin/services/parent-categories' },
]);

type Translation = {
    name: string;
    permalink: string;
    short_description: string | null;
};

type Category = {
    id: number;
    name: string;
    icon: string | null;
    status: string;
    is_featured: boolean;
    is_popular: boolean;
    sort_order: number;
    children_count: number;
    translations: Record<string, Translation>;
    created_at: string | null;
};

type Language = {
    code: string;
    name: string;
    native_name: string;
    flag: string | null;
    is_default: boolean;
};

type Filters = {
    q: string | null;
    sort_by: string | null;
    sort_dir: 'asc' | 'desc';
    per_page: number;
};

const props = defineProps<{
    categories: Category[];
    languages: Language[];
    filters: Filters;
    pagination: PaginationMeta;
}>();

defineOptions({});

type Row = Category & { displayName: string; displayPermalink: string };

const defaultLangCode = computed<string>(
    () => props.languages.find((l) => l.is_default)?.code ?? 'de',
);

function pickTranslation(c: Category): { name: string; permalink: string } {
    const langCode = adminLang.value;
    const preferred = c.translations[langCode];
    if (preferred && preferred.name) {
        return { name: preferred.name, permalink: preferred.permalink };
    }
    const fallback = c.translations[defaultLangCode.value];
    if (fallback && fallback.name) {
        return { name: fallback.name, permalink: fallback.permalink };
    }
    return { name: c.name, permalink: '' };
}

const visibleRows = computed<Row[]>(() =>
    props.categories.map((c) => {
        const t = pickTranslation(c);
        return {
            ...c,
            displayName: t.name,
            displayPermalink: t.permalink,
        };
    }),
);

const { search, sortBy, sortDir, perPage, isLoading, setSearch, toggleSort, setPerPage, resetAll } =
    useTableQuery(
        '/admin/services/parent-categories',
        {
            q: props.filters.q,
            sort_by: props.filters.sort_by,
            sort_dir: props.filters.sort_dir,
            per_page: props.filters.per_page,
        },
        { only: ['categories', 'pagination', 'filters'] },
    );

const isFiltered = computed(
    () => (search.value && search.value.length > 0) || sortBy.value !== null,
);

function confirmDelete(c: Row): boolean {
    return confirm(t('table.confirm_delete_named', { name: c.displayName }));
}

const selection = useRowSelection();
const visibleIds = computed(() => visibleRows.value.map((r) => r.id));
const allOnPageSelected = computed(() => selection.areAllSelected(visibleIds.value));
const someOnPageSelected = computed(
    () => !allOnPageSelected.value && selection.someSelected(visibleIds.value),
);

const bulkActions = computed<BulkAction[]>(() => [
    { value: 'publish', label: t('table.bulk_publish') },
    { value: 'draft', label: t('table.bulk_draft') },
    { value: 'inactive', label: t('table.bulk_inactive') },
    { value: 'mark_featured', label: t('service_categories.bulk_mark_featured') },
    { value: 'unmark_featured', label: t('service_categories.bulk_unmark_featured') },
    { value: 'mark_popular', label: t('service_categories.bulk_mark_popular') },
    { value: 'unmark_popular', label: t('service_categories.bulk_unmark_popular') },
    {
        value: 'delete',
        label: t('table.bulk_delete'),
        destructive: true,
        confirm: t('table.bulk_confirm_delete'),
    },
]);

function applyBulkAction(action: string): void {
    if (selection.isEmpty.value) return;
    router.post(
        '/admin/services/parent-categories/bulk-action',
        { action, ids: selection.ids.value },
        {
            preserveScroll: true,
            preserveState: true,
            onSuccess: () => selection.clear(),
        },
    );
}
</script>

<template>
    <Head :title="t('service_parent_categories.title')" />

    <div class="flex flex-col gap-6 p-4">
        <div class="flex items-start justify-between gap-4">
            <Heading
                :title="t('service_parent_categories.title')"
                :description="t('service_parent_categories.description')"
            />
            <Button as-child>
                <Link href="/admin/services/parent-categories/create">
                    <Plus class="size-4" />
                    {{ t('service_parent_categories.create') }}
                </Link>
            </Button>
        </div>

        <Card>
            <CardHeader>
                <div class="flex flex-col items-start justify-between gap-3 sm:flex-row sm:items-center">
                    <div>
                        <CardTitle>{{ t('service_parent_categories.all_categories') }}</CardTitle>
                        <CardDescription>
                            {{ t('table.total_with_languages', { total: pagination.total, langs: languages.length }) }}
                        </CardDescription>
                    </div>
                    <div class="flex w-full items-center gap-2 sm:w-auto sm:justify-end">
                        <PerPageSelect
                            :model-value="perPage"
                            :options="[10, 25, 50, 100]"
                            @update:model-value="setPerPage"
                        />
                        <BulkActions
                            :actions="bulkActions"
                            :count="selection.count.value"
                            @action="applyBulkAction"
                        />
                        <SearchInput
                            :model-value="search"
                            :loading="isLoading"
                            :placeholder="t('service_categories.search_placeholder')"
                            @search="setSearch"
                        />
                        <Button v-if="isFiltered" variant="ghost" size="sm" @click="resetAll">
                            <X class="size-4" />
                            {{ t('table.clear') }}
                        </Button>
                    </div>
                </div>
            </CardHeader>

            <CardContent class="px-0 pb-0">
                <div
                    v-if="visibleRows.length === 0"
                    class="flex flex-col items-center justify-center gap-3 px-6 py-12 text-center"
                >
                    <p class="text-sm text-muted-foreground">
                        {{ isFiltered ? t('table.no_results_filtered') : t('service_parent_categories.empty') }}
                    </p>
                    <Button v-if="!isFiltered" as-child variant="outline">
                        <Link href="/admin/services/parent-categories/create">
                            <Plus class="size-4" />
                            {{ t('service_parent_categories.create') }}
                        </Link>
                    </Button>
                </div>

                <div
                    v-else
                    class="overflow-x-auto border-t border-sidebar-border/70 dark:border-sidebar-border"
                >
                    <table class="w-full text-sm">
                        <thead class="bg-muted/50 text-left text-xs">
                            <tr>
                                <th class="w-10 px-2 py-3">
                                    <div class="flex items-center justify-center">
                                        <Checkbox
                                            :model-value="
                                                allOnPageSelected
                                                    ? true
                                                    : someOnPageSelected
                                                      ? 'indeterminate'
                                                      : false
                                            "
                                            :aria-label="t('table.select_all_on_page')"
                                            @update:model-value="selection.toggleAll(visibleIds)"
                                        />
                                    </div>
                                </th>
                                <th class="w-20 px-2 py-3">
                                    <SortableColumn column="sort_order" :label="t('table.col_order')" :active-column="sortBy" :direction="sortDir" @sort="toggleSort" />
                                </th>
                                <th class="px-4 py-3">
                                    <SortableColumn column="name" :label="t('table.col_name')" :active-column="sortBy" :direction="sortDir" @sort="toggleSort" />
                                </th>
                                <th class="px-4 py-3 font-medium tracking-wide text-muted-foreground uppercase">
                                    Icon
                                </th>
                                <th class="px-4 py-3">
                                    <SortableColumn column="children" :label="t('service_parent_categories.col_children_count')" :active-column="sortBy" :direction="sortDir" @sort="toggleSort" />
                                </th>
                                <th class="px-4 py-3">
                                    <SortableColumn column="translations" :label="t('table.col_translations')" :active-column="sortBy" :direction="sortDir" @sort="toggleSort" />
                                </th>
                                <th class="px-4 py-3">
                                    <SortableColumn column="status" :label="t('table.col_status')" :active-column="sortBy" :direction="sortDir" @sort="toggleSort" />
                                </th>
                                <th class="px-4 py-3">
                                    <SortableColumn column="flags" :label="t('service_categories.col_featured')" :active-column="sortBy" :direction="sortDir" @sort="toggleSort" />
                                </th>
                                <th class="px-4 py-3">
                                    <SortableColumn column="popular" :label="t('service_categories.col_popular')" :active-column="sortBy" :direction="sortDir" @sort="toggleSort" />
                                </th>
                                <th class="px-4 py-3">
                                    <SortableColumn column="created_at" :label="t('table.col_created')" :active-column="sortBy" :direction="sortDir" @sort="toggleSort" />
                                </th>
                                <th class="px-4 py-3 text-right font-medium tracking-wide text-muted-foreground uppercase">
                                    {{ t('table.col_actions') }}
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr
                                v-for="node in visibleRows"
                                :key="node.id"
                                class="border-t border-sidebar-border/70 transition-colors hover:bg-muted/30 dark:border-sidebar-border"
                            >
                                <td class="px-2 py-3">
                                    <div class="flex items-center justify-center">
                                        <Checkbox
                                            :model-value="selection.isSelected(node.id)"
                                            :aria-label="t('table.select_row', { name: node.displayName })"
                                            @update:model-value="selection.toggle(node.id)"
                                        />
                                    </div>
                                </td>
                                <td class="px-2 py-3 text-center">
                                    <span class="inline-flex h-6 min-w-6 items-center justify-center rounded-md bg-muted px-1.5 font-mono text-xs font-medium">
                                        {{ node.sort_order }}
                                    </span>
                                </td>
                                <td class="px-4 py-3">
                                    <Link :href="`/admin/services/parent-categories/${node.id}/edit`" class="font-medium hover:underline">
                                        {{ node.displayName }}
                                    </Link>
                                    <p v-if="node.displayPermalink" class="mt-0.5 text-xs text-muted-foreground">
                                        /{{ node.displayPermalink }}
                                    </p>
                                </td>
                                <td class="px-4 py-3">
                                    <div
                                        v-if="getIcon(node.icon)"
                                        class="inline-flex size-8 items-center justify-center rounded-md bg-muted text-foreground"
                                        :title="node.icon ?? ''"
                                    >
                                        <component :is="getIcon(node.icon)" class="size-4" />
                                    </div>
                                    <span v-else class="text-xs text-muted-foreground">—</span>
                                </td>
                                <td class="px-4 py-3">
                                    <Badge variant="secondary" class="text-[10px]">
                                        {{ node.children_count }}
                                    </Badge>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex flex-wrap gap-1">
                                        <Badge
                                            v-for="lang in languages"
                                            :key="lang.code"
                                            :variant="node.translations[lang.code] ? 'secondary' : 'outline'"
                                            class="text-[10px]"
                                        >
                                            <span v-if="lang.flag">{{ lang.flag }}</span>
                                            {{ lang.code.toUpperCase() }}
                                            <span v-if="!node.translations[lang.code]" class="opacity-50">·{{ t('table.translation_missing') }}</span>
                                        </Badge>
                                    </div>
                                </td>
                                <td class="px-4 py-3">
                                    <Badge
                                        :variant="
                                            node.status === 'published'
                                                ? 'default'
                                                : node.status === 'draft'
                                                  ? 'outline'
                                                  : 'destructive'
                                        "
                                    >
                                        {{ t(`status.${node.status}`) }}
                                    </Badge>
                                </td>
                                <td class="px-4 py-3">
                                    <Star v-if="node.is_featured" class="size-4 fill-amber-400 text-amber-500" />
                                    <span v-else class="text-xs text-muted-foreground">—</span>
                                </td>
                                <td class="px-4 py-3">
                                    <Star v-if="node.is_popular" class="size-4 fill-sky-400 text-sky-500" />
                                    <span v-else class="text-xs text-muted-foreground">—</span>
                                </td>
                                <td class="px-4 py-3">
                                    <span class="text-xs whitespace-nowrap text-muted-foreground">
                                        {{ formatDate(node.created_at) }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-right">
                                    <div class="flex items-center justify-end gap-1">
                                        <Button as-child variant="ghost" size="sm">
                                            <Link :href="`/admin/services/parent-categories/${node.id}/edit`">
                                                <Pencil class="size-4" />
                                            </Link>
                                        </Button>
                                        <Button as-child variant="ghost" size="sm" class="text-destructive hover:text-destructive">
                                            <Link
                                                :href="`/admin/services/parent-categories/${node.id}`"
                                                method="delete"
                                                preserve-scroll
                                                :on-before="() => confirmDelete(node)"
                                                :title="`Delete ${node.displayName}`"
                                            >
                                                <Trash2 class="size-4" />
                                            </Link>
                                        </Button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <Pagination :pagination="pagination" :only="['categories', 'pagination', 'filters']" />
            </CardContent>
        </Card>
    </div>
</template>
