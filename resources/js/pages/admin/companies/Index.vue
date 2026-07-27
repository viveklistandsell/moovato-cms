<script setup lang="ts">
import { Head, Link, router, useForm, usePage } from '@inertiajs/vue3';
import {
    BadgeCheck,
    Briefcase,
    GripVertical,
    Pencil,
    Plus,
    Star,
    Trash2,
} from 'lucide-vue-next';
import { computed } from 'vue';
import { toast } from 'vue-sonner';
import Heading from '@/components/Heading.vue';
import BulkActions, {
    type BulkAction,
} from '@/components/common/BulkActions.vue';
import Pagination, {
    type PaginationMeta,
} from '@/components/common/Pagination.vue';
import PerPageSelect from '@/components/common/PerPageSelect.vue';
import SearchInput from '@/components/common/SearchInput.vue';
import FlagImage from '@/components/common/FlagImage.vue';
import SortableColumn from '@/components/common/SortableColumn.vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Checkbox } from '@/components/ui/checkbox';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import { setBreadcrumbs } from '@/composables/common/useBreadcrumbs';
import { useDragReorder } from '@/composables/common/useDragReorder';
import { useRowSelection } from '@/composables/common/useRowSelection';
import { useTableQuery } from '@/composables/common/useTableQuery';
import { useT } from '@/composables/useT';

const t = useT();

setBreadcrumbs(() => [
    { title: t('sidebar.dashboard'), href: '/dashboard' },
    { title: t('sidebar.companies'), href: '/admin/companies' },
]);

type Translation = { lang: string; name: string; permalink: string };
type Company = {
    id: number;
    primary_city_id: number | null;
    primary_district_id: number | null;
    city_name: string | null;
    state_id: number | null;
    state_name: string | null;
    state_code: string | null;
    country_id: number | null;
    country_iso: string | null;
    district_name: string | null;
    logo: string | null;
    verified: boolean;
    is_top_rated: boolean;
    plan_tier: string;
    rating_avg: number;
    review_count: number;
    status: string;
    sort_order: number;
    translations: Translation[];
    created_at: string | null;
};

type Country = { id: number; name: string; iso_code: string};
type State = { id: number; country_id: number; name: string; code: string };
type City = { id: number; state_id: number; name: string; permalink: string };
type District = { id: number; city_id: number; name: string; permalink: string };
type Service = { id: number; parent_category_id: number; name: string };

type Filters = {
    q: string | null;
    country_id: number | null;
    state_id: number | null;
    city_id: number | null;
    district_id: number | null;
    service_id: number | null;
    verified: string | null;
    top_rated: string | null;
    sort_by: string | null;
    sort_dir: 'asc' | 'desc';
    per_page: number;
};

const props = defineProps<{
    companies: Company[];
    countries: Country[];
    states: State[];
    cities: City[];
    districts: District[];
    services: Service[];
    filters: Filters;
    pagination: PaginationMeta;
}>();

const page = usePage<{ locale?: string }>();
const currentLocale = computed<string>(() => (page.props.locale as string) ?? 'de');

function displayName(row: Company): string {
    return (
        row.translations.find((t) => t.lang === currentLocale.value)?.name ??
        row.translations[0]?.name ??
        '—'
    );
}
function displayPermalink(row: Company): string | null {
    return (
        row.translations.find((t) => t.lang === currentLocale.value)?.permalink ??
        row.translations[0]?.permalink ??
        null
    );
}

const { search, sortBy, sortDir, perPage, setSearch, toggleSort, setPerPage, resetAll } =
    useTableQuery(
        '/admin/companies',
        {
            q: props.filters.q,
            sort_by: props.filters.sort_by,
            sort_dir: props.filters.sort_dir,
            per_page: props.filters.per_page,
        },
        {
            only: ['companies', 'pagination', 'filters', 'states', 'cities', 'districts'],
        },
    );

const isFiltered = computed(
    () =>
        (search.value && search.value.length > 0) ||
        sortBy.value !== null ||
        props.filters.country_id !== null ||
        props.filters.state_id !== null ||
        props.filters.city_id !== null ||
        props.filters.district_id !== null ||
        props.filters.service_id !== null ||
        props.filters.verified !== null ||
        props.filters.top_rated !== null,
);

function pickId(value: unknown): number | null {
    const normalized = typeof value === 'bigint' ? value.toString() : value;
    if (
        normalized === 'all' ||
        normalized === null ||
        typeof normalized === 'boolean' ||
        typeof normalized === 'object'
    ) {
        return null;
    }
    return Number(normalized);
}

function applyFilters(overrides: {
    country_id?: number | null;
    state_id?: number | null;
    city_id?: number | null;
    district_id?: number | null;
    service_id?: number | null;
    verified?: string | null;
    top_rated?: string | null;
}): void {
    const country_id =
        overrides.country_id !== undefined ? overrides.country_id : props.filters.country_id;
    const state_id =
        overrides.state_id !== undefined ? overrides.state_id : props.filters.state_id;
    const city_id =
        overrides.city_id !== undefined ? overrides.city_id : props.filters.city_id;
    const district_id =
        overrides.district_id !== undefined ? overrides.district_id : props.filters.district_id;
    const service_id =
        overrides.service_id !== undefined ? overrides.service_id : props.filters.service_id;
    const verified =
        overrides.verified !== undefined ? overrides.verified : props.filters.verified;
    const top_rated =
        overrides.top_rated !== undefined ? overrides.top_rated : props.filters.top_rated;

    const params: Record<string, string | number> = {};
    if (search.value) params.q = search.value;
    if (sortBy.value) {
        params.sort_by = sortBy.value;
        params.sort_dir = sortDir.value;
    }
    if (perPage.value !== 10) params.per_page = perPage.value;
    if (country_id !== null && country_id !== undefined) params.country_id = country_id;
    if (state_id !== null && state_id !== undefined) params.state_id = state_id;
    if (city_id !== null && city_id !== undefined) params.city_id = city_id;
    if (district_id !== null && district_id !== undefined) params.district_id = district_id;
    if (service_id !== null && service_id !== undefined) params.service_id = service_id;
    if (verified !== null && verified !== undefined) params.verified = verified;
    if (top_rated !== null && top_rated !== undefined) params.top_rated = top_rated;

    router.get('/admin/companies', params, {
        only: ['companies', 'pagination', 'filters', 'states', 'cities', 'districts'],
        preserveScroll: true,
        preserveState: true,
        replace: true,
    });
}

function onCountryChange(v: unknown): void {
    applyFilters({ country_id: pickId(v), state_id: null, city_id: null, district_id: null });
}
function onStateChange(v: unknown): void {
    applyFilters({ state_id: pickId(v), city_id: null, district_id: null });
}
function onCityChange(v: unknown): void {
    applyFilters({ city_id: pickId(v), district_id: null });
}
function onDistrictChange(v: unknown): void {
    applyFilters({ district_id: pickId(v) });
}
function onServiceChange(v: unknown): void {
    applyFilters({ service_id: pickId(v) });
}
function onVerifiedChange(v: unknown): void {
    applyFilters({ verified: typeof v === 'string' && v !== 'all' ? v : null });
}
function onTopRatedChange(v: unknown): void {
    applyFilters({ top_rated: typeof v === 'string' && v !== 'all' ? v : null });
}

type Row = Company & { parent_id: number };
const visibleRows = computed<Row[]>(() =>
    props.companies.map((c) => ({ ...c, parent_id: 0 })),
);

const canDrag = computed(
    () =>
        !(search.value && search.value.length > 0) &&
        sortBy.value === null &&
        props.filters.country_id === null &&
        props.filters.state_id === null &&
        props.filters.city_id === null &&
        props.filters.district_id === null &&
        props.filters.service_id === null &&
        props.filters.verified === null &&
        props.filters.top_rated === null,
);

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
                '/admin/companies/reorder',
                { ordered_ids },
                {
                    preserveScroll: true,
                    preserveState: true,
                    onFinish: () => resolve(),
                },
            );
        }),
});

// Row selection + bulk actions
const selection = useRowSelection();
const visibleIds = computed(() => props.companies.map((c) => c.id));
const allVisibleSelected = computed(
    () => visibleIds.value.length > 0 && visibleIds.value.every((id) => selection.isSelected(id)),
);

const bulkActions = computed<BulkAction[]>(() => [
    { value: 'publish', label: t('table.bulk_publish') },
    { value: 'draft', label: t('table.bulk_draft') },
    { value: 'inactive', label: t('table.bulk_inactive') },
    { value: 'mark_verified', label: t('companies.bulk_mark_verified') },
    { value: 'unmark_verified', label: t('companies.bulk_unmark_verified') },
    { value: 'mark_top_rated', label: t('companies.bulk_mark_top_rated') },
    { value: 'unmark_top_rated', label: t('companies.bulk_unmark_top_rated') },
    { value: 'delete', label: t('table.bulk_delete'), destructive: true },
]);

function applyBulkAction(action: string): void {
    if (selection.count.value === 0) return;
    const ids = selection.ids.value;
    router.post(
        '/admin/companies/bulk-action',
        { action, ids },
        {
            preserveScroll: true,
            onSuccess: () => selection.clear(),
        },
    );
}

function confirmDelete(c: Company): boolean {
    return confirm(t('table.confirm_delete_named', { name: displayName(c) }));
}

const deleteForm = useForm({});
function performDelete(c: Company): void {
    if (!confirmDelete(c)) return;
    deleteForm.delete(`/admin/companies/${c.id}`, { preserveScroll: true });
}

</script>

<template>
    <Head :title="t('sidebar.companies')" />

    <div class="flex flex-col gap-6 p-4">
        <div class="flex items-start justify-between gap-4">
            <Heading
                :title="t('companies.companies_title')"
                :description="t('companies.companies_description')"
            />
            <Button as-child>
                <Link href="/admin/companies/create">
                    <Plus class="size-4" />
                    {{ t('companies.company_create') }}
                </Link>
            </Button>
        </div>

        <!-- Filters bar: cascading location + service + trust toggles -->
        <Card>
            <CardHeader class="pb-3">
                <CardTitle class="text-sm font-medium">
                    {{ t('companies.Filters') }}
                </CardTitle>
            </CardHeader>
            <CardContent>
                <div class="flex flex-wrap items-end gap-3">
                    <div class="min-w-[240px] flex-1">
                        <SearchInput
                            :model-value="search"
                            :placeholder="t('companies.search_companies_placeholder')"
                            @update:model-value="setSearch"
                        />
                    </div>

                    <Select
                        :model-value="filters.country_id?.toString() ?? 'all'"
                        @update:model-value="onCountryChange"
                    >
                        <SelectTrigger class="w-[160px]">
                            <SelectValue :placeholder="t('locations.all_countries')" />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem value="all">{{ t('locations.all_countries') }}</SelectItem>
                            <SelectItem
                                v-for="c in countries"
                                :key="c.id"
                                :value="c.id.toString()"
                            >
                                <FlagImage :code="c.iso_code" size="sm" class="mr-2 inline align-middle" />
                                {{ c.name }}
                            </SelectItem>
                        </SelectContent>
                    </Select>

                    <Select
                        :model-value="filters.state_id?.toString() ?? 'all'"
                        :disabled="filters.country_id === null"
                        @update:model-value="onStateChange"
                    >
                        <SelectTrigger class="w-[160px]">
                            <SelectValue :placeholder="t('locations.all_states')" />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem value="all">{{ t('locations.all_states') }}</SelectItem>
                            <SelectItem
                                v-for="s in states"
                                :key="s.id"
                                :value="s.id.toString()"
                            >
                                {{ s.code }} · {{ s.name }}
                            </SelectItem>
                        </SelectContent>
                    </Select>

                    <Select
                        :model-value="filters.city_id?.toString() ?? 'all'"
                        :disabled="filters.state_id === null"
                        @update:model-value="onCityChange"
                    >
                        <SelectTrigger class="w-[160px]">
                            <SelectValue :placeholder="t('locations.all_cities')" />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem value="all">{{ t('locations.all_cities') }}</SelectItem>
                            <SelectItem
                                v-for="ci in cities"
                                :key="ci.id"
                                :value="ci.id.toString()"
                            >
                                {{ ci.name }}
                            </SelectItem>
                        </SelectContent>
                    </Select>

                    <Select
                        :model-value="filters.district_id?.toString() ?? 'all'"
                        :disabled="filters.city_id === null"
                        @update:model-value="onDistrictChange"
                    >
                        <SelectTrigger class="w-[160px]">
                            <SelectValue :placeholder="t('locations.all_districts')" />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem value="all">{{ t('locations.all_districts') }}</SelectItem>
                            <SelectItem
                                v-for="d in districts"
                                :key="d.id"
                                :value="d.id.toString()"
                            >
                                {{ d.name }}
                            </SelectItem>
                        </SelectContent>
                    </Select>

                    <Select
                        :model-value="filters.service_id?.toString() ?? 'all'"
                        @update:model-value="onServiceChange"
                    >
                        <SelectTrigger class="w-[180px]">
                            <SelectValue :placeholder="t('companies.all_services')" />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem value="all">{{ t('companies.all_services') }}</SelectItem>
                            <SelectItem
                                v-for="s in services"
                                :key="s.id"
                                :value="s.id.toString()"
                            >
                                {{ s.name }}
                            </SelectItem>
                        </SelectContent>
                    </Select>

                    <Select
                        :model-value="filters.verified ?? 'all'"
                        @update:model-value="onVerifiedChange"
                    >
                        <SelectTrigger class="w-[140px]">
                            <SelectValue :placeholder="t('companies.verified_all')" />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem value="all">{{ t('companies.verified_all') }}</SelectItem>
                            <SelectItem value="1">{{ t('companies.verified_yes') }}</SelectItem>
                            <SelectItem value="0">{{ t('companies.verified_no') }}</SelectItem>
                        </SelectContent>
                    </Select>

                    <Select
                        :model-value="filters.top_rated ?? 'all'"
                        @update:model-value="onTopRatedChange"
                    >
                        <SelectTrigger class="w-[160px]">
                            <SelectValue :placeholder="t('companies.top_rated_all')" />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem value="all">{{ t('companies.top_rated_all') }}</SelectItem>
                            <SelectItem value="1">{{ t('companies.top_rated_yes') }}</SelectItem>
                            <SelectItem value="0">{{ t('companies.top_rated_no') }}</SelectItem>
                        </SelectContent>
                    </Select>

                    <Button
                        v-if="isFiltered"
                        variant="outline"
                        size="sm"
                        @click="resetAll"
                    >
                        {{ t('companies.clear_filters') }}
                    </Button>
                </div>
            </CardContent>
        </Card>

        <BulkActions
            v-if="selection.count.value > 0"
            :count="selection.count.value"
            :actions="bulkActions"
            @action="applyBulkAction"
        />

        <Card>
            <CardContent class="p-0">
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-muted/40 text-xs uppercase">
                            <tr>
                                <th class="w-10 px-2 py-3">
                                    <Checkbox
                                        :model-value="allVisibleSelected"
                                        @update:model-value="() => selection.toggleAll(visibleIds)"
                                    />
                                </th>
                                <th class="w-10 px-2 py-3"></th>
                                <th class="w-16 px-4 py-3 text-left">
                                    <SortableColumn
                                        column="sort_order"
                                        :label="t('table.col_order')"
                                        :active-column="sortBy"
                                        :direction="sortDir"
                                        @sort="toggleSort"
                                    />
                                </th>
                                <th class="px-4 py-3 text-left">
                                    {{ t('table.col_name') }}
                                </th>
                                <th class="px-4 py-3 text-left">
                                    <SortableColumn
                                        column="city"
                                        :label="t('locations.col_city')"
                                        :active-column="sortBy"
                                        :direction="sortDir"
                                        @sort="toggleSort"
                                    />
                                </th>
                                <th class="w-28 px-4 py-3 text-center">
                                    <SortableColumn
                                        column="rating_avg"
                                        :label="t('companies.col_rating')"
                                        :active-column="sortBy"
                                        :direction="sortDir"
                                        @sort="toggleSort"
                                    />
                                </th>
                                <th class="w-20 px-4 py-3 text-center">
                                    {{ t('companies.col_trust') }}
                                </th>
                                <th class="w-24 px-4 py-3 text-left">
                                    {{ t('companies.col_plan') }}
                                </th>
                                <th class="w-28 px-4 py-3 text-left">
                                    <SortableColumn
                                        column="status"
                                        :label="t('table.col_status')"
                                        :active-column="sortBy"
                                        :direction="sortDir"
                                        @sort="toggleSort"
                                    />
                                </th>
                                <th class="w-24 px-4 py-3 text-right">
                                    {{ t('table.col_actions') }}
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr
                                v-if="companies.length === 0"
                                class="border-t"
                            >
                                <td :colspan="10" class="px-4 py-12 text-center text-muted-foreground">
                                    {{ t('companies.no_companies_yet') }}
                                </td>
                            </tr>
                            <tr
                                v-for="row in visibleRows"
                                v-else
                                :key="row.id"
                                class="border-t transition-colors"
                                :class="{
                                    'opacity-40': isDragging(row),
                                    'bg-emerald-50 dark:bg-emerald-950/40': isDropTarget(row) && !isInvalidDrop(row),
                                    'bg-destructive/10': isInvalidDrop(row),
                                    'hover:bg-muted/30': !isDragging(row) && !isDropTarget(row),
                                }"
                                :draggable="canDrag && !isReordering"
                                @dragstart="canDrag && onDragStart($event, row)"
                                @dragover.prevent="canDrag && onDragOver($event, row)"
                                @dragleave="canDrag && onDragLeave(row)"
                                @dragend="canDrag && onDragEnd()"
                                @drop.prevent="canDrag && onDrop($event, row)"
                            >
                                <td class="px-2 py-3">
                                    <Checkbox
                                        :model-value="selection.isSelected(row.id)"
                                        @update:model-value="() => selection.toggle(row.id)"
                                    />
                                </td>
                                <td class="px-2 py-3 text-muted-foreground">
                                    <div
                                        v-if="canDrag"
                                        class="flex cursor-grab items-center justify-center active:cursor-grabbing"
                                    >
                                        <GripVertical class="size-4" />
                                    </div>
                                </td>
                                <td class="px-4 py-3">
                                    <span
                                        class="inline-flex h-6 min-w-6 items-center justify-center rounded-md bg-muted px-1.5 font-mono text-xs font-medium"
                                    >
                                        {{ row.sort_order }}
                                    </span>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex items-center gap-3">
                                        <div
                                            v-if="row.logo"
                                            class="size-9 shrink-0 overflow-hidden rounded-md border bg-muted"
                                        >
                                            <img
                                                :src="row.logo.startsWith('http') ? row.logo : `/storage/${row.logo}`"
                                                :alt="displayName(row)"
                                                class="size-full object-cover"
                                            />
                                        </div>
                                        <div
                                            v-else
                                            class="flex size-9 shrink-0 items-center justify-center rounded-md border bg-muted text-muted-foreground"
                                        >
                                            <Briefcase class="size-4" />
                                        </div>
                                        <div class="min-w-0">
                                            <Link
                                                :href="`/admin/companies/${row.id}/edit`"
                                                class="font-medium hover:underline"
                                            >
                                                {{ displayName(row) }}
                                            </Link>
                                            <p
                                                v-if="displayPermalink(row)"
                                                class="mt-0.5 truncate text-xs text-muted-foreground"
                                            >
                                                /{{ displayPermalink(row) }}
                                            </p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-3">
                                    <span class="text-sm">{{ row.city_name ?? '—' }}</span>
                                    <p
                                        v-if="row.district_name"
                                        class="text-xs text-muted-foreground"
                                    >
                                        {{ row.district_name }}
                                    </p>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <div v-if="row.review_count > 0" class="flex flex-col items-center">
                                        <span class="text-sm font-semibold">
                                            {{ row.rating_avg.toFixed(1) }}
                                        </span>
                                        <span class="text-[10px] text-muted-foreground">
                                            {{ row.review_count }} {{ t('companies.reviews_short') }}
                                        </span>
                                    </div>
                                    <span v-else class="text-xs text-muted-foreground">—</span>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex items-center justify-center gap-1">
                                        <BadgeCheck
                                            v-if="row.verified"
                                            class="size-4 text-blue-500"
                                            :title="t('companies.verified_yes')"
                                        />
                                        <Star
                                            v-if="row.is_top_rated"
                                            class="size-4 fill-amber-400 text-amber-500"
                                            :title="t('companies.top_rated_yes')"
                                        />
                                        <span
                                            v-if="!row.verified && !row.is_top_rated"
                                            class="text-xs text-muted-foreground"
                                        >—</span>
                                    </div>
                                </td>
                                <td class="px-4 py-3">
                                    <Badge
                                        :variant="row.plan_tier === 'gold' ? 'default' : row.plan_tier === 'silver' ? 'secondary' : 'outline'"
                                        class="text-xs capitalize"
                                    >
                                        {{ row.plan_tier }}
                                    </Badge>
                                </td>
                                <td class="px-4 py-3">
                                    <Badge
                                        :variant="
                                            row.status === 'published'
                                                ? 'default'
                                                : row.status === 'draft'
                                                  ? 'outline'
                                                  : 'destructive'
                                        "
                                        class="text-xs capitalize"
                                    >
                                        {{ row.status }}
                                    </Badge>
                                </td>
                                <td class="px-4 py-3 text-right">
                                    <div class="flex items-center justify-end gap-1">
                                        <Button variant="ghost" size="icon-sm" as-child>
                                            <Link :href="`/admin/companies/${row.id}/edit`">
                                                <Pencil class="size-4" />
                                            </Link>
                                        </Button>
                                        <Button
                                            variant="ghost"
                                            size="icon-sm"
                                            class="text-destructive hover:text-destructive"
                                            @click="performDelete(row)"
                                        >
                                            <Trash2 class="size-4" />
                                        </Button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="flex items-center justify-between gap-4 border-t px-4 py-3">
                    <PerPageSelect :model-value="perPage" @update:model-value="setPerPage" />
                    <Pagination
                        :pagination="pagination"
                        :only="['companies', 'pagination', 'filters']"
                    />
                </div>
            </CardContent>
        </Card>
    </div>
</template>
