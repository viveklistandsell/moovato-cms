<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import { GripVertical, Map, Pencil, Plus, Trash2, X } from 'lucide-vue-next';
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
    { title: t('sidebar.directory_management'), href: '/admin/directory/countries' },
    { title: t('sidebar.countries'), href: '/admin/directory/countries' },
]);

type Country = {
    id: number;
    name: string;
    iso_code: string;
    phone_code: string | null;
    status: string;
    sort_order: number;
    states_count: number;
    created_at: string | null;
};

type Filters = {
    q: string | null;
    sort_by: string | null;
    sort_dir: 'asc' | 'desc';
    per_page: number;
};

const props = defineProps<{
    countries: Country[];
    filters: Filters;
    pagination: PaginationMeta;
}>();

defineOptions({});

const { search, sortBy, sortDir, perPage, isLoading, setSearch, toggleSort, setPerPage, resetAll } =
    useTableQuery(
        '/admin/directory/countries',
        {
            q: props.filters.q,
            sort_by: props.filters.sort_by,
            sort_dir: props.filters.sort_dir,
            per_page: props.filters.per_page,
        },
        { only: ['countries', 'pagination', 'filters'] },
    );

const isFiltered = computed(
    () => (search.value && search.value.length > 0) || sortBy.value !== null,
);

type Row = Country & { parent_id: null };
const visibleRows = computed<Row[]>(() =>
    props.countries.map((c) => ({ ...c, parent_id: null })),
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
    onReorder: ({ ordered_ids }) =>
        new Promise<void>((resolve) => {
            router.post(
                '/admin/directory/countries/reorder',
                { ordered_ids },
                {
                    preserveScroll: true,
                    preserveState: true,
                    onFinish: () => resolve(),
                },
            );
        }),
});

function confirmDelete(c: Country): boolean {
    return confirm(t('table.confirm_delete_named', { name: c.name }));
}

const selection = useRowSelection();
const visibleIds = computed(() => props.countries.map((c) => c.id));
const allOnPageSelected = computed(() => selection.areAllSelected(visibleIds.value));
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
    if (selection.isEmpty.value) return;
    router.post(
        '/admin/directory/countries/bulk-action',
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
    <Head :title="t('locations.countries_title')" />

    <div class="flex flex-col gap-6 p-4">
        <div class="flex items-start justify-between gap-4">
            <Heading
                :title="t('locations.countries_title')"
                :description="t('locations.countries_description')"
            />
            <Button as-child>
                <Link href="/admin/directory/countries/create">
                    <Plus class="size-4" />
                    {{ t('locations.country_create') }}
                </Link>
            </Button>
        </div>

        <Card>
            <CardHeader>
                <div class="flex flex-col items-start justify-between gap-3 sm:flex-row sm:items-center">
                    <div>
                        <CardTitle>{{ t('locations.all_countries') }}</CardTitle>
                        <CardDescription>
                            {{ t('locations.countries_total', { total: pagination.total }) }}
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
                            :placeholder="t('locations.search_country_placeholder')"
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
                    v-if="countries.length === 0"
                    class="flex flex-col items-center justify-center gap-3 px-6 py-12 text-center"
                >
                    <p class="text-sm text-muted-foreground">
                        {{ isFiltered ? t('table.no_results_filtered') : t('locations.countries_empty') }}
                    </p>
                    <Button v-if="!isFiltered" as-child variant="outline">
                        <Link href="/admin/directory/countries/create">
                            <Plus class="size-4" />
                            {{ t('locations.country_create') }}
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
                                        column="iso_code"
                                        :label="t('locations.col_iso_code')"
                                        :active-column="sortBy"
                                        :direction="sortDir"
                                        @sort="toggleSort"
                                    />
                                </th>
                                <th class="px-4 py-3 font-medium tracking-wide text-muted-foreground uppercase">
                                    {{ t('locations.col_phone_code') }}
                                </th>
                                <th class="px-4 py-3">
                                    <SortableColumn
                                        column="states"
                                        :label="t('locations.col_states_count')"
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
                                        column="created_at"
                                        :label="t('table.col_created')"
                                        :active-column="sortBy"
                                        :direction="sortDir"
                                        @sort="toggleSort"
                                    />
                                </th>
                                <th class="px-4 py-3 text-right font-medium tracking-wide text-muted-foreground uppercase">
                                    {{ t('table.col_actions') }}
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr
                                v-for="c in visibleRows"
                                :key="c.id"
                                :draggable="canDrag"
                                class="border-t border-sidebar-border/70 transition-colors dark:border-sidebar-border"
                                :class="{
                                    'opacity-40': isDragging(c),
                                    '!border-t-2 !border-primary': isDropTarget(c),
                                    'bg-destructive/5': isInvalidDrop(c),
                                    'hover:bg-muted/30':
                                        !isDragging(c) && !isDropTarget(c),
                                }"
                                @dragstart="canDrag && onDragStart($event, c)"
                                @dragover="canDrag && onDragOver($event, c)"
                                @dragleave="canDrag && onDragLeave(c)"
                                @drop="canDrag && onDrop($event, c)"
                                @dragend="onDragEnd"
                            >
                                <td class="px-2 py-3">
                                    <div class="flex items-center justify-center">
                                        <Checkbox
                                            :model-value="selection.isSelected(c.id)"
                                            :aria-label="t('table.select_row', { name: c.name })"
                                            @update:model-value="selection.toggle(c.id)"
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
                                        {{ c.sort_order }}
                                    </span>
                                </td>
                                <td class="px-4 py-3">
                                    <Link
                                        :href="`/admin/directory/countries/${c.id}/edit`"
                                        class="font-medium hover:underline"
                                    >
                                        {{ c.name }}
                                    </Link>
                                </td>
                                <td class="px-4 py-3">
                                    <Badge variant="outline" class="font-mono text-[10px]">
                                        {{ c.iso_code }}
                                    </Badge>
                                </td>
                                <td class="px-4 py-3">
                                    <span v-if="c.phone_code" class="font-mono text-xs text-muted-foreground">
                                        {{ c.phone_code }}
                                    </span>
                                    <span v-else class="text-xs text-muted-foreground">—</span>
                                </td>
                                <td class="px-4 py-3">
                                    <Link
                                        v-if="c.states_count > 0"
                                        :href="`/admin/directory/states?country_id=${c.id}`"
                                        class="inline-flex items-center gap-1 rounded-md border border-indigo-200 bg-indigo-50 px-2 py-0.5 text-xs font-medium text-indigo-700 transition-colors hover:border-indigo-300 hover:bg-indigo-100 dark:border-indigo-900 dark:bg-indigo-950 dark:text-indigo-200 dark:hover:bg-indigo-900"
                                        :title="t('locations.states')"
                                    >
                                        <Map class="size-3" />
                                        {{ c.states_count }}
                                    </Link>
                                    <span v-else class="text-xs text-muted-foreground">—</span>
                                </td>
                                <td class="px-4 py-3">
                                    <Badge
                                        :variant="
                                            c.status === 'published'
                                                ? 'default'
                                                : c.status === 'draft'
                                                  ? 'outline'
                                                  : 'destructive'
                                        "
                                    >
                                        {{ t(`status.${c.status}`) }}
                                    </Badge>
                                </td>
                                <td class="px-4 py-3">
                                    <span class="text-xs whitespace-nowrap text-muted-foreground">
                                        {{ formatDate(c.created_at) }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-right">
                                    <div class="flex items-center justify-end gap-1">
                                        <Button as-child variant="ghost" size="sm">
                                            <Link :href="`/admin/directory/countries/${c.id}/edit`">
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
                                                :href="`/admin/directory/countries/${c.id}`"
                                                method="delete"
                                                preserve-scroll
                                                :on-before="() => confirmDelete(c)"
                                                :title="`Delete ${c.name}`"
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

                <Pagination :pagination="pagination" :only="['countries', 'pagination', 'filters']" />
            </CardContent>
        </Card>
    </div>
</template>
