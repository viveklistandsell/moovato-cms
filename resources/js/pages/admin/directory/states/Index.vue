<script setup lang="ts">
import { Head, Link, router, useForm, usePage } from '@inertiajs/vue3';
import {
    AlertTriangle,
    Download,
    FileSpreadsheet,
    GripVertical,
    Pencil,
    Plus,
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
    { title: t('sidebar.directory_management'), href: '/admin/directory/states' },
    { title: t('sidebar.states'), href: '/admin/directory/states' },
]);

type State = {
    id: number;
    country_id: number;
    country_name: string | null;
    country_iso: string | null;
    name: string;
    code: string;
    permalink: string;
    status: string;
    sort_order: number;
    cities_count: number;
    created_at: string | null;
};

type Country = { id: number; name: string; iso_code: string };

type Filters = {
    q: string | null;
    country_id: number | null;
    sort_by: string | null;
    sort_dir: 'asc' | 'desc';
    per_page: number;
};

const props = defineProps<{
    states: State[];
    countries: Country[];
    filters: Filters;
    pagination: PaginationMeta;
}>();

defineOptions({});

const { search, sortBy, sortDir, perPage, isLoading, setSearch, toggleSort, setPerPage, resetAll } =
    useTableQuery(
        '/admin/directory/states',
        {
            q: props.filters.q,
            sort_by: props.filters.sort_by,
            sort_dir: props.filters.sort_dir,
            per_page: props.filters.per_page,
        },
        { only: ['states', 'pagination', 'filters'] },
    );

const isFiltered = computed(
    () =>
        (search.value && search.value.length > 0) ||
        sortBy.value !== null ||
        props.filters.country_id !== null,
);

function onCountryChange(value: unknown): void {
    const normalizedValue =
        typeof value === 'bigint' ? value.toString() : value;
    const id =
        normalizedValue === 'all' ||
        normalizedValue === null ||
        typeof normalizedValue === 'boolean' ||
        typeof normalizedValue === 'object'
            ? null
            : Number(normalizedValue);
    const params: Record<string, string | number> = {};
    if (search.value) params.q = search.value;
    if (sortBy.value) {
        params.sort_by = sortBy.value;
        params.sort_dir = sortDir.value;
    }
    if (perPage.value !== 10) params.per_page = perPage.value;
    if (id !== null) params.country_id = id;
    router.get('/admin/directory/states', params, {
        only: ['states', 'pagination', 'filters'],
        preserveScroll: true,
        preserveState: true,
        replace: true,
    });
}

type Row = State & { parent_id: number };
const visibleRows = computed<Row[]>(() =>
    props.states.map((s) => ({ ...s, parent_id: s.country_id })),
);

const canDrag = computed(
    () =>
        !(search.value && search.value.length > 0) &&
        sortBy.value === null &&
        props.filters.country_id !== null,
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
                '/admin/directory/states/reorder',
                { country_id: parent_id, ordered_ids },
                {
                    preserveScroll: true,
                    preserveState: true,
                    onFinish: () => resolve(),
                },
            );
        }),
});

function confirmDelete(s: State): boolean {
    return confirm(t('table.confirm_delete_named', { name: s.name }));
}

const selection = useRowSelection();
const visibleIds = computed(() => props.states.map((s) => s.id));
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
        '/admin/directory/states/bulk-action',
        { action, ids: selection.ids.value },
        {
            preserveScroll: true,
            preserveState: true,
            onSuccess: () => selection.clear(),
        },
    );
}

const fileInputRef = ref<HTMLInputElement | null>(null);

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
    importForm.post('/admin/directory/states/import', {
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

function exportUrl(): string {
    return props.filters.country_id !== null
        ? `/admin/directory/states/export?country_id=${props.filters.country_id}`
        : '/admin/directory/states/export';
}
</script>

<template>
    <Head :title="t('locations.states_title')" />

    <div class="flex flex-col gap-6 p-4">
        <div class="flex items-start justify-between gap-4">
            <Heading
                :title="t('locations.states_title')"
                :description="t('locations.states_description')"
            />
            <div class="flex flex-wrap items-center gap-2">
                <Button
                    variant="outline"
                    class="group relative h-9 gap-2.5 border-slate-200 bg-gradient-to-br from-slate-50 to-slate-100 px-4 font-medium text-slate-700 shadow-sm ring-1 ring-slate-100 transition-all duration-200 hover:-translate-y-0.5 hover:border-slate-300 hover:from-slate-100 hover:to-slate-200 hover:text-slate-900 hover:shadow-md hover:ring-slate-200 dark:border-slate-700 dark:from-slate-800 dark:to-slate-900 dark:text-slate-100 dark:ring-slate-800/50 dark:hover:from-slate-700 dark:hover:to-slate-800 dark:hover:ring-slate-700"
                    :disabled="isDownloading('/admin/directory/states/sample-csv')"
                    @click="
                        downloadCsv(
                            '/admin/directory/states/sample-csv',
                            'states-sample.csv',
                        )
                    "
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
                    @click="downloadCsv(exportUrl(), 'states-export.csv')"
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
                    <Link href="/admin/directory/states/create">
                        <Plus class="size-4" />
                        {{ t('locations.state_create') }}
                    </Link>
                </Button>
            </div>
        </div>
        <Card
            v-if="showImportResult"
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
                                    created: importResult?.created ?? 0,
                                    updated: importResult?.updated ?? 0,
                                    skipped: importResult?.skipped ?? 0,
                                })
                            }}
                        </CardDescription>
                    </div>
                </div>
                <Button
                    type="button"
                    variant="ghost"
                    size="icon"
                    @click="dismissImportResult"
                >
                    <X class="size-4" />
                </Button>
            </CardHeader>
            <CardContent class="pt-0">
                <div class="overflow-x-auto rounded-md border">
                    <table class="w-full text-sm">
                        <thead class="bg-muted/50 text-left text-xs">
                            <tr>
                                <th class="px-3 py-2">{{ t('locations.csv_col_row') }}</th>
                                <th class="px-3 py-2">{{ t('locations.csv_col_field') }}</th>
                                <th class="px-3 py-2">{{ t('locations.csv_col_error') }}</th>
                                <th class="px-3 py-2">{{ t('locations.csv_col_value') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            <template
                                v-for="err in importResult?.errors ?? []"
                                :key="err.row"
                            >
                                <tr
                                    v-for="(messages, field) in err.errors"
                                    :key="`${err.row}-${field}`"
                                    class="border-t"
                                >
                                    <td class="px-3 py-2 font-mono text-xs">
                                        {{ err.row }}
                                    </td>
                                    <td class="px-3 py-2 font-mono text-xs">
                                        {{ field }}
                                    </td>
                                    <td class="px-3 py-2 text-destructive">
                                        {{ messages.join(' · ') }}
                                    </td>
                                    <td class="px-3 py-2 font-mono text-xs text-muted-foreground">
                                        {{ err.values[field] ?? '' }}
                                    </td>
                                </tr>
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
                        <CardTitle>{{ t('locations.all_states') }}</CardTitle>
                        <CardDescription>
                            {{ t('locations.states_total', { total: pagination.total }) }}
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
                            <SelectTrigger class="w-40">
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
                            :placeholder="t('locations.search_state_placeholder')"
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
                    v-if="states.length === 0"
                    class="flex flex-col items-center justify-center gap-3 px-6 py-12 text-center"
                >
                    <p class="text-sm text-muted-foreground">
                        {{ isFiltered ? t('table.no_results_filtered') : t('locations.states_empty') }}
                    </p>
                    <Button v-if="!isFiltered" as-child variant="outline">
                        <Link href="/admin/directory/states/create">
                            <Plus class="size-4" />
                            {{ t('locations.state_create') }}
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
                                        column="code"
                                        :label="t('locations.col_code')"
                                        :active-column="sortBy"
                                        :direction="sortDir"
                                        @sort="toggleSort"
                                    />
                                </th>
                                <th class="px-4 py-3">
                                    <SortableColumn
                                        column="country"
                                        :label="t('locations.col_country')"
                                        :active-column="sortBy"
                                        :direction="sortDir"
                                        @sort="toggleSort"
                                    />
                                </th>
                                <th class="px-4 py-3">
                                    <SortableColumn
                                        column="cities"
                                        :label="t('locations.col_cities_count')"
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
                                v-for="s in visibleRows"
                                :key="s.id"
                                :draggable="canDrag"
                                class="border-t border-sidebar-border/70 transition-colors dark:border-sidebar-border"
                                :class="{
                                    'opacity-40': isDragging(s),
                                    '!border-t-2 !border-primary': isDropTarget(s),
                                    'bg-destructive/5': isInvalidDrop(s),
                                    'hover:bg-muted/30':
                                        !isDragging(s) && !isDropTarget(s),
                                }"
                                @dragstart="canDrag && onDragStart($event, s)"
                                @dragover="canDrag && onDragOver($event, s)"
                                @dragleave="canDrag && onDragLeave(s)"
                                @drop="canDrag && onDrop($event, s)"
                                @dragend="onDragEnd"
                            >
                                <td class="px-2 py-3">
                                    <div class="flex items-center justify-center">
                                        <Checkbox
                                            :model-value="selection.isSelected(s.id)"
                                            :aria-label="t('table.select_row', { name: s.name })"
                                            @update:model-value="selection.toggle(s.id)"
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
                                        {{ s.sort_order }}
                                    </span>
                                </td>
                                <td class="px-4 py-3">
                                    <Link
                                        :href="`/admin/directory/states/${s.id}/edit`"
                                        class="font-medium hover:underline"
                                    >
                                        {{ s.name }}
                                    </Link>
                                    <p class="mt-0.5 text-xs text-muted-foreground">/{{ s.permalink }}</p>
                                </td>
                                <td class="px-4 py-3">
                                    <Badge variant="outline" class="font-mono text-[10px]">
                                        {{ s.code }}
                                    </Badge>
                                </td>
                                <td class="px-4 py-3">
                                    <span v-if="s.country_name" class="text-sm">{{ s.country_name }}</span>
                                    <span v-else class="text-xs text-muted-foreground">—</span>
                                </td>
                                <td class="px-4 py-3">
                                    <span class="text-xs">{{ s.cities_count }}</span>
                                </td>
                                <td class="px-4 py-3">
                                    <Badge
                                        :variant="
                                            s.status === 'published'
                                                ? 'default'
                                                : s.status === 'draft'
                                                  ? 'outline'
                                                  : 'destructive'
                                        "
                                    >
                                        {{ t(`status.${s.status}`) }}
                                    </Badge>
                                </td>
                                <td class="px-4 py-3">
                                    <span class="text-xs whitespace-nowrap text-muted-foreground">
                                        {{ formatDate(s.created_at) }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-right">
                                    <div class="flex items-center justify-end gap-1">
                                        <Button as-child variant="ghost" size="sm">
                                            <Link :href="`/admin/directory/states/${s.id}/edit`">
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
                                                :href="`/admin/directory/states/${s.id}`"
                                                method="delete"
                                                preserve-scroll
                                                :on-before="() => confirmDelete(s)"
                                                :title="`Delete ${s.name}`"
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

                <Pagination :pagination="pagination" :only="['states', 'pagination', 'filters']" />
            </CardContent>
        </Card>
    </div>
</template>
