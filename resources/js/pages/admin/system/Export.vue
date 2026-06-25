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
import { setBreadcrumbs } from '@/composables/common/useBreadcrumbs';
import { useT } from '@/composables/useT';

const t = useT();
setBreadcrumbs(() => [
    { title: t('sidebar.dashboard'), href: '/dashboard' },
    { title: t('sidebar.system'), href: '/admin/system/cache' },
    { title: t('sidebar.export_backup'), href: '/admin/system/export' },
]);

defineProps<{
    counts: { pages: number; posts: number };
    database: {
        driver: string;
        mysqldump_available: boolean;
        mysqldump_path: string | null;
    };
}>();

// Breadcrumbs set dynamically above via setBreadcrumbs().
defineOptions({});
</script>

<template>
    <Head :title="t('system.export_title')" />

    <div class="space-y-6 p-4">
        <Heading
            :title="t('system.export_title')"
            :description="t('system.export_description')"
        />

        <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
            <!-- PAGES -->
            <Card>
                <CardHeader>
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <PageIcon class="size-5 text-violet-600" />
                            <CardTitle class="text-base">{{ t('system.export_pages_card') }}</CardTitle>
                        </div>
                        <span class="text-xs text-muted-foreground">
                            {{ t('system.export_total', { count: counts.pages }) }}
                        </span>
                    </div>
                    <CardDescription class="text-xs">
                        {{ t('system.export_pages_desc') }}
                    </CardDescription>
                </CardHeader>
                <CardContent>
                    <Button as-child class="w-full">
                        <a
                            href="/admin/system/export/pages"
                            class="inline-flex items-center gap-2"
                        >
                            <FileJson class="size-4" />
                            {{ t('system.export_pages_dl') }}
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
                            <CardTitle class="text-base">{{ t('system.export_posts_card') }}</CardTitle>
                        </div>
                        <span class="text-xs text-muted-foreground">
                            {{ t('system.export_total', { count: counts.posts }) }}
                        </span>
                    </div>
                    <CardDescription class="text-xs">
                        {{ t('system.export_posts_desc') }}
                    </CardDescription>
                </CardHeader>
                <CardContent>
                    <Button as-child class="w-full">
                        <a
                            href="/admin/system/export/posts"
                            class="inline-flex items-center gap-2"
                        >
                            <FileJson class="size-4" />
                            {{ t('system.export_posts_dl') }}
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
                            <CardTitle class="text-base">{{ t('system.export_db_title') }}</CardTitle>
                        </div>
                        <span class="text-xs font-mono uppercase text-muted-foreground">
                            {{ database.driver }}
                        </span>
                    </div>
                    <CardDescription class="text-xs">
                        {{ t('system.export_db_desc') }}
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
                            {{ t('system.export_db_dl') }}
                        </a>
                        <span
                            v-else
                            class="inline-flex w-full items-center justify-center gap-2"
                        >
                            <AlertTriangle class="size-4" />
                            {{ t('system.export_db_not_available') }}
                        </span>
                    </Button>
                    <p
                        v-if="database.mysqldump_available && database.mysqldump_path"
                        class="mt-3 text-xs text-muted-foreground"
                    >
                        {{ t('system.export_db_using', { path: database.mysqldump_path }) }}
                    </p>
                    <p
                        v-if="!database.mysqldump_available"
                        class="mt-3 text-xs text-muted-foreground"
                    >
                        {{ t('system.export_db_missing_hint') }}
                    </p>
                </CardContent>
            </Card>
        </div>

        <!-- Footnote -->
        <Card>
            <CardContent class="text-xs leading-relaxed text-muted-foreground">
                <p class="mb-2 font-semibold text-foreground">
                    {{ t('system.export_footnote_title') }}
                </p>
                <ul class="list-disc space-y-1 pl-5">
                    <li>{{ t('system.export_footnote_1') }}</li>
                    <li>{{ t('system.export_footnote_2') }}</li>
                    <li>{{ t('system.export_footnote_3') }}</li>
                </ul>
            </CardContent>
        </Card>
    </div>
</template>
