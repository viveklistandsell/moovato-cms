<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import {
    AlertTriangle,
    ArrowUpRight,
    Eye,
    FileEdit,
    Files,
    HardDrive,
    Image as ImageIcon,
    Languages as LanguagesIcon,
    Newspaper,
    Plus,
    Sparkles,
    TrendingDown,
    TrendingUp,
    UserPlus,
    Users,
    Zap,
} from 'lucide-vue-next';
import { computed } from 'vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import AchievementBadge from '@/components/admin/dashboard/AchievementBadge.vue';
import FlagImage from '@/components/common/FlagImage.vue';
import DateRangeSelector from '@/components/dashboard/DateRangeSelector.vue';
import { setBreadcrumbs } from '@/composables/common/useBreadcrumbs';
import { useT } from '@/composables/useT';
import { dashboard } from '@/routes';

const t = useT();

// Reactive breadcrumb: re-runs when the locale changes so the header
// title flips with the rest of the UI. `defineOptions` ran once at
// compile time so it can't reach the live translation dict.
setBreadcrumbs(() => [{ title: t('dashboard.title'), href: dashboard() }]);

type Welcome = {
    name: string | null;
    avatar_url: string | null;
    role_display_name: string | null;
    member_since: string | null;
};

type Kpis = {
    pages: { total: number; published: number; draft: number; inactive: number };
    posts: {
        total: number;
        published: number;
        draft: number;
        inactive: number;
        views: number;
    };
    users: {
        total: number;
        verified: number;
        unverified: number;
        new_in_range: number;
    };
    media: { files: number; folders: number; bytes: number };
};

type Chart = { weeks: string[]; pages: number[]; posts: number[] };

type TopPost = {
    id: number;
    title: string;
    permalink: string;
    views: number;
    author: string | null;
};

type LangCoverage = {
    code: string;
    native_name: string;
    flag: string | null;
    is_default: boolean;
    page_coverage: number;
    post_coverage: number;
};

type TopCategory = { id: number; name: string; page_count: number };

type Attention = {
    page_drafts: number;
    post_drafts: number;
    unverified_users: number;
    missing_translations: number;
};

type Permissions = {
    users_view: boolean;
    pages_create: boolean;
    posts_create: boolean;
    users_create: boolean;
};

type Achievement = {
    key: string;
    icon: string;
    label: string;
    value: string | number;
    tone: 'fire' | 'orange' | 'sky' | 'violet';
};

const props = defineProps<{
    welcome: Welcome;
    range: string;
    achievements?: Achievement[];
    kpis: Kpis;
    chart: Chart;
    topPosts: TopPost[];
    languageCoverage: LangCoverage[];
    topCategories: TopCategory[];
    attention: Attention;
    permissions: Permissions;
}>();

// Breadcrumb is set dynamically above via setBreadcrumbs().
defineOptions({});

const greeting = computed<string>(() => {
    const h = new Date().getHours();
    if (h >= 5 && h < 12) return t('dashboard.greeting_morning');
    if (h >= 12 && h < 18) return t('dashboard.greeting_day');
    if (h >= 18 && h < 23) return t('dashboard.greeting_evening');
    return t('dashboard.greeting_hi');
});

const rangeLabel = computed<string>(() => {
    switch (props.range) {
        case '7d':
            return t('date_range.last_7_days');
        case '90d':
            return t('date_range.last_90_days');
        case 'year':
            return t('date_range.this_year');
        default:
            return t('date_range.last_30_days');
    }
});

const newInRangeLabel = computed<string>(() => {
    switch (props.range) {
        case '7d':
            return t('dashboard.new_last_7d');
        case '90d':
            return t('dashboard.new_last_90d');
        case 'year':
            return t('dashboard.new_this_year');
        default:
            return t('dashboard.new_last_30d');
    }
});

function formatDate(iso: string | null): string {
    if (!iso) return '—';
    return new Date(iso).toLocaleDateString(undefined, {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
    });
}

function formatBytes(bytes: number): string {
    if (bytes < 1024) return `${bytes} B`;
    if (bytes < 1024 * 1024) return `${(bytes / 1024).toFixed(1)} KB`;
    if (bytes < 1024 * 1024 * 1024)
        return `${(bytes / (1024 * 1024)).toFixed(1)} MB`;
    return `${(bytes / (1024 * 1024 * 1024)).toFixed(2)} GB`;
}

// =====================================================================
// Hero metric: content velocity (derived from chart data)
// =====================================================================
const weekTotals = computed<number[]>(() =>
    props.chart.weeks.map(
        (_, i) =>
            (props.chart.pages[i] ?? 0) + (props.chart.posts[i] ?? 0),
    ),
);

const heroTotal = computed<number>(() =>
    weekTotals.value.reduce((a, b) => a + b, 0),
);

const heroDelta = computed<number | null>(() => {
    const totals = weekTotals.value;
    if (totals.length < 4) return null;
    const halfIdx = Math.floor(totals.length / 2);
    const recent = totals.slice(halfIdx).reduce((a, b) => a + b, 0);
    const prior = totals.slice(0, halfIdx).reduce((a, b) => a + b, 0);
    if (prior === 0) {
        return recent > 0 ? 100 : null;
    }
    return Math.round(((recent - prior) / prior) * 100);
});

const sparklinePath = computed<string>(() => {
    const totals = weekTotals.value;
    if (totals.length === 0) return '';
    const max = Math.max(1, ...totals);
    const stepX = 200 / Math.max(1, totals.length - 1);
    return totals
        .map((v, i) => {
            const x = i * stepX;
            const y = 40 - (v / max) * 36 - 2;
            return `${i === 0 ? 'M' : 'L'}${x.toFixed(1)},${y.toFixed(1)}`;
        })
        .join(' ');
});

const sparklineAreaPath = computed<string>(() => {
    const line = sparklinePath.value;
    if (line === '') return '';
    return `${line} L200,40 L0,40 Z`;
});

const chartMax = computed(() => Math.max(1, ...weekTotals.value));

function pagesHeight(i: number): number {
    return ((props.chart.pages[i] ?? 0) / chartMax.value) * 100;
}

function postsHeight(i: number): number {
    return ((props.chart.posts[i] ?? 0) / chartMax.value) * 100;
}

const attentionItems = computed(() =>
    [
        {
            key: 'page_drafts',
            label: t('dashboard.page_drafts'),
            count: props.attention.page_drafts,
            href: '/admin/pages?status=draft',
        },
        {
            key: 'post_drafts',
            label: t('dashboard.blog_drafts'),
            count: props.attention.post_drafts,
            href: '/admin/blog/posts?status=draft',
        },
        {
            key: 'unverified_users',
            label: t('dashboard.unverified_users'),
            count: props.attention.unverified_users,
            href: props.permissions.users_view ? '/admin/users' : null,
        },
        {
            key: 'missing_translations',
            label: t('dashboard.missing_translations'),
            count: props.attention.missing_translations,
            href: '/admin/pages?lang=de',
        },
    ].filter((row) => row.count > 0),
);

function publishedRatio(total: number, published: number): number {
    if (total === 0) return 0;
    return Math.round((published / total) * 100);
}
</script>

<template>
    <Head title="Dashboard" />

    <div class="flex flex-col gap-5 p-4 sm:p-6">
        <div
            class="relative z-30 rounded-2xl border border-white/40 bg-white/70 p-5 backdrop-blur-xl dark:border-white/5 dark:bg-[var(--midnight)]/70"
        >
            <div
                class="pointer-events-none absolute inset-0 overflow-hidden rounded-2xl"
                aria-hidden="true"
            >
                <div
                    class="absolute -top-16 -right-16 size-64 rounded-full bg-gradient-to-br from-[var(--orange)]/25 to-transparent blur-3xl"
                />
            </div>
            <div
                class="relative flex flex-col items-start justify-between gap-5 sm:flex-row sm:items-center"
            >
                <div class="flex items-center gap-4">
                    <div
                        class="flex size-12 items-center justify-center overflow-hidden rounded-full bg-[var(--midnight)] text-[var(--linen)] ring-2 ring-[var(--orange)]/40"
                    >
                        <img
                            v-if="welcome.avatar_url"
                            :src="welcome.avatar_url"
                            :alt="welcome.name ?? ''"
                            class="size-full object-cover"
                        />
                        <Users v-else class="size-5" />
                    </div>
                    <div>
                        <p
                            class="flex items-center gap-1.5 text-xs font-bold uppercase tracking-widest text-[var(--orange)]"
                        >
                            <Zap class="size-3" />
                            {{ greeting }}
                        </p>
                        <h1
                            class="text-xl font-bold tracking-tight text-[var(--midnight)] dark:text-[var(--linen)]"
                        >
                            {{ welcome.name ?? 'there' }}
                        </h1>
                        <p class="mt-0.5 text-xs text-muted-foreground">
                            <span v-if="welcome.role_display_name">
                                {{ t('dashboard.logged_in_as') }}
                                <span class="font-medium text-foreground">
                                    {{ welcome.role_display_name }}
                                </span>
                            </span>
                            <span v-if="welcome.member_since">
                                · {{ t('dashboard.member_since') }} {{ formatDate(welcome.member_since) }}
                            </span>
                        </p>
                        <div
                            v-if="achievements && achievements.length > 0"
                            class="mt-2 flex flex-wrap items-center gap-1.5"
                        >
                            <AchievementBadge
                                v-for="badge in achievements.slice(0, 3)"
                                :key="badge.key"
                                :icon="badge.icon"
                                :label="badge.label"
                                :tone="badge.tone"
                            />
                        </div>
                    </div>
                </div>
                <div class="flex flex-wrap items-center gap-2">
                    <Button v-if="permissions.pages_create" as-child size="sm">
                        <Link href="/admin/pages/create">
                            <Plus class="size-4" />
                            {{ t('dashboard.new_page') }}
                        </Link>
                    </Button>
                    <Button
                        v-if="permissions.posts_create"
                        as-child
                        size="sm"
                        variant="secondary"
                    >
                        <Link href="/admin/blog/posts/create">
                            <Plus class="size-4" />
                            {{ t('dashboard.new_blog') }}
                        </Link>
                    </Button>
                    <Button
                        v-if="permissions.users_create"
                        as-child
                        size="sm"
                        variant="outline"
                    >
                        <Link href="/admin/users/create">
                            <UserPlus class="size-4" />
                            {{ t('dashboard.new_user') }}
                        </Link>
                    </Button>
                    <DateRangeSelector :value="range" />
                </div>
            </div>
        </div>

        <!-- Hero metric -->
        <div
            class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-[var(--midnight)] via-[var(--midnight-soft)] to-[var(--midnight)] p-5 text-[var(--linen)] shadow-lg"
        >
            <div
                class="pointer-events-none absolute -top-16 -left-10 size-48 rounded-full bg-[var(--orange)]/20 blur-3xl"
                aria-hidden="true"
            />
            <div
                class="pointer-events-none absolute -right-10 -bottom-16 size-48 rounded-full bg-[var(--yellow-dark)]/15 blur-3xl"
                aria-hidden="true"
            />
            <div
                class="relative flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between"
            >
                <div>
                    <div
                        class="inline-flex items-center gap-1.5 rounded-full bg-[var(--orange)]/15 px-3 py-0.5 text-[10px] font-semibold uppercase tracking-wider text-[var(--orange)] ring-1 ring-inset ring-[var(--orange)]/30"
                    >
                        <Sparkles class="size-3" />
                        {{ t('dashboard.hero_velocity_range', { range: rangeLabel }) }}
                    </div>
                    <div class="mt-3 flex items-end gap-2">
                        <span class="text-4xl font-bold leading-none tracking-tight">
                            {{ heroTotal.toLocaleString() }}
                        </span>
                        <span class="pb-0.5 text-xs text-[var(--linen)]/60">
                            {{ t('dashboard.hero_items_created') }}
                        </span>
                    </div>
                    <div
                        v-if="heroDelta !== null"
                        class="mt-2 flex items-center gap-2 text-xs"
                    >
                        <span
                            v-if="heroDelta >= 0"
                            class="inline-flex items-center gap-1 rounded-md bg-emerald-500/15 px-1.5 py-0.5 font-semibold text-emerald-300 ring-1 ring-inset ring-emerald-500/30"
                        >
                            <TrendingUp class="size-3" />
                            +{{ heroDelta }}%
                        </span>
                        <span
                            v-else
                            class="inline-flex items-center gap-1 rounded-md bg-rose-500/15 px-1.5 py-0.5 font-semibold text-rose-300 ring-1 ring-inset ring-rose-500/30"
                        >
                            <TrendingDown class="size-3" />
                            {{ heroDelta }}%
                        </span>
                        <span class="text-[var(--linen)]/60">
                            {{ t('dashboard.hero_vs_prior_half') }}
                        </span>
                    </div>
                </div>
                <div class="w-full max-w-xs">
                    <svg
                        viewBox="0 0 200 40"
                        class="h-12 w-full"
                        preserveAspectRatio="none"
                    >
                        <defs>
                            <linearGradient id="sparkFill" x1="0" y1="0" x2="0" y2="1">
                                <stop offset="0%" stop-color="var(--orange)" stop-opacity="0.5" />
                                <stop offset="100%" stop-color="var(--orange)" stop-opacity="0" />
                            </linearGradient>
                            <linearGradient id="sparkLine" x1="0" y1="0" x2="1" y2="0">
                                <stop offset="0%" stop-color="var(--orange)" />
                                <stop offset="100%" stop-color="var(--yellow-dark)" />
                            </linearGradient>
                        </defs>
                        <path
                            v-if="sparklineAreaPath"
                            :d="sparklineAreaPath"
                            fill="url(#sparkFill)"
                        />
                        <path
                            v-if="sparklinePath"
                            :d="sparklinePath"
                            fill="none"
                            stroke="url(#sparkLine)"
                            stroke-width="2"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                        />
                    </svg>
                    <p
                        class="mt-1 text-right text-[9px] uppercase tracking-wider text-[var(--linen)]/40"
                    >
                        {{ t('dashboard.hero_trend') }}
                    </p>
                </div>
            </div>
        </div>

        <!-- KPI tiles -->
        <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
            <div
                class="group relative overflow-hidden rounded-2xl border border-white/40 bg-white/70 p-5 backdrop-blur-xl transition-all hover:-translate-y-1 hover:border-[var(--orange)]/40 hover:shadow-2xl hover:shadow-[var(--orange)]/15 dark:border-white/5 dark:bg-[var(--midnight)]/60"
            >
                <div
                    class="pointer-events-none absolute -top-12 -right-12 size-32 rounded-full bg-gradient-to-br from-[var(--orange)]/25 to-transparent blur-2xl"
                    aria-hidden="true"
                />
                <div class="relative">
                    <div class="flex items-start justify-between">
                        <div>
                            <p class="text-[11px] font-bold uppercase tracking-widest text-muted-foreground">
                                {{ t('dashboard.kpi_pages') }}
                            </p>
                            <div class="mt-1 text-4xl font-black text-[var(--midnight)] dark:text-[var(--linen)]">
                                {{ kpis.pages.total.toLocaleString() }}
                            </div>
                        </div>
                        <div
                            class="flex size-10 items-center justify-center rounded-xl bg-gradient-to-br from-[var(--orange)]/20 to-[var(--orange)]/5 text-[var(--orange)] ring-1 ring-[var(--orange)]/20"
                        >
                            <Files class="size-5" />
                        </div>
                    </div>
                    <p class="mt-2 text-xs text-muted-foreground">
                        <span class="font-semibold text-foreground">{{ kpis.pages.published }}</span>
                        {{ t('dashboard.published') }} · {{ kpis.pages.draft }} {{ t('dashboard.draft') }}
                    </p>
                    <div class="mt-3 h-1.5 overflow-hidden rounded-full bg-muted">
                        <div
                            class="h-full rounded-full bg-gradient-to-r from-[var(--orange)] to-[var(--yellow-dark)]"
                            :style="{ width: `${publishedRatio(kpis.pages.total, kpis.pages.published)}%` }"
                        />
                    </div>
                    <Link
                        href="/admin/pages"
                        class="mt-3 inline-flex items-center gap-0.5 text-xs font-semibold text-[var(--orange)] transition-all hover:gap-1.5"
                    >
                        {{ t('dashboard.manage_pages') }} <ArrowUpRight class="size-3" />
                    </Link>
                </div>
            </div>

            <div
                class="group relative overflow-hidden rounded-2xl border border-white/40 bg-white/70 p-5 backdrop-blur-xl transition-all hover:-translate-y-1 hover:border-[var(--orange)]/40 hover:shadow-2xl hover:shadow-[var(--orange)]/15 dark:border-white/5 dark:bg-[var(--midnight)]/60"
            >
                <div
                    class="pointer-events-none absolute -top-12 -right-12 size-32 rounded-full bg-gradient-to-br from-[var(--yellow-dark)]/25 to-transparent blur-2xl"
                    aria-hidden="true"
                />
                <div class="relative">
                    <div class="flex items-start justify-between">
                        <div>
                            <p class="text-[11px] font-bold uppercase tracking-widest text-muted-foreground">
                                {{ t('dashboard.kpi_blogs') }}
                            </p>
                            <div class="mt-1 text-4xl font-black text-[var(--midnight)] dark:text-[var(--linen)]">
                                {{ kpis.posts.total.toLocaleString() }}
                            </div>
                        </div>
                        <div
                            class="flex size-10 items-center justify-center rounded-xl bg-gradient-to-br from-[var(--yellow-dark)]/20 to-[var(--yellow-dark)]/5 text-[var(--yellow-dark)] ring-1 ring-[var(--yellow-dark)]/20"
                        >
                            <Newspaper class="size-5" />
                        </div>
                    </div>
                    <p class="mt-2 flex items-center gap-1.5 text-xs text-muted-foreground">
                        <Eye class="size-3" />
                        <span class="font-semibold text-foreground">
                            {{ kpis.posts.views.toLocaleString() }}
                        </span>
                        {{ t('dashboard.total_views') }}
                    </p>
                    <div class="mt-3 h-1.5 overflow-hidden rounded-full bg-muted">
                        <div
                            class="h-full rounded-full bg-gradient-to-r from-[var(--orange)] to-[var(--yellow-dark)]"
                            :style="{ width: `${publishedRatio(kpis.posts.total, kpis.posts.published)}%` }"
                        />
                    </div>
                    <Link
                        href="/admin/blog/posts"
                        class="mt-3 inline-flex items-center gap-0.5 text-xs font-semibold text-[var(--orange)] transition-all hover:gap-1.5"
                    >
                        {{ t('dashboard.manage_blogs') }} <ArrowUpRight class="size-3" />
                    </Link>
                </div>
            </div>

            <div
                v-if="permissions.users_view"
                class="group relative overflow-hidden rounded-2xl border border-white/40 bg-white/70 p-5 backdrop-blur-xl transition-all hover:-translate-y-1 hover:border-[var(--orange)]/40 hover:shadow-2xl hover:shadow-[var(--orange)]/15 dark:border-white/5 dark:bg-[var(--midnight)]/60"
            >
                <div
                    class="pointer-events-none absolute -top-12 -right-12 size-32 rounded-full bg-gradient-to-br from-emerald-400/20 to-transparent blur-2xl"
                    aria-hidden="true"
                />
                <div class="relative">
                    <div class="flex items-start justify-between">
                        <div>
                            <p class="text-[11px] font-bold uppercase tracking-widest text-muted-foreground">
                                {{ t('dashboard.kpi_users') }}
                            </p>
                            <div class="mt-1 text-4xl font-black text-[var(--midnight)] dark:text-[var(--linen)]">
                                {{ kpis.users.total.toLocaleString() }}
                            </div>
                        </div>
                        <div
                            class="flex size-10 items-center justify-center rounded-xl bg-gradient-to-br from-emerald-500/20 to-emerald-500/5 text-emerald-600 ring-1 ring-emerald-500/20"
                        >
                            <Users class="size-5" />
                        </div>
                    </div>
                    <p class="mt-2 text-xs text-muted-foreground">
                        <span class="font-semibold text-foreground">+{{ kpis.users.new_in_range }}</span>
                        {{ newInRangeLabel }}
                    </p>
                    <div class="mt-3 h-1.5 overflow-hidden rounded-full bg-muted">
                        <div
                            class="h-full rounded-full bg-emerald-500"
                            :style="{ width: `${publishedRatio(kpis.users.total, kpis.users.verified)}%` }"
                        />
                    </div>
                    <Link
                        href="/admin/users"
                        class="mt-3 inline-flex items-center gap-0.5 text-xs font-semibold text-[var(--orange)] transition-all hover:gap-1.5"
                    >
                        {{ t('dashboard.manage_users') }} <ArrowUpRight class="size-3" />
                    </Link>
                </div>
            </div>

            <div
                class="group relative overflow-hidden rounded-2xl border border-white/40 bg-white/70 p-5 backdrop-blur-xl transition-all hover:-translate-y-1 hover:border-[var(--orange)]/40 hover:shadow-2xl hover:shadow-[var(--orange)]/15 dark:border-white/5 dark:bg-[var(--midnight)]/60"
            >
                <div
                    class="pointer-events-none absolute -top-12 -right-12 size-32 rounded-full bg-gradient-to-br from-violet-400/20 to-transparent blur-2xl"
                    aria-hidden="true"
                />
                <div class="relative">
                    <div class="flex items-start justify-between">
                        <div>
                            <p class="text-[11px] font-bold uppercase tracking-widest text-muted-foreground">
                                {{ t('dashboard.kpi_media') }}
                            </p>
                            <div class="mt-1 text-4xl font-black text-[var(--midnight)] dark:text-[var(--linen)]">
                                {{ kpis.media.files.toLocaleString() }}
                            </div>
                        </div>
                        <div
                            class="flex size-10 items-center justify-center rounded-xl bg-gradient-to-br from-violet-500/20 to-violet-500/5 text-violet-600 ring-1 ring-violet-500/20"
                        >
                            <HardDrive class="size-5" />
                        </div>
                    </div>
                    <p class="mt-2 text-xs text-muted-foreground">
                        <span class="font-semibold text-foreground">{{ formatBytes(kpis.media.bytes) }}</span>
                        · {{ kpis.media.folders }} {{ t('dashboard.folders') }}
                    </p>
                    <div class="mt-3 h-1.5 rounded-full bg-violet-500/20" />
                    <Link
                        href="/admin/media"
                        class="mt-3 inline-flex items-center gap-0.5 text-xs font-semibold text-[var(--orange)] transition-all hover:gap-1.5"
                    >
                        {{ t('dashboard.open_library') }} <ArrowUpRight class="size-3" />
                    </Link>
                </div>
            </div>
        </div>

        <!-- Chart + Top blogs -->
        <div class="grid gap-4 lg:grid-cols-2">
            <Card class="overflow-hidden border-white/40 bg-white/70 backdrop-blur-xl dark:border-white/5 dark:bg-[var(--midnight)]/60">
                <CardHeader>
                    <CardTitle class="flex items-center gap-2 text-base">
                        <div
                            class="flex size-7 items-center justify-center rounded-md bg-gradient-to-br from-[var(--orange)] to-[var(--yellow-dark)] text-white shadow-md shadow-[var(--orange)]/30"
                        >
                            <TrendingUp class="size-3.5" />
                        </div>
                        {{ t('dashboard.content_created_range', { range: rangeLabel }) }}
                    </CardTitle>
                </CardHeader>
                <CardContent>
                    <div
                        v-if="heroTotal === 0"
                        class="flex h-40 items-center justify-center text-sm text-muted-foreground"
                    >
                        {{ t('dashboard.no_content_in_range', { range: rangeLabel }) }}
                    </div>
                    <div v-else>
                        <div class="flex h-48 gap-1.5">
                            <div
                                v-for="(label, i) in chart.weeks"
                                :key="i"
                                class="group flex flex-1 flex-col justify-end gap-px"
                                :title="`${label}: ${chart.pages[i] ?? 0} pages, ${chart.posts[i] ?? 0} blogs`"
                            >
                                <div
                                    v-if="(chart.posts[i] ?? 0) > 0"
                                    class="rounded-t-md bg-gradient-to-t from-[var(--orange)]/60 to-[var(--orange)] transition-all group-hover:from-[var(--yellow-dark)] group-hover:to-[var(--orange)]"
                                    :style="{ height: `${postsHeight(i)}%` }"
                                />
                                <div
                                    v-if="(chart.pages[i] ?? 0) > 0"
                                    class="bg-gradient-to-t from-[var(--midnight)]/60 to-[var(--midnight)] transition-all group-hover:from-[var(--midnight-soft)] group-hover:to-[var(--midnight)] dark:from-[var(--linen)]/40 dark:to-[var(--linen)]"
                                    :class="(chart.posts[i] ?? 0) === 0 ? 'rounded-t-md' : ''"
                                    :style="{ height: `${pagesHeight(i)}%` }"
                                />
                            </div>
                        </div>
                        <div class="mt-3 flex items-center justify-between text-[10px] font-medium text-muted-foreground">
                            <span>{{ chart.weeks[0] }}</span>
                            <span>{{ chart.weeks[chart.weeks.length - 1] }}</span>
                        </div>
                        <div class="mt-3 flex items-center gap-4 text-xs">
                            <span class="flex items-center gap-1.5">
                                <span
                                    class="size-3 rounded-sm bg-gradient-to-t from-[var(--midnight)]/60 to-[var(--midnight)] dark:from-[var(--linen)]/40 dark:to-[var(--linen)]"
                                />
                                {{ t('dashboard.kpi_pages') }}
                            </span>
                            <span class="flex items-center gap-1.5">
                                <span class="size-3 rounded-sm bg-gradient-to-t from-[var(--orange)]/60 to-[var(--orange)]" />
                                {{ t('dashboard.kpi_blogs') }}
                            </span>
                        </div>
                    </div>
                </CardContent>
            </Card>

            <Card class="overflow-hidden border-white/40 bg-white/70 backdrop-blur-xl dark:border-white/5 dark:bg-[var(--midnight)]/60">
                <CardHeader>
                    <CardTitle class="flex items-center gap-2 text-base">
                        <div
                            class="flex size-7 items-center justify-center rounded-md bg-gradient-to-br from-[var(--orange)] to-[var(--yellow-dark)] text-white shadow-md shadow-[var(--orange)]/30"
                        >
                            <Eye class="size-3.5" />
                        </div>
                        {{ t('dashboard.top_viewed_blogs') }}
                    </CardTitle>
                </CardHeader>
                <CardContent class="px-0">
                    <div
                        v-if="topPosts.length === 0"
                        class="px-6 py-8 text-center text-sm text-muted-foreground"
                    >
                        {{ t('dashboard.no_blogs_yet') }}
                    </div>
                    <ol v-else class="divide-y divide-border/40">
                        <li
                            v-for="(p, i) in topPosts"
                            :key="p.id"
                            class="flex items-center gap-3 px-4 py-3 transition-colors hover:bg-[var(--orange)]/5"
                        >
                            <span
                                class="flex size-8 shrink-0 items-center justify-center rounded-xl text-xs font-bold shadow-sm"
                                :class="
                                    i === 0
                                        ? 'bg-gradient-to-br from-[var(--orange)] to-[var(--yellow-dark)] text-white shadow-[var(--orange)]/40'
                                        : i === 1
                                          ? 'bg-gradient-to-br from-[var(--midnight)] to-[var(--midnight-soft)] text-[var(--linen)]'
                                          : 'bg-muted text-muted-foreground'
                                "
                            >
                                {{ i + 1 }}
                            </span>
                            <div class="min-w-0 flex-1">
                                <Link
                                    :href="`/admin/blog/posts/${p.id}/edit`"
                                    class="block truncate text-sm font-semibold hover:text-[var(--orange)] hover:underline"
                                >
                                    {{ p.title }}
                                </Link>
                                <p class="text-[10px] text-muted-foreground">
                                    {{ t('dashboard.by_author', { author: p.author ?? '—' }) }}
                                </p>
                            </div>
                            <span class="flex shrink-0 items-center gap-1 rounded-full bg-muted/60 px-2 py-0.5 text-xs font-bold text-muted-foreground">
                                <Eye class="size-3" />
                                {{ p.views.toLocaleString() }}
                            </span>
                        </li>
                    </ol>
                </CardContent>
            </Card>
        </div>

        <!-- System overview -->
        <div class="grid gap-4 lg:grid-cols-3">
            <Card class="overflow-hidden border-white/40 bg-white/70 backdrop-blur-xl dark:border-white/5 dark:bg-[var(--midnight)]/60">
                <CardHeader>
                    <CardTitle class="flex items-center gap-2 text-base">
                        <div
                            class="flex size-7 items-center justify-center rounded-md bg-gradient-to-br from-[var(--orange)] to-[var(--yellow-dark)] text-white shadow-md shadow-[var(--orange)]/30"
                        >
                            <LanguagesIcon class="size-3.5" />
                        </div>
                        {{ t('dashboard.languages_title') }}
                    </CardTitle>
                </CardHeader>
                <CardContent class="space-y-4">
                    <div v-if="languageCoverage.length === 0" class="text-sm text-muted-foreground">
                        {{ t('dashboard.no_active_languages') }}
                    </div>
                    <div
                        v-for="lang in languageCoverage"
                        :key="lang.code"
                        class="flex items-center gap-3"
                    >
                        <FlagImage :code="lang.flag" size="lg" />
                        <div class="flex-1">
                            <div class="flex items-center justify-between text-sm">
                                <span class="font-semibold">
                                    {{ lang.code.toUpperCase() }} · {{ lang.native_name }}
                                    <Badge v-if="lang.is_default" variant="secondary" class="ml-1 text-[10px]">
                                        {{ t('dashboard.default') }}
                                    </Badge>
                                </span>
                                <span class="text-sm font-black text-[var(--orange)]">
                                    {{ Math.round((lang.page_coverage + lang.post_coverage) / 2) }}%
                                </span>
                            </div>
                            <div class="mt-1.5 grid grid-cols-2 gap-2 text-[10px] text-muted-foreground">
                                <div>
                                    <div class="flex justify-between font-medium">
                                        <span>Pages</span>
                                        <span>{{ lang.page_coverage }}%</span>
                                    </div>
                                    <div class="mt-1 h-2 overflow-hidden rounded-full bg-muted">
                                        <div
                                            class="h-full rounded-full bg-gradient-to-r from-[var(--midnight)] to-[var(--midnight-soft)] dark:from-[var(--linen)] dark:to-white"
                                            :style="{ width: `${lang.page_coverage}%` }"
                                        />
                                    </div>
                                </div>
                                <div>
                                    <div class="flex justify-between font-medium">
                                        <span>Blogs</span>
                                        <span>{{ lang.post_coverage }}%</span>
                                    </div>
                                    <div class="mt-1 h-2 overflow-hidden rounded-full bg-muted">
                                        <div
                                            class="h-full rounded-full bg-gradient-to-r from-[var(--orange)] to-[var(--yellow-dark)]"
                                            :style="{ width: `${lang.post_coverage}%` }"
                                        />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </CardContent>
            </Card>

            <Card class="overflow-hidden border-white/40 bg-white/70 backdrop-blur-xl dark:border-white/5 dark:bg-[var(--midnight)]/60">
                <CardHeader>
                    <CardTitle class="flex items-center gap-2 text-base">
                        <div
                            class="flex size-7 items-center justify-center rounded-md bg-gradient-to-br from-[var(--orange)] to-[var(--yellow-dark)] text-white shadow-md shadow-[var(--orange)]/30"
                        >
                            <ImageIcon class="size-3.5" />
                        </div>
                        {{ t('dashboard.top_page_categories') }}
                    </CardTitle>
                </CardHeader>
                <CardContent class="px-0">
                    <div
                        v-if="topCategories.length === 0"
                        class="px-6 py-8 text-center text-sm text-muted-foreground"
                    >
                        {{ t('dashboard.no_categories') }}
                    </div>
                    <ul v-else class="divide-y divide-border/40">
                        <li
                            v-for="c in topCategories"
                            :key="c.id"
                            class="flex items-center justify-between gap-3 px-4 py-3 transition-colors hover:bg-[var(--orange)]/5"
                        >
                            <Link
                                :href="`/admin/pages/categories/${c.id}/edit`"
                                class="truncate text-sm font-semibold hover:text-[var(--orange)] hover:underline"
                            >
                                {{ c.name }}
                            </Link>
                            <span class="shrink-0 rounded-full bg-muted/60 px-2.5 py-0.5 text-[10px] font-bold text-muted-foreground">
                                {{ c.page_count }} {{ t('dashboard.kpi_pages') }}
                            </span>
                        </li>
                    </ul>
                </CardContent>
            </Card>

            <Card class="overflow-hidden border-white/40 bg-white/70 backdrop-blur-xl dark:border-white/5 dark:bg-[var(--midnight)]/60">
                <CardHeader>
                    <CardTitle class="flex items-center gap-2 text-base">
                        <div
                            class="flex size-7 items-center justify-center rounded-md bg-gradient-to-br from-[var(--orange)] to-[var(--yellow-dark)] text-white shadow-md shadow-[var(--orange)]/30"
                        >
                            <AlertTriangle class="size-3.5" />
                        </div>
                        {{ t('dashboard.needs_attention') }}
                    </CardTitle>
                </CardHeader>
                <CardContent>
                    <div
                        v-if="attentionItems.length === 0"
                        class="flex flex-col items-center justify-center gap-2 py-8 text-center text-sm text-muted-foreground"
                    >
                        <FileEdit class="size-5" />
                        {{ t('dashboard.all_caught_up') }}
                    </div>
                    <ul v-else class="space-y-2">
                        <li
                            v-for="row in attentionItems"
                            :key="row.key"
                            class="flex items-center justify-between gap-2 rounded-xl border border-[var(--orange)]/20 bg-gradient-to-r from-[var(--orange)]/10 to-[var(--orange)]/0 px-3 py-2.5 text-sm"
                        >
                            <span class="flex items-center gap-2">
                                <span
                                    class="inline-flex size-7 items-center justify-center rounded-full bg-gradient-to-br from-[var(--orange)] to-[var(--yellow-dark)] text-xs font-bold text-white shadow-md shadow-[var(--orange)]/40"
                                >
                                    {{ row.count }}
                                </span>
                                <span class="font-medium">{{ row.label }}</span>
                            </span>
                            <Link
                                v-if="row.href"
                                :href="row.href"
                                class="inline-flex items-center gap-0.5 text-xs font-semibold text-[var(--orange)] hover:gap-1.5"
                            >
                                {{ t('common.review') }} <ArrowUpRight class="size-3" />
                            </Link>
                        </li>
                    </ul>
                </CardContent>
            </Card>
        </div>
    </div>
</template>
