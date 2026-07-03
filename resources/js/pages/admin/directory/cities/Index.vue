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
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import { setBreadcrumbs } from '@/composables/common/useBreadcrumbs';
import { useRowSelection } from '@/composables/common/useRowSelection';
import { useTableQuery } from '@/composables/common/useTableQuery';
import { useFormatDate } from '@/composables/useAdminLocale';
import { useT } from '@/composables/useT';

const formatDate = useFormatDate();
const t = useT();

setBreadcrumbs(() => [
    { title: t('sidebar.dashboard'), href: '/dashboard' },
    { title: t('sidebar.directory_management'), href: '/admin/directory/cities' },
    { title: t('sidebar.cities'), href: '/admin/directory/cities' },
]);

type City = {
    id: number;
    state_id: number;
    state_name: string | null;
    state_code: string | null;
    country_id: number | null;
    name: string;
    permalink: string;
    postal_code: string | null;
    is_popular: boolean;
    status: string;
    sort_order: number;
    created_at: string | null;
};

type Country = { id: number; name: string; iso_code: string };
type State = { id: number; country_id: number; name: string; code: string };

type Filters = {
    q: string | null;
    country_id: number | null;
    state_id: number | null;
    popular: string | null;
    sort_by: string | null;
    sort_dir: 'asc' | 'desc';
    per_page: number;
};

const props = defineProps<{
    cities: City[];
    countries: Country[];
    states: State[];
    filters: Filters;
    pagination: PaginationMeta;
}>();

defineOptions({});

const { search, sortBy, sortDir, perPage, isLoading, setSearch, toggleSort, setPerPage, resetAll } =
    useTableQuery(
        '/admin/directory/cities',
        {
            q: props.filters.q,
            sort_by: props.filters.sort_by,
            sort_dir: props.filters.sort_dir,
            per_page: props.filters.per_page,
        },
        { only: ['cities', 'pagination', 'filters', 'states'] },
    );

const isFiltered = computed(
    () =>
        (search.value && search.value.length > 0) ||
        sortBy.value !== null ||
        props.filters.country_id !== null ||
        props.filters.state_id !== null ||
        props.filters.popular !== null,
);

/**
 * Build a fresh query string preserving search/sort/perPage and the given
 * filter values, then perform an Inertia partial reload. We do this manually
 * because `useTableQuery` only tracks the standard table params — every
 * cascade dropdown is module-specific.
 */
function applyFilters(overrides: {
    country_id?: number | null;
    state_id?: number | null;
    popular?: string | null;
}): void {
    const country_id =
        overrides.country_id !== undefined
            ? overrides.country_id
            : props.filters.country_id;
    const state_id =
        overrides.state_id !== undefined
            ? overrides.state_id
            : props.filters.state_id;
    const popular =
        overrides.popular !== undefined
            ? overrides.popular
            : props.filters.popular;

    const params: Record<string, string | number> = {};
    if (search.value) params.q = search.value;
    if (sortBy.value) {
        params.sort_by = sortBy.value;
        params.sort_dir = sortDir.value;
    }
    if (perPage.value !== 10) params.per_page = perPage.value;
    if (country_id !== null) params.country_id = country_id;
    if (state_id !== null) params.state_id = state_id;
    if (popular !== null && popular !== '') params.popular = popular;

    router.get('/admin/directory/cities', params, {
        only: ['cities', 'pagination', 'filters', 'states'],
        preserveScroll: true,
        preserveState: true,
        replace: true,
    });
}

function onCountryChange(value: string): void {
    const id = value === 'all' ? null : Number(value);
    // Switching country resets the state filter — old states are no longer
    // valid under the new country.
    applyFilters({ country_id: id, state_id: null });
}

function onStateChange(value: string): void {
    const id = value === 'all' ? null : Number(value);
    applyFilters({ state_id: id });
}

function onPopularChange(value: string): void {
    applyFilters({ popular: value === 'all' ? null : value });
}

function confirmDelete(c: City): boolean {
    return confirm(t('table.confirm_delete_named', { name: c.name }));
}

const selection = useRowSelection();
const visibleIds = computed(() => props.cities.map((c) => c.id));
const allOnPageSelected = computed(() => selection.areAllSelected(visibleIds.value));
const someOnPageSelected = computed(
    () => !allOnPageSelected.value && selection.someSelected(visibleIds.value),
);

const bulkActions = computed<BulkAction[]>(() => [
    { value: 'publish', label: t('table.bulk_publish') },
    { value: 'draft', label: t('table.bulk_draft') },
    { value: 'inactive', label: t('table.bulk_inactive') },
    { value: 'mark_popular', label: t('locations.bulk_mark_popular') },
    { value: 'unmark_popular', label: t('locations.bulk_unmark_popular') },
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
        '/admin/directory/cities/bulk-action',
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
    <Head :title="t('locations.cities_title')" />

    <div class="flex flex-col gap-6 p-4">
        <div class="flex items-start justify-between gap-4">
            <Heading
                :title="t('locations.cities_title')"
                :description="t('locations.cities_description')"
            />
            <Button as-child>
                <Link href="/admin/directory/cities/create">
                    <Plus class="size-4" />
                    {{ t('locations.city_create') }}
                </Link>
            </Button>
        </div>

        <Card>
            <CardHeader>
                <div class="flex flex-col items-start justify-between gap-3 sm:flex-row sm:items-center">
                    <div>
                        <CardTitle>{{ t('locations.all_cities') }}</CardTitle>
                        <CardDescription>
                            {{ t('locations.cities_total', { total: pagination.total }) }}
                        </CardDescription>
                    </div>
                    <div class="flex w-full items-center gap-2 sm:w-auto sm:justify-end">
                        <Select
                            :model-value="
                                filters.country_id === null
                                    ? 'all'
                                    : String(filters.country_id)
                            "
                            @update:model-value="onCountryChange"
                        >
                            <SelectTrigger class="w-36">
                                <SelectValue :placeholder="t('locations.filter_all_countries')" />
                            </SelectTrigger>
                            <SelectContent>
                                <SelectItem value="all">
                                    {{ t('locations.filter_all_countries') }}
                                </SelectItem>
                                <SelectItem
                                    v-for="c in countries"
                                    :key="c.id"
                                    :value="String(c.id)"
                                >
                                    {{ c.name }}
                                </SelectItem>
                            </SelectContent>
                        </Select>
                        <Select
                            :model-value="
                                filters.state_id === null
                                    ? 'all'
                                    : String(filters.state_id)
                            "
                            :disabled="filters.country_id === null"
                            @update:model-value="onStateChange"
                        >
                            <SelectTrigger class="w-44">
                                <SelectValue :placeholder="t('locations.filter_all_states')" />
                            </SelectTrigger>
                            <SelectContent>
                                <SelectItem value="all">
                                    {{ t('locations.filter_all_states') }}
                                </SelectItem>
                                <SelectItem
                                    v-for="s in states"
                                    :key="s.id"
                                    :value="String(s.id)"
                                >
                                    {{ s.name }}
                                </SelectItem>
                            </SelectContent>
                        </Select>
                        <Select
                            :model-value="filters.popular === null ? 'all' : filters.popular"
                            @update:model-value="onPopularChange"
                        >
                            <SelectTrigger class="w-32">
                                <SelectValue :placeholder="t('locations.filter_popular_all')" />
                            </SelectTrigger>
                            <SelectContent>
                                <SelectItem value="all">{{ t('locations.filter_popular_all') }}</SelectItem>
                                <SelectItem value="1">{{ t('locations.filter_popular_only') }}</SelectItem>
                                <SelectItem value="0">{{ t('locations.filter_popular_none') }}</SelectItem>
                            </SelectContent>
                        </Select>
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
                            :placeholder="t('locations.search_city_placeholder')"
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
                    v-if="cities.length === 0"
                    class="flex flex-col items-center justify-center gap-3 px-6 py-12 text-center"
                >
                    <p class="text-sm text-muted-foreground">
                        {{ isFiltered ? t('table.no_results_filtered') : t('locations.cities_empty') }}
                    </p>
                    <Button v-if="!isFiltered" as-child variant="outline">
                        <Link href="/admin/directory/cities/create">
                            <Plus class="size-4" />
                            {{ t('locations.city_create') }}
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
                                        column="state"
                                        :label="t('locations.col_state')"
                                        :active-column="sortBy"
                                        :direction="sortDir"
                                        @sort="toggleSort"
                                    />
                                </th>
                                <th class="px-4 py-3">
                                    <SortableColumn
                                        column="postal_code"
                                        :label="t('locations.col_postal_code')"
                                        :active-column="sortBy"
                                        :direction="sortDir"
                                        @sort="toggleSort"
                                    />
                                </th>
                                <th class="px-4 py-3">
                                    <SortableColumn
                                        column="popular"
                                        :label="t('locations.col_popular')"
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
                                v-for="c in cities"
                                :key="c.id"
                                class="border-t border-sidebar-border/70 transition-colors hover:bg-muted/30 dark:border-sidebar-border"
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
                                <td class="px-2 py-3 text-center">
                                    <span
                                        class="inline-flex h-6 min-w-6 items-center justify-center rounded-md bg-muted px-1.5 font-mono text-xs font-medium"
                                    >
                                        {{ c.sort_order }}
                                    </span>
                                </td>
                                <td class="px-4 py-3">
                                    <Link
                                        :href="`/admin/directory/cities/${c.id}/edit`"
                                        class="font-medium hover:underline"
                                    >
                                        {{ c.name }}
                                    </Link>
                                    <p class="mt-0.5 text-xs text-muted-foreground">/{{ c.permalink }}</p>
                                </td>
                                <td class="px-4 py-3">
                                    <span v-if="c.state_name" class="text-sm">{{ c.state_name }}</span>
                                    <span v-else class="text-xs text-muted-foreground">—</span>
                                    <p v-if="c.state_code" class="font-mono text-[10px] text-muted-foreground">
                                        {{ c.state_code }}
                                    </p>
                                </td>
                                <td class="px-4 py-3">
                                    <span v-if="c.postal_code" class="font-mono text-xs text-muted-foreground">
                                        {{ c.postal_code }}
                                    </span>
                                    <span v-else class="text-xs text-muted-foreground">—</span>
                                </td>
                                <td class="px-4 py-3">
                                    <Star
                                        v-if="c.is_popular"
                                        class="size-4 fill-amber-400 text-amber-500"
                                    />
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
                                            <Link :href="`/admin/directory/cities/${c.id}/edit`">
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
                                                :href="`/admin/directory/cities/${c.id}`"
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

                <Pagination :pagination="pagination" :only="['cities', 'pagination', 'filters', 'states']" />
            </CardContent>
        </Card>
    </div>
</template>
