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
import { useDragReorder } from '@/composables/common/useDragReorder';
import { useRowSelection } from '@/composables/common/useRowSelection';
import { useTableQuery } from '@/composables/common/useTableQuery';

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

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Dashboard', href: '/dashboard' },
            { title: 'Blog', href: '/admin/blog/posts' },
            { title: 'Posts', href: '/admin/blog/posts' },
        ],
    },
});

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
    router.get('/admin/blog/posts', {}, {
        only: ['posts', 'pagination', 'filters'],
        preserveScroll: true,
        preserveState: true,
        replace: true,
    });
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
        label: 'All',
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
        label: 'Mine',
        count: props.statusCounts.mine,
        active: mineOnly.value,
        activeClass: 'bg-neutral-900 text-white shadow-sm',
        idleClass:
            'bg-neutral-200 text-neutral-700 hover:bg-neutral-300 dark:bg-neutral-800 dark:text-neutral-300 dark:hover:bg-neutral-700',
        onClick: toggleMine,
    },
    {
        key: 'published',
        label: 'Published',
        count: props.statusCounts.published,
        active: statusFilter.value === 'published',
        activeClass: 'bg-emerald-500 text-white shadow-sm',
        idleClass:
            'bg-emerald-100 text-emerald-700 hover:bg-emerald-200 dark:bg-emerald-900/40 dark:text-emerald-300 dark:hover:bg-emerald-900/60',
        onClick: () => setStatus('published'),
    },
    {
        key: 'draft',
        label: 'Drafts',
        count: props.statusCounts.draft,
        active: statusFilter.value === 'draft',
        activeClass: 'bg-amber-500 text-white shadow-sm',
        idleClass:
            'bg-amber-100 text-amber-700 hover:bg-amber-200 dark:bg-amber-900/40 dark:text-amber-300 dark:hover:bg-amber-900/60',
        onClick: () => setStatus('draft'),
    },
    {
        key: 'inactive',
        label: 'Inactive',
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
    return confirm(`Delete post "${p.name}"?`);
}

const selection = useRowSelection();
const visibleIds = computed(() => visibleRows.value.map((r) => r.id));
const allOnPageSelected = computed(() =>
    selection.areAllSelected(visibleIds.value),
);
const someOnPageSelected = computed(
    () =>
        !allOnPageSelected.value && selection.someSelected(visibleIds.value),
);

const bulkActions: BulkAction[] = [
    { value: 'publish', label: 'Publish' },
    { value: 'draft', label: 'Set to draft' },
    { value: 'inactive', label: 'Mark inactive' },
    {
        value: 'delete',
        label: 'Delete',
        destructive: true,
        confirm: 'Delete {count} selected post(s)?',
    },
];

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

function formatDate(iso: string | null): string {
    if (!iso) {
        return '—';
    }
    return new Date(iso).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
    });
}
</script>

<template>
    <Head title="Blog posts" />

    <div class="flex flex-col gap-6 p-4">
        <div class="flex items-start justify-between gap-4">
            <Heading
                title="Blog posts"
                :description="
                    canDrag
                        ? 'Drag a row by the handle to reorder. Sort order is rebuilt automatically.'
                        : 'Reorder is disabled while filtered or custom-sorted. Clear filters to drag.'
                "
            />
            <Button as-child>
                <Link href="/admin/blog/posts/create">
                    <Plus class="size-4" />
                    New post
                </Link>
            </Button>
        </div>

        <Card>
            <CardHeader>
                <div
                    class="flex flex-col items-start justify-between gap-3 sm:flex-row sm:items-center"
                >
                    <div>
                        <CardTitle>All posts</CardTitle>
                        <CardDescription>
                            {{ pagination.total }} total ·
                            {{ languages.length }} languages active
                        </CardDescription>
                    </div>
                    <div
                        class="flex w-full items-center gap-2 sm:w-auto sm:justify-end"
                    >
                        <PerPageSelect
                            :model-value="perPage"
                            :options="[10, 25, 50, 100]"
                            label="Per page"
                            @update:model-value="setPerPage"
                        />
                        <BulkActions
                            :actions="bulkActions"
                            :count="selection.count.value"
                            label="Bulk action"
                            @action="applyBulkAction"
                        />
                        <SearchInput
                            :model-value="search"
                            :loading="isLoading"
                            placeholder="Search title or permalink…"
                            @search="setSearch"
                        />
                        <Button
                            v-if="isFiltered"
                            variant="ghost"
                            size="sm"
                            @click="resetAllFilters"
                        >
                            <X class="size-4" />
                            Clear
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
                            class="rounded-full px-4 py-1.5 text-xs font-semibold transition focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2"
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
                                Category
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
                                        All categories
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
                                Language
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
                                        Any language
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
                                        Has both translations
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
                                ? 'No posts match your filters.'
                                : 'No posts yet.'
                        }}
                    </p>
                    <Button
                        v-if="!isFiltered"
                        as-child
                        variant="outline"
                    >
                        <Link href="/admin/blog/posts/create">
                            <Plus class="size-4" />
                            Create your first post
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
                                            aria-label="Select all on this page"
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
                                        label="Order"
                                        :active-column="sortBy"
                                        :direction="sortDir"
                                        @sort="toggleSort"
                                    />
                                </th>
                                <th class="w-16 px-2 py-3 font-medium uppercase tracking-wide text-muted-foreground">
                                    Image
                                </th>
                                <th class="px-4 py-3">
                                    <SortableColumn
                                        column="name"
                                        label="Title"
                                        :active-column="sortBy"
                                        :direction="sortDir"
                                        @sort="toggleSort"
                                    />
                                </th>
                                <th class="px-4 py-3 font-medium uppercase tracking-wide text-muted-foreground">
                                    Blog categories / tags
                                </th>
                                <th class="px-4 py-3 font-medium uppercase tracking-wide text-muted-foreground">
                                    Translations
                                </th>
                                <th class="px-4 py-3">
                                    <SortableColumn
                                        column="status"
                                        label="Status"
                                        :active-column="sortBy"
                                        :direction="sortDir"
                                        @sort="toggleSort"
                                    />
                                </th>
                                <th class="px-4 py-3 font-medium uppercase tracking-wide text-muted-foreground">
                                    Flags
                                </th>
                                <th class="px-4 py-3">
                                    <SortableColumn
                                        column="view_count"
                                        label="Views"
                                        :active-column="sortBy"
                                        :direction="sortDir"
                                        @sort="toggleSort"
                                    />
                                </th>
                                <th class="px-4 py-3">
                                    <SortableColumn
                                        column="created_at"
                                        label="Created"
                                        :active-column="sortBy"
                                        :direction="sortDir"
                                        @sort="toggleSort"
                                    />
                                </th>
                                <th
                                    class="px-4 py-3 text-right font-medium uppercase tracking-wide text-muted-foreground"
                                >
                                    Actions
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
                                        !isDragging(row) &&
                                        !isDropTarget(row),
                                }"
                                @dragstart="canDrag && onDragStart($event, row)"
                                @dragover="canDrag && onDragOver($event, row)"
                                @dragleave="canDrag && onDragLeave(row)"
                                @drop="canDrag && onDrop($event, row)"
                                @dragend="onDragEnd"
                            >
                                <td class="px-2 py-3">
                                    <div class="flex items-center justify-center">
                                        <Checkbox
                                            :model-value="
                                                selection.isSelected(row.id)
                                            "
                                            :aria-label="`Select ${row.name}`"
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
                                                ? 'Drag to reorder'
                                                : 'Reorder disabled while filtered/sorted'
                                        "
                                    >
                                        <GripVertical class="size-4" />
                                    </div>
                                </td>
                                <td class="px-2 py-3 text-center">
                                    <span
                                        class="inline-flex h-6 min-w-6 items-center justify-center rounded-md bg-muted px-1.5 text-xs font-mono font-medium"
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
                                        by {{ row.user_name }}
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
                                                v-for="(name, i) in row.category_names"
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
                                                v-for="(name, i) in row.tag_names"
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
                                                    !row.translations[
                                                        lang.code
                                                    ]
                                                "
                                                class="opacity-50"
                                                >·missing</span
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
                                        class="capitalize"
                                    >
                                        {{ row.status }}
                                    </Badge>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex items-center gap-1.5">
                                        <Pin
                                            v-if="row.is_sticky"
                                            class="size-4 text-blue-500"
                                            title="Sticky"
                                        />
                                        <Star
                                            v-if="row.is_featured"
                                            class="size-4 fill-amber-400 text-amber-500"
                                            title="Featured"
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
                                        class="whitespace-nowrap text-xs text-muted-foreground"
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
                                            :title="`View ${row.name}`"
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
                                                :title="`Edit ${row.name}`"
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
                                                :title="`Delete ${row.name}`"
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
