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
import FlagImage from '@/components/common/FlagImage.vue';
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
import { setBreadcrumbs } from '@/composables/common/useBreadcrumbs';
import { useDragReorder } from '@/composables/common/useDragReorder';
import { useRowSelection } from '@/composables/common/useRowSelection';
import { useTableQuery } from '@/composables/common/useTableQuery';
import { useAdminLanguage, useFormatDate } from '@/composables/useAdminLocale';
import { useT } from '@/composables/useT';

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
    image: string | null;
    image_url: string | null;
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

type Row = Category & {
    displayName: string;
    displayPermalink: string;
    parent_id: null;
};

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
            parent_id: null,
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
                '/admin/services/parent-categories/reorder',
                { ordered_ids },
                {
                    preserveScroll: true,
                    preserveState: true,
                    onFinish: () => resolve(),
                },
            );
        }),
});

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
    importForm.post('/admin/services/parent-categories/import', {
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
 * Preserve the same search filter the admin has applied to the Index —
 * the export includes only the visible slice rather than the whole
 * table, matching what "Export" intuitively means.
 */
function exportUrl(): string {
    const params = new URLSearchParams();
    if (props.filters.q) {
        params.set('q', props.filters.q);
    }
    const qs = params.toString();
    return qs === ''
        ? '/admin/services/parent-categories/export'
        : `/admin/services/parent-categories/export?${qs}`;
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
            <div class="flex flex-wrap items-center gap-2">
                <!--
                    CSV toolbar: same colour identity as States/Cities
                    (slate/emerald/blue) so admins pattern-match the
                    actions across every list view.
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
                    @click="downloadCsv(exportUrl(), 'parent-categories-export.csv')"
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
                    <Link href="/admin/services/parent-categories/create">
                        <Plus class="size-4" />
                        {{ t('service_parent_categories.create') }}
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
                                    <SortableColumn column="sort_order" :label="t('table.col_order')" :active-column="sortBy" :direction="sortDir" @sort="toggleSort" />
                                </th>
                                <th class="px-4 py-3">
                                    <SortableColumn column="name" :label="t('table.col_name')" :active-column="sortBy" :direction="sortDir" @sort="toggleSort" />
                                </th>
                                <th class="px-4 py-3 font-medium tracking-wide text-muted-foreground uppercase">
                                    {{ t('service_categories.col_image') }}
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
                                :draggable="canDrag"
                                class="border-t border-sidebar-border/70 transition-colors dark:border-sidebar-border"
                                :class="{
                                    'opacity-40': isDragging(node),
                                    '!border-t-2 !border-primary': isDropTarget(node),
                                    'bg-destructive/5': isInvalidDrop(node),
                                    'hover:bg-muted/30':
                                        !isDragging(node) && !isDropTarget(node),
                                }"
                                @dragstart="canDrag && onDragStart($event, node)"
                                @dragover="canDrag && onDragOver($event, node)"
                                @dragleave="canDrag && onDragLeave(node)"
                                @drop="canDrag && onDrop($event, node)"
                                @dragend="onDragEnd"
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
                                        v-if="node.image_url"
                                        class="inline-flex size-10 overflow-hidden rounded-md border border-border bg-muted"
                                        :title="node.displayName"
                                    >
                                        <img
                                            :src="node.image_url"
                                            :alt="node.displayName"
                                            class="size-full object-cover"
                                            loading="lazy"
                                        />
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
                                            <FlagImage v-if="lang.flag" :code="lang.flag" size="xs" />
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

        <SampleCsvPreview
            v-model:open="showSamplePreview"
            url="/admin/services/parent-categories/sample-csv"
            fallback-filename="parent-categories-sample.csv"
        />
    </div>
</template>
