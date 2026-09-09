<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import {
    AlertTriangle,
    ArrowUpRight,
    Building2,
    Crown,
    Eye,
    FileEdit,
    Files,
    HardDrive,
    Image as ImageIcon,
    Languages as LanguagesIcon,
    Menu as MenuIcon,
    Newspaper,
    Plus,
    ShieldCheck,
    Sparkles,
    TrendingDown,
    TrendingUp,
    Upload,
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

type CompaniesOverview = {
    total: number;
    unclaimed: number;
    pending_applications: number;
    plan_basic: number;
    plan_premium: number;
    plan_gold: number;
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
    companiesOverview?: CompaniesOverview;
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

type QuickTile = {
    key: string;
    label: string;
    icon: unknown;
    manageHref: string;
    manageLabel: string;
    createHref: string | null;
    createLabel: string | null;
    kpiCount: number | null;
    kpiHint: string | null;
    tone: 'orange' | 'yellow' | 'emerald' | 'violet' | 'midnight' | 'linen';
    show: boolean;
};

const quickTiles = computed<QuickTile[]>(() =>
    ([
        {
            key: 'pages',
            label: t('dashboard.kpi_pages'),
            icon: Files,
            manageHref: '/admin/pages',
            manageLabel: t('dashboard.manage_pages'),
            createHref: props.permissions.pages_create ? '/admin/pages/create' : null,
            createLabel: props.permissions.pages_create ? t('dashboard.quick_new_page') : null,
            kpiCount: props.kpis.pages.total,
            kpiHint: `${props.kpis.pages.published} ${t('dashboard.published')} · ${props.kpis.pages.draft} ${t('dashboard.draft')}`,
            tone: 'orange',
            show: true,
        },
        {
            key: 'blogs',
            label: t('dashboard.kpi_blogs'),
            icon: Newspaper,
            manageHref: '/admin/blog/posts',
            manageLabel: t('dashboard.manage_blogs'),
            createHref: props.permissions.posts_create ? '/admin/blog/posts/create' : null,
            createLabel: props.permissions.posts_create ? t('dashboard.quick_new_post') : null,
            kpiCount: props.kpis.posts.total,
            kpiHint: `${props.kpis.posts.views.toLocaleString()} ${t('dashboard.total_views')}`,
            tone: 'yellow',
            show: true,
        },
        {
            key: 'users',
            label: t('dashboard.kpi_users'),
            icon: Users,
            manageHref: '/admin/users',
            manageLabel: t('dashboard.manage_users'),
            createHref: props.permissions.users_create ? '/admin/users/create' : null,
            createLabel: props.permissions.users_create ? t('dashboard.new_user') : null,
            kpiCount: props.kpis.users.total,
            kpiHint: `+${props.kpis.users.new_in_range} ${newInRangeLabel.value}`,
            tone: 'emerald',
            show: props.permissions.users_view,
        },
        {
            key: 'media',
            label: t('dashboard.kpi_media'),
            icon: HardDrive,
            manageHref: '/admin/media',
            manageLabel: t('dashboard.open_library'),
            createHref: null,
            createLabel: null,
            kpiCount: props.kpis.media.files,
            kpiHint: `${formatBytes(props.kpis.media.bytes)} · ${props.kpis.media.folders} ${t('dashboard.folders')}`,
            tone: 'violet',
            show: true,
        },
        {
            key: 'import_pages',
            label: t('dashboard.quick_import_pages'),
            icon: Upload,
            manageHref: '/admin/pages/import',
            manageLabel: t('dashboard.quick_go'),
            createHref: null,
            createLabel: null,
            kpiCount: null,
            kpiHint: null,
            tone: 'midnight',
            show: props.permissions.pages_create,
        },
        {
            key: 'menus',
            label: t('dashboard.quick_menus'),
            icon: MenuIcon,
            manageHref: '/admin/menus',
            manageLabel: t('dashboard.quick_go'),
            createHref: null,
            createLabel: null,
            kpiCount: null,
            kpiHint: null,
            tone: 'linen',
            show: true,
        },
    ] as QuickTile[]).filter((row) => row.show),
);
type ToneStyle = {
    card: string;
    ringHover: string;
    iconChip: string;
    accentText: string;
    createChip: string;
    manageText: string;
};
const toneStyles: Record<QuickTile['tone'], ToneStyle> = {
    orange: {
        card: 'bg-gradient-to-br from-[var(--orange-soft)] to-[color-mix(in_srgb,var(--orange-soft)_50%,white)] ring-[var(--orange)]/25 dark:from-[var(--midnight-soft)]/70 dark:to-[var(--midnight-soft)]/40 dark:ring-white/10',
        ringHover: 'hover:ring-[var(--orange)]/60 hover:shadow-[var(--orange)]/20',
        iconChip: 'bg-gradient-to-br from-[var(--orange)]/25 to-[var(--orange)]/10 text-[var(--orange)] ring-[var(--orange)]/30',
        accentText: 'text-[var(--orange)]',
        createChip: 'bg-[var(--orange)] text-white hover:bg-[color-mix(in_srgb,var(--orange)_80%,black)]',
        manageText: 'text-[var(--orange)]',
    },
    yellow: {
        card: 'bg-gradient-to-br from-amber-50 to-yellow-100 ring-amber-400/30 dark:from-amber-950/50 dark:to-yellow-950/40 dark:ring-amber-400/30',
        ringHover: 'hover:ring-amber-500/60 hover:shadow-amber-500/20',
        iconChip: 'bg-gradient-to-br from-amber-400/25 to-yellow-500/10 text-amber-700 ring-amber-500/30 dark:text-amber-300',
        accentText: 'text-amber-700 dark:text-amber-300',
        createChip: 'bg-amber-500 text-white hover:bg-amber-600',
        manageText: 'text-amber-700 dark:text-amber-300',
    },
    emerald: {
        card: 'bg-gradient-to-br from-emerald-50 to-teal-50 ring-emerald-400/30 dark:from-emerald-950/50 dark:to-teal-950/40 dark:ring-emerald-400/30',
        ringHover: 'hover:ring-emerald-500/60 hover:shadow-emerald-500/20',
        iconChip: 'bg-gradient-to-br from-emerald-400/25 to-teal-500/10 text-emerald-700 ring-emerald-500/30 dark:text-emerald-300',
        accentText: 'text-emerald-700 dark:text-emerald-300',
        createChip: 'bg-emerald-500 text-white hover:bg-emerald-600',
        manageText: 'text-emerald-700 dark:text-emerald-300',
    },
    violet: {
        card: 'bg-gradient-to-br from-violet-50 to-indigo-50 ring-violet-400/30 dark:from-violet-950/50 dark:to-indigo-950/40 dark:ring-violet-400/30',
        ringHover: 'hover:ring-violet-500/60 hover:shadow-violet-500/20',
        iconChip: 'bg-gradient-to-br from-violet-400/25 to-indigo-500/10 text-violet-700 ring-violet-500/30 dark:text-violet-300',
        accentText: 'text-violet-700 dark:text-violet-300',
        createChip: 'bg-violet-500 text-white hover:bg-violet-600',
        manageText: 'text-violet-700 dark:text-violet-300',
    },
    midnight: {
        card: 'bg-gradient-to-br from-[color-mix(in_srgb,var(--midnight)_92%,var(--orange))] to-[var(--midnight)] ring-white/10 text-white',
        ringHover: 'hover:ring-white/25 hover:shadow-[var(--midnight)]/40',
        iconChip: 'bg-white/15 text-white ring-white/20',
        accentText: 'text-white/80',
        createChip: '',
        manageText: 'text-white',
    },
    linen: {
        card: 'bg-gradient-to-br from-[var(--paper)] to-[var(--linen)] ring-[var(--midnight)]/10 dark:from-[var(--midnight)]/70 dark:to-[var(--midnight-soft)]/60 dark:ring-white/10',
        ringHover: 'hover:ring-[var(--midnight)]/25 hover:shadow-slate-400/20',
        iconChip: 'bg-white/70 text-[var(--midnight)] ring-[var(--midnight)]/15 dark:bg-white/10 dark:text-[var(--linen)] dark:ring-white/15',
        accentText: 'text-[var(--midnight)] dark:text-[var(--linen)]',
        createChip: '',
        manageText: 'text-[var(--midnight)] dark:text-[var(--linen)]',
    },
};
const styleFor = (tone: QuickTile['tone']): ToneStyle => toneStyles[tone];
</script>

<template>
    <Head title="Dashboard" />

    <div class="flex flex-col gap-5 p-4 sm:p-6">
        <div
            class="relative z-30 overflow-hidden rounded-2xl border border-white/40 bg-gradient-to-br from-white/85 via-[var(--paper)]/70 to-[var(--orange-soft)]/40 p-5 shadow-lg backdrop-blur-xl sm:p-6 dark:border-white/5 dark:from-[var(--midnight)]/85 dark:via-[var(--midnight)]/75 dark:to-[var(--midnight-soft)]/60"
        >
            <div class="pointer-events-none absolute inset-0" aria-hidden="true">
                <div
                    class="absolute -top-20 -right-20 size-72 rounded-full bg-gradient-to-br from-[var(--orange)]/30 to-transparent blur-3xl"
                />
                <div
                    class="absolute -bottom-24 -left-16 size-64 rounded-full bg-gradient-to-tr from-[var(--midnight)]/15 to-transparent blur-3xl dark:from-[var(--orange)]/10"
                />
            </div>
            <div
                class="relative flex flex-col items-start justify-between gap-4 sm:flex-row sm:items-center"
            >
                <div class="flex items-center gap-4">
                    <div class="relative">
                        <span
                            aria-hidden="true"
                            class="absolute inset-0 rounded-full bg-gradient-to-br from-[var(--orange)] to-[color-mix(in_srgb,var(--orange)_55%,var(--midnight))] opacity-60 blur-md"
                        />
                        <div
                            class="relative flex size-14 items-center justify-center overflow-hidden rounded-full bg-[var(--midnight)] text-[var(--linen)] shadow-md ring-2 ring-[var(--orange)]/60"
                        >
                            <img
                                v-if="welcome.avatar_url"
                                :src="welcome.avatar_url"
                                :alt="welcome.name ?? ''"
                                class="size-full object-cover"
                            />
                            <Users v-else class="size-6" />
                        </div>
                    </div>
                    <div>
                        <p
                            class="flex items-center gap-1.5 text-[11px] font-bold uppercase tracking-widest text-[var(--orange)]"
                        >
                            <Zap class="size-3" />
                            {{ greeting }}
                        </p>
                        <h1
                            class="mt-0.5 bg-gradient-to-r from-[var(--midnight)] to-[color-mix(in_srgb,var(--midnight)_55%,var(--orange))] bg-clip-text text-2xl font-bold leading-tight tracking-tight text-transparent dark:from-[var(--linen)] dark:to-[color-mix(in_srgb,var(--linen)_75%,var(--orange))]"
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
                <DateRangeSelector :value="range" />
            </div>
            <div
                class="relative mt-5 overflow-hidden rounded-xl bg-gradient-to-br from-[var(--midnight)] via-[var(--midnight-soft)] to-[var(--midnight)] p-4 text-[var(--linen)] shadow-md"
            >
                <div
                    aria-hidden="true"
                    class="pointer-events-none absolute -top-16 -left-10 size-48 rounded-full bg-[var(--orange)]/25 blur-3xl"
                />
                <div
                    aria-hidden="true"
                    class="pointer-events-none absolute -bottom-16 -right-10 size-48 rounded-full bg-[var(--yellow-dark)]/15 blur-3xl"
                />
                <div
                    class="relative flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between"
                >
                    <div>
                        <div
                            class="inline-flex items-center gap-1.5 rounded-full bg-[var(--orange)]/15 px-2.5 py-0.5 text-[10px] font-semibold uppercase tracking-wider text-[var(--orange)] ring-1 ring-inset ring-[var(--orange)]/30"
                        >
                            <Sparkles class="size-3" />
                            {{ t('dashboard.hero_velocity_range', { range: rangeLabel }) }}
                        </div>
                        <div class="mt-2 flex items-baseline gap-2">
                            <span class="text-3xl font-bold leading-none tracking-tight">
                                {{ heroTotal.toLocaleString() }}
                            </span>
                            <span class="text-xs text-[var(--linen)]/60">
                                {{ t('dashboard.hero_items_created') }}
                            </span>
                            <span
                                v-if="heroDelta !== null && heroDelta >= 0"
                                class="ml-1 inline-flex items-center gap-1 rounded-md bg-emerald-500/15 px-1.5 py-0.5 text-[11px] font-semibold text-emerald-300 ring-1 ring-inset ring-emerald-500/30"
                            >
                                <TrendingUp class="size-3" />
                                +{{ heroDelta }}%
                            </span>
                            <span
                                v-else-if="heroDelta !== null"
                                class="ml-1 inline-flex items-center gap-1 rounded-md bg-rose-500/15 px-1.5 py-0.5 text-[11px] font-semibold text-rose-300 ring-1 ring-inset ring-rose-500/30"
                            >
                                <TrendingDown class="size-3" />
                                {{ heroDelta }}%
                            </span>
                        </div>
                        <p
                            v-if="heroDelta !== null"
                            class="mt-1 text-[10px] uppercase tracking-wider text-[var(--linen)]/40"
                        >
                            {{ t('dashboard.hero_vs_prior_half') }}
                        </p>
                    </div>
                    <div class="w-full max-w-[260px] shrink-0 sm:w-56">
                        <svg
                            viewBox="0 0 200 40"
                            class="h-10 w-full"
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
                            class="mt-0.5 text-right text-[9px] uppercase tracking-wider text-[var(--linen)]/40"
                        >
                            {{ t('dashboard.hero_trend') }}
                        </p>
                    </div>
                </div>
            </div>
            <div
                v-if="quickTiles.length > 0"
                class="relative mt-6 border-t border-white/40 pt-5 dark:border-white/10"
            >
                <header class="mb-3 flex items-center gap-2">
                    <span
                        class="flex size-6 items-center justify-center rounded-full bg-gradient-to-br from-[var(--orange)] to-[color-mix(in_srgb,var(--orange)_60%,var(--midnight))] text-white shadow-sm"
                    >
                        <Zap class="size-3" />
                    </span>
                    <h2 class="text-[11px] font-bold uppercase tracking-widest text-[var(--midnight)]/70 dark:text-[var(--linen)]/70">
                        {{ t('dashboard.quick_actions_title') }}
                    </h2>
                </header>
                <ul class="grid grid-cols-2 gap-3 sm:grid-cols-3 lg:grid-cols-6">
                    <li v-for="tile in quickTiles" :key="tile.key">
                        <div
                            class="group relative flex h-full flex-col overflow-hidden rounded-xl p-3 shadow-md ring-1 transition-all duration-300 hover:-translate-y-0.5"
                            :class="[styleFor(tile.tone).card, styleFor(tile.tone).ringHover]"
                        >
                            <span
                                aria-hidden="true"
                                class="pointer-events-none absolute inset-y-0 -left-1/2 w-1/3 skew-x-[-20deg] bg-gradient-to-r from-transparent via-white/40 to-transparent transition-transform duration-700 ease-out group-hover:translate-x-[500%]"
                            />
                            <div class="relative flex items-start justify-between gap-2">
                                <span
                                    class="flex size-8 shrink-0 items-center justify-center rounded-lg ring-1 backdrop-blur-sm"
                                    :class="styleFor(tile.tone).iconChip"
                                >
                                    <component :is="tile.icon" class="size-4" />
                                </span>
                                <Link
                                    v-if="tile.createHref"
                                    :href="tile.createHref"
                                    class="inline-flex size-6 shrink-0 items-center justify-center rounded-full text-white shadow-sm transition-all hover:scale-110"
                                    :class="styleFor(tile.tone).createChip"
                                    :title="tile.createLabel ?? undefined"
                                    :aria-label="tile.createLabel ?? undefined"
                                >
                                    <Plus class="size-3.5" />
                                </Link>
                            </div>
                            <div class="relative mt-2">
                                <p
                                    class="text-[10px] font-bold uppercase tracking-widest"
                                    :class="styleFor(tile.tone).accentText"
                                >
                                    {{ tile.label }}
                                </p>
                                <div
                                    v-if="tile.kpiCount !== null"
                                    class="mt-0.5 text-2xl font-bold leading-none tracking-tight text-[var(--midnight)] dark:text-[var(--linen)]"
                                    :class="tile.tone === 'midnight' ? '!text-white' : ''"
                                >
                                    {{ tile.kpiCount.toLocaleString() }}
                                </div>
                            </div>
                            <p
                                v-if="tile.kpiHint"
                                class="relative mt-1 line-clamp-2 text-[11px] text-muted-foreground"
                                :class="tile.tone === 'midnight' ? '!text-white/70' : ''"
                            >
                                {{ tile.kpiHint }}
                            </p>
                            <Link
                                :href="tile.manageHref"
                                class="relative mt-auto pt-3 inline-flex items-center gap-0.5 text-[11px] font-semibold transition-all hover:gap-1.5"
                                :class="styleFor(tile.tone).manageText"
                            >
                                {{ tile.manageLabel }}
                                <ArrowUpRight class="size-3" />
                            </Link>
                        </div>
                    </li>
                </ul>
            </div>
            <div
                v-if="companiesOverview"
                class="relative mt-6 border-t border-white/40 pt-5 dark:border-white/10"
            >
                <header class="mb-3 flex items-center justify-between gap-3">
                    <div class="flex items-center gap-2">
                        <span
                            class="flex size-6 items-center justify-center rounded-full bg-gradient-to-br from-[var(--midnight)] to-[color-mix(in_srgb,var(--midnight)_60%,var(--orange))] text-white shadow-sm"
                        >
                            <Building2 class="size-3" />
                        </span>
                        <h2 class="text-[11px] font-bold uppercase tracking-widest text-[var(--midnight)]/70 dark:text-[var(--linen)]/70">
                            {{ t('dashboard.companies_title') }}
                        </h2>
                    </div>
                    <Link
                        href="/admin/companies"
                        class="group inline-flex items-center gap-1 rounded-full border border-[var(--midnight)]/15 bg-white/60 px-3 py-1 text-[10px] font-semibold uppercase tracking-wider text-[var(--midnight)] backdrop-blur-sm transition-all hover:border-[var(--midnight)] hover:bg-[var(--midnight)] hover:text-white dark:border-white/20 dark:bg-white/10 dark:text-[var(--linen)] dark:hover:bg-white/25"
                    >
                        {{ t('dashboard.companies_manage') }}
                        <ArrowUpRight class="size-3 transition-transform group-hover:translate-x-0.5 group-hover:-translate-y-0.5" />
                    </Link>
                </header>

                <ul class="grid grid-cols-2 gap-2.5 sm:grid-cols-3 lg:grid-cols-6">
                    <li
                        class="group relative overflow-hidden rounded-xl bg-gradient-to-br from-[var(--orange)] to-[color-mix(in_srgb,var(--orange)_60%,var(--midnight))] p-3 text-white shadow-md shadow-[var(--orange)]/25 ring-1 ring-white/20 transition-all duration-300 hover:-translate-y-0.5 hover:shadow-lg hover:shadow-[var(--orange)]/40"
                    >
                        <span
                            aria-hidden="true"
                            class="pointer-events-none absolute inset-y-0 -left-1/2 w-1/3 skew-x-[-20deg] bg-gradient-to-r from-transparent via-white/40 to-transparent transition-transform duration-700 ease-out group-hover:translate-x-[500%]"
                        />
                        <div
                            aria-hidden="true"
                            class="pointer-events-none absolute -top-6 -right-6 size-20 rounded-full bg-white/15 blur-2xl"
                        />
                        <div class="relative flex items-center gap-1.5">
                            <span class="flex size-6 items-center justify-center rounded-md bg-white/20 ring-1 ring-white/30 backdrop-blur-sm">
                                <Building2 class="size-3" />
                            </span>
                            <span class="text-[10px] font-semibold uppercase tracking-wider">
                                {{ t('dashboard.companies_total_hint') }}
                            </span>
                        </div>
                        <div class="relative mt-1 text-3xl font-bold leading-none tracking-tight">
                            {{ companiesOverview.total.toLocaleString() }}
                        </div>
                        <p class="relative mt-1 text-[10px] text-white/70">
                            {{ t('dashboard.companies_subheading') }}
                        </p>
                    </li>
                    <li
                        class="group relative overflow-hidden rounded-xl bg-gradient-to-br from-emerald-50 to-sky-50 p-3 shadow-sm ring-1 ring-emerald-500/25 transition-all duration-300 hover:-translate-y-0.5 hover:shadow-md hover:shadow-emerald-500/20 dark:from-emerald-950/50 dark:to-sky-950/50 dark:ring-emerald-400/30"
                    >
                        <span
                            aria-hidden="true"
                            class="pointer-events-none absolute inset-y-0 -left-1/2 w-1/3 skew-x-[-20deg] bg-gradient-to-r from-transparent via-white/50 to-transparent transition-transform duration-700 ease-out group-hover:translate-x-[500%]"
                        />
                        <div class="relative flex items-center gap-1.5">
                            <span class="flex size-6 items-center justify-center rounded-md bg-emerald-500/15 text-emerald-700 ring-1 ring-emerald-500/30 dark:text-emerald-300">
                                <ShieldCheck class="size-3" />
                            </span>
                            <span class="text-[10px] font-semibold uppercase tracking-wider text-emerald-700 dark:text-emerald-300">
                                {{ t('dashboard.companies_unclaimed') }}
                            </span>
                        </div>
                        <div class="relative mt-1 text-2xl font-bold text-[var(--midnight)] dark:text-[var(--linen)]">
                            {{ companiesOverview.unclaimed.toLocaleString() }}
                        </div>
                    </li>
                    <li
                        class="group relative overflow-hidden rounded-xl bg-gradient-to-br from-amber-50 to-rose-50 p-3 shadow-sm ring-1 ring-amber-500/30 transition-all duration-300 hover:-translate-y-0.5 hover:shadow-md hover:shadow-amber-500/20 dark:from-amber-950/50 dark:to-rose-950/40 dark:ring-amber-400/40"
                    >
                        <span
                            aria-hidden="true"
                            class="pointer-events-none absolute inset-y-0 -left-1/2 w-1/3 skew-x-[-20deg] bg-gradient-to-r from-transparent via-white/50 to-transparent transition-transform duration-700 ease-out group-hover:translate-x-[500%]"
                        />
                        <div class="relative flex items-center gap-1.5">
                            <span class="flex size-6 items-center justify-center rounded-md bg-amber-500/15 text-amber-700 ring-1 ring-amber-500/40 dark:text-amber-300">
                                <AlertTriangle class="size-3" />
                            </span>
                            <span class="text-[10px] font-semibold uppercase tracking-wider text-amber-800 dark:text-amber-300">
                                {{ t('dashboard.companies_pending') }}
                            </span>
                        </div>
                        <div class="relative mt-1 flex items-baseline gap-1.5">
                            <span class="text-2xl font-bold text-[var(--midnight)] dark:text-[var(--linen)]">
                                {{ companiesOverview.pending_applications.toLocaleString() }}
                            </span>
                            <span
                                v-if="companiesOverview.pending_applications > 0"
                                aria-hidden="true"
                                class="relative flex size-2"
                            >
                                <span class="absolute inline-flex size-full animate-ping rounded-full bg-amber-500 opacity-70" />
                                <span class="relative inline-flex size-2 rounded-full bg-amber-500" />
                            </span>
                        </div>
                    </li>
                    <li
                        class="group relative overflow-hidden rounded-xl bg-gradient-to-br from-slate-50 to-slate-100 p-3 shadow-sm ring-1 ring-slate-300 transition-all duration-300 hover:-translate-y-0.5 hover:shadow-md hover:shadow-slate-400/20 dark:from-slate-800/60 dark:to-slate-900/60 dark:ring-slate-600/50"
                    >
                        <span
                            aria-hidden="true"
                            class="pointer-events-none absolute inset-y-0 -left-1/2 w-1/3 skew-x-[-20deg] bg-gradient-to-r from-transparent via-white/50 to-transparent transition-transform duration-700 ease-out group-hover:translate-x-[500%]"
                        />
                        <div class="relative flex items-center gap-1.5">
                            <span class="flex size-6 items-center justify-center rounded-md bg-slate-500/15 text-slate-700 ring-1 ring-slate-400/40 dark:text-slate-300">
                                <Building2 class="size-3" />
                            </span>
                            <span class="text-[10px] font-semibold uppercase tracking-wider text-slate-700 dark:text-slate-300">
                                {{ t('dashboard.companies_basic') }}
                            </span>
                        </div>
                        <div class="relative mt-1 text-2xl font-bold text-[var(--midnight)] dark:text-[var(--linen)]">
                            {{ companiesOverview.plan_basic.toLocaleString() }}
                        </div>
                    </li>
                    <li
                        class="group relative overflow-hidden rounded-xl bg-gradient-to-br from-violet-50 to-indigo-100 p-3 shadow-sm ring-1 ring-violet-400/40 transition-all duration-300 hover:-translate-y-0.5 hover:shadow-md hover:shadow-violet-500/25 dark:from-violet-950/60 dark:to-indigo-950/60 dark:ring-violet-400/40"
                    >
                        <span
                            aria-hidden="true"
                            class="pointer-events-none absolute inset-y-0 -left-1/2 w-1/3 skew-x-[-20deg] bg-gradient-to-r from-transparent via-white/50 to-transparent transition-transform duration-700 ease-out group-hover:translate-x-[500%]"
                        />
                        <div class="relative flex items-center gap-1.5">
                            <span class="flex size-6 items-center justify-center rounded-md bg-violet-500/20 text-violet-700 ring-1 ring-violet-400/40 dark:text-violet-300">
                                <Sparkles class="size-3" />
                            </span>
                            <span class="text-[10px] font-semibold uppercase tracking-wider text-violet-700 dark:text-violet-300">
                                {{ t('dashboard.companies_premium') }}
                            </span>
                        </div>
                        <div class="relative mt-1 text-2xl font-bold text-[var(--midnight)] dark:text-[var(--linen)]">
                            {{ companiesOverview.plan_premium.toLocaleString() }}
                        </div>
                    </li>
                    <li
                        class="group relative overflow-hidden rounded-xl bg-gradient-to-br from-amber-200 via-yellow-200 to-amber-300 p-3 shadow-md shadow-amber-500/30 ring-1 ring-amber-400/60 transition-all duration-300 hover:-translate-y-0.5 hover:shadow-lg hover:shadow-amber-500/40 dark:from-amber-900/60 dark:via-yellow-900/50 dark:to-amber-800/60 dark:ring-amber-400/50"
                    >
                        <span
                            aria-hidden="true"
                            class="pointer-events-none absolute inset-y-0 -left-1/2 w-1/3 skew-x-[-20deg] bg-gradient-to-r from-transparent via-white/50 to-transparent transition-transform duration-700 ease-out group-hover:translate-x-[500%]"
                        />
                        <div class="relative flex items-center gap-1.5">
                            <span class="flex size-6 items-center justify-center rounded-md bg-white/40 text-amber-800 ring-1 ring-amber-500/50 backdrop-blur-sm dark:bg-white/10 dark:text-amber-200">
                                <Crown class="size-3" />
                            </span>
                            <span class="text-[10px] font-semibold uppercase tracking-wider text-amber-800 dark:text-amber-200">
                                {{ t('dashboard.companies_gold') }}
                            </span>
                        </div>
                        <div class="relative mt-1 text-2xl font-bold text-[var(--midnight)] dark:text-[var(--linen)]">
                            {{ companiesOverview.plan_gold.toLocaleString() }}
                        </div>
                    </li>
                </ul>
            </div>
        </div>
        <div class="grid gap-4 lg:grid-cols-2">
            <Card class="group relative overflow-hidden border-white/40 bg-gradient-to-br from-[var(--orange-soft)]/70 via-white/80 to-[var(--orange-soft)]/40 shadow-md shadow-[var(--orange)]/10 backdrop-blur-xl dark:border-white/5 dark:from-[var(--midnight)]/85 dark:via-[var(--midnight)]/70 dark:to-[color-mix(in_srgb,var(--midnight)_70%,var(--orange))]">
                <span
                    aria-hidden="true"
                    class="pointer-events-none absolute inset-y-0 -left-1/2 z-10 w-1/3 skew-x-[-20deg] bg-gradient-to-r from-transparent via-white/40 to-transparent transition-transform duration-700 ease-out group-hover:translate-x-[500%]"
                />
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

            <Card class="group relative overflow-hidden border-white/40 bg-gradient-to-br from-amber-50/80 via-white/80 to-yellow-50/60 shadow-md shadow-amber-500/10 backdrop-blur-xl dark:border-white/5 dark:from-amber-950/40 dark:via-[var(--midnight)]/70 dark:to-yellow-950/40">
                <span
                    aria-hidden="true"
                    class="pointer-events-none absolute inset-y-0 -left-1/2 z-10 w-1/3 skew-x-[-20deg] bg-gradient-to-r from-transparent via-white/40 to-transparent transition-transform duration-700 ease-out group-hover:translate-x-[500%]"
                />
                <CardHeader>
                    <CardTitle class="flex items-center gap-2 text-base">
                        <div
                            class="flex size-7 items-center justify-center rounded-md bg-gradient-to-br from-amber-500 to-yellow-500 text-white shadow-md shadow-amber-500/40"
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
            <Card class="group relative overflow-hidden border-white/40 bg-gradient-to-br from-sky-50/80 via-white/80 to-cyan-50/60 shadow-md shadow-sky-500/10 backdrop-blur-xl dark:border-white/5 dark:from-sky-950/40 dark:via-[var(--midnight)]/70 dark:to-cyan-950/40">
                <span
                    aria-hidden="true"
                    class="pointer-events-none absolute inset-y-0 -left-1/2 z-10 w-1/3 skew-x-[-20deg] bg-gradient-to-r from-transparent via-white/40 to-transparent transition-transform duration-700 ease-out group-hover:translate-x-[500%]"
                />
                <CardHeader>
                    <CardTitle class="flex items-center gap-2 text-base">
                        <div
                            class="flex size-7 items-center justify-center rounded-md bg-gradient-to-br from-sky-500 to-cyan-500 text-white shadow-md shadow-sky-500/40"
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

            <Card class="group relative overflow-hidden border-white/40 bg-gradient-to-br from-emerald-50/80 via-white/80 to-teal-50/60 shadow-md shadow-emerald-500/10 backdrop-blur-xl dark:border-white/5 dark:from-emerald-950/40 dark:via-[var(--midnight)]/70 dark:to-teal-950/40">
                <span
                    aria-hidden="true"
                    class="pointer-events-none absolute inset-y-0 -left-1/2 z-10 w-1/3 skew-x-[-20deg] bg-gradient-to-r from-transparent via-white/40 to-transparent transition-transform duration-700 ease-out group-hover:translate-x-[500%]"
                />
                <CardHeader>
                    <CardTitle class="flex items-center gap-2 text-base">
                        <div
                            class="flex size-7 items-center justify-center rounded-md bg-gradient-to-br from-emerald-500 to-teal-500 text-white shadow-md shadow-emerald-500/40"
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

            <Card class="group relative overflow-hidden border-white/40 bg-gradient-to-br from-rose-50/80 via-white/80 to-orange-50/60 shadow-md shadow-rose-500/10 backdrop-blur-xl dark:border-white/5 dark:from-rose-950/40 dark:via-[var(--midnight)]/70 dark:to-orange-950/40">
                <span
                    aria-hidden="true"
                    class="pointer-events-none absolute inset-y-0 -left-1/2 z-10 w-1/3 skew-x-[-20deg] bg-gradient-to-r from-transparent via-white/40 to-transparent transition-transform duration-700 ease-out group-hover:translate-x-[500%]"
                />
                <CardHeader>
                    <CardTitle class="flex items-center gap-2 text-base">
                        <div
                            class="flex size-7 items-center justify-center rounded-md bg-gradient-to-br from-rose-500 to-orange-500 text-white shadow-md shadow-rose-500/40"
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
