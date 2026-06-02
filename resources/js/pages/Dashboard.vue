<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import {
    AlertTriangle,
    Eye,
    FileEdit,
    Files,
    HardDrive,
    Image as ImageIcon,
    Languages as LanguagesIcon,
    Newspaper,
    Plus,
    TrendingUp,
    UserPlus,
    Users,
} from 'lucide-vue-next';
import { computed } from 'vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { dashboard } from '@/routes';

type StatusPill = 'published' | 'draft' | 'inactive';

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
        new_this_week: number;
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

type RecentPage = {
    id: number;
    title: string;
    permalink: string;
    status: StatusPill;
    author: string | null;
    created_at: string | null;
};

type RecentPost = {
    id: number;
    title: string;
    permalink: string;
    status: StatusPill;
    views: number;
    created_at: string | null;
};

type RecentUser = {
    id: number;
    name: string;
    email: string;
    role: string | null;
    created_at: string | null;
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

const props = defineProps<{
    welcome: Welcome;
    kpis: Kpis;
    chart: Chart;
    topPosts: TopPost[];
    recentPages: RecentPage[];
    recentPosts: RecentPost[];
    recentUsers?: RecentUser[];
    languageCoverage: LangCoverage[];
    topCategories: TopCategory[];
    attention: Attention;
    permissions: Permissions;
}>();

defineOptions({
    layout: {
        breadcrumbs: [{ title: 'Dashboard', href: dashboard() }],
    },
});

function formatDate(iso: string | null): string {
    if (!iso) return '—';
    return new Date(iso).toLocaleDateString(undefined, {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
    });
}

function relative(iso: string | null): string {
    if (!iso) return '—';
    const diffMs = Date.now() - new Date(iso).getTime();
    const days = Math.floor(diffMs / (1000 * 60 * 60 * 24));
    if (days < 1) return 'today';
    if (days === 1) return 'yesterday';
    if (days < 7) return `${days}d ago`;
    if (days < 30) return `${Math.floor(days / 7)}w ago`;
    if (days < 365) return `${Math.floor(days / 30)}mo ago`;
    return `${Math.floor(days / 365)}y ago`;
}

function formatBytes(bytes: number): string {
    if (bytes < 1024) return `${bytes} B`;
    if (bytes < 1024 * 1024) return `${(bytes / 1024).toFixed(1)} KB`;
    if (bytes < 1024 * 1024 * 1024)
        return `${(bytes / (1024 * 1024)).toFixed(1)} MB`;
    return `${(bytes / (1024 * 1024 * 1024)).toFixed(2)} GB`;
}

function statusVariant(s: StatusPill): 'default' | 'outline' | 'destructive' {
    return s === 'published'
        ? 'default'
        : s === 'draft'
          ? 'outline'
          : 'destructive';
}

// Chart layout — single SVG with stacked bars. We compute the max once so the
// page bars and post bars share a Y scale.
const chartMax = computed(() =>
    Math.max(
        1,
        ...props.chart.weeks.map(
            (_, i) =>
                (props.chart.pages[i] ?? 0) + (props.chart.posts[i] ?? 0),
        ),
    ),
);

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
            label: 'Page drafts',
            count: props.attention.page_drafts,
            href: '/admin/pages?status=draft',
        },
        {
            key: 'post_drafts',
            label: 'Blog drafts',
            count: props.attention.post_drafts,
            href: '/admin/blog/posts?status=draft',
        },
        {
            key: 'unverified_users',
            label: 'Unverified users (last 30d)',
            count: props.attention.unverified_users,
            href: props.permissions.users_view ? '/admin/users' : null,
        },
        {
            key: 'missing_translations',
            label: 'Items missing translations',
            count: props.attention.missing_translations,
            href: '/admin/pages?lang=de',
        },
    ].filter((row) => row.count > 0),
);

const totalContentInChart = computed(
    () =>
        props.chart.pages.reduce((a, b) => a + b, 0) +
        props.chart.posts.reduce((a, b) => a + b, 0),
);
</script>

<template>
    <Head title="Dashboard" />

    <div class="flex flex-col gap-4 p-4">
        <!-- Welcome strip -->
        <Card>
            <CardContent class="flex flex-col items-start justify-between gap-4 p-4 sm:flex-row sm:items-center">
                <div class="flex items-center gap-3">
                    <div class="flex size-12 items-center justify-center overflow-hidden rounded-full bg-muted">
                        <img
                            v-if="welcome.avatar_url"
                            :src="welcome.avatar_url"
                            :alt="welcome.name ?? ''"
                            class="size-full object-cover"
                        />
                        <Users v-else class="size-5 text-muted-foreground" />
                    </div>
                    <div>
                        <h1 class="text-xl font-semibold">
                            Hi, {{ welcome.name ?? 'there' }}
                        </h1>
                        <p class="text-xs text-muted-foreground">
                            <span v-if="welcome.role_display_name">
                                Logged in as
                                <span class="font-medium text-foreground">{{ welcome.role_display_name }}</span>
                            </span>
                            <span v-if="welcome.member_since">
                                · since {{ formatDate(welcome.member_since) }}
                            </span>
                        </p>
                    </div>
                </div>
                <div class="flex flex-wrap items-center gap-2">
                    <Button v-if="permissions.pages_create" as-child size="sm">
                        <Link href="/admin/pages/create">
                            <Plus class="size-4" />
                            New page
                        </Link>
                    </Button>
                    <Button v-if="permissions.posts_create" as-child size="sm" variant="secondary">
                        <Link href="/admin/blog/posts/create">
                            <Plus class="size-4" />
                            New blog
                        </Link>
                    </Button>
                    <Button v-if="permissions.users_create" as-child size="sm" variant="outline">
                        <Link href="/admin/users?new=1">
                            <UserPlus class="size-4" />
                            New user
                        </Link>
                    </Button>
                </div>
            </CardContent>
        </Card>

        <!-- KPI tiles -->
        <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
            <Card>
                <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                    <CardTitle class="text-sm font-medium text-muted-foreground">
                        Pages
                    </CardTitle>
                    <Files class="size-4 text-muted-foreground" />
                </CardHeader>
                <CardContent>
                    <div class="text-3xl font-semibold">{{ kpis.pages.total }}</div>
                    <div class="mt-2 flex flex-wrap gap-1">
                        <Badge variant="default">{{ kpis.pages.published }} published</Badge>
                        <Badge variant="outline">{{ kpis.pages.draft }} draft</Badge>
                        <Badge v-if="kpis.pages.inactive > 0" variant="destructive">
                            {{ kpis.pages.inactive }} inactive
                        </Badge>
                    </div>
                    <Link href="/admin/pages" class="mt-3 inline-block text-xs text-muted-foreground hover:underline">
                        Manage pages →
                    </Link>
                </CardContent>
            </Card>

            <Card>
                <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                    <CardTitle class="text-sm font-medium text-muted-foreground">
                        Blogs
                    </CardTitle>
                    <Newspaper class="size-4 text-muted-foreground" />
                </CardHeader>
                <CardContent>
                    <div class="text-3xl font-semibold">{{ kpis.posts.total }}</div>
                    <div class="mt-2 flex flex-wrap gap-1">
                        <Badge variant="default">{{ kpis.posts.published }} published</Badge>
                        <Badge variant="outline">{{ kpis.posts.draft }} draft</Badge>
                    </div>
                    <p class="mt-2 flex items-center gap-1 text-xs text-muted-foreground">
                        <Eye class="size-3" />
                        {{ kpis.posts.views.toLocaleString() }} total views
                    </p>
                    <Link href="/admin/blog/posts" class="mt-1 inline-block text-xs text-muted-foreground hover:underline">
                        Manage blogs →
                    </Link>
                </CardContent>
            </Card>

            <Card v-if="permissions.users_view">
                <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                    <CardTitle class="text-sm font-medium text-muted-foreground">
                        Users
                    </CardTitle>
                    <Users class="size-4 text-muted-foreground" />
                </CardHeader>
                <CardContent>
                    <div class="text-3xl font-semibold">{{ kpis.users.total }}</div>
                    <div class="mt-2 flex flex-wrap gap-1">
                        <Badge variant="default">{{ kpis.users.verified }} verified</Badge>
                        <Badge v-if="kpis.users.unverified > 0" variant="outline">
                            {{ kpis.users.unverified }} unverified
                        </Badge>
                    </div>
                    <p class="mt-2 text-xs text-muted-foreground">
                        +{{ kpis.users.new_this_week }} this week
                    </p>
                    <Link href="/admin/users" class="mt-1 inline-block text-xs text-muted-foreground hover:underline">
                        Manage users →
                    </Link>
                </CardContent>
            </Card>

            <Card>
                <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
                    <CardTitle class="text-sm font-medium text-muted-foreground">
                        Media
                    </CardTitle>
                    <HardDrive class="size-4 text-muted-foreground" />
                </CardHeader>
                <CardContent>
                    <div class="text-3xl font-semibold">
                        {{ kpis.media.files.toLocaleString() }}
                    </div>
                    <p class="mt-2 text-xs text-muted-foreground">
                        {{ formatBytes(kpis.media.bytes) }} used ·
                        {{ kpis.media.folders }} folders
                    </p>
                    <Link href="/admin/media" class="mt-3 inline-block text-xs text-muted-foreground hover:underline">
                        Open library →
                    </Link>
                </CardContent>
            </Card>
        </div>

        <!-- Content chart + Top blogs -->
        <div class="grid gap-4 lg:grid-cols-2">
            <Card>
                <CardHeader>
                    <div class="flex items-center justify-between">
                        <CardTitle class="text-base">Content created (last 12 weeks)</CardTitle>
                        <TrendingUp class="size-4 text-muted-foreground" />
                    </div>
                    <p class="text-xs text-muted-foreground">
                        {{ totalContentInChart }} items · pages + blogs stacked
                    </p>
                </CardHeader>
                <CardContent>
                    <div v-if="totalContentInChart === 0" class="flex h-40 items-center justify-center text-sm text-muted-foreground">
                        No content created in the last 12 weeks.
                    </div>
                    <div v-else>
                        <!-- items-stretch (default) makes each column take the
                             full 160px height; flex-col justify-end then pushes
                             the percentage-height bars to the bottom of that
                             column. items-end on the parent would collapse the
                             columns to 0px and the percentage children render
                             blank. -->
                        <div class="flex h-40 gap-1.5">
                            <div
                                v-for="(label, i) in chart.weeks"
                                :key="i"
                                class="flex flex-1 flex-col justify-end gap-px"
                                :title="`${label}: ${chart.pages[i] ?? 0} pages, ${chart.posts[i] ?? 0} blogs`"
                            >
                                <div
                                    v-if="(chart.posts[i] ?? 0) > 0"
                                    class="rounded-t-sm bg-emerald-500/80"
                                    :style="{ height: `${postsHeight(i)}%` }"
                                />
                                <div
                                    v-if="(chart.pages[i] ?? 0) > 0"
                                    class="bg-blue-500/80"
                                    :class="
                                        (chart.posts[i] ?? 0) === 0
                                            ? 'rounded-t-sm'
                                            : ''
                                    "
                                    :style="{ height: `${pagesHeight(i)}%` }"
                                />
                            </div>
                        </div>
                        <div class="mt-2 flex items-center justify-between text-[10px] text-muted-foreground">
                            <span>{{ chart.weeks[0] }}</span>
                            <span>{{ chart.weeks[chart.weeks.length - 1] }}</span>
                        </div>
                        <div class="mt-3 flex items-center gap-4 text-xs">
                            <span class="flex items-center gap-1.5">
                                <span class="size-2.5 rounded-sm bg-blue-500/80" />
                                Pages
                            </span>
                            <span class="flex items-center gap-1.5">
                                <span class="size-2.5 rounded-sm bg-emerald-500/80" />
                                Blogs
                            </span>
                        </div>
                    </div>
                </CardContent>
            </Card>

            <Card>
                <CardHeader>
                    <CardTitle class="text-base">Top viewed blogs</CardTitle>
                    <p class="text-xs text-muted-foreground">
                        Most-read articles across all time.
                    </p>
                </CardHeader>
                <CardContent class="px-0">
                    <div v-if="topPosts.length === 0" class="px-6 py-8 text-center text-sm text-muted-foreground">
                        No blogs yet.
                    </div>
                    <table v-else class="w-full text-sm">
                        <tbody>
                            <tr
                                v-for="(p, i) in topPosts"
                                :key="p.id"
                                class="border-t border-border/60"
                            >
                                <td class="px-4 py-2 w-10 text-xs text-muted-foreground">
                                    {{ i + 1 }}
                                </td>
                                <td class="px-4 py-2">
                                    <Link
                                        :href="`/admin/blog/posts/${p.id}/edit`"
                                        class="font-medium hover:underline"
                                    >
                                        {{ p.title }}
                                    </Link>
                                    <p class="text-[10px] text-muted-foreground">
                                        by {{ p.author ?? '—' }}
                                    </p>
                                </td>
                                <td class="px-4 py-2 text-right text-xs">
                                    <span class="inline-flex items-center gap-1 text-muted-foreground">
                                        <Eye class="size-3" />
                                        {{ p.views.toLocaleString() }}
                                    </span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </CardContent>
            </Card>
        </div>

        <!-- Recent activity -->
        <div class="grid gap-4 lg:grid-cols-2">
            <Card>
                <CardHeader>
                    <CardTitle class="text-base">Recent pages</CardTitle>
                </CardHeader>
                <CardContent class="px-0">
                    <div v-if="recentPages.length === 0" class="px-6 py-8 text-center text-sm text-muted-foreground">
                        No pages yet.
                    </div>
                    <table v-else class="w-full text-sm">
                        <tbody>
                            <tr v-for="p in recentPages" :key="p.id" class="border-t border-border/60">
                                <td class="px-4 py-2">
                                    <Link :href="`/admin/pages/${p.id}/edit`" class="font-medium hover:underline">
                                        {{ p.title }}
                                    </Link>
                                    <p class="text-[10px] text-muted-foreground">
                                        by {{ p.author ?? '—' }} · {{ relative(p.created_at) }}
                                    </p>
                                </td>
                                <td class="px-4 py-2 text-right">
                                    <Badge :variant="statusVariant(p.status)" class="capitalize text-[10px]">
                                        {{ p.status }}
                                    </Badge>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <div class="border-t border-border/60 px-4 py-2 text-right">
                        <Link href="/admin/pages" class="text-xs text-muted-foreground hover:underline">
                            See all pages →
                        </Link>
                    </div>
                </CardContent>
            </Card>

            <Card>
                <CardHeader>
                    <CardTitle class="text-base">Recent blogs</CardTitle>
                </CardHeader>
                <CardContent class="px-0">
                    <div v-if="recentPosts.length === 0" class="px-6 py-8 text-center text-sm text-muted-foreground">
                        No blogs yet.
                    </div>
                    <table v-else class="w-full text-sm">
                        <tbody>
                            <tr v-for="p in recentPosts" :key="p.id" class="border-t border-border/60">
                                <td class="px-4 py-2">
                                    <Link :href="`/admin/blog/posts/${p.id}/edit`" class="font-medium hover:underline">
                                        {{ p.title }}
                                    </Link>
                                    <p class="flex items-center gap-1 text-[10px] text-muted-foreground">
                                        <Eye class="size-3" />
                                        {{ p.views.toLocaleString() }} · {{ relative(p.created_at) }}
                                    </p>
                                </td>
                                <td class="px-4 py-2 text-right">
                                    <Badge :variant="statusVariant(p.status)" class="capitalize text-[10px]">
                                        {{ p.status }}
                                    </Badge>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <div class="border-t border-border/60 px-4 py-2 text-right">
                        <Link href="/admin/blog/posts" class="text-xs text-muted-foreground hover:underline">
                            See all blogs →
                        </Link>
                    </div>
                </CardContent>
            </Card>

            <Card v-if="permissions.users_view && recentUsers">
                <CardHeader>
                    <CardTitle class="text-base">Recent users</CardTitle>
                </CardHeader>
                <CardContent class="px-0">
                    <div v-if="recentUsers.length === 0" class="px-6 py-8 text-center text-sm text-muted-foreground">
                        No users yet.
                    </div>
                    <table v-else class="w-full text-sm">
                        <tbody>
                            <tr v-for="u in recentUsers" :key="u.id" class="border-t border-border/60">
                                <td class="px-4 py-2">
                                    <div class="font-medium">{{ u.name }}</div>
                                    <p class="text-[10px] text-muted-foreground">
                                        {{ u.email }} · {{ relative(u.created_at) }}
                                    </p>
                                </td>
                                <td class="px-4 py-2 text-right">
                                    <Badge v-if="u.role" variant="secondary" class="text-[10px]">
                                        {{ u.role }}
                                    </Badge>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <div class="border-t border-border/60 px-4 py-2 text-right">
                        <Link href="/admin/users" class="text-xs text-muted-foreground hover:underline">
                            See all users →
                        </Link>
                    </div>
                </CardContent>
            </Card>
        </div>

        <!-- System overview -->
        <div class="grid gap-4 lg:grid-cols-3">
            <Card>
                <CardHeader>
                    <div class="flex items-center justify-between">
                        <CardTitle class="text-base">Languages</CardTitle>
                        <LanguagesIcon class="size-4 text-muted-foreground" />
                    </div>
                    <p class="text-xs text-muted-foreground">
                        Translation coverage across pages and posts.
                    </p>
                </CardHeader>
                <CardContent class="space-y-3">
                    <div v-if="languageCoverage.length === 0" class="text-sm text-muted-foreground">
                        No active languages.
                    </div>
                    <div
                        v-for="lang in languageCoverage"
                        :key="lang.code"
                        class="flex items-center gap-3"
                    >
                        <span class="text-lg leading-none">{{ lang.flag ?? '🏳️' }}</span>
                        <div class="flex-1">
                            <div class="flex items-center justify-between text-sm">
                                <span class="font-medium">
                                    {{ lang.code.toUpperCase() }} · {{ lang.native_name }}
                                    <Badge v-if="lang.is_default" variant="secondary" class="ml-1 text-[10px]">
                                        default
                                    </Badge>
                                </span>
                                <span class="text-xs text-muted-foreground">
                                    {{ Math.round((lang.page_coverage + lang.post_coverage) / 2) }}%
                                </span>
                            </div>
                            <div class="mt-1 grid grid-cols-2 gap-2 text-[10px] text-muted-foreground">
                                <div>
                                    <div class="flex justify-between">
                                        <span>Pages</span>
                                        <span>{{ lang.page_coverage }}%</span>
                                    </div>
                                    <div class="mt-0.5 h-1.5 rounded-full bg-muted">
                                        <div
                                            class="h-full rounded-full bg-blue-500/80"
                                            :style="{ width: `${lang.page_coverage}%` }"
                                        />
                                    </div>
                                </div>
                                <div>
                                    <div class="flex justify-between">
                                        <span>Blogs</span>
                                        <span>{{ lang.post_coverage }}%</span>
                                    </div>
                                    <div class="mt-0.5 h-1.5 rounded-full bg-muted">
                                        <div
                                            class="h-full rounded-full bg-emerald-500/80"
                                            :style="{ width: `${lang.post_coverage}%` }"
                                        />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </CardContent>
            </Card>

            <Card>
                <CardHeader>
                    <div class="flex items-center justify-between">
                        <CardTitle class="text-base">Top page categories</CardTitle>
                        <ImageIcon class="size-4 text-muted-foreground" />
                    </div>
                </CardHeader>
                <CardContent class="px-0">
                    <div v-if="topCategories.length === 0" class="px-6 py-8 text-center text-sm text-muted-foreground">
                        No categories yet.
                    </div>
                    <table v-else class="w-full text-sm">
                        <tbody>
                            <tr v-for="c in topCategories" :key="c.id" class="border-t border-border/60">
                                <td class="px-4 py-2">
                                    <Link
                                        :href="`/admin/pages/categories/${c.id}/edit`"
                                        class="font-medium hover:underline"
                                    >
                                        {{ c.name }}
                                    </Link>
                                </td>
                                <td class="px-4 py-2 text-right text-xs text-muted-foreground">
                                    {{ c.page_count }} page{{ c.page_count === 1 ? '' : 's' }}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <div class="border-t border-border/60 px-4 py-2 text-right">
                        <Link href="/admin/pages/categories" class="text-xs text-muted-foreground hover:underline">
                            Manage categories →
                        </Link>
                    </div>
                </CardContent>
            </Card>

            <Card>
                <CardHeader>
                    <div class="flex items-center justify-between">
                        <CardTitle class="text-base">Needs your attention</CardTitle>
                        <AlertTriangle class="size-4 text-amber-500" />
                    </div>
                </CardHeader>
                <CardContent>
                    <div v-if="attentionItems.length === 0" class="flex flex-col items-center justify-center gap-2 py-8 text-center text-sm text-muted-foreground">
                        <FileEdit class="size-5" />
                        All caught up — nothing pending.
                    </div>
                    <ul v-else class="space-y-1">
                        <li
                            v-for="row in attentionItems"
                            :key="row.key"
                            class="flex items-center justify-between gap-2 rounded-md border border-border/60 px-3 py-2 text-sm"
                        >
                            <span class="flex items-center gap-2">
                                <span class="inline-flex size-6 items-center justify-center rounded-full bg-amber-100 text-xs font-semibold text-amber-700 dark:bg-amber-900/40 dark:text-amber-300">
                                    {{ row.count }}
                                </span>
                                {{ row.label }}
                            </span>
                            <Link
                                v-if="row.href"
                                :href="row.href"
                                class="text-xs text-muted-foreground hover:underline"
                            >
                                review →
                            </Link>
                        </li>
                    </ul>
                </CardContent>
            </Card>
        </div>
    </div>
</template>
