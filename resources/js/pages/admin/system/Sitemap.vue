<script setup lang="ts">
import { Head, router, usePage } from '@inertiajs/vue3';
import {
    CheckCircle2,
    Copy,
    Download,
    ExternalLink,
    Globe,
    ListTree as SitemapIcon,
    Loader2,
    RefreshCw,
} from 'lucide-vue-next';
import { computed, ref } from 'vue';
import Heading from '@/components/Heading.vue';
import { Button } from '@/components/ui/button';
import {
    Card,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';

type SitemapCounts = {
    home: number;
    pages: number;
    blog_posts: number;
    blog_index: number;
    locales: number;
};

const props = defineProps<{
    sitemap: { url: string; counts: SitemapCounts };
}>();

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Dashboard', href: '/dashboard' },
            { title: 'System', href: '/admin/system/cache' },
            { title: 'Sitemap', href: '/admin/system/sitemap' },
        ],
    },
});

const page = usePage();
const flash = computed<{ type?: string; message?: string } | null>(() => {
    const t = (page.props as Record<string, unknown>).toast;
    return (t ?? null) as { type?: string; message?: string } | null;
});

const flushing = ref(false);
function flushSitemap(): void {
    if (flushing.value) return;
    flushing.value = true;
    router.post(
        '/admin/system/sitemap/flush',
        {},
        {
            preserveScroll: true,
            preserveState: true,
            onFinish: () => {
                flushing.value = false;
            },
        },
    );
}

const copied = ref(false);
function copyUrl(): void {
    navigator.clipboard.writeText(props.sitemap.url).then(() => {
        copied.value = true;
        window.setTimeout(() => (copied.value = false), 1500);
    });
}

const totalUrls = computed<number>(() => {
    const c = props.sitemap.counts;
    return c.home + c.pages + c.blog_posts + c.blog_index;
});

const stats = computed(() => [
    { label: 'Total URLs', value: totalUrls.value, tone: 'primary' as const },
    { label: 'Home', value: props.sitemap.counts.home },
    { label: 'Blog index', value: props.sitemap.counts.blog_index },
    { label: 'Pages', value: props.sitemap.counts.pages },
    { label: 'Blog posts', value: props.sitemap.counts.blog_posts },
]);
</script>

<template>
    <Head title="Sitemap" />

    <div class="flex flex-col gap-6 p-4">
        <Heading
            title="Sitemap"
            description="Dynamic sitemap.xml — generated from the database on demand and cached for 30 minutes. Submit this URL to Google Search Console / Bing Webmaster Tools."
        />

        <div
            v-if="flash?.message"
            class="rounded-md border px-4 py-3 text-sm"
            :class="flash.type === 'error'
                ? 'border-rose-200 bg-rose-50 text-rose-700 dark:border-rose-900/60 dark:bg-rose-950/30 dark:text-rose-200'
                : 'border-emerald-200 bg-emerald-50 text-emerald-700 dark:border-emerald-900/60 dark:bg-emerald-950/30 dark:text-emerald-200'"
        >
            {{ flash.message }}
        </div>

        <Card>
            <CardHeader class="flex flex-row items-start justify-between gap-4">
                <div class="space-y-1">
                    <CardTitle class="flex items-center gap-2">
                        <SitemapIcon class="size-5 text-primary" />
                        sitemap.xml
                    </CardTitle>
                    <CardDescription>
                        Lists the home page, every published Page and every published blog post for every active locale.
                    </CardDescription>
                </div>
                <div class="flex flex-wrap items-center gap-2">
                    <!-- Download button — hits the admin endpoint that streams
                         the XML with a Content-Disposition: attachment header
                         so the browser saves it instead of rendering. -->
                    <Button as-child variant="default">
                        <a href="/admin/system/sitemap/download" download>
                            <Download class="size-4" />
                            Download XML
                        </a>
                    </Button>
                    <Button variant="outline" :disabled="flushing" @click="flushSitemap">
                        <Loader2 v-if="flushing" class="size-4 animate-spin" />
                        <RefreshCw v-else class="size-4" />
                        Flush cache
                    </Button>
                </div>
            </CardHeader>
            <CardContent class="space-y-4">
                <!-- URL row -->
                <div class="flex items-center gap-2 rounded-md border bg-muted/40 p-3 font-mono text-xs">
                    <Globe class="size-4 shrink-0 text-muted-foreground" />
                    <code class="flex-1 truncate">{{ sitemap.url }}</code>
                    <Button
                        type="button"
                        size="sm"
                        variant="ghost"
                        class="h-7 gap-1"
                        @click="copyUrl"
                    >
                        <CheckCircle2 v-if="copied" class="size-3.5 text-emerald-600" />
                        <Copy v-else class="size-3.5" />
                        <span class="text-[11px]">{{ copied ? 'Copied' : 'Copy' }}</span>
                    </Button>
                    <Button as-child size="sm" variant="ghost" class="h-7 gap-1">
                        <a :href="sitemap.url" target="_blank" rel="noopener">
                            <ExternalLink class="size-3.5" />
                            <span class="text-[11px]">Open</span>
                        </a>
                    </Button>
                </div>

                <!-- Counts grid -->
                <div class="grid grid-cols-2 gap-3 sm:grid-cols-5">
                    <div
                        v-for="stat in stats"
                        :key="stat.label"
                        class="rounded-md border bg-card p-3"
                        :class="stat.tone === 'primary' ? 'border-primary/40 bg-primary/5' : ''"
                    >
                        <div class="text-xs text-muted-foreground">{{ stat.label }}</div>
                        <div
                            class="mt-1 font-mono text-lg font-semibold"
                            :class="stat.tone === 'primary' ? 'text-primary' : ''"
                        >
                            {{ stat.value }}
                        </div>
                    </div>
                </div>

                <p class="text-xs text-muted-foreground">
                    Counts are multiplied by <strong>{{ sitemap.counts.locales }}</strong> active locale<span v-if="sitemap.counts.locales > 1">s</span> — each URL exists once per language.
                </p>
            </CardContent>
        </Card>
    </div>
</template>
