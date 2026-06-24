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
import { setBreadcrumbs } from '@/composables/common/useBreadcrumbs';
import { useT } from '@/composables/useT';

const t = useT();
setBreadcrumbs(() => [
    { title: t('sidebar.dashboard'), href: '/dashboard' },
    { title: t('sidebar.system'), href: '/admin/system/cache' },
    { title: t('sidebar.sitemap'), href: '/admin/system/sitemap' },
]);

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

// Breadcrumbs set dynamically above via setBreadcrumbs().
defineOptions({});

const page = usePage();
const flash = computed<{ type?: string; message?: string } | null>(() => {
    const toast = (page.props as Record<string, unknown>).toast;
    return (toast ?? null) as { type?: string; message?: string } | null;
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
    { label: t('system.sitemap_total_urls'), value: totalUrls.value, tone: 'primary' as const },
    { label: t('system.sitemap_home'), value: props.sitemap.counts.home },
    { label: t('system.sitemap_blog_index'), value: props.sitemap.counts.blog_index },
    { label: t('system.sitemap_pages'), value: props.sitemap.counts.pages },
    { label: t('system.sitemap_blog_posts'), value: props.sitemap.counts.blog_posts },
]);
</script>

<template>
    <Head :title="t('system.sitemap_title')" />

    <div class="flex flex-col gap-6 p-4">
        <Heading
            :title="t('system.sitemap_title')"
            :description="t('system.sitemap_description')"
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
                        {{ t('system.sitemap_lists') }}
                    </CardDescription>
                </div>
                <div class="flex flex-wrap items-center gap-2">
                    <!-- Download button — hits the admin endpoint that streams
                         the XML with a Content-Disposition: attachment header
                         so the browser saves it instead of rendering. -->
                    <Button as-child variant="default">
                        <a href="/admin/system/sitemap/download" download>
                            <Download class="size-4" />
                            {{ t('system.sitemap_download_xml') }}
                        </a>
                    </Button>
                    <Button variant="outline" :disabled="flushing" @click="flushSitemap">
                        <Loader2 v-if="flushing" class="size-4 animate-spin" />
                        <RefreshCw v-else class="size-4" />
                        {{ t('system.sitemap_flush_cache') }}
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
                        <span class="text-[11px]">{{ copied ? t('system.sitemap_copied') : t('system.sitemap_copy') }}</span>
                    </Button>
                    <Button as-child size="sm" variant="ghost" class="h-7 gap-1">
                        <a :href="sitemap.url" target="_blank" rel="noopener">
                            <ExternalLink class="size-3.5" />
                            <span class="text-[11px]">{{ t('system.sitemap_open') }}</span>
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
                    {{ t('system.sitemap_locales_hint', { count: sitemap.counts.locales }) }}
                </p>
            </CardContent>
        </Card>
    </div>
</template>
