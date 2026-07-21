<script setup lang="ts">
import { Head, Link, router, useForm, usePage } from '@inertiajs/vue3';
import {
    AlertTriangle,
    Download,
    FileSpreadsheet,
    Flag,
    GripVertical,
    Map as MapIcon,
    Pencil,
    Plus,
    Star,
    Trash2,
    Upload,
    X,
} from 'lucide-vue-next';
import { computed, ref } from 'vue';
import { toast } from 'vue-sonner';
import Heading from '@/components/Heading.vue';
import BulkActions, {
    type BulkAction,
} from '@/components/common/BulkActions.vue';
import SampleCsvPreview from '@/components/common/SampleCsvPreview.vue';
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
import { useDragReorder } from '@/composables/common/useDragReorder';
import { useRowSelection } from '@/composables/common/useRowSelection';
import { useTableQuery } from '@/composables/common/useTableQuery';
import { useT } from '@/composables/useT';

const t = useT();

setBreadcrumbs(() => [
    { title: t('sidebar.dashboard'), href: '/dashboard' },
    { title: t('sidebar.directory_management'), href: '/admin/directory/districts' },
    { title: t('locations.districts_title'), href: '/admin/directory/districts' },
]);

type District = {
    id: number;
    city_id: number;
    city_name: string | null;
    city_permalink: string | null;
    state_id: number | null;
    state_name: string | null;
    state_code: string | null;
    country_id: number | null;
    country_name: string | null;
    country_iso: string | null;
    name: string;
    code: string | null;
    permalink: string;
    postal_code_prefix: string | null;
    is_popular: boolean;
    status: string;
    sort_order: number;
    created_at: string | null;
};

type Country = { id: number; name: string; iso_code: string };
type State = { id: number; country_id: number; name: string; code: string };
type City = { id: number; state_id: number; name: string; permalink: string };

type Filters = {
    q: string | null;
    country_id: number | null;
    state_id: number | null;
    city_id: number | null;
    popular: string | null;
    sort_by: string | null;
    sort_dir: 'asc' | 'desc';
    per_page: number;
};

const props = defineProps<{
    districts: District[];
    countries: Country[];
    states: State[];
    cities: City[];
    filters: Filters;
    pagination: PaginationMeta;
}>();

defineOptions({});

const { search, sortBy, sortDir, perPage, isLoading, setSearch, toggleSort, setPerPage, resetAll } =
    useTableQuery(
        '/admin/directory/districts',
        {
            q: props.filters.q,
            sort_by: props.filters.sort_by,
            sort_dir: props.filters.sort_dir,
            per_page: props.filters.per_page,
        },
        { only: ['districts', 'pagination', 'filters', 'states', 'cities'] },
    );

const isFiltered = computed(
    () =>
        (search.value && search.value.length > 0) ||
        sortBy.value !== null ||
        props.filters.country_id !== null ||
        props.filters.state_id !== null ||
        props.filters.city_id !== null ||
        props.filters.popular !== null,
);

/**
 * Cascading filter: picking a country resets state + city, picking a
 * state resets city. Preserves search / sort / perPage across the reload.
 */
function applyFilters(overrides: {
    country_id?: number | null;
    state_id?: number | null;
    city_id?: number | null;
    popular?: string | null;
}): void {
    const country_id =
        overrides.country_id !== undefined ? overrides.country_id : props.filters.country_id;
    const state_id =
        overrides.state_id !== undefined ? overrides.state_id : props.filters.state_id;
    const city_id =
        overrides.city_id !== undefined ? overrides.city_id : props.filters.city_id;
    const popular =
        overrides.popular !== undefined ? overrides.popular : props.filters.popular;

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
    if (popular !== null && popular !== undefined) params.popular = popular;

    router.get('/admin/directory/districts', params, {
        only: ['districts', 'pagination', 'filters', 'states', 'cities'],
        preserveScroll: true,
        preserveState: true,
        replace: true,
    });
}

function onCountryChange(value: unknown): void {
    applyFilters({ country_id: pickId(value), state_id: null, city_id: null });
}
function onStateChange(value: unknown): void {
    applyFilters({ state_id: pickId(value), city_id: null });
}
function onCityChange(value: unknown): void {
    applyFilters({ city_id: pickId(value) });
}

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

/**
 * Drag-reorder is only meaningful WITHIN a single city (sort_order is
 * unique per city). Enable dragging only when a city filter is active
 * AND we're not filtered/sorted in a way that would confuse the ordering.
 */
type Row = District & { parent_id: number };
const visibleRows = computed<Row[]>(() =>
    props.districts.map((d) => ({ ...d, parent_id: d.city_id })),
);

const canDrag = computed(
    () =>
        !(search.value && search.value.length > 0) &&
        sortBy.value === null &&
        props.filters.city_id !== null,
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
    onReorder: ({ parent_id, ordered_ids }) =>
        new Promise<void>((resolve) => {
            router.post(
                '/admin/directory/districts/reorder',
                { city_id: parent_id, ordered_ids },
                {
                    preserveScroll: true,
                    preserveState: true,
                    onFinish: () => resolve(),
                },
            );
        }),
});

function confirmDelete(d: District): boolean {
    return confirm(t('table.confirm_delete_named', { name: d.name }));
}

// Bulk selection
const selection = useRowSelection();
const visibleIds = computed(() => props.districts.map((d) => d.id));
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
        '/admin/directory/districts/bulk-action',
        { action, ids: selection.ids.value },
        {
            preserveScroll: true,
            preserveState: true,
            onSuccess: () => selection.clear(),
        },
    );
}

// =====================================================================
// CSV: Sample preview / Export / Import
// =====================================================================

const fileInputRef = ref<HTMLInputElement | null>(null);
const showSamplePreview = ref(false);

const importForm = useForm({
    file: null as File | null,
});

function openFilePicker(): void {
    fileInputRef.value?.click();
}

function onFileSelected(event: Event): void {
    const input = event.target as HTMLInputElement;
    const file = input.files?.[0];
    if (!file) return;

    importForm.file = file;
    importForm.post('/admin/directory/districts/import', {
        preserveScroll: true,
        forceFormData: true,
        onFinish: () => {
            if (input) input.value = '';
            importForm.reset();
        },
    });
}

type ImportResultShape = {
    created: number;
    updated: number;
    skipped: number;
    total: number;
    errors: Array<{
        row: number;
        errors: Record<string, string[]>;
        values: Record<string, string>;
    }>;
};

const page = usePage();
const importResult = computed<ImportResultShape | null>(() => {
    const flash = (page.props as { flash?: { importResult?: unknown } }).flash;
    return (flash?.importResult as ImportResultShape | null) ?? null;
});

const dismissedImportResult = ref(false);
function dismissImportResult(): void {
    dismissedImportResult.value = true;
}
const showImportResult = computed(
    () =>
        !dismissedImportResult.value &&
        importResult.value !== null &&
        (importResult.value.errors?.length ?? 0) > 0,
);

const downloadingUrls = ref<Set<string>>(new Set());
function isDownloading(url: string): boolean {
    return downloadingUrls.value.has(url);
}

async function downloadCsv(url: string, fallbackFilename: string): Promise<void> {
    if (downloadingUrls.value.has(url)) return;
    const toastId = toast.loading(t('locations.csv_download_starting'));
    downloadingUrls.value.add(url);

    try {
        const response = await fetch(url, {
            headers: { Accept: 'text/csv, text/plain, */*' },
            credentials: 'same-origin',
        });
        if (!response.ok) throw new Error(`HTTP ${response.status}`);

        const disposition = response.headers.get('Content-Disposition') ?? '';
        const match = disposition.match(/filename\*?=(?:UTF-8'')?"?([^";]+)"?/i);
        const filename = match?.[1] ?? fallbackFilename;

        const blob = await response.blob();
        const objectUrl = URL.createObjectURL(blob);
        const anchor = document.createElement('a');
        anchor.href = objectUrl;
        anchor.download = decodeURIComponent(filename);
        document.body.appendChild(anchor);
        anchor.click();
        anchor.remove();
        setTimeout(() => URL.revokeObjectURL(objectUrl), 0);

        toast.success(
            t('locations.csv_download_success', { filename: decodeURIComponent(filename) }),
            { id: toastId },
        );
    } catch (e) {
        const message = e instanceof Error ? e.message : String(e);
        toast.error(t('locations.csv_download_error', { error: message }), { id: toastId });
    } finally {
        downloadingUrls.value.delete(url);
    }
}

/**
 * Preserve the country/state/city/popular/search filters so the export
 * matches what the admin is currently viewing.
 */
function exportUrl(): string {
    const params = new URLSearchParams();
    if (props.filters.country_id !== null) params.set('country_id', String(props.filters.country_id));
    if (props.filters.state_id !== null) params.set('state_id', String(props.filters.state_id));
    if (props.filters.city_id !== null) params.set('city_id', String(props.filters.city_id));
    if (props.filters.popular !== null) params.set('popular', props.filters.popular);
    if (props.filters.q) params.set('q', props.filters.q);
    const qs = params.toString();
    return qs === ''
        ? '/admin/directory/districts/export'
        : `/admin/directory/districts/export?${qs}`;
}
</script>

<template>
    <Head :title="t('locations.districts_title')" />

    <div class="flex flex-col gap-6 p-4">
        <div class="flex items-start justify-between gap-4">
            <Heading
                :title="t('locations.districts_title')"
                :description="t('locations.districts_description')"
            />
            <div class="flex flex-wrap items-center gap-2">
                <!--
                    CSV toolbar: same colour identity as States/Cities/
                    Parent Categories (slate/emerald/blue) so admins
                    pattern-match the actions across every list view.
                -->
                <Button
                    variant="outline"
                    class="group relative h-9 gap-2.5 border-slate-200 bg-gradient-to-br from-slate-50 to-slate-100 px-4 font-medium text-slate-700 shadow-sm ring-1 ring-slate-100 transition-all duration-200 hover:-translate-y-0.5 hover:border-slate-300 hover:from-slate-100 hover:to-slate-200 hover:text-slate-900 hover:shadow-md hover:ring-slate-200 dark:border-slate-700 dark:from-slate-800 dark:to-slate-900 dark:text-slate-100 dark:ring-slate-800/50 dark:hover:from-slate-700 dark:hover:to-slate-800 dark:hover:ring-slate-700"
                    @click="showSamplePreview = true"
                >
                    <span
                        class="flex size-5 items-center justify-center rounded-md bg-slate-200/80 text-slate-700 transition-all duration-200 group-hover:scale-110 group-hover:bg-slate-300 dark:bg-slate-700/60 dark:text-slate-200 dark:group-hover:bg-slate-600"
                    >
                        <FileSpreadsheet class="size-3.5" />
                    </span>
                    {{ t('locations.csv_sample') }}
                </Button>
                <Button
                    variant="outline"
                    class="group relative h-9 gap-2.5 border-emerald-200 bg-gradient-to-br from-emerald-50 to-emerald-100 px-4 font-medium text-emerald-700 shadow-sm ring-1 ring-emerald-100 transition-all duration-200 hover:-translate-y-0.5 hover:border-emerald-300 hover:from-emerald-100 hover:to-emerald-200 hover:text-emerald-900 hover:shadow-md hover:ring-emerald-200 dark:border-emerald-800 dark:from-emerald-950/70 dark:to-emerald-950 dark:text-emerald-200 dark:ring-emerald-900/50 dark:hover:from-emerald-900/70 dark:hover:to-emerald-950 dark:hover:ring-emerald-800"
                    :disabled="isDownloading(exportUrl())"
                    @click="downloadCsv(exportUrl(), 'districts-export.csv')"
                >
                    <span
                        class="flex size-5 items-center justify-center rounded-md bg-emerald-200/80 text-emerald-700 transition-all duration-200 group-hover:-translate-y-px group-hover:scale-110 group-hover:bg-emerald-300 dark:bg-emerald-800/60 dark:text-emerald-200 dark:group-hover:bg-emerald-700"
                    >
                        <Download class="size-3.5" />
                    </span>
                    {{ t('locations.csv_export') }}
                </Button>
                <Button
                    variant="outline"
                    class="group relative h-9 gap-2.5 border-blue-200 bg-gradient-to-br from-blue-50 to-blue-100 px-4 font-medium text-blue-700 shadow-sm ring-1 ring-blue-100 transition-all duration-200 hover:-translate-y-0.5 hover:border-blue-300 hover:from-blue-100 hover:to-blue-200 hover:text-blue-900 hover:shadow-md hover:ring-blue-200 dark:border-blue-800 dark:from-blue-950/70 dark:to-blue-950 dark:text-blue-200 dark:ring-blue-900/50 dark:hover:from-blue-900/70 dark:hover:to-blue-950 dark:hover:ring-blue-800"
                    :disabled="importForm.processing"
                    @click="openFilePicker"
                >
                    <span
                        class="flex size-5 items-center justify-center rounded-md bg-blue-200/80 text-blue-700 transition-all duration-200 group-hover:translate-y-px group-hover:scale-110 group-hover:bg-blue-300 dark:bg-blue-800/60 dark:text-blue-200 dark:group-hover:bg-blue-700"
                    >
                        <Upload class="size-3.5" />
                    </span>
                    {{
                        importForm.processing
                            ? t('locations.csv_import_processing')
                            : t('locations.csv_import')
                    }}
                </Button>
                <input
                    ref="fileInputRef"
                    type="file"
                    accept=".csv,text/csv,text/plain"
                    class="hidden"
                    @change="onFileSelected"
                />
                <Button as-child>
                    <Link href="/admin/directory/districts/create">
                        <Plus class="size-4" />
                        {{ t('locations.district_create') }}
                    </Link>
                </Button>
            </div>
        </div>

        <!-- Import result panel — shown only when there are row errors. -->
        <Card
            v-if="showImportResult && importResult"
            class="border-amber-200 dark:border-amber-900"
        >
            <CardHeader class="flex flex-row items-start justify-between gap-2 space-y-0">
                <div class="flex items-start gap-3">
                    <AlertTriangle class="mt-0.5 size-5 text-amber-600" />
                    <div>
                        <CardTitle class="text-base">
                            {{ t('locations.csv_import_finished') }}
                        </CardTitle>
                        <CardDescription>
                            {{
                                t('locations.csv_import_summary', {
                                    created: importResult.created,
                                    updated: importResult.updated,
                                    skipped: importResult.skipped,
                                })
                            }}
                        </CardDescription>
                    </div>
                </div>
                <Button variant="ghost" size="icon-sm" @click="dismissImportResult">
                    <X class="size-4" />
                </Button>
            </CardHeader>
            <CardContent>
                <div class="overflow-x-auto rounded-md border">
                    <table class="w-full border-collapse text-xs">
                        <thead class="bg-muted/40">
                            <tr>
                                <th class="w-16 border-b px-3 py-2 text-left font-semibold">
                                    {{ t('locations.csv_col_row') }}
                                </th>
                                <th class="w-40 border-b px-3 py-2 text-left font-semibold">
                                    {{ t('locations.csv_col_field') }}
                                </th>
                                <th class="border-b px-3 py-2 text-left font-semibold">
                                    {{ t('locations.csv_col_error') }}
                                </th>
                                <th class="border-b px-3 py-2 text-left font-semibold">
                                    {{ t('locations.csv_col_value') }}
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <template
                                v-for="err in importResult.errors"
                                :key="err.row"
                            >
                                <template
                                    v-for="(messages, field) in err.errors"
                                    :key="`${err.row}-${field}`"
                                >
                                    <tr
                                        v-for="(msg, i) in messages"
                                        :key="`${err.row}-${field}-${i}`"
                                        class="border-t"
                                    >
                                        <td class="px-3 py-2 font-mono text-muted-foreground">
                                            {{ err.row }}
                                        </td>
                                        <td class="px-3 py-2 font-mono">
                                            {{ field }}
                                        </td>
                                        <td class="px-3 py-2 text-destructive">
                                            {{ msg }}
                                        </td>
                                        <td class="px-3 py-2 font-mono text-muted-foreground">
                                            {{ err.values[field] ?? '—' }}
                                        </td>
                                    </tr>
                                </template>
                            </template>
                        </tbody>
                    </table>
                </div>
            </CardContent>
        </Card>

        <Card>
            <CardHeader>
                <div class="flex flex-col items-start justify-between gap-3 sm:flex-row sm:items-center">
                    <div>
                        <CardTitle>{{ t('locations.all_districts') }}</CardTitle>
                        <CardDescription>
                            {{
                                t('locations.districts_total', {
                                    total: pagination.total,
                                })
                            }}
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
                                <SelectValue :placeholder="t('locations.all_countries')" />
                            </SelectTrigger>
                            <SelectContent>
                                <SelectItem value="all">
                                    {{ t('locations.all_countries') }}
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
                            @update:model-value="onStateChange"
                        >
                            <SelectTrigger class="w-36">
                                <SelectValue :placeholder="t('locations.all_states')" />
                            </SelectTrigger>
                            <SelectContent>
                                <SelectItem value="all">
                                    {{ t('locations.all_states') }}
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
                            :model-value="
                                filters.city_id === null
                                    ? 'all'
                                    : String(filters.city_id)
                            "
                            @update:model-value="onCityChange"
                        >
                            <SelectTrigger class="w-40">
                                <SelectValue :placeholder="t('locations.all_cities')" />
                            </SelectTrigger>
                            <SelectContent>
                                <SelectItem value="all">
                                    {{ t('locations.all_cities') }}
                                </SelectItem>
                                <SelectItem
                                    v-for="c in cities"
                                    :key="c.id"
                                    :value="String(c.id)"
                                >
                                    {{ c.name }}
                                </SelectItem>
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
                            :placeholder="t('locations.search_districts_placeholder')"
                            @update:model-value="setSearch"
                        />
                        <Button
                            v-if="isFiltered"
                            variant="outline"
                            size="sm"
                            @click="resetAll(); applyFilters({ country_id: null, state_id: null, city_id: null, popular: null })"
                        >
                            <X class="size-4" />
                            {{ t('table.clear_filters') }}
                        </Button>
                    </div>
                </div>
            </CardHeader>
            <CardContent>
                <p
                    v-if="!canDrag"
                    class="mb-2 text-xs text-muted-foreground"
                >
                    {{ t('locations.districts_drag_hint') }}
                </p>

                <div class="overflow-x-auto rounded-md border">
                    <table class="w-full border-collapse text-sm">
                        <thead class="bg-muted/40 text-xs uppercase">
                            <tr>
                                <th class="w-10 px-2 py-3">
                                    <Checkbox
                                        :model-value="allOnPageSelected"
                                        :indeterminate="someOnPageSelected"
                                        @update:model-value="() => selection.toggleAll(visibleIds)"
                                    />
                                </th>
                                <th class="w-10 px-2 py-3"></th>
                                <th class="w-16 px-4 py-3 text-left">
                                    <SortableColumn column="sort_order" :label="t('table.col_order')" :active-column="sortBy" :direction="sortDir" @sort="toggleSort" />
                                </th>
                                <th class="px-4 py-3 text-left">
                                    <SortableColumn column="name" :label="t('table.col_name')" :active-column="sortBy" :direction="sortDir" @sort="toggleSort" />
                                </th>
                                <th class="w-24 px-4 py-3 text-left">
                                    {{ t('locations.field_code') }}
                                </th>
                                <th class="px-4 py-3 text-left">
                                    <SortableColumn column="city" :label="t('locations.col_city')" :active-column="sortBy" :direction="sortDir" @sort="toggleSort" />
                                </th>
                                <th class="w-32 px-4 py-3 text-left">
                                    <SortableColumn column="postal_code_prefix" :label="t('locations.field_postal_code_prefix')" :active-column="sortBy" :direction="sortDir" @sort="toggleSort" />
                                </th>
                                <th class="w-24 px-4 py-3 text-center">
                                    <SortableColumn column="popular" :label="t('locations.col_popular')" :active-column="sortBy" :direction="sortDir" @sort="toggleSort" />
                                </th>
                                <th class="w-28 px-4 py-3 text-left">
                                    <SortableColumn column="status" :label="t('table.col_status')" :active-column="sortBy" :direction="sortDir" @sort="toggleSort" />
                                </th>
                                <th class="w-24 px-4 py-3 text-right">
                                    {{ t('table.col_actions') }}
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr
                                v-if="districts.length === 0"
                                class="border-t"
                            >
                                <td colspan="10" class="px-4 py-12 text-center text-sm text-muted-foreground">
                                    {{ t('locations.no_districts_yet') }}
                                </td>
                            </tr>
                            <tr
                                v-for="d in visibleRows"
                                :key="d.id"
                                :draggable="canDrag"
                                class="border-t transition-colors"
                                :class="[
                                    isDragging(d) ? 'opacity-40' : '',
                                    isDropTarget(d) && !isInvalidDrop(d) ? 'bg-emerald-50 dark:bg-emerald-950/40' : 'hover:bg-muted/30',
                                    isDropTarget(d) && isInvalidDrop(d) ? 'bg-destructive/10' : '',
                                ]"
                                @dragstart="onDragStart($event, d)"
                                @dragover="onDragOver($event, d)"
                                @dragleave="onDragLeave(d)"
                                @drop="onDrop($event, d)"
                                @dragend="onDragEnd"
                            >
                                <td class="px-2 py-3">
                                    <Checkbox
                                        :model-value="selection.isSelected(d.id)"
                                        @update:model-value="() => selection.toggle(d.id)"
                                    />
                                </td>
                                <td class="px-2 py-3 text-center text-muted-foreground">
                                    <GripVertical
                                        v-if="canDrag"
                                        class="size-4 cursor-grab"
                                        :class="isReordering ? 'opacity-40' : ''"
                                    />
                                </td>
                                <td class="px-4 py-3 text-muted-foreground">
                                    {{ d.sort_order }}
                                </td>
                                <td class="px-4 py-3">
                                    <Link
                                        :href="`/admin/directory/districts/${d.id}/edit`"
                                        class="font-medium hover:underline"
                                    >
                                        {{ d.name }}
                                    </Link>
                                    <p class="text-xs text-muted-foreground">
                                        /{{ d.permalink }}
                                    </p>
                                </td>
                                <td class="px-4 py-3">
                                    <code
                                        v-if="d.code"
                                        class="rounded bg-muted px-1.5 py-0.5 font-mono text-xs"
                                    >{{ d.code }}</code>
                                    <span v-else class="text-xs text-muted-foreground">—</span>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex flex-col gap-1">
                                        <Link
                                            v-if="d.city_id"
                                            :href="`/admin/directory/cities?state_id=${d.state_id ?? ''}`"
                                            class="text-sm font-medium hover:underline"
                                            :title="t('locations.cities')"
                                        >
                                            {{ d.city_name ?? '—' }}
                                        </Link>
                                        <span v-else class="text-sm text-muted-foreground">—</span>
                                        <div class="flex flex-wrap items-center gap-1">
                                            <Link
                                                v-if="d.state_id"
                                                :href="`/admin/directory/states?country_id=${d.country_id ?? ''}`"
                                                class="inline-flex items-center gap-1 rounded-md border border-slate-200 bg-slate-50 px-1.5 py-0.5 text-[10px] font-medium text-slate-700 transition-colors hover:border-slate-300 hover:bg-slate-100 dark:border-slate-800 dark:bg-slate-900 dark:text-slate-200 dark:hover:bg-slate-800"
                                                :title="t('locations.states')"
                                            >
                                                <MapIcon class="size-3" />
                                                {{ d.state_code ?? d.state_name }}
                                            </Link>
                                            <span v-if="d.state_id && d.country_id" class="text-[10px] text-muted-foreground">›</span>
                                            <Link
                                                v-if="d.country_id"
                                                href="/admin/directory/countries"
                                                class="inline-flex items-center gap-1 rounded-md border border-slate-200 bg-slate-50 px-1.5 py-0.5 text-[10px] font-medium text-slate-700 transition-colors hover:border-slate-300 hover:bg-slate-100 dark:border-slate-800 dark:bg-slate-900 dark:text-slate-200 dark:hover:bg-slate-800"
                                                :title="t('locations.countries_title')"
                                            >
                                                <Flag class="size-3" />
                                                {{ d.country_iso ?? d.country_name ?? '?' }}
                                            </Link>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-3 font-mono text-xs text-muted-foreground">
                                    {{ d.postal_code_prefix ?? '—' }}
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <Star
                                        v-if="d.is_popular"
                                        class="mx-auto size-4 fill-amber-400 text-amber-500"
                                    />
                                    <span v-else class="text-xs text-muted-foreground">—</span>
                                </td>
                                <td class="px-4 py-3">
                                    <Badge
                                        :variant="
                                            d.status === 'published'
                                                ? 'default'
                                                : d.status === 'draft'
                                                  ? 'secondary'
                                                  : 'outline'
                                        "
                                    >
                                        {{ t(`status.${d.status}`) }}
                                    </Badge>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex items-center justify-end gap-1">
                                        <Button as-child variant="ghost" size="icon-sm">
                                            <Link :href="`/admin/directory/districts/${d.id}/edit`">
                                                <Pencil class="size-4" />
                                            </Link>
                                        </Button>
                                        <Link
                                            :href="`/admin/directory/districts/${d.id}`"
                                            method="delete"
                                            as="button"
                                            :before="() => confirmDelete(d)"
                                            preserve-scroll
                                            class="inline-flex size-8 items-center justify-center rounded-md text-destructive transition-colors hover:bg-destructive/10"
                                        >
                                            <Trash2 class="size-4" />
                                        </Link>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <Pagination :pagination="pagination" :only="['districts', 'pagination', 'filters', 'states', 'cities']" />
            </CardContent>
        </Card>

        <SampleCsvPreview
            v-model:open="showSamplePreview"
            url="/admin/directory/districts/sample-csv"
            fallback-filename="districts-sample.csv"
        />
    </div>
</template>
