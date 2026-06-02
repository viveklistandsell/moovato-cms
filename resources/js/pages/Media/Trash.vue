<script setup lang="ts">
import { ref } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ArrowLeft, RotateCcw, Trash2 } from 'lucide-vue-next';
import MediaLayout from '@/layouts/MediaLayout.vue';
import { Button } from '@/components/ui/button';
import DeleteConfirmDialog from '@/components/Media/DeleteConfirmDialog.vue';

type TrashedFolder = {
    id: number;
    name: string;
    path: string;
    deleted_at: string | null;
};

type TrashedFile = {
    id: number;
    name: string;
    mime_type: string;
    size: number;
    thumb_url: string | null;
    deleted_at: string | null;
};

type PaginatedFiles = {
    data: TrashedFile[];
    links: Array<{ url: string | null; label: string; active: boolean }>;
    current_page: number;
    last_page: number;
    total: number;
};

defineProps<{
    folders: TrashedFolder[];
    files: PaginatedFiles;
}>();

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Media', href: '/admin/media' },
            { title: 'Trash', href: '/admin/media/trash' },
        ],
    },
});

const forceDeleteOpen = ref(false);
const forceDeleteTarget = ref<{ id: number; name: string } | null>(null);
const forceDeleteType = ref<'folder' | 'file'>('folder');

function restoreFolder(id: number): void {
    router.post(
        `/admin/media/trash/folders/${id}/restore`,
        {},
        { preserveScroll: true },
    );
}

function restoreFile(id: number): void {
    router.post(
        `/admin/media/trash/files/${id}/restore`,
        {},
        { preserveScroll: true },
    );
}

function openForceDeleteFolder(folder: TrashedFolder): void {
    forceDeleteTarget.value = { id: folder.id, name: folder.name };
    forceDeleteType.value = 'folder';
    forceDeleteOpen.value = true;
}

function openForceDeleteFile(file: TrashedFile): void {
    forceDeleteTarget.value = { id: file.id, name: file.name };
    forceDeleteType.value = 'file';
    forceDeleteOpen.value = true;
}

function readableSize(bytes: number): string {
    if (bytes < 1024) {
        return `${bytes} B`;
    }
    if (bytes < 1024 * 1024) {
        return `${(bytes / 1024).toFixed(1)} KB`;
    }
    if (bytes < 1024 * 1024 * 1024) {
        return `${(bytes / (1024 * 1024)).toFixed(1)} MB`;
    }
    return `${(bytes / (1024 * 1024 * 1024)).toFixed(2)} GB`;
}
</script>

<template>
    <Head title="Trash" />

    <MediaLayout>
        <div class="flex flex-1 flex-col gap-6 p-4">
            <header class="flex items-center justify-between">
                <h1 class="text-xl font-semibold">Trash</h1>
                <Button as-child variant="outline" size="sm">
                    <Link href="/admin/media">
                        <ArrowLeft class="h-4 w-4" />
                        Back to media
                    </Link>
                </Button>
            </header>

            <section v-if="folders.length > 0" class="flex flex-col gap-2">
                <h2 class="text-sm font-medium">
                    Folders ({{ folders.length }})
                </h2>
                <ul
                    class="divide-y divide-border rounded-lg border border-border"
                >
                    <li
                        v-for="folder in folders"
                        :key="folder.id"
                        class="flex flex-wrap items-center justify-between gap-2 p-3"
                    >
                        <div class="flex flex-col">
                            <span class="text-sm font-medium">{{
                                folder.name
                            }}</span>
                            <span class="text-xs text-muted-foreground">
                                {{ folder.path }}
                            </span>
                        </div>
                        <div class="flex items-center gap-2">
                            <Button
                                type="button"
                                size="sm"
                                variant="outline"
                                @click="restoreFolder(folder.id)"
                            >
                                <RotateCcw class="h-4 w-4" />
                                Restore
                            </Button>
                            <Button
                                type="button"
                                size="sm"
                                variant="destructive"
                                @click="openForceDeleteFolder(folder)"
                            >
                                <Trash2 class="h-4 w-4" />
                                Delete forever
                            </Button>
                        </div>
                    </li>
                </ul>
            </section>

            <section class="flex flex-col gap-2">
                <h2 class="text-sm font-medium">Files ({{ files.total }})</h2>
                <ul
                    v-if="files.data.length > 0"
                    class="divide-y divide-border rounded-lg border border-border"
                >
                    <li
                        v-for="file in files.data"
                        :key="file.id"
                        class="flex flex-wrap items-center gap-3 p-3"
                    >
                        <img
                            v-if="file.thumb_url"
                            :src="file.thumb_url"
                            :alt="file.name"
                            class="h-12 w-12 rounded object-cover"
                        />
                        <div
                            v-else
                            class="flex h-12 w-12 items-center justify-center rounded bg-muted text-xs text-muted-foreground uppercase"
                        >
                            file
                        </div>
                        <div class="flex flex-1 flex-col">
                            <span class="text-sm font-medium">{{
                                file.name
                            }}</span>
                            <span class="text-xs text-muted-foreground">
                                {{ file.mime_type }} ·
                                {{ readableSize(file.size) }}
                            </span>
                        </div>
                        <div class="flex items-center gap-2">
                            <Button
                                type="button"
                                size="sm"
                                variant="outline"
                                @click="restoreFile(file.id)"
                            >
                                <RotateCcw class="h-4 w-4" />
                                Restore
                            </Button>
                            <Button
                                type="button"
                                size="sm"
                                variant="destructive"
                                @click="openForceDeleteFile(file)"
                            >
                                <Trash2 class="h-4 w-4" />
                                Delete forever
                            </Button>
                        </div>
                    </li>
                </ul>
                <p
                    v-else
                    class="rounded-lg border border-dashed border-border p-8 text-center text-sm text-muted-foreground"
                >
                    Trash is empty.
                </p>
            </section>
        </div>

        <DeleteConfirmDialog
            v-model:open="forceDeleteOpen"
            :target="forceDeleteTarget"
            :type="forceDeleteType"
            force-delete
        />
    </MediaLayout>
</template>
