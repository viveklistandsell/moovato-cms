<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { Pencil, Plus, Trash2, X } from 'lucide-vue-next';
import { computed } from 'vue';
import Heading from '@/components/Heading.vue';
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
import { useTableQuery } from '@/composables/common/useTableQuery';

type Language = {
    id: number;
    code: string;
    name: string;
    native_name: string;
    flag: string | null;
    lang_locale: string | null;
    lang_is_default: boolean;
    status: boolean;
    sort_order: number;
    created_at: string | null;
    updated_at: string | null;
};

type Filters = {
    q: string | null;
    sort_by: string | null;
    sort_dir: 'asc' | 'desc';
};

const props = defineProps<{
    languages: Language[];
    filters: Filters;
}>();

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Dashboard', href: '/dashboard' },
            { title: 'Languages', href: '/admin/languages' },
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

const { search, sortBy, sortDir, isLoading, setSearch, toggleSort, resetAll } =
    useTableQuery(
        '/admin/languages',
        {
            q: props.filters.q,
            sort_by: props.filters.sort_by,
            sort_dir: props.filters.sort_dir,
        },
        { only: ['languages', 'filters'] },
    );

const isFiltered = computed(
    () => (search.value && search.value.length > 0) || sortBy.value !== null,
);

function confirmDelete(l: Language): boolean {
    if (l.lang_is_default) {
        alert(
            `"${l.name}" is the default language. Promote another language to default before removing this one.`,
        );
        return false;
    }
    return confirm(
        `Remove language "${l.name}"? Existing translation rows for this locale will be orphaned.`,
    );
}
</script>

<template>
    <Head title="Languages" />

    <div class="flex flex-col gap-6 p-4">
        <div class="flex items-start justify-between gap-4">
            <Heading
                title="Languages"
                description="Add, remove, and reorder the locales the CMS supports."
            />
            <Button as-child>
                <Link href="/admin/languages/create">
                    <Plus class="size-4" />
                    Add language
                </Link>
            </Button>
        </div>

        <Card>
            <CardHeader>
                <div
                    class="flex flex-col items-start justify-between gap-3 sm:flex-row sm:items-center"
                >
                    <div>
                        <CardTitle>All languages</CardTitle>
                        <CardDescription>
                            {{ languages.length }} total
                        </CardDescription>
                    </div>
                    <div
                        class="flex w-full items-center gap-2 sm:w-auto sm:justify-end"
                    >
                        <SearchInput
                            :model-value="search"
                            :loading="isLoading"
                            placeholder="Search code, name, native…"
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
                    v-if="languages.length === 0"
                    class="flex flex-col items-center justify-center gap-3 px-6 py-12 text-center"
                >
                    <p class="text-sm text-muted-foreground">
                        {{
                            isFiltered
                                ? 'No languages match your filters.'
                                : 'No languages yet.'
                        }}
                    </p>
                    <Button v-if="!isFiltered" as-child variant="outline">
                        <Link href="/admin/languages/create">
                            <Plus class="size-4" />
                            Add your first language
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
                                <th class="w-20 px-4 py-3">Flag</th>
                                <th class="px-4 py-3">
                                    <SortableColumn
                                        column="code"
                                        label="Code"
                                        :active-column="sortBy"
                                        :direction="sortDir"
                                        @sort="toggleSort"
                                    />
                                </th>
                                <th class="px-4 py-3">
                                    <SortableColumn
                                        column="name"
                                        label="Name"
                                        :active-column="sortBy"
                                        :direction="sortDir"
                                        @sort="toggleSort"
                                    />
                                </th>
                                <th
                                    class="px-4 py-3 font-medium tracking-wide text-muted-foreground uppercase"
                                >
                                    Native
                                </th>
                                <th
                                    class="px-4 py-3 font-medium tracking-wide text-muted-foreground uppercase"
                                >
                                    Locale
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
                                <th
                                    class="px-4 py-3 font-medium tracking-wide text-muted-foreground uppercase"
                                >
                                    Default
                                </th>
                                <th class="px-4 py-3">
                                    <SortableColumn
                                        column="sort_order"
                                        label="Order"
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
                                    class="px-4 py-3 text-right font-medium tracking-wide text-muted-foreground uppercase"
                                >
                                    Actions
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr
                                v-for="row in languages"
                                :key="row.id"
                                class="border-t border-sidebar-border/70 transition-colors hover:bg-muted/30 dark:border-sidebar-border"
                            >
                                <td class="px-4 py-3">
                                    <span class="text-xl" :title="row.code">
                                        {{ row.flag || '—' }}
                                    </span>
                                </td>
                                <td class="px-4 py-3">
                                    <code
                                        class="rounded bg-muted px-1.5 py-0.5 font-mono text-xs"
                                        >{{ row.code }}</code
                                    >
                                </td>
                                <td class="px-4 py-3">
                                    <Link
                                        :href="`/admin/languages/${row.id}/edit`"
                                        class="font-medium hover:underline"
                                    >
                                        {{ row.name }}
                                    </Link>
                                </td>
                                <td class="px-4 py-3 text-muted-foreground">
                                    {{ row.native_name }}
                                </td>
                                <td class="px-4 py-3">
                                    <span
                                        v-if="row.lang_locale"
                                        class="font-mono text-xs text-muted-foreground"
                                        >{{ row.lang_locale }}</span
                                    >
                                    <span
                                        v-else
                                        class="text-xs text-muted-foreground"
                                        >—</span
                                    >
                                </td>
                                <td class="px-4 py-3">
                                    <Badge
                                        :variant="
                                            row.status ? 'default' : 'outline'
                                        "
                                    >
                                        {{ row.status ? 'Active' : 'Inactive' }}
                                    </Badge>
                                </td>
                                <td class="px-4 py-3">
                                    <Badge
                                        v-if="row.lang_is_default"
                                        variant="secondary"
                                        class="text-[10px]"
                                        >default</Badge
                                    >
                                    <span
                                        v-else
                                        class="text-xs text-muted-foreground"
                                        >—</span
                                    >
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <span
                                        class="inline-flex h-6 min-w-6 items-center justify-center rounded-md bg-muted px-1.5 font-mono text-xs"
                                    >
                                        {{ row.sort_order }}
                                    </span>
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
                                        >
                                            <Link
                                                :href="`/admin/languages/${row.id}/edit`"
                                            >
                                                <Pencil class="size-4" />
                                            </Link>
                                        </Button>
                                        <Button
                                            as-child
                                            variant="ghost"
                                            size="sm"
                                            class="text-destructive hover:text-destructive"
                                            :disabled="row.lang_is_default"
                                        >
                                            <Link
                                                :href="`/admin/languages/${row.id}`"
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
            </CardContent>
        </Card>
    </div>
</template>
