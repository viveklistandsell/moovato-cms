<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import { GripVertical, Pencil, Plus, Star, Trash2, X } from 'lucide-vue-next';
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
import { useDragReorder } from '@/composables/common/useDragReorder';
import { useRowSelection } from '@/composables/common/useRowSelection';
import { useTableQuery } from '@/composables/common/useTableQuery';
import { useFormatDate } from '@/composables/useAdminLocale';
import { useT } from '@/composables/useT';

const formatDate = useFormatDate();

const t = useT();
setBreadcrumbs(() => [
    { title: t('sidebar.dashboard'), href: '/dashboard' },
    { title: t('sidebar.blog_categories'), href: '/admin/blog/categories' },
]);
import { getIcon } from '@/lib/iconMap';

type Translation = {
    name: string;
    permalink: string;
    short_description: string | null;
};

type Category = {
    id: number;
    name: string;
    permalink: string;
    parent_id: number | null;
    parent_name: string | null;
    icon: string | null;
    status: string;
    is_featured: boolean;
    is_default: boolean;
    sort_order: number;
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

// Breadcrumbs set dynamically above via setBreadcrumbs().
defineOptions({});

type Row = Category & { depth: number };

// Light depth calc — show └ indent if parent is on the same page.
const visibleRows = computed<Row[]>(() => {
    const ids = new Set(props.categories.map((c) => c.id));
    return props.categories.map((c) => ({
        ...c,
        depth: c.parent_id !== null && ids.has(c.parent_id) ? 1 : 0,
    }));
});

const {
    search,
    sortBy,
    sortDir,
    perPage,
    isLoading,
    setSearch,
    toggleSort,
    setPerPage,
    resetAll,
} = useTableQuery(
    '/admin/blog/categories',
    {
        q: props.filters.q,
        sort_by: props.filters.sort_by,
        sort_dir: props.filters.sort_dir,
        per_page: props.filters.per_page,
    },
    {
        only: ['categories', 'pagination', 'filters'],
    },
);

const isFiltered = computed(
    () => (search.value && search.value.length > 0) || sortBy.value !== null,
);
const canDrag = computed(() => !isFiltered.value);

const {
    isReordering,
    isDragging,
    isDropTarget,
    isInvalidDrop,
    onDragStart,
    onDragOver,
    onDragLeave,
    onDragEnd,
    onDrop,
} = useDragReorder<Row>({
    getItems: () => visibleRows.value,
    onReorder: ({ parent_id, ordered_ids }) =>
        new Promise<void>((resolve) => {
            router.post(
                '/admin/blog/categories/reorder',
                { parent_id, ordered_ids },
                {
                    preserveScroll: true,
                    preserveState: true,
                    onFinish: () => resolve(),
                },
            );
        }),
});

function confirmDelete(c: Category): boolean {
    return confirm(t('table.confirm_delete_named', { name: c.name }));
}

const selection = useRowSelection();
const visibleIds = computed(() => visibleRows.value.map((r) => r.id));
const allOnPageSelected = computed(() =>
    selection.areAllSelected(visibleIds.value),
);
const someOnPageSelected = computed(
    () => !allOnPageSelected.value && selection.someSelected(visibleIds.value),
);

const bulkActions = computed<BulkAction[]>(() => [
    { value: 'publish', label: t('table.bulk_publish') },
    { value: 'draft', label: t('table.bulk_draft') },
    { value: 'inactive', label: t('table.bulk_inactive') },
    {
        value: 'delete',
        label: t('table.bulk_delete'),
        destructive: true,
        confirm: t('table.bulk_confirm_delete'),
    },
]);

function applyBulkAction(action: string): void {
    if (selection.isEmpty.value) {
        return;
    }
    router.post(
        '/admin/blog/categories/bulk-action',
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
    <Head :title="t('blog.categories_title')" />

    <div class="flex flex-col gap-6 p-4">
        <div class="flex items-start justify-between gap-4">
            <Heading
                :title="t('blog.categories_title')"
                :description="t('blog.categories_description')"
            />
            <Button as-child>
                <Link href="/admin/blog/categories/create">
                    <Plus class="size-4" />
                    {{ t('blog.category_create') }}
                </Link>
            </Button>
        </div>

        <Card>
            <CardHeader>
                <div
                    class="flex flex-col items-start justify-between gap-3 sm:flex-row sm:items-center"
                >
                    <div>
                        <CardTitle>{{ t('table.all_categories') }}</CardTitle>
                        <CardDescription>
                            {{
                                t('table.total_with_languages', {
                                    total: pagination.total,
                                    langs: languages.length,
                                })
                            }}
                        </CardDescription>
                    </div>
                    <div
                        class="flex w-full items-center gap-2 sm:w-auto sm:justify-end"
                    >
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
                            :placeholder="t('table.search_name_permalink')"
                            @search="setSearch"
                        />
                        <Button
                            v-if="isFiltered"
                            variant="ghost"
                            size="sm"
                            @click="resetAll"
                        >
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
                        {{
                            isFiltered
                                ? t('table.no_results_filtered')
                                : t('blog.category_no_items')
                        }}
                    </p>
                    <Button v-if="!isFiltered" as-child variant="outline">
                        <Link href="/admin/blog/categories/create">
                            <Plus class="size-4" />
                            {{ t('blog.category_create') }}
                        </Link>
                    </Button>
                </div>

                <div
                    v-else
                    class="overflow-x-auto border-t border-sidebar-border/70 dark:border-sidebar-border"
                    :class="{ 'opacity-60': isReordering }"
                >
                    <table class="w-full text-sm">
                        <thead class="bg-muted/50 text-left text-xs">
                            <tr>
                                <th class="w-10 px-2 py-3">
                                    <div
                                        class="flex items-center justify-center"
                                    >
                                        <Checkbox
                                            :model-value="
                                                allOnPageSelected
                                                    ? true
                                                    : someOnPageSelected
                                                      ? 'indeterminate'
                                                      : false
                                            "
                                            :aria-label="t('table.select_all_on_page')"
                                            @update:model-value="
                                                selection.toggleAll(visibleIds)
                                            "
                                        />
                                    </div>
                                </th>
                                <th class="w-10 px-2 py-3"></th>
                                <th class="w-20 px-2 py-3">
                                    <SortableColumn
                                        column="sort_order"
                                        :label="t('table.col_order')"
                                        :active-column="sortBy"
                                        :direction="sortDir"
                                        @sort="toggleSort"
                                    />
                                </th>
                                <th class="px-4 py-3">
                                    <SortableColumn
                                        column="name"
                                        :label="t('table.col_name')"
                                        :active-column="sortBy"
                                        :direction="sortDir"
                                        @sort="toggleSort"
                                    />
                                </th>
                                <th class="px-4 py-3">
                                    <SortableColumn
                                        column="parent"
                                        :label="t('common.parent')"
                                        :active-column="sortBy"
                                        :direction="sortDir"
                                        @sort="toggleSort"
                                    />
                                </th>
                                <th
                                    class="px-4 py-3 font-medium tracking-wide text-muted-foreground uppercase"
                                >
                                    Icon
                                </th>
                                <th class="px-4 py-3">
                                    <SortableColumn
                                        column="translations"
                                        :label="t('table.col_translations')"
                                        :active-column="sortBy"
                                        :direction="sortDir"
                                        @sort="toggleSort"
                                    />
                                </th>
                                <th class="px-4 py-3">
                                    <SortableColumn
                                        column="status"
                                        :label="t('table.col_status')"
                                        :active-column="sortBy"
                                        :direction="sortDir"
                                        @sort="toggleSort"
                                    />
                                </th>
                                <th class="px-4 py-3">
                                    <SortableColumn
                                        column="flags"
                                        :label="t('table.col_flags')"
                                        :active-column="sortBy"
                                        :direction="sortDir"
                                        @sort="toggleSort"
                                    />
                                </th>
                                <th class="px-4 py-3">
                                    <SortableColumn
                                        column="is_default"
                                        :label="t('table.col_default')"
                                        :active-column="sortBy"
                                        :direction="sortDir"
                                        @sort="toggleSort"
                                    />
                                </th>
                                <th class="px-4 py-3">
                                    <SortableColumn
                                        column="created_at"
                                        :label="t('table.col_created')"
                                        :active-column="sortBy"
                                        :direction="sortDir"
                                        @sort="toggleSort"
                                    />
                                </th>
                                <th
                                    class="px-4 py-3 text-right font-medium tracking-wide text-muted-foreground uppercase"
                                >
                                    {{ t('table.col_actions') }}
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr
                                v-for="node in visibleRows"
                                :key="node.id"
                                :draggable="canDrag"
                                class="border-t border-sidebar-border/70 transition-colors dark:border-sidebar-border"
                                :class="{
                                    'opacity-40': isDragging(node),
                                    '!border-t-2 !border-primary':
                                        isDropTarget(node),
                                    'bg-destructive/5': isInvalidDrop(node),
                                    'hover:bg-muted/30':
                                        !isDragging(node) &&
                                        !isDropTarget(node),
                                }"
                                @dragstart="
                                    canDrag && onDragStart($event, node)
                                "
                                @dragover="canDrag && onDragOver($event, node)"
                                @dragleave="canDrag && onDragLeave(node)"
                                @drop="canDrag && onDrop($event, node)"
                                @dragend="onDragEnd"
                            >
                                <td class="px-2 py-3">
                                    <div
                                        class="flex items-center justify-center"
                                    >
                                        <Checkbox
                                            :model-value="
                                                selection.isSelected(node.id)
                                            "
                                            :aria-label="t('table.select_row', { name: node.name })"
                                            @update:model-value="
                                                selection.toggle(node.id)
                                            "
                                        />
                                    </div>
                                </td>
                                <td class="px-2 py-3">
                                    <div
                                        class="flex items-center justify-center text-muted-foreground"
                                        :class="
                                            canDrag
                                                ? 'cursor-grab hover:text-foreground active:cursor-grabbing'
                                                : 'cursor-not-allowed opacity-30'
                                        "
                                        :title="
                                            canDrag
                                                ? t('table.reorder_drag_hint')
                                                : t('table.reorder_disabled_hint')
                                        "
                                    >
                                        <GripVertical class="size-4" />
                                    </div>
                                </td>
                                <td class="px-2 py-3 text-center">
                                    <span
                                        class="inline-flex h-6 min-w-6 items-center justify-center rounded-md bg-muted px-1.5 font-mono text-xs font-medium"
                                    >
                                        {{ node.sort_order }}
                                    </span>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex items-center gap-2">
                                        <span
                                            v-if="node.depth > 0"
                                            class="text-muted-foreground"
                                            >└</span
                                        >
                                        <Link
                                            :href="`/admin/blog/categories/${node.id}/edit`"
                                            class="font-medium hover:underline"
                                        >
                                            {{ node.name }}
                                        </Link>
                                        <Badge
                                            v-if="node.is_default"
                                            variant="secondary"
                                            class="text-[10px]"
                                            >{{ t('dashboard.default') }}</Badge
                                        >
                                    </div>
                                    <p
                                        class="mt-0.5 text-xs text-muted-foreground"
                                    >
                                        /{{ node.permalink }}
                                    </p>
                                </td>
                                <td class="px-4 py-3">
                                    <span
                                        v-if="node.parent_name"
                                        class="text-sm"
                                        >{{ node.parent_name }}</span
                                    >
                                    <span
                                        v-else
                                        class="text-xs text-muted-foreground"
                                        >—</span
                                    >
                                </td>
                                <td class="px-4 py-3">
                                    <div
                                        v-if="getIcon(node.icon)"
                                        class="inline-flex size-8 items-center justify-center rounded-md bg-muted text-foreground"
                                        :title="node.icon ?? ''"
                                    >
                                        <component
                                            :is="getIcon(node.icon)"
                                            class="size-4"
                                        />
                                    </div>
                                    <span
                                        v-else
                                        class="text-xs text-muted-foreground"
                                        >—</span
                                    >
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex flex-wrap gap-1">
                                        <Badge
                                            v-for="lang in languages"
                                            :key="lang.code"
                                            :variant="
                                                node.translations[lang.code]
                                                    ? 'secondary'
                                                    : 'outline'
                                            "
                                            class="text-[10px]"
                                        >
                                            <span v-if="lang.flag">{{
                                                lang.flag
                                            }}</span>
                                            {{ lang.code.toUpperCase() }}
                                            <span
                                                v-if="
                                                    !node.translations[
                                                        lang.code
                                                    ]
                                                "
                                                class="opacity-50"
                                                >·{{ t('table.translation_missing') }}</span
                                            >
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
                                    <Star
                                        v-if="node.is_featured"
                                        class="size-4 fill-amber-400 text-amber-500"
                                    />
                                    <span
                                        v-else
                                        class="text-xs text-muted-foreground"
                                        >—</span
                                    >
                                </td>
                                <td class="px-4 py-3">
                                    <Badge
                                        v-if="node.is_default"
                                        variant="secondary"
                                        class="text-[10px]"
                                        >{{ t('dashboard.default') }}</Badge
                                    >
                                    <span
                                        v-else
                                        class="text-xs text-muted-foreground"
                                        >—</span
                                    >
                                </td>
                                <td class="px-4 py-3">
                                    <span
                                        class="text-xs whitespace-nowrap text-muted-foreground"
                                    >
                                        {{ formatDate(node.created_at) }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-right">
                                    <div
                                        class="flex items-center justify-end gap-1"
                                    >
                                        <Button
                                            as-child
                                            variant="ghost"
                                            size="sm"
                                        >
                                            <Link
                                                :href="`/admin/blog/categories/${node.id}/edit`"
                                            >
                                                <Pencil class="size-4" />
                                            </Link>
                                        </Button>
                                        <Button
                                            as-child
                                            variant="ghost"
                                            size="sm"
                                            class="text-destructive hover:text-destructive"
                                        >
                                            <Link
                                                :href="`/admin/blog/categories/${node.id}`"
                                                method="delete"
                                                preserve-scroll
                                                :on-before="
                                                    () => confirmDelete(node)
                                                "
                                                :title="`Delete ${node.name}`"
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

                <Pagination
                    :pagination="pagination"
                    :only="['categories', 'pagination', 'filters']"
                />
            </CardContent>
        </Card>
    </div>
</template>
