<script setup lang="ts">
/**
 * Admin approval queue for pending partner registrations.
 */
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import {
    Building2,
    Check,
    ExternalLink,
    FileText,
    Loader2,
    Mail,
    MapPin,
    Pencil,
    Phone,
    Search,
    Trash2,
    X,
    XCircle,
} from 'lucide-vue-next';
import { computed, ref } from 'vue';
import Heading from '@/components/Heading.vue';
import Pagination, {
    type PaginationMeta,
} from '@/components/common/Pagination.vue';
import PerPageSelect from '@/components/common/PerPageSelect.vue';
import SearchInput from '@/components/common/SearchInput.vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import { setBreadcrumbs } from '@/composables/common/useBreadcrumbs';
import { useTableQuery } from '@/composables/common/useTableQuery';
import { useT } from '@/composables/useT';

const t = useT();

setBreadcrumbs(() => [
    { title: t('sidebar.dashboard'), href: '/dashboard' },
    { title: t('applications.title'), href: '/admin/company-applications' },
]);

type DocLink = { name: string; url: string };

type Application = {
    id: number;
    first_name: string;
    last_name: string;
    full_name: string;
    email: string;
    phone: string | null;
    status: 'pending' | 'approved' | 'rejected' | 'inactive';
    rejection_reason: string | null;
    company_id: number | null;
    company_name: string | null;
    company_permalink: string | null;
    company_city: string | null;
    company_street: string | null;
    company_postal: string | null;
    company_status: string | null;
    reviewer_name: string | null;
    reviewed_at: string | null;
    created_at: string | null;
    documents: DocLink[];
    edit_url: string | null;
};

type Stats = { pending: number; approved: number; rejected: number; total: number };

type Filters = { status: string; q: string; per_page: number };

const props = defineProps<{
    applications: Application[];
    stats: Stats;
    filters: Filters;
    allowed: { statuses: string[] };
    pagination: PaginationMeta;
}>();

const { search, perPage, setSearch, setPerPage } = useTableQuery(
    '/admin/company-applications',
    { q: props.filters.q, per_page: props.filters.per_page },
    { only: ['applications', 'pagination', 'filters', 'stats'] },
);

function applyFilter(key: string, value: string | number | null): void {
    const params: Record<string, string | number> = {};
    if (search.value) params.q = search.value;
    if (perPage.value !== 15) params.per_page = perPage.value;
    if (props.filters.status !== 'pending' && key !== 'status') {
        params.status = props.filters.status;
    }
    if (value !== null && value !== '') params[key] = value;
    router.get('/admin/company-applications', params, {
        preserveScroll: true,
        preserveState: true,
        only: ['applications', 'pagination', 'filters', 'stats'],
    });
}

/* ---------------------------------------------- actions */

function approve(app: Application): void {
    if (!confirm(t('applications.confirm_approve', { name: app.full_name }))) return;
    router.post(`/admin/company-applications/${app.id}/approve`, {}, { preserveScroll: true });
}

function destroy(app: Application): void {
    if (!confirm(t('applications.confirm_delete', { name: app.full_name }))) return;
    router.delete(`/admin/company-applications/${app.id}`, { preserveScroll: true });
}

const rejectingApp = ref<Application | null>(null);
const rejectForm = useForm<{ reason: string }>({ reason: '' });

function openReject(app: Application): void {
    rejectingApp.value = app;
    rejectForm.reset();
    rejectForm.clearErrors();
}

function submitReject(): void {
    if (rejectingApp.value === null) return;
    rejectForm.post(`/admin/company-applications/${rejectingApp.value.id}/reject`, {
        preserveScroll: true,
        onSuccess: () => { rejectingApp.value = null; },
    });
}

function formatDate(iso: string | null): string {
    if (!iso) return '—';
    try {
        return new Date(iso).toLocaleDateString(undefined, {
            year: 'numeric', month: 'short', day: 'numeric', hour: '2-digit', minute: '2-digit',
        });
    } catch {
        return iso;
    }
}

const statusColor = computed(() => (status: string) => {
    switch (status) {
        case 'pending': return 'bg-amber-100 text-amber-700 dark:bg-amber-950 dark:text-amber-300';
        case 'approved': return 'bg-emerald-100 text-emerald-700 dark:bg-emerald-950 dark:text-emerald-300';
        case 'rejected': return 'bg-red-100 text-red-700 dark:bg-red-950 dark:text-red-300';
        default: return 'bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-300';
    }
});
</script>

<template>
    <Head :title="t('applications.title')" />

    <div class="flex flex-col gap-6 p-4">
        <Heading
            :title="t('applications.title')"
            :description="t('applications.description')"
        />

        <!-- Stat strip -->
        <div class="grid grid-cols-2 gap-3 md:grid-cols-4">
            <Card class="border-amber-200 bg-amber-50/40 dark:bg-amber-950/20">
                <CardContent class="p-4">
                    <p class="text-xs text-muted-foreground">{{ t('applications.stats.pending') }}</p>
                    <p class="text-2xl font-bold text-amber-700">{{ stats.pending }}</p>
                </CardContent>
            </Card>
            <Card>
                <CardContent class="p-4">
                    <p class="text-xs text-muted-foreground">{{ t('applications.stats.approved') }}</p>
                    <p class="text-2xl font-bold text-emerald-600">{{ stats.approved }}</p>
                </CardContent>
            </Card>
            <Card>
                <CardContent class="p-4">
                    <p class="text-xs text-muted-foreground">{{ t('applications.stats.rejected') }}</p>
                    <p class="text-2xl font-bold text-red-600">{{ stats.rejected }}</p>
                </CardContent>
            </Card>
            <Card>
                <CardContent class="p-4">
                    <p class="text-xs text-muted-foreground">{{ t('applications.stats.total') }}</p>
                    <p class="text-2xl font-bold">{{ stats.total }}</p>
                </CardContent>
            </Card>
        </div>

        <!-- Filter rail -->
        <Card>
            <CardHeader class="pb-3">
                <CardTitle class="text-sm font-medium">
                    {{ t('applications.filters') }}
                </CardTitle>
            </CardHeader>
            <CardContent>
                <div class="flex flex-wrap items-end gap-3">
                    <div class="min-w-[240px] flex-1">
                        <SearchInput
                            :model-value="search"
                            :placeholder="t('applications.search_placeholder')"
                            @update:model-value="setSearch"
                        />
                    </div>
                    <div class="min-w-[160px]">
                        <label class="mb-1 block text-xs text-muted-foreground">
                            {{ t('applications.filter_status') }}
                        </label>
                        <Select
                            :model-value="filters.status"
                            @update:model-value="(v) => applyFilter('status', String(v))"
                        >
                            <SelectTrigger>
                                <SelectValue />
                            </SelectTrigger>
                            <SelectContent>
                                <SelectItem v-for="s in allowed.statuses" :key="s" :value="s">
                                    {{ t(`applications.status.${s}`) }}
                                </SelectItem>
                            </SelectContent>
                        </Select>
                    </div>
                </div>
            </CardContent>
        </Card>

        <!-- Applications list -->
        <Card>
            <CardContent class="p-0">
                <div v-if="applications.length === 0" class="p-8 text-center text-sm text-muted-foreground">
                    {{ t('applications.no_applications') }}
                </div>

                <ul v-else class="divide-y divide-border">
                    <li v-for="app in applications" :key="app.id" class="p-4 md:p-5">
                        <!-- Header: applicant + status + submitted at -->
                        <div class="mb-3 flex flex-wrap items-start justify-between gap-3">
                            <div class="min-w-0 flex-1">
                                <p class="text-sm font-semibold text-[var(--midnight)] dark:text-white">
                                    {{ app.full_name }}
                                </p>
                                <div class="mt-1 flex flex-wrap items-center gap-x-3 gap-y-1 text-xs text-muted-foreground">
                                    <a :href="`mailto:${app.email}`" class="inline-flex items-center gap-1 hover:text-[var(--orange)]">
                                        <Mail class="size-3" /> {{ app.email }}
                                    </a>
                                    <span v-if="app.phone" class="inline-flex items-center gap-1">
                                        <Phone class="size-3" /> {{ app.phone }}
                                    </span>
                                    <span>· {{ formatDate(app.created_at) }}</span>
                                </div>
                            </div>
                            <Badge :class="statusColor(app.status)">
                                {{ t(`applications.status.${app.status}`) }}
                            </Badge>
                        </div>

                        <!-- Company card -->
                        <div class="mb-3 flex flex-wrap items-start gap-3 rounded-md border border-[var(--linen)] bg-[var(--paper)] p-3 dark:border-slate-800 dark:bg-slate-900/40">
                            <Building2 class="mt-0.5 size-5 shrink-0 text-[var(--slate)]" />
                            <div class="min-w-0 flex-1">
                                <p class="text-sm font-medium">
                                    {{ app.company_name ?? t('applications.no_name') }}
                                </p>
                                <p v-if="app.company_street || app.company_city" class="mt-0.5 flex items-center gap-1 text-xs text-muted-foreground">
                                    <MapPin class="size-3" />
                                    <span>
                                        <span v-if="app.company_street">{{ app.company_street }}, </span>
                                        <span v-if="app.company_postal">{{ app.company_postal }} </span>
                                        <span v-if="app.company_city">{{ app.company_city }}</span>
                                    </span>
                                </p>
                                <p v-if="app.company_status" class="mt-1 text-[10px] uppercase tracking-wide text-muted-foreground">
                                    {{ t('applications.company_status') }}: {{ app.company_status }}
                                </p>
                            </div>
                        </div>

                        <!-- Documents (if any) -->
                        <div v-if="app.documents.length > 0" class="mb-3 flex flex-wrap gap-2">
                            <a
                                v-for="doc in app.documents"
                                :key="doc.url"
                                :href="doc.url"
                                target="_blank"
                                rel="noopener"
                                class="inline-flex items-center gap-1.5 rounded-md border border-[var(--linen)] bg-white px-2.5 py-1 text-xs font-medium text-[var(--slate)] hover:border-[var(--orange)] hover:text-[var(--orange)] dark:bg-slate-900"
                            >
                                <FileText class="size-3.5" />
                                {{ doc.name }}
                            </a>
                        </div>

                        <!-- Rejection reason if rejected -->
                        <div v-if="app.status === 'rejected' && app.rejection_reason" class="mb-3 rounded-md border border-red-200 bg-red-50 p-3 dark:border-red-900 dark:bg-red-950/40">
                            <p class="mb-1 text-[10px] font-semibold uppercase tracking-wide text-red-700 dark:text-red-300">
                                {{ t('applications.rejection_reason') }}
                            </p>
                            <p class="text-xs text-red-700 dark:text-red-300">
                                {{ app.rejection_reason }}
                            </p>
                        </div>

                        <!-- Reviewer trail -->
                        <p v-if="app.reviewer_name && app.reviewed_at" class="mb-3 text-[10px] text-muted-foreground">
                            {{ t('applications.reviewed_by', { name: app.reviewer_name, when: formatDate(app.reviewed_at) }) }}
                        </p>

                        <!-- Actions -->
                        <div class="flex flex-wrap items-center gap-2">
                            <Button
                                v-if="app.status === 'pending'"
                                size="sm"
                                @click="approve(app)"
                            >
                                <Check class="size-4" />
                                {{ t('applications.approve') }}
                            </Button>
                            <Button
                                v-if="app.status === 'pending'"
                                size="sm"
                                variant="outline"
                                class="text-red-600 hover:text-red-700"
                                @click="openReject(app)"
                            >
                                <XCircle class="size-4" />
                                {{ t('applications.reject') }}
                            </Button>
                            <Button v-if="app.edit_url" size="sm" variant="outline" as-child>
                                <a :href="app.edit_url">
                                    <Pencil class="size-4" />
                                    {{ t('applications.edit_company') }}
                                </a>
                            </Button>
                            <Button
                                size="sm"
                                variant="outline"
                                class="text-red-600 hover:text-red-700"
                                @click="destroy(app)"
                            >
                                <Trash2 class="size-4" />
                                {{ t('applications.delete') }}
                            </Button>
                            <Link
                                v-if="app.company_id && app.company_permalink"
                                :href="`/company-portal/${app.company_id}/${app.company_permalink}`"
                                class="ml-auto inline-flex items-center gap-1 text-xs text-[var(--orange)] hover:underline"
                            >
                                <ExternalLink class="size-3" />
                                {{ t('applications.view_portal') }}
                            </Link>
                        </div>
                    </li>
                </ul>
            </CardContent>
        </Card>

        <!-- Pagination + per-page -->
        <div class="flex flex-wrap items-center justify-between gap-3">
            <PerPageSelect :model-value="perPage" @update:model-value="setPerPage" />
            <Pagination :pagination="pagination" />
        </div>

        <!-- Reject dialog -->
        <Dialog :open="rejectingApp !== null" @update:open="(v: boolean) => !v && (rejectingApp = null)">
            <DialogContent v-if="rejectingApp" class="sm:max-w-md">
                <DialogHeader>
                    <DialogTitle>
                        {{ t('applications.reject_dialog_title', { name: rejectingApp.full_name }) }}
                    </DialogTitle>
                    <DialogDescription>
                        {{ t('applications.reject_dialog_desc') }}
                    </DialogDescription>
                </DialogHeader>
                <form class="space-y-3" @submit.prevent="submitReject">
                    <div>
                        <label class="mb-1 block text-xs font-medium text-[var(--slate)]">
                            {{ t('applications.reject_reason_label') }}
                        </label>
                        <textarea
                            v-model="rejectForm.reason"
                            rows="4"
                            :placeholder="t('applications.reject_reason_placeholder')"
                            class="w-full rounded-md border border-[var(--linen)] px-3 py-2 text-sm focus:border-[var(--orange)] focus:outline-none"
                            :class="{ 'border-red-400': !!rejectForm.errors.reason }"
                        />
                        <p v-if="rejectForm.errors.reason" class="mt-1 text-xs text-red-600">
                            {{ rejectForm.errors.reason }}
                        </p>
                    </div>
                    <DialogFooter>
                        <Button type="button" variant="ghost" @click="rejectingApp = null">
                            <X class="size-4" />
                            {{ t('applications.cancel') }}
                        </Button>
                        <Button
                            type="submit"
                            :disabled="rejectForm.processing"
                            class="bg-red-600 text-white hover:bg-red-700"
                        >
                            <Loader2 v-if="rejectForm.processing" class="size-4 animate-spin" />
                            <XCircle v-else class="size-4" />
                            {{ t('applications.reject_confirm') }}
                        </Button>
                    </DialogFooter>
                </form>
            </DialogContent>
        </Dialog>
    </div>
</template>
