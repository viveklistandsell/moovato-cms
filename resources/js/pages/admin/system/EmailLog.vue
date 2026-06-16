<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import {
    AlertCircle,
    CheckCircle2,
    ChevronLeft,
    ChevronRight,
    Clock,
    Filter,
    Mail,
    Paperclip,
    Search,
    X,
} from 'lucide-vue-next';
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

type EmailLogRow = {
    id: number;
    mailer: string;
    from_address: string;
    from_name: string | null;
    to_addresses: Array<{ address: string; name: string | null }>;
    subject: string;
    status: 'pending' | 'sent' | 'failed';
    error: string | null;
    attachment_count: number;
    created_at: string | null;
    sent_at: string | null;
    triggered_by: { id: number; name: string; email: string } | null;
};

type PaginationLink = { url: string | null; label: string; active: boolean };

const props = defineProps<{
    logs: {
        data: EmailLogRow[];
        current_page: number;
        last_page: number;
        total: number;
        links: PaginationLink[];
    };
    filters: {
        status: string | null;
        triggered_by: number | null;
        q: string;
    };
    options: {
        actors: Array<{ id: number; name: string }>;
    };
}>();

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Dashboard', href: '/dashboard' },
            { title: 'System', href: '/admin/system/cache' },
            { title: 'Email log', href: '/admin/system/email-log' },
        ],
    },
});

const search = ref<string>(props.filters.q ?? '');
const status = ref<string | null>(props.filters.status);
const triggeredBy = ref<number | null>(props.filters.triggered_by);

let searchTimer: ReturnType<typeof setTimeout> | null = null;

function buildParams(): Record<string, string | number> {
    const params: Record<string, string | number> = {};

    if (search.value.trim()) {
params.q = search.value.trim();
}

    if (status.value) {
params.status = status.value;
}

    if (triggeredBy.value) {
params.triggered_by = triggeredBy.value;
}

    return params;
}

function apply(): void {
    router.get('/admin/system/email-log', buildParams(), {
        preserveState: true,
        preserveScroll: true,
        replace: true,
    });
}

function onSearchInput(): void {
    if (searchTimer !== null) {
clearTimeout(searchTimer);
}

    searchTimer = setTimeout(apply, 300);
}

function clearFilters(): void {
    search.value = '';
    status.value = null;
    triggeredBy.value = null;
    apply();
}

watch([status, triggeredBy], apply);

const hasFilters = computed<boolean>(
    () =>
        search.value.trim() !== '' ||
        status.value !== null ||
        triggeredBy.value !== null,
);

function formatTimestamp(iso: string | null): string {
    if (iso === null) {
return '—';
}

    return new Date(iso).toLocaleString();
}

function recipientLabel(row: EmailLogRow): string {
    if (row.to_addresses.length === 0) {
return '—';
}

    const first = row.to_addresses[0];
    const more = row.to_addresses.length - 1;
    const base = first.name ? `${first.name} <${first.address}>` : first.address;

    return more > 0 ? `${base} +${more}` : base;
}

const STATUS_BADGE: Record<EmailLogRow['status'], string> = {
    sent: 'bg-emerald-100 text-emerald-700 dark:bg-emerald-950 dark:text-emerald-300',
    pending: 'bg-amber-100 text-amber-700 dark:bg-amber-950 dark:text-amber-300',
    failed: 'bg-rose-100 text-rose-700 dark:bg-rose-950 dark:text-rose-300',
};

const STATUS_ICON: Record<EmailLogRow['status'], typeof CheckCircle2> = {
    sent: CheckCircle2,
    pending: Clock,
    failed: AlertCircle,
};

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
    <Head title="Email log" />

    <div class="space-y-6 p-4">
        <Heading
            title="Email log"
            description="Every outbound mail Laravel attempted to send, with full body and headers. Read-only — outgoing email is not editable after the fact."
        />

        <!-- Filters -->
        <Card>
            <CardHeader>
                <CardTitle class="text-base">Filters</CardTitle>
                <CardDescription class="text-xs">
                    Search subject + sender + recipients, or narrow by status
                    and actor.
                </CardDescription>
            </CardHeader>
            <CardContent class="grid grid-cols-1 gap-3 md:grid-cols-3">
                <div class="relative">
                    <Search
                        class="absolute top-1/2 left-3 size-4 -translate-y-1/2 text-muted-foreground"
                    />
                    <Input
                        v-model="search"
                        placeholder="Search subject, sender, recipient…"
                        class="pl-9"
                        @input="onSearchInput"
                    />
                </div>

                <select
                    v-model="status"
                    class="h-9 rounded-md border bg-background px-3 text-sm"
                >
                    <option :value="null">All statuses</option>
                    <option value="sent">Sent</option>
                    <option value="pending">Pending</option>
                    <option value="failed">Failed</option>
                </select>

                <select
                    v-model="triggeredBy"
                    class="h-9 rounded-md border bg-background px-3 text-sm"
                >
                    <option :value="null">All actors</option>
                    <option
                        v-for="a in options.actors"
                        :key="a.id"
                        :value="a.id"
                    >
                        {{ a.name }}
                    </option>
                </select>
            </CardContent>
            <CardContent
                v-if="hasFilters"
                class="flex items-center justify-between pt-0"
            >
                <p class="text-xs text-muted-foreground">
                    Showing
                    <span class="font-semibold text-foreground">
                        {{ logs.total }}
                    </span>
                    matching emails.
                </p>
                <Button
                    type="button"
                    variant="ghost"
                    size="sm"
                    @click="clearFilters"
                >
                    <X class="size-3.5" />
                    Clear filters
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
                                <th class="px-4 py-3 font-medium">When</th>
                                <th class="px-4 py-3 font-medium">Status</th>
                                <th class="px-4 py-3 font-medium">From</th>
                                <th class="px-4 py-3 font-medium">To</th>
                                <th class="px-4 py-3 font-medium">Subject</th>
                                <th class="px-4 py-3 font-medium">Mailer</th>
                                <th class="px-4 py-3 font-medium">Actor</th>
                                <th class="px-4 py-3 font-medium text-right">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr
                                v-if="logs.data.length === 0"
                                class="border-t"
                            >
                                <td
                                    colspan="7"
                                    class="px-4 py-12 text-center text-muted-foreground"
                                >
                                    <Mail class="mx-auto mb-2 size-6 opacity-40" />
                                    No emails match the current filters.
                                </td>
                            </tr>
                            <tr
                                v-for="row in logs.data"
                                :key="row.id"
                                class="border-t hover:bg-muted/30"
                            >
                                <td
                                    class="px-4 py-3 whitespace-nowrap text-xs text-muted-foreground"
                                >
                                    {{ formatTimestamp(row.created_at) }}
                                </td>
                                <td class="px-4 py-3">
                                    <span
                                        class="inline-flex items-center gap-1 rounded-full px-2 py-0.5 text-[10px] font-semibold tracking-wide uppercase"
                                        :class="STATUS_BADGE[row.status]"
                                    >
                                        <component
                                            :is="STATUS_ICON[row.status]"
                                            class="size-3"
                                        />
                                        {{ row.status }}
                                    </span>
                                </td>
                                 <td
                                    class="max-w-[280px] truncate px-4 py-3 text-xs"
                                    :title="recipientLabel(row)"
                                >
                                    {{ recipientLabel(row) }}
                                </td>
                                <td
                                    class="max-w-[280px] truncate px-4 py-3 text-xs"
                                    :title="recipientLabel(row)"
                                >
                                    {{ recipientLabel(row) }}
                                </td>
                                <td
                                    class="max-w-[320px] truncate px-4 py-3 text-sm font-medium"
                                    :title="row.subject"
                                >
                                    <span class="flex items-center gap-1.5">
                                        {{ row.subject }}
                                        <Paperclip
                                            v-if="row.attachment_count > 0"
                                            class="size-3 text-muted-foreground"
                                        />
                                    </span>
                                </td>
                                <td
                                    class="px-4 py-3 font-mono text-xs text-muted-foreground"
                                >
                                    {{ row.mailer }}
                                </td>
                                <td class="px-4 py-3 text-xs">
                                    <span v-if="row.triggered_by">
                                        {{ row.triggered_by.name }}
                                    </span>
                                    <span
                                        v-else
                                        class="text-muted-foreground"
                                    >
                                        system
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-right">
                                    <Link
                                        :href="`/admin/system/email-log/${row.id}`"
                                        class="text-xs text-primary underline-offset-4 hover:underline"
                                    >
                                        View
                                    </Link>
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
