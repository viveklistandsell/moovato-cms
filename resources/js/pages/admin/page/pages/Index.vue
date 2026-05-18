<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import {
    ExternalLink,
    Home,
    Image as ImageIcon,
    Pencil,
    Plus,
    Trash2,
    X,
} from 'lucide-vue-next';
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
import { useRowSelection } from '@/composables/common/useRowSelection';
import { useTableQuery } from '@/composables/common/useTableQuery';

type Translation = {
    title: string;
    permalink: string;
    content: string | null;
};

type Page = {
    id: number;
    title: string;
    permalink: string;
    image: string | null;
    image_url: string | null;
    template: string;
    is_home: boolean;
    user_id: number | null;
    user_name: string | null;
    category_ids: number[];
    category_names: string[];
    status: string;
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
    pages: Page[];
    languages: Language[];
    filters: Filters;
    pagination: PaginationMeta;
}>();

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Dashboard', href: '/dashboard' },
            { title: 'Pages', href: '/admin/pages' },
            { title: 'Pages', href: '/admin/pages' },
        ],
    },
});

function formatDate(iso: string | null): string {
    if (!iso) return '—';
    return new Date(iso).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
    });
}

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
    '/admin/pages',
    {
        q: props.filters.q,
        sort_by: props.filters.sort_by,
        sort_dir: props.filters.sort_dir,
        per_page: props.filters.per_page,
    },
    { only: ['pages', 'pagination', 'filters'] },
);

const isFiltered = computed(
    () => (search.value && search.value.length > 0) || sortBy.value !== null,
);

function confirmDelete(p: Page): boolean {
    return confirm(`Delete page "${p.title}"?`);
}

const selection = useRowSelection();
const visibleIds = computed(() => props.pages.map((p) => p.id));
const allOnPageSelected = computed(() =>
    selection.areAllSelected(visibleIds.value),
);
const someOnPageSelected = computed(
    () => !allOnPageSelected.value && selection.someSelected(visibleIds.value),
);

const bulkActions: BulkAction[] = [
    { value: 'publish', label: 'Publish' },
    { value: 'draft', label: 'Set to draft' },
    { value: 'inactive', label: 'Mark inactive' },
    {
        value: 'delete',
        label: 'Delete',
        destructive: true,
        confirm: 'Delete {count} selected page(s)?',
    },
];

function applyBulkAction(action: string): void {
    if (selection.isEmpty.value) return;
    router.post(
        '/admin/pages/bulk-action',
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
    <Head title="Pages" />

    <div class="flex flex-col gap-6 p-4">
        <div class="flex items-start justify-between gap-4">
            <Heading
                title="Pages"
                description="Manage CMS pages with multilingual translations."
            />
            <Button as-child>
                <Link href="/admin/pages/create">
                    <Plus class="size-4" />
                    New page
                </Link>
            </Button>
        </div>

        <Card>
            <CardHeader>
                <div
                    class="flex flex-col items-start justify-between gap-3 sm:flex-row sm:items-center"
                >
                    <div>
                        <CardTitle>All pages</CardTitle>
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
                            @click="resetAll"
                        >
                            <X class="size-4" />
                            Clear
                        </Button>
                    </div>
                </div>
            </CardHeader>

            <CardContent class="px-0 pb-0">
                <div
                    v-if="pages.length === 0"
                    class="flex flex-col items-center justify-center gap-3 px-6 py-12 text-center"
                >
                    <p class="text-sm text-muted-foreground">
                        {{
                            isFiltered
                                ? 'No pages match your filters.'
                                : 'No pages yet.'
                        }}
                    </p>
                    <Button v-if="!isFiltered" as-child variant="outline">
                        <Link href="/admin/pages/create">
                            <Plus class="size-4" />
                            Create your first page
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
                                <th class="w-16 px-2 py-3">Image</th>
                                <th class="px-4 py-3">
                                    <SortableColumn
                                        column="title"
                                        label="Title"
                                        :active-column="sortBy"
                                        :direction="sortDir"
                                        @sort="toggleSort"
                                    />
                                </th>
                                <th
                                    class="px-4 py-3 font-medium uppercase tracking-wide text-muted-foreground"
                                >
                                    Categories
                                </th>
                                <th
                                    class="px-4 py-3 font-medium uppercase tracking-wide text-muted-foreground"
                                >
                                    Translations
                                </th>
                                <th
                                    class="px-4 py-3 font-medium uppercase tracking-wide text-muted-foreground"
                                >
                                    Template
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
                                <th class="px-4 py-3">
                                    <SortableColumn
                                        column="is_home"
                                        label="Home"
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
                                v-for="row in pages"
                                :key="row.id"
                                class="border-t border-sidebar-border/70 transition-colors hover:bg-muted/30 dark:border-sidebar-border"
                            >
                                <td class="px-2 py-3">
                                    <div
                                        class="flex items-center justify-center"
                                    >
                                        <Checkbox
                                            :model-value="
                                                selection.isSelected(row.id)
                                            "
                                            :aria-label="`Select ${row.title}`"
                                            @update:model-value="
                                                selection.toggle(row.id)
                                            "
                                        />
                                    </div>
                                </td>
                                <td class="px-2 py-3">
                                    <div
                                        class="flex aspect-square size-12 items-center justify-center overflow-hidden rounded-md bg-muted"
                                    >
                                        <img
                                            v-if="row.image_url"
                                            :src="row.image_url"
                                            :alt="row.title"
                                            class="size-full object-cover"
                                        />
                                        <ImageIcon
                                            v-else
                                            class="size-5 text-muted-foreground/50"
                                        />
                                    </div>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex items-center gap-2">
                                        <a
                                            :href="`/${row.permalink}`"
                                            target="_blank"
                                            rel="noopener"
                                            class="inline-flex items-center gap-1 font-medium hover:underline"
                                            :title="`Open public page: /${row.permalink}`"
                                        >
                                            {{ row.title }}
                                            <ExternalLink
                                                class="size-3 text-muted-foreground"
                                            />
                                        </a>
                                    </div>
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
                                    <div
                                        v-if="row.category_names.length > 0"
                                        class="flex flex-wrap gap-1"
                                    >
                                        <Badge
                                            v-for="(name, i) in row.category_names"
                                            :key="i"
                                            variant="secondary"
                                            class="text-[10px]"
                                        >
                                            {{ name }}
                                        </Badge>
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
                                    <Badge variant="outline" class="capitalize">
                                        {{ row.template }}
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
                                        class="capitalize"
                                    >
                                        {{ row.status }}
                                    </Badge>
                                </td>
                                <td class="px-4 py-3">
                                    <Home
                                        v-if="row.is_home"
                                        class="size-4 text-primary"
                                    />
                                    <span
                                        v-else
                                        class="text-xs text-muted-foreground"
                                        >—</span
                                    >
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
                                        >
                                            <Link
                                                :href="`/admin/pages/${row.id}/edit`"
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
                                                :href="`/admin/pages/${row.id}`"
                                                method="delete"
                                                preserve-scroll
                                                :on-before="
                                                    () => confirmDelete(row)
                                                "
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
                    :only="['pages', 'pagination', 'filters']"
                />
            </CardContent>
        </Card>
    </div>
</template>
