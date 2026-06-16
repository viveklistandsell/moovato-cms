<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import {
    AlertTriangle,
    Database,
    Download,
    FileJson,
    Newspaper,
    Files as PageIcon,
} from 'lucide-vue-next';
import Heading from '@/components/Heading.vue';
import { Button } from '@/components/ui/button';
import {
    Card,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';

defineProps<{
    counts: { pages: number; posts: number };
    database: {
        driver: string;
        mysqldump_available: boolean;
        mysqldump_path: string | null;
    };
}>();

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Dashboard', href: '/dashboard' },
            { title: 'System', href: '/admin/system/cache' },
            { title: 'Export & backup', href: '/admin/system/export' },
        ],
    },
});
</script>

<template>
    <Head title="Export & backup" />

    <div class="space-y-6 p-4">
        <Heading
            title="Export & backup"
            description="Stream a portable JSON snapshot of your content, or a full SQL dump of the database, for migration or disaster recovery."
        />

        <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
            <!-- PAGES -->
            <Card>
                <CardHeader>
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <PageIcon class="size-5 text-violet-600" />
                            <CardTitle class="text-base">Pages</CardTitle>
                        </div>
                        <span class="text-xs text-muted-foreground">
                            {{ counts.pages }} total
                        </span>
                    </div>
                    <CardDescription class="text-xs">
                        Every page with its translations, categories, widget
                        tree (settings + per-locale data) — bundled as
                        pretty-printed JSON.
                    </CardDescription>
                </CardHeader>
                <CardContent>
                    <Button as-child class="w-full">
                        <a
                            href="/admin/system/export/pages"
                            class="inline-flex items-center gap-2"
                        >
                            <FileJson class="size-4" />
                            Download pages.json
                        </a>
                    </Button>
                </CardContent>
            </Card>

            <!-- POSTS -->
            <Card>
                <CardHeader>
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <Newspaper class="size-5 text-sky-600" />
                            <CardTitle class="text-base">Blog posts</CardTitle>
                        </div>
                        <span class="text-xs text-muted-foreground">
                            {{ counts.posts }} total
                        </span>
                    </div>
                    <CardDescription class="text-xs">
                        Every blog post with translations, category +
                        tag permalinks, and author metadata.
                    </CardDescription>
                </CardHeader>
                <CardContent>
                    <Button as-child class="w-full">
                        <a
                            href="/admin/system/export/posts"
                            class="inline-flex items-center gap-2"
                        >
                            <FileJson class="size-4" />
                            Download blog-posts.json
                        </a>
                    </Button>
                </CardContent>
            </Card>

            <!-- DATABASE -->
            <Card>
                <CardHeader>
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <Database class="size-5 text-rose-600" />
                            <CardTitle class="text-base">Full database</CardTitle>
                        </div>
                        <span class="text-xs font-mono uppercase text-muted-foreground">
                            {{ database.driver }}
                        </span>
                    </div>
                    <CardDescription class="text-xs">
                        Streams a complete <code>mysqldump</code> of every
                        table, including users + settings + media metadata.
                        Restore with <code>mysql &lt; backup.sql</code>.
                    </CardDescription>
                </CardHeader>
                <CardContent>
                    <Button
                        as-child
                        :disabled="!database.mysqldump_available"
                        class="w-full"
                    >
                        <a
                            v-if="database.mysqldump_available"
                            href="/admin/system/export/database"
                            class="inline-flex items-center gap-2"
                        >
                            <Download class="size-4" />
                            Download database.sql
                        </a>
                        <span
                            v-else
                            class="inline-flex w-full items-center justify-center gap-2"
                        >
                            <AlertTriangle class="size-4" />
                            Not available
                        </span>
                    </Button>
                    <p
                        v-if="database.mysqldump_available && database.mysqldump_path"
                        class="mt-3 text-xs text-muted-foreground"
                    >
                        Using:
                        <code class="break-all">
                            {{ database.mysqldump_path }}
                        </code>
                    </p>
                    <p
                        v-if="!database.mysqldump_available"
                        class="mt-3 text-xs text-muted-foreground"
                    >
                        <code>mysqldump</code> was not found in any of the
                        common install locations, on PATH, or in the
                        <code>DB_DUMP_BIN</code> .env override. Install the
                        MySQL client tools (or set <code>DB_DUMP_BIN</code> to
                        an absolute path), then refresh this page.
                    </p>
                </CardContent>
            </Card>
        </div>

        <!-- Footnote -->
        <Card>
            <CardContent class="text-xs leading-relaxed text-muted-foreground">
                <p class="mb-2 font-semibold text-foreground">
                    A few things worth knowing
                </p>
                <ul class="list-disc space-y-1 pl-5">
                    <li>
                        JSON exports are <strong>content snapshots</strong>,
                        not deployment artefacts — they don't include the
                        actual uploaded media bytes, only their paths.
                    </li>
                    <li>
                        The SQL dump <strong>is</strong> a full backup of the
                        database, but the <code>storage/app/public</code>
                        folder still needs separate handling for media files.
                    </li>
                    <li>
                        Downloads stream as they're generated — large databases
                        won't OOM the server.
                    </li>
                </ul>
            </CardContent>
        </Card>
    </div>
</template>
