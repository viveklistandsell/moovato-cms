<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import {
    ExternalLink,
    Eye,
    GripVertical,
    Image as ImageIcon,
    Pencil,
    Pin,
    Plus,
    Star,
    Trash2,
    X,
} from 'lucide-vue-next';
import { computed, ref } from 'vue';
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
    { title: t('sidebar.blog_posts'), href: '/admin/blog/posts' },
]);

type Translation = {
    name: string;
    permalink: string;
    short_description: string | null;
    content: string | null;
};

type Post = {
    id: number;
    name: string;
    permalink: string;
    image: string | null;
    image_url: string | null;
    user_id: number | null;
    user_name: string | null;
    category_names: string[];
    tag_names: string[];
    status: string;
    sort_order: number;
    reading_time: number;
    view_count: number;
    is_sticky: boolean;
    is_featured: boolean;
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

type CategoryOption = { id: number; name: string };

type StatusCounts = {
    all: number;
    mine: number;
    published: number;
    draft: number;
    inactive: number;
};

type Filters = {
    q: string | null;
    sort_by: string | null;
    sort_dir: 'asc' | 'desc';
    per_page: number;
    status: 'all' | 'published' | 'draft' | 'inactive';
    mine: boolean;
    category_id: number | null;
    lang: string; // 'any' | <lang code> | 'both'
};

const props = defineProps<{
    posts: Post[];
    languages: Language[];
    categoryOptions: CategoryOption[];
    statusCounts: StatusCounts;
    filters: Filters;
    pagination: PaginationMeta;
}>();

// Breadcrumbs set dynamically above via setBreadcrumbs().
defineOptions({});

type Row = Post & { parent_id: null };

const visibleRows = computed<Row[]>(() =>
    props.posts.map((p) => ({ ...p, parent_id: null })),
);

const {
    search,
    sortBy,
    sortDir,
    perPage,
    isLoading,
    setSearch,
    toggleSort,
    setPerPage,
} = useTableQuery(
    '/admin/blog/posts',
    {
        q: props.filters.q,
        sort_by: props.filters.sort_by,
        sort_dir: props.filters.sort_dir,
        per_page: props.filters.per_page,
    },
    { only: ['posts', 'pagination', 'filters'] },
);

// Local refs for the new filter dimensions. We bypass the table-query composable
// for these and rebuild the full query string each toggle.
const statusFilter = ref<Filters['status']>(props.filters.status);
const mineOnly = ref<boolean>(props.filters.mine);
const categoryId = ref<number | null>(props.filters.category_id);
const langFilter = ref<string>(props.filters.lang);

function applyFilters(): void {
    const params: Record<string, string | number> = {};
    if (search.value) params.q = search.value;
    if (sortBy.value) {
        params.sort_by = sortBy.value;
        params.sort_dir = sortDir.value;
    }
    if (perPage.value && perPage.value !== 10) params.per_page = perPage.value;
    if (statusFilter.value !== 'all') params.status = statusFilter.value;
    if (mineOnly.value) params.mine = '1';
    if (categoryId.value !== null && categoryId.value > 0) {
        params.category_id = categoryId.value;
    }
    if (langFilter.value !== 'any') params.lang = langFilter.value;

    router.get('/admin/blog/posts', params, {
        only: ['posts', 'pagination', 'filters'],
        preserveScroll: true,
        preserveState: true,
        replace: true,
    });
}

function setStatus(next: Filters['status']): void {
    statusFilter.value = next;
    applyFilters();
}

function toggleMine(): void {
    mineOnly.value = !mineOnly.value;
    applyFilters();
}

function onCategoryChange(value: string): void {
    categoryId.value = value === 'all' ? null : Number(value);
    applyFilters();
}

function onLangChange(value: string): void {
    langFilter.value = value;
    applyFilters();
}

function resetAllFilters(): void {
    statusFilter.value = 'all';
    mineOnly.value = false;
    categoryId.value = null;
    langFilter.value = 'any';
    search.value = '';
    sortBy.value = null;
    sortDir.value = 'asc';
    perPage.value = 10;
    router.get(
        '/admin/blog/posts',
        {},
        {
            only: ['posts', 'pagination', 'filters'],
            preserveScroll: true,
            preserveState: true,
            replace: true,
        },
    );
}

// Active state for the "All" pill: no status filter AND not the Mine view.
const allActive = computed(
    () => statusFilter.value === 'all' && !mineOnly.value,
);

type Pill = {
    key: 'all' | 'mine' | 'published' | 'draft' | 'inactive';
    label: string;
    count: number;
    active: boolean;
    activeClass: string;
    idleClass: string;
    onClick: () => void;
};

const pills = computed<Pill[]>(() => [
    {
        key: 'all',
        label: t('table.pill_all'),
        count: props.statusCounts.all,
        active: allActive.value,
        activeClass: 'bg-neutral-900 text-white shadow-sm',
        idleClass:
            'bg-neutral-200 text-neutral-700 hover:bg-neutral-300 dark:bg-neutral-800 dark:text-neutral-300 dark:hover:bg-neutral-700',
        onClick: () => {
            statusFilter.value = 'all';
            mineOnly.value = false;
            applyFilters();
        },
    },
    {
        key: 'mine',
        label: t('table.pill_mine'),
        count: props.statusCounts.mine,
        active: mineOnly.value,
        activeClass: 'bg-neutral-900 text-white shadow-sm',
        idleClass:
            'bg-neutral-200 text-neutral-700 hover:bg-neutral-300 dark:bg-neutral-800 dark:text-neutral-300 dark:hover:bg-neutral-700',
        onClick: toggleMine,
    },
    {
        key: 'published',
        label: t('table.pill_published'),
        count: props.statusCounts.published,
        active: statusFilter.value === 'published',
        activeClass: 'bg-emerald-500 text-white shadow-sm',
        idleClass:
            'bg-emerald-100 text-emerald-700 hover:bg-emerald-200 dark:bg-emerald-900/40 dark:text-emerald-300 dark:hover:bg-emerald-900/60',
        onClick: () => setStatus('published'),
    },
    {
        key: 'draft',
        label: t('table.pill_drafts'),
        count: props.statusCounts.draft,
        active: statusFilter.value === 'draft',
        activeClass: 'bg-amber-500 text-white shadow-sm',
        idleClass:
            'bg-amber-100 text-amber-700 hover:bg-amber-200 dark:bg-amber-900/40 dark:text-amber-300 dark:hover:bg-amber-900/60',
        onClick: () => setStatus('draft'),
    },
    {
        key: 'inactive',
        label: t('table.pill_inactive'),
        count: props.statusCounts.inactive,
        active: statusFilter.value === 'inactive',
        activeClass: 'bg-rose-500 text-white shadow-sm',
        idleClass:
            'bg-rose-100 text-rose-700 hover:bg-rose-200 dark:bg-rose-900/40 dark:text-rose-300 dark:hover:bg-rose-900/60',
        onClick: () => setStatus('inactive'),
    },
]);

const isFiltered = computed(
    () =>
        (search.value && search.value.length > 0) ||
        sortBy.value !== null ||
        statusFilter.value !== 'all' ||
        mineOnly.value ||
        categoryId.value !== null ||
        langFilter.value !== 'any',
);
const canDrag = computed(() => !isFiltered.value);

const {
    isReordering,
    isDragging,
    isDropTarget,
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
                '/admin/blog/posts/reorder',
                { ordered_ids },
                {
                    preserveScroll: true,
                    preserveState: true,
                    onFinish: () => resolve(),
                },
            );
        }),
});

function confirmDelete(p: Post): boolean {
    return confirm(
        t('table.confirm_delete_named', { name: p.name }),
    );
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
        '/admin/blog/posts/bulk-action',
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
    <Head :title="t('blog.posts_title')" />

    <div class="flex flex-col gap-6 p-4">
        <div class="flex items-start justify-between gap-4">
            <Heading
                :title="t('blog.posts_title')"
                :description="t('blog.posts_description')"
            />
            <Button as-child>
                <Link href="/admin/blog/posts/create">
                    <Plus class="size-4" />
                    {{ t('blog.posts_create') }}
                </Link>
            </Button>
        </div>

        <Card>
            <CardHeader>
                <div
                    class="flex flex-col items-start justify-between gap-3 sm:flex-row sm:items-center"
                >
                    <div>
                        <CardTitle>{{ t('table.all_posts') }}</CardTitle>
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
                            :placeholder="t('table.search_title_permalink')"
                            @search="setSearch"
                        />
                        <Button
                            v-if="isFiltered"
                            variant="ghost"
                            size="sm"
                            @click="resetAllFilters"
                        >
                            <X class="size-4" />
                            {{ t('table.clear') }}
                        </Button>
                    </div>
                </div>

                <!-- Colored pill filter row + category + language. -->
                <div
                    class="mt-4 flex flex-col gap-3 border-t pt-4 lg:flex-row lg:flex-wrap lg:items-center lg:justify-between"
                >
                    <div class="flex flex-wrap items-center gap-2">
                        <button
                            v-for="pill in pills"
                            :key="pill.key"
                            type="button"
                            class="rounded-full px-4 py-1.5 text-xs font-semibold transition focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 focus-visible:outline-none"
                            :class="
                                pill.active ? pill.activeClass : pill.idleClass
                            "
                            @click="pill.onClick"
                        >
                            {{ pill.label }}({{ pill.count }})
                        </button>
                    </div>

                    <div class="flex flex-wrap items-center gap-2">
                        <div class="flex items-center gap-2">
                            <span class="text-xs text-muted-foreground">
                                {{ t('table.category_label') }}
                            </span>
                            <Select
                                :model-value="
                                    categoryId === null
                                        ? 'all'
                                        : String(categoryId)
                                "
                                @update:model-value="
                                    (v) => onCategoryChange(v as string)
                                "
                            >
                                <SelectTrigger class="h-9 w-44">
                                    <SelectValue />
                                </SelectTrigger>
                                <SelectContent>
                                    <SelectItem value="all">
                                        {{ t('table.category_all') }}
                                    </SelectItem>
                                    <SelectItem
                                        v-for="opt in categoryOptions"
                                        :key="opt.id"
                                        :value="String(opt.id)"
                                    >
                                        {{ opt.name }}
                                    </SelectItem>
                                </SelectContent>
                            </Select>
                        </div>

                        <div class="flex items-center gap-2">
                            <span class="text-xs text-muted-foreground">
                                {{ t('table.language_label') }}
                            </span>
                            <Select
                                :model-value="langFilter"
                                @update:model-value="
                                    (v) => onLangChange(v as string)
                                "
                            >
                                <SelectTrigger class="h-9 w-40">
                                    <SelectValue />
                                </SelectTrigger>
                                <SelectContent>
                                    <SelectItem value="any">
                                        {{ t('table.language_any') }}
                                    </SelectItem>
                                    <SelectItem
                                        v-for="lang in languages"
                                        :key="lang.code"
                                        :value="lang.code"
                                    >
                                        {{ lang.native_name }} ({{
                                            lang.code.toUpperCase()
                                        }})
                                    </SelectItem>
                                    <SelectItem value="both">
                                        {{ t('table.language_both') }}
                                    </SelectItem>
                                </SelectContent>
                            </Select>
                        </div>
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
                                : t('table.no_posts_yet')
                        }}
                    </p>
                    <Button v-if="!isFiltered" as-child variant="outline">
                        <Link href="/admin/blog/posts/create">
                            <Plus class="size-4" />
                            {{ t('table.create_first_post') }}
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
                                <th
                                    class="w-16 px-2 py-3 font-medium tracking-wide text-muted-foreground uppercase"
                                >
                                    {{ t('table.col_image') }}
                                </th>
                                <th class="px-4 py-3">
                                    <SortableColumn
                                        column="name"
                                        :label="t('table.col_title')"
                                        :active-column="sortBy"
                                        :direction="sortDir"
                                        @sort="toggleSort"
                                    />
                                </th>
                                <th
                                    class="px-4 py-3 font-medium tracking-wide text-muted-foreground uppercase"
                                >
                                    {{ t('table.col_categories_tags') }}
                                </th>
                                <th
                                    class="px-4 py-3 font-medium tracking-wide text-muted-foreground uppercase"
                                >
                                    {{ t('table.col_translations') }}
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
                                <th
                                    class="px-4 py-3 font-medium tracking-wide text-muted-foreground uppercase"
                                >
                                    {{ t('table.col_flags') }}
                                </th>
                                <th class="px-4 py-3">
                                    <SortableColumn
                                        column="view_count"
                                        :label="t('table.col_views')"
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
                                v-for="row in visibleRows"
                                :key="row.id"
                                :draggable="canDrag"
                                class="border-t border-sidebar-border/70 transition-colors dark:border-sidebar-border"
                                :class="{
                                    'opacity-40': isDragging(row),
                                    '!border-t-2 !border-primary':
                                        isDropTarget(row),
                                    'hover:bg-muted/30':
                                        !isDragging(row) && !isDropTarget(row),
                                }"
                                @dragstart="canDrag && onDragStart($event, row)"
                                @dragover="canDrag && onDragOver($event, row)"
                                @dragleave="canDrag && onDragLeave(row)"
                                @drop="canDrag && onDrop($event, row)"
                                @dragend="onDragEnd"
                            >
                                <td class="px-2 py-3">
                                    <div
                                        class="flex items-center justify-center"
                                    >
                                        <Checkbox
                                            :model-value="
                                                selection.isSelected(row.id)
                                            "
                                            :aria-label="t('table.select_row', { name: row.name })"
                                            @update:model-value="
                                                selection.toggle(row.id)
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
                                        {{ row.sort_order }}
                                    </span>
                                </td>
                                <td class="px-2 py-3">
                                    <div
                                        class="flex size-12 items-center justify-center overflow-hidden rounded-md bg-muted"
                                    >
                                        <img
                                            v-if="row.image_url"
                                            :src="row.image_url"
                                            :alt="row.name"
                                            class="size-full object-cover"
                                        />
                                        <ImageIcon
                                            v-else
                                            class="size-5 text-muted-foreground"
                                        />
                                    </div>
                                </td>
                                <td class="px-4 py-3">
                                    <a
                                        :href="`/blog/${row.permalink}`"
                                        target="_blank"
                                        rel="noopener"
                                        class="inline-flex items-center gap-1 font-medium hover:underline"
                                        :title="`Open public page: /blog/${row.permalink}`"
                                    >
                                        {{ row.name }}
                                        <ExternalLink
                                            class="size-3 text-muted-foreground"
                                        />
                                    </a>
                                    <p
                                        class="mt-0.5 text-xs text-muted-foreground"
                                    >
                                        /{{ row.permalink }}
                                    </p>
                                    <p
                                        v-if="row.user_name"
                                        class="mt-0.5 text-[10px] text-muted-foreground"
                                    >
                                        {{ t('dashboard.by_author', { author: row.user_name }) }}
                                    </p>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex flex-col gap-1">
                                        <div
                                            v-if="
                                                row.category_names &&
                                                row.category_names.length > 0
                                            "
                                            class="flex flex-wrap gap-1"
                                        >
                                            <Badge
                                                v-for="(
                                                    name, i
                                                ) in row.category_names"
                                                :key="`c-${i}`"
                                                variant="secondary"
                                                class="text-[10px]"
                                                >{{ name }}</Badge
                                            >
                                        </div>
                                        <span
                                            v-else
                                            class="text-xs text-muted-foreground"
                                            >—</span
                                        >
                                        <div
                                            v-if="
                                                row.tag_names &&
                                                row.tag_names.length > 0
                                            "
                                            class="flex flex-wrap gap-1"
                                        >
                                            <Badge
                                                v-for="(
                                                    name, i
                                                ) in row.tag_names"
                                                :key="`t-${i}`"
                                                variant="outline"
                                                class="text-[10px]"
                                                >#{{ name }}</Badge
                                            >
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex flex-wrap gap-1">
                                        <Badge
                                            v-for="lang in languages"
                                            :key="lang.code"
                                            :variant="
                                                row.translations[lang.code]
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
                                                    !row.translations[lang.code]
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
                                            row.status === 'published'
                                                ? 'default'
                                                : row.status === 'draft'
                                                  ? 'outline'
                                                  : 'destructive'
                                        "
                                    >
                                        {{ t(`status.${row.status}`) }}
                                    </Badge>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex items-center gap-1.5">
                                        <Pin
                                            v-if="row.is_sticky"
                                            class="size-4 text-blue-500"
                                            :title="t('blog.is_sticky')"
                                        />
                                        <Star
                                            v-if="row.is_featured"
                                            class="size-4 fill-amber-400 text-amber-500"
                                            :title="t('blog.is_featured')"
                                        />
                                        <span
                                            v-if="
                                                !row.is_sticky &&
                                                !row.is_featured
                                            "
                                            class="text-xs text-muted-foreground"
                                            >—</span
                                        >
                                    </div>
                                </td>
                                <td class="px-4 py-3">
                                    <div
                                        class="flex items-center gap-1 text-xs text-muted-foreground"
                                    >
                                        <Eye class="size-3" />
                                        {{ row.view_count.toLocaleString() }}
                                    </div>
                                </td>
                                <td class="px-4 py-3">
                                    <span
                                        class="text-xs whitespace-nowrap text-muted-foreground"
                                    >
                                        {{ formatDate(row.created_at) }}
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
                                            :title="t('table.view_row', { name: row.name })"
                                        >
                                            <a
                                                :href="`/blog/${row.permalink}`"
                                                target="_blank"
                                                rel="noopener"
                                            >
                                                <Eye class="size-4" />
                                            </a>
                                        </Button>
                                        <Button
                                            as-child
                                            variant="ghost"
                                            size="sm"
                                        >
                                            <Link
                                                :href="`/admin/blog/posts/${row.id}/edit`"
                                                :title="t('table.edit_row', { name: row.name })"
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
                                                :href="`/admin/blog/posts/${row.id}`"
                                                method="delete"
                                                preserve-scroll
                                                :on-before="
                                                    () => confirmDelete(row)
                                                "
                                                :title="t('table.delete_row', { name: row.name })"
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
                    :only="['posts', 'pagination', 'filters']"
                />
            </CardContent>
        </Card>
    </div>
</template>
