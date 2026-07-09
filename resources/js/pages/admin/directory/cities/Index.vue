<script setup lang="ts">
import { Head, Link, router, useForm, usePage } from '@inertiajs/vue3';
import {
    AlertTriangle,
    Download,
    FileSpreadsheet,
    GripVertical,
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

function onCountryChange(value: unknown): void {
    let v: string | number = (value as string | number) ?? 'all';
    if (typeof v === 'number') v = String(v);
    const id = v === 'all' ? null : Number(v);
    applyFilters({ country_id: id, state_id: null });
}

function onStateChange(value: unknown): void {
    let v: string | number = (value as string | number) ?? 'all';
    if (typeof v === 'number') v = String(v);
    const id = v === 'all' ? null : Number(v);
    applyFilters({ state_id: id });
}

function onPopularChange(value: unknown): void {
    let v: string | number = (value as string | number) ?? 'all';
    if (typeof v === 'number') v = String(v);
    applyFilters({ popular: v === 'all' ? null : String(v) });
}

type Row = City & { parent_id: number };
const visibleRows = computed<Row[]>(() =>
    props.cities.map((c) => ({ ...c, parent_id: c.state_id })),
);

const canDrag = computed(
    () =>
        !(search.value && search.value.length > 0) &&
        sortBy.value === null &&
        props.filters.state_id !== null &&
        props.filters.popular === null,
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
                '/admin/directory/cities/reorder',
                { state_id: parent_id, ordered_ids },
                {
                    preserveScroll: true,
                    preserveState: true,
                    onFinish: () => resolve(),
                },
            );
        }),
});

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
    importForm.post('/admin/directory/cities/import', {
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
        if (!response.ok) {
            throw new Error(`HTTP ${response.status}`);
        }
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
            t('locations.csv_download_success', {
                filename: decodeURIComponent(filename),
            }),
            { id: toastId },
        );
    } catch (e) {
        const message = e instanceof Error ? e.message : String(e);
        toast.error(
            t('locations.csv_download_error', { error: message }),
            { id: toastId },
        );
    } finally {
        downloadingUrls.value.delete(url);
    }
}

/**
 * Preserve the same country/state/popular/search filters the admin has
 * applied to the Index — the export includes only the visible slice
 * rather than the whole table, matching what "Export" intuitively means.
 */
function exportUrl(): string {
    const params = new URLSearchParams();
    if (props.filters.country_id !== null) {
        params.set('country_id', String(props.filters.country_id));
    }
    if (props.filters.state_id !== null) {
        params.set('state_id', String(props.filters.state_id));
    }
    if (props.filters.popular !== null) {
        params.set('popular', props.filters.popular);
    }
    if (props.filters.q) {
        params.set('q', props.filters.q);
    }
    const qs = params.toString();
    return qs === ''
        ? '/admin/directory/cities/export'
        : `/admin/directory/cities/export?${qs}`;
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
            <div class="flex flex-wrap items-center gap-2">
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
                    @click="downloadCsv(exportUrl(), 'cities-export.csv')"
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
                    <Link href="/admin/directory/cities/create">
                        <Plus class="size-4" />
                        {{ t('locations.city_create') }}
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
                <Button
                    variant="ghost"
                    size="icon-sm"
                    @click="dismissImportResult"
                >
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

        <SampleCsvPreview
            v-model:open="showSamplePreview"
            url="/admin/directory/cities/sample-csv"
            fallback-filename="cities-sample.csv"
        />
    </div>
</template>
