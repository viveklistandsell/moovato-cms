<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import { ChevronLeft, ChevronRight, Filter, Search, X } from 'lucide-vue-next';
import { computed, ref, watch } from 'vue';
import Heading from '@/components/Heading.vue';
import { Button } from '@/components/ui/button';
import {
    Card,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { setBreadcrumbs } from '@/composables/common/useBreadcrumbs';
import { useAdminLocale } from '@/composables/useAdminLocale';
import { useT } from '@/composables/useT';

const t = useT();
const adminLocale = useAdminLocale();
setBreadcrumbs(() => [
    { title: t('sidebar.dashboard'), href: '/dashboard' },
    { title: t('sidebar.system'), href: '/admin/system/cache' },
    { title: t('sidebar.activity_log'), href: '/admin/system/activity' },
]);

type ActivityLogRow = {
    id: number;
    action: string;
    description: string | null;
    subject_type: string | null;
    subject_id: number | null;
    ip_address: string | null;
    user_agent: string | null;
    properties: Record<string, unknown> | null;
    created_at: string | null;
    user: { id: number; name: string; email: string } | null;
};

type PaginationLink = { url: string | null; label: string; active: boolean };

const props = defineProps<{
    logs: {
        data: ActivityLogRow[];
        current_page: number;
        last_page: number;
        total: number;
        links: PaginationLink[];
    };
    filters: {
        user_id: number | null;
        action: string | null;
        subject_type: string | null;
        q: string;
    };
    options: {
        users: Array<{ id: number; name: string }>;
        actions: string[];
        subject_types: string[];
    };
}>();

// Breadcrumbs set dynamically above via setBreadcrumbs().
defineOptions({});

// Mirror the server filters locally so typing into the search box feels
// instant while a 300ms debounce holds back the HTTP request.
const search = ref<string>(props.filters.q ?? '');
const userId = ref<number | null>(props.filters.user_id ?? null);
const action = ref<string | null>(props.filters.action ?? null);
const subjectType = ref<string | null>(props.filters.subject_type ?? null);

let searchTimer: ReturnType<typeof setTimeout> | null = null;

function buildParams(): Record<string, string | number> {
    const params: Record<string, string | number> = {};
    if (search.value.trim()) params.q = search.value.trim();
    if (userId.value) params.user_id = userId.value;
    if (action.value) params.action = action.value;
    if (subjectType.value) params.subject_type = subjectType.value;
    return params;
}

function apply(): void {
    router.get('/admin/system/activity', buildParams(), {
        preserveState: true,
        preserveScroll: true,
        replace: true,
    });
}

function onSearchInput(): void {
    if (searchTimer !== null) clearTimeout(searchTimer);
    searchTimer = setTimeout(apply, 300);
}

function clearFilters(): void {
    search.value = '';
    userId.value = null;
    action.value = null;
    subjectType.value = null;
    apply();
}

watch([userId, action, subjectType], apply);

const hasFilters = computed<boolean>(
    () =>
        search.value.trim() !== '' ||
        userId.value !== null ||
        action.value !== null ||
        subjectType.value !== null,
);

function formatTimestamp(iso: string | null): string {
    if (iso === null) return '—';
    return new Date(iso).toLocaleString(adminLocale.value);
}

function prettyAction(raw: string): string {
    const dot = raw.lastIndexOf('.');
    if (dot === -1) return raw;
    const left = raw.slice(0, dot);
    const right = raw.slice(dot + 1);
    const cls = left.split('\\').pop() ?? left;
    return `${cls} · ${right}`;
}

function shortClass(fqcn: string | null): string {
    if (fqcn === null) return '—';
    return fqcn.split('\\').pop() ?? fqcn;
}

function isPrev(label: string): boolean {
    return label.toLowerCase().includes('previous') || label.includes('&laquo;');
}
function isNext(label: string): boolean {
    return label.toLowerCase().includes('next') || label.includes('&raquo;');
}
function cleanLabel(label: string): string {
    return label.replace(/&laquo;|&raquo;|Previous|Next/gi, '').trim();
}
</script>

<template>
    <Head :title="t('system.activity_title')" />

    <div class="space-y-6 p-4">
        <Heading
            :title="t('system.activity_title')"
            :description="t('system.activity_description')"
        />

        <!-- Filter bar -->
        <Card>
            <CardHeader>
                <CardTitle class="text-base">{{ t('system.activity_filters_title') }}</CardTitle>
                <CardDescription class="text-xs">
                    {{ t('system.activity_filters_description') }}
                </CardDescription>
            </CardHeader>
            <CardContent class="grid grid-cols-1 gap-3 md:grid-cols-4">
                <div class="relative">
                    <Search
                        class="absolute top-1/2 left-3 size-4 -translate-y-1/2 text-muted-foreground"
                    />
                    <Input
                        v-model="search"
                        :placeholder="t('system.activity_search_full')"
                        class="pl-9"
                        @input="onSearchInput"
                    />
                </div>

                <select
                    v-model="userId"
                    class="h-9 rounded-md border bg-background px-3 text-sm"
                >
                    <option :value="null">{{ t('system.activity_filter_all_users') }}</option>
                    <option
                        v-for="u in options.users"
                        :key="u.id"
                        :value="u.id"
                    >
                        {{ u.name }}
                    </option>
                </select>

                <select
                    v-model="action"
                    class="h-9 rounded-md border bg-background px-3 text-sm"
                >
                    <option :value="null">{{ t('system.activity_filter_all_actions') }}</option>
                    <option
                        v-for="a in options.actions"
                        :key="a"
                        :value="a"
                    >
                        {{ prettyAction(a) }}
                    </option>
                </select>

                <select
                    v-model="subjectType"
                    class="h-9 rounded-md border bg-background px-3 text-sm"
                >
                    <option :value="null">{{ t('system.activity_filter_all_subjects') }}</option>
                    <option
                        v-for="s in options.subject_types"
                        :key="s"
                        :value="s"
                    >
                        {{ shortClass(s) }}
                    </option>
                </select>
            </CardContent>
            <CardContent
                v-if="hasFilters"
                class="flex items-center justify-between pt-0"
            >
                <p class="text-xs text-muted-foreground">
                    {{ t('system.activity_showing_matching', { count: logs.total }) }}
                </p>
                <Button
                    type="button"
                    variant="ghost"
                    size="sm"
                    @click="clearFilters"
                >
                    <X class="size-3.5" />
                    {{ t('system.activity_clear_filters_btn') }}
                </Button>
            </CardContent>
        </Card>

        <!-- Table -->
        <Card>
            <CardContent class="p-0">
                <div class="overflow-x-auto">
                    <table class="w-full border-collapse text-sm">
                        <thead class="bg-muted/40 text-left text-xs uppercase">
                            <tr>
                                <th class="px-4 py-3 font-medium">{{ t('system.activity_col_when') }}</th>
                                <th class="px-4 py-3 font-medium">{{ t('system.activity_col_user') }}</th>
                                <th class="px-4 py-3 font-medium">{{ t('system.activity_col_action') }}</th>
                                <th class="px-4 py-3 font-medium">{{ t('system.activity_col_subject') }}</th>
                                <th class="px-4 py-3 font-medium">
                                    {{ t('system.activity_col_description') }}
                                </th>
                                <th class="px-4 py-3 font-medium">{{ t('system.activity_col_ip') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr
                                v-if="logs.data.length === 0"
                                class="border-t"
                            >
                                <td
                                    colspan="6"
                                    class="px-4 py-12 text-center text-muted-foreground"
                                >
                                    <Filter class="mx-auto mb-2 size-6 opacity-40" />
                                    {{ t('system.activity_no_match') }}
                                </td>
                            </tr>
                            <tr
                                v-for="row in logs.data"
                                :key="row.id"
                                class="border-t hover:bg-muted/30"
                            >
                                <td class="px-4 py-3 whitespace-nowrap text-xs text-muted-foreground">
                                    {{ formatTimestamp(row.created_at) }}
                                </td>
                                <td class="px-4 py-3">
                                    <div
                                        v-if="row.user"
                                        class="leading-tight"
                                    >
                                        <div class="font-medium">
                                            {{ row.user.name }}
                                        </div>
                                        <div class="text-xs text-muted-foreground">
                                            {{ row.user.email }}
                                        </div>
                                    </div>
                                    <span
                                        v-else
                                        class="text-xs text-muted-foreground"
                                    >
                                        {{ t('system.activity_system_actor') }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 font-mono text-xs">
                                    {{ prettyAction(row.action) }}
                                </td>
                                <td class="px-4 py-3 text-xs">
                                    <div v-if="row.subject_type">
                                        <span class="font-medium">
                                            {{ shortClass(row.subject_type) }}
                                        </span>
                                        <span
                                            v-if="row.subject_id"
                                            class="text-muted-foreground"
                                        >
                                            #{{ row.subject_id }}
                                        </span>
                                    </div>
                                    <span
                                        v-else
                                        class="text-muted-foreground"
                                    >
                                        —
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-xs">
                                    {{ row.description ?? '—' }}
                                </td>
                                <td class="px-4 py-3 font-mono text-xs text-muted-foreground">
                                    {{ row.ip_address ?? '—' }}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </CardContent>
        </Card>

        <!-- Pagination -->
        <div
            v-if="logs.last_page > 1"
            class="flex items-center justify-center gap-1"
        >
            <template
                v-for="(link, idx) in logs.links"
                :key="idx"
            >
                <Link
                    v-if="isPrev(link.label) && link.url"
                    :href="link.url"
                    class="inline-flex h-9 items-center rounded-md border px-3 text-sm hover:bg-muted"
                >
                    <ChevronLeft class="size-4" />
                </Link>
                <span
                    v-else-if="isPrev(link.label)"
                    class="inline-flex h-9 items-center rounded-md border px-3 text-sm opacity-40"
                >
                    <ChevronLeft class="size-4" />
                </span>

                <Link
                    v-else-if="isNext(link.label) && link.url"
                    :href="link.url"
                    class="inline-flex h-9 items-center rounded-md border px-3 text-sm hover:bg-muted"
                >
                    <ChevronRight class="size-4" />
                </Link>
                <span
                    v-else-if="isNext(link.label)"
                    class="inline-flex h-9 items-center rounded-md border px-3 text-sm opacity-40"
                >
                    <ChevronRight class="size-4" />
                </span>

                <Link
                    v-else-if="link.url && !link.active"
                    :href="link.url"
                    class="inline-flex h-9 min-w-9 items-center justify-center rounded-md border px-2 text-sm hover:bg-muted"
                >
                    <span v-html="cleanLabel(link.label) || link.label" />
                </Link>
                <span
                    v-else
                    class="inline-flex h-9 min-w-9 items-center justify-center rounded-md border px-2 text-sm"
                    :class="
                        link.active
                            ? 'border-primary bg-primary text-primary-foreground'
                            : 'opacity-40'
                    "
                >
                    <span v-html="cleanLabel(link.label) || link.label" />
                </span>
            </template>
        </div>
    </div>
</template>
