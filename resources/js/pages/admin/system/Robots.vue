<script setup lang="ts">
import { Head, usePage } from '@inertiajs/vue3';
import {
    AlertTriangle,
    Bot,
    CheckCircle2,
    Copy,
    ExternalLink,
    Globe,
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

const props = defineProps<{
    robots: { url: string; body: string; allowsIndexing: boolean };
}>();

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Dashboard', href: '/dashboard' },
            { title: 'System', href: '/admin/system/cache' },
            { title: 'Robots.txt', href: '/admin/system/robots' },
        ],
    },
});

const page = usePage();
const flash = computed<{ type?: string; message?: string } | null>(() => {
    const t = (page.props as Record<string, unknown>).toast;
    return (t ?? null) as { type?: string; message?: string } | null;
});

const copied = ref(false);
function copyUrl(): void {
    navigator.clipboard.writeText(props.robots.url).then(() => {
        copied.value = true;
        window.setTimeout(() => (copied.value = false), 1500);
    });
}
</script>

<template>
    <Head title="Robots.txt" />

    <div class="flex flex-col gap-6 p-4">
        <Heading
            title="Robots.txt"
            description="Driven by the Allow search indexing toggle in Settings → SEO Defaults. When off, the entire site is disallowed."
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
            <CardHeader class="space-y-1">
                <CardTitle class="flex items-center gap-2">
                    <Bot class="size-5 text-primary" />
                    robots.txt
                </CardTitle>
                <CardDescription>
                    Live preview of the exact response served at <code class="font-mono">/robots.txt</code>.
                </CardDescription>
            </CardHeader>
            <CardContent class="space-y-4">
                <!-- Indexing status badge -->
                <div
                    class="flex items-start gap-3 rounded-md border p-3"
                    :class="robots.allowsIndexing
                        ? 'border-emerald-200 bg-emerald-50 dark:border-emerald-900/60 dark:bg-emerald-950/20'
                        : 'border-rose-200 bg-rose-50 dark:border-rose-900/60 dark:bg-rose-950/20'"
                >
                    <component
                        :is="robots.allowsIndexing ? CheckCircle2 : AlertTriangle"
                        class="mt-0.5 size-5 shrink-0"
                        :class="robots.allowsIndexing
                            ? 'text-emerald-600 dark:text-emerald-400'
                            : 'text-rose-600 dark:text-rose-400'"
                    />
                    <div class="space-y-0.5 text-sm">
                        <div
                            class="font-medium"
                            :class="robots.allowsIndexing
                                ? 'text-emerald-900 dark:text-emerald-100'
                                : 'text-rose-900 dark:text-rose-100'"
                        >
                            {{ robots.allowsIndexing ? 'Search engines may crawl the site' : 'Site is blocked from all crawlers' }}
                        </div>
                        <p
                            class="text-xs"
                            :class="robots.allowsIndexing
                                ? 'text-emerald-800/80 dark:text-emerald-300/80'
                                : 'text-rose-800/80 dark:text-rose-300/80'"
                        >
                            Change this in <a class="underline" href="/admin/settings/seo">Settings → SEO Defaults</a>.
                        </p>
                    </div>
                </div>

                <!-- URL row -->
                <div class="flex items-center gap-2 rounded-md border bg-muted/40 p-3 font-mono text-xs">
                    <Globe class="size-4 shrink-0 text-muted-foreground" />
                    <code class="flex-1 truncate">{{ robots.url }}</code>
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
                        <a :href="robots.url" target="_blank" rel="noopener">
                            <ExternalLink class="size-3.5" />
                            <span class="text-[11px]">Open</span>
                        </a>
                    </Button>
                </div>

                <!-- Body preview -->
                <div class="space-y-1">
                    <div class="text-xs font-medium text-muted-foreground">Current response body</div>
                    <pre class="overflow-x-auto rounded-md border bg-muted/40 p-3 font-mono text-xs leading-relaxed">{{ robots.body }}</pre>
                </div>
            </CardContent>
        </Card>
    </div>
</template>
