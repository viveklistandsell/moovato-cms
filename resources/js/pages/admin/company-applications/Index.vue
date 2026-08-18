<script setup lang="ts">

import { Head, Link, router, useForm } from '@inertiajs/vue3';
import {
    AlertCircle,
    Building2,
    Check,
    CheckCircle2,
    Clock,
    ExternalLink,
    FileText,
    Loader2,
    Mail,
    Paperclip,
    Pencil,
    Phone,
    Trash2,
    UserRound,
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

function applyStatusFilter(value: string | number): void {
    const params: Record<string, string | number> = {};
    if (search.value) params.q = search.value;
    if (perPage.value !== 15) params.per_page = perPage.value;
    if (String(value) !== 'all') params.status = String(value);
    router.get('/admin/company-applications', params, {
        preserveScroll: true,
        preserveState: true,
        only: ['applications', 'pagination', 'filters', 'stats'],
    });
}

function resetFilters(): void {
    router.get('/admin/company-applications', {}, {
        preserveScroll: true,
        preserveState: true,
        only: ['applications', 'pagination', 'filters', 'stats'],
    });
}

const isFiltered = computed(
    () => props.filters.status !== 'all' || props.filters.q !== '',
);

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

/* ---------------------------------------------- presenters */

function formatDate(iso: string | null): string {
    if (!iso) return '—';
    try {
        return new Date(iso).toLocaleDateString(undefined, {
            year: 'numeric', month: 'short', day: 'numeric',
        });
    } catch {
        return iso;
    }
}

function formatDateTime(iso: string | null): string {
    if (!iso) return '';
    try {
        return new Date(iso).toLocaleString(undefined, {
            year: 'numeric', month: 'short', day: 'numeric',
            hour: '2-digit', minute: '2-digit',
        });
    } catch {
        return iso;
    }
}

function initials(name: string): string {
    const parts = name.trim().split(/\s+/);
    const a = parts[0]?.charAt(0) ?? '';
    const b = parts.length > 1 ? (parts[parts.length - 1]?.charAt(0) ?? '') : '';
    return (a + b).toUpperCase() || '?';
}

const statusStyle = (s: string): string => {
    switch (s) {
        case 'pending': return 'border-amber-300 bg-amber-50 text-amber-800 dark:border-amber-800 dark:bg-amber-950 dark:text-amber-300';
        case 'approved': return 'border-emerald-300 bg-emerald-50 text-emerald-800 dark:border-emerald-800 dark:bg-emerald-950 dark:text-emerald-300';
        case 'rejected': return 'border-red-300 bg-red-50 text-red-800 dark:border-red-800 dark:bg-red-950 dark:text-red-300';
        default: return 'border-slate-300 bg-slate-50 text-slate-800 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-300';
    }
};

const statusIcon = (s: string) => {
    switch (s) {
        case 'pending': return Clock;
        case 'approved': return CheckCircle2;
        case 'rejected': return XCircle;
        default: return AlertCircle;
    }
};
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
            <Card class="border-amber-200 dark:border-amber-900">
                <CardContent class="flex items-center gap-3 p-4">
                    <div class="flex size-10 items-center justify-center rounded-full bg-amber-100 text-amber-700 dark:bg-amber-950 dark:text-amber-300">
                        <Clock class="size-5" />
                    </div>
                    <div>
                        <p class="text-xs text-muted-foreground">{{ t('applications.stats.pending') }}</p>
                        <p class="text-2xl font-bold text-amber-700 dark:text-amber-300">{{ stats.pending }}</p>
                    </div>
                </CardContent>
            </Card>
            <Card>
                <CardContent class="flex items-center gap-3 p-4">
                    <div class="flex size-10 items-center justify-center rounded-full bg-emerald-100 text-emerald-700 dark:bg-emerald-950 dark:text-emerald-300">
                        <CheckCircle2 class="size-5" />
                    </div>
                    <div>
                        <p class="text-xs text-muted-foreground">{{ t('applications.stats.approved') }}</p>
                        <p class="text-2xl font-bold text-emerald-600 dark:text-emerald-400">{{ stats.approved }}</p>
                    </div>
                </CardContent>
            </Card>
            <Card>
                <CardContent class="flex items-center gap-3 p-4">
                    <div class="flex size-10 items-center justify-center rounded-full bg-red-100 text-red-700 dark:bg-red-950 dark:text-red-300">
                        <XCircle class="size-5" />
                    </div>
                    <div>
                        <p class="text-xs text-muted-foreground">{{ t('applications.stats.rejected') }}</p>
                        <p class="text-2xl font-bold text-red-600 dark:text-red-400">{{ stats.rejected }}</p>
                    </div>
                </CardContent>
            </Card>
            <Card>
                <CardContent class="flex items-center gap-3 p-4">
                    <div class="flex size-10 items-center justify-center rounded-full bg-slate-100 text-slate-700 dark:bg-slate-900 dark:text-slate-300">
                        <UserRound class="size-5" />
                    </div>
                    <div>
                        <p class="text-xs text-muted-foreground">{{ t('applications.stats.total') }}</p>
                        <p class="text-2xl font-bold">{{ stats.total }}</p>
                    </div>
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
                    <Select
                        :model-value="filters.status"
                        @update:model-value="(v) => applyStatusFilter(String(v))"
                    >
                        <SelectTrigger class="w-[180px]">
                            <SelectValue />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem v-for="s in allowed.statuses" :key="s" :value="s">
                                {{ t(`applications.status.${s}`) }}
                            </SelectItem>
                        </SelectContent>
                    </Select>
                    <Button
                        v-if="isFiltered"
                        variant="outline"
                        size="sm"
                        @click="resetFilters"
                    >
                        <X class="size-4" />
                        {{ t('common.clear_filters') }}
                    </Button>
                </div>
            </CardContent>
        </Card>

        <!-- Table -->
        <Card>
            <CardContent class="p-0">
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-muted/40 text-xs uppercase">
                            <tr>
                                <th class="w-80 px-4 py-3 text-left">{{ t('applications.col_applicant') }}</th>
                                <th class="px-4 py-3 text-left">{{ t('applications.col_company') }}</th>
                                <th class="px-4 py-3 text-left">{{ t('applications.col_contact') }}</th>
                                <th class="w-20 px-4 py-3 text-center">{{ t('applications.col_docs') }}</th>
                                <th class="w-32 px-4 py-3 text-left">{{ t('applications.col_status') }}</th>
                                <th class="w-32 px-4 py-3 text-left">{{ t('applications.col_submitted') }}</th>
                                <th class="w-72 px-4 py-3 text-right">{{ t('table.col_actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-if="applications.length === 0" class="border-t">
                                <td :colspan="7" class="px-4 py-12 text-center text-muted-foreground">
                                    {{ t('applications.no_applications') }}
                                </td>
                            </tr>
                            <tr
                                v-for="app in applications"
                                v-else
                                :key="app.id"
                                class="border-t transition-colors hover:bg-muted/30"
                            >
                                <!-- Applicant: avatar + name + email -->
                                <td class="px-4 py-3">
                                    <div class="flex items-start gap-3">
                                        <div class="mt-0.5 flex size-9 shrink-0 items-center justify-center rounded-full bg-primary/10 text-xs font-semibold text-primary">
                                            {{ initials(app.full_name) }}
                                        </div>
                                        <div class="min-w-0 flex-1">
                                            <p class="truncate font-medium">{{ app.full_name }}</p>
                                            <a :href="`mailto:${app.email}`" class="inline-flex items-center gap-1 truncate text-xs text-muted-foreground hover:text-primary">
                                                <Mail class="size-3" />
                                                {{ app.email }}
                                            </a>
                                            <div v-if="app.documents.length > 0" class="mt-2 grid max-w-full grid-cols-2 gap-1">
                                                <a
                                                    v-for="doc in app.documents"
                                                    :key="doc.url"
                                                    :href="doc.url"
                                                    target="_blank"
                                                    rel="noopener"
                                                    class="min-w-0 truncate rounded-md border border-border bg-background px-2 py-1 text-[11px] font-medium text-foreground hover:border-primary hover:text-primary"
                                                    :title="doc.name"
                                                >
                                                    {{ doc.name }}
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </td>

                                <!-- Company -->
                                <td class="px-4 py-3">
                                    <div class="flex items-start gap-2">
                                        <Building2 class="mt-0.5 size-4 shrink-0 text-muted-foreground" />
                                        <div class="min-w-0">
                                            <p class="truncate font-medium">
                                                {{ app.company_name ?? t('applications.no_name') }}
                                            </p>
                                            <p v-if="app.company_city || app.company_postal" class="truncate text-xs text-muted-foreground">
                                                <span v-if="app.company_postal">{{ app.company_postal }} </span>
                                                <span v-if="app.company_city">{{ app.company_city }}</span>
                                            </p>
                                        </div>
                                    </div>
                                </td>

                                <!-- Phone -->
                                <td class="px-4 py-3">
                                    <div v-if="app.phone" class="flex items-center gap-1.5 text-xs">
                                        <Phone class="size-3.5 text-muted-foreground" />
                                        {{ app.phone }}
                                    </div>
                                    <span v-else class="text-xs text-muted-foreground">—</span>
                                </td>

                                <!-- Docs count -->
                                <td class="px-4 py-3 text-center">
                                    <span
                                        v-if="app.documents.length > 0"
                                        class="inline-flex items-center gap-1 rounded-md border border-border bg-muted px-1.5 py-0.5 text-xs"
                                        :title="app.documents.map((d) => d.name).join(', ')"
                                    >
                                        <Paperclip class="size-3" />
                                        {{ app.documents.length }}
                                    </span>
                                    <span v-else class="text-xs text-muted-foreground">—</span>
                                </td>

                                <!-- Status pill -->
                                <td class="px-4 py-3">
                                    <Badge
                                        variant="outline"
                                        class="gap-1"
                                        :class="statusStyle(app.status)"
                                    >
                                        <component :is="statusIcon(app.status)" class="size-3" />
                                        {{ t(`applications.status.${app.status}`) }}
                                    </Badge>
                                    <p
                                        v-if="app.status === 'rejected' && app.rejection_reason"
                                        class="mt-1 line-clamp-2 text-[10px] text-muted-foreground"
                                        :title="app.rejection_reason"
                                    >
                                        {{ app.rejection_reason }}
                                    </p>
                                </td>

                                <!-- Submitted -->
                                <td class="px-4 py-3">
                                    <div class="text-xs">
                                        <p class="font-medium">{{ formatDate(app.created_at) }}</p>
                                        <p v-if="app.reviewer_name" class="mt-0.5 text-muted-foreground" :title="formatDateTime(app.reviewed_at)">
                                            {{ t('applications.by_short', { name: app.reviewer_name }) }}
                                        </p>
                                    </div>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex flex-col items-end gap-2">
                                        <div class="flex flex-nowrap items-center justify-end gap-2 whitespace-nowrap">
                                            <button
                                                v-if="app.status === 'pending'"
                                                type="button"
                                                class="inline-flex items-center gap-1.5 rounded-md border border-emerald-300 bg-emerald-50 px-2 py-1 text-[11px] font-medium text-emerald-700 transition-colors hover:bg-emerald-100 dark:border-emerald-800 dark:bg-emerald-950 dark:text-emerald-300 dark:hover:bg-emerald-900"
                                                @click="approve(app)"
                                            >
                                                <Check class="size-3.5" />
                                                {{ t('applications.approve') }}
                                            </button>
                                            <button
                                                v-if="app.status === 'pending'"
                                                type="button"
                                                class="inline-flex items-center gap-1.5 rounded-md border border-red-300 bg-red-50 px-2 py-1 text-[11px] font-medium text-red-700 transition-colors hover:bg-red-100 dark:border-red-800 dark:bg-red-950 dark:text-red-300 dark:hover:bg-red-900"
                                                @click="openReject(app)"
                                            >
                                                <XCircle class="size-3.5" />
                                                {{ t('applications.reject') }}
                                            </button>
                                            <button
                                                type="button"
                                                class="inline-flex items-center gap-1.5 rounded-md border border-red-300 bg-red-50 px-2 py-1 text-[11px] font-medium text-red-700 transition-colors hover:bg-red-100 dark:border-red-800 dark:bg-red-950 dark:text-red-300 dark:hover:bg-red-900"
                                                @click="destroy(app)"
                                            >
                                                <Trash2 class="size-3.5" />
                                                {{ t('applications.delete') }}
                                            </button>
                                        </div>

                                        <!-- Row 2: navigation shortcuts (neutral) -->
                                        <div class="flex flex-wrap items-center justify-end gap-1">
                                            <a
                                                v-if="app.edit_url"
                                                :href="app.edit_url"
                                                class="inline-flex items-center gap-1.5 rounded-md border border-border bg-background px-2 py-1 text-[11px] font-medium text-foreground hover:border-primary hover:text-primary"
                                            >
                                                <Pencil class="size-3.5" />
                                                {{ t('applications.edit_company') }}
                                            </a>
                                            <Link
                                                v-if="app.company_id && app.company_permalink"
                                                :href="`/company-portal/${app.company_id}/${app.company_permalink}`"
                                                class="inline-flex items-center gap-1.5 rounded-md border border-border bg-background px-2 py-1 text-[11px] font-medium text-foreground hover:border-primary hover:text-primary"
                                            >
                                                <ExternalLink class="size-3.5" />
                                                {{ t('applications.view_portal') }}
                                            </Link>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
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
                        <label class="mb-1 block text-xs font-medium text-muted-foreground">
                            {{ t('applications.reject_reason_label') }}
                        </label>
                        <textarea
                            v-model="rejectForm.reason"
                            rows="4"
                            :placeholder="t('applications.reject_reason_placeholder')"
                            class="w-full rounded-md border border-input bg-background px-3 py-2 text-sm focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/20"
                            :class="{ 'border-red-500': !!rejectForm.errors.reason }"
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
                            variant="destructive"
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
