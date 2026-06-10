<script setup lang="ts">
import { computed, ref, watch } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ArrowUpDown, FolderPlus, Search, Trash2, X } from 'lucide-vue-next';
import MediaLayout from '@/layouts/MediaLayout.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';
import FolderTree from '@/components/Media/FolderTree.vue';
import type { FolderNode } from '@/components/Media/FolderTreeNode.vue';
import FolderCard, {
    type MediaFolderItem,
} from '@/components/Media/FolderCard.vue';
import FileCard, { type MediaFileItem } from '@/components/Media/FileCard.vue';
import MediaBreadcrumbs from '@/components/Media/MediaBreadcrumbs.vue';
import Uploader from '@/components/Media/Uploader.vue';
import NewFolderDialog from '@/components/Media/NewFolderDialog.vue';
import RenameDialog from '@/components/Media/RenameDialog.vue';
import DeleteConfirmDialog from '@/components/Media/DeleteConfirmDialog.vue';
import MoveDialog from '@/components/Media/MoveDialog.vue';
import FilePreviewModal from '@/components/Media/FilePreviewModal.vue';

type CurrentFolder = {
    id: number;
    name: string;
    path: string;
    parent_id: number | null;
} | null;

type PaginatedFiles = {
    data: MediaFileItem[];
    links: Array<{ url: string | null; label: string; active: boolean }>;
    current_page: number;
    last_page: number;
    total: number;
};

const props = defineProps<{
    currentFolder: CurrentFolder;
    breadcrumbs: Array<{ id: number; name: string }>;
    folders: MediaFolderItem[];
    files: PaginatedFiles;
    tree: FolderNode[];
    search: string;
    sort: string;
    totalSize: number;
}>();

defineOptions({
    layout: {
        breadcrumbs: [{ title: 'Media', href: '/admin/media' }],
    },
});

const newFolderOpen = ref(false);

const renameOpen = ref(false);
const renameTarget = ref<{ id: number; name: string } | null>(null);
const renameType = ref<'folder' | 'file'>('folder');

const deleteOpen = ref(false);
const deleteTarget = ref<{ id: number; name: string } | null>(null);
const deleteType = ref<'folder' | 'file'>('folder');
const deleteIsBulk = ref(false);
const bulkDeleteIds = ref<number[]>([]);
const bulkDeleteCount = ref(0);

type MoveTarget =
    | { type: 'file'; id: number; name: string; currentFolderId: number | null }
    | {
          type: 'folder';
          id: number;
          name: string;
          currentFolderId: number | null;
      }
    | {
          type: 'bulk-files';
          ids: number[];
          name: string;
          currentFolderId: number | null;
      };

const moveOpen = ref(false);
const moveTarget = ref<MoveTarget | null>(null);

const previewOpen = ref(false);
const previewFile = ref<MediaFileItem | null>(null);

const selectedIds = ref<number[]>([]);
const selectedFolderIds = ref<number[]>([]);
const isSelected = (id: number): boolean => selectedIds.value.includes(id);
const isFolderSelected = (id: number): boolean =>
    selectedFolderIds.value.includes(id);
const totalSelected = computed(
    () => selectedIds.value.length + selectedFolderIds.value.length,
);
const allOnPageSelected = computed(() => {
    const allFilesSelected =
        props.files.data.length === 0 ||
        props.files.data.every((f) => selectedIds.value.includes(f.id));
    const allFoldersSelected =
        props.folders.length === 0 ||
        props.folders.every((f) => selectedFolderIds.value.includes(f.id));
    return (
        allFilesSelected &&
        allFoldersSelected &&
        (props.files.data.length > 0 || props.folders.length > 0)
    );
});

function toggleSelect(file: MediaFileItem): void {
    const idx = selectedIds.value.indexOf(file.id);
    if (idx >= 0) selectedIds.value.splice(idx, 1);
    else selectedIds.value.push(file.id);
}

function toggleSelectFolder(folder: MediaFolderItem): void {
    const idx = selectedFolderIds.value.indexOf(folder.id);
    if (idx >= 0) selectedFolderIds.value.splice(idx, 1);
    else selectedFolderIds.value.push(folder.id);
}

function toggleSelectAll(): void {
    if (allOnPageSelected.value) {
        const fileIds = props.files.data.map((f) => f.id);
        const folderIds = props.folders.map((f) => f.id);
        selectedIds.value = selectedIds.value.filter(
            (id) => !fileIds.includes(id),
        );
        selectedFolderIds.value = selectedFolderIds.value.filter(
            (id) => !folderIds.includes(id),
        );
    } else {
        const fileSet = new Set(selectedIds.value);
        props.files.data.forEach((f) => fileSet.add(f.id));
        selectedIds.value = Array.from(fileSet);
        const folderSet = new Set(selectedFolderIds.value);
        props.folders.forEach((f) => folderSet.add(f.id));
        selectedFolderIds.value = Array.from(folderSet);
    }
}

function clearSelection(): void {
    selectedIds.value = [];
    selectedFolderIds.value = [];
}

watch(
    () => props.files.data.map((f) => f.id).join(','),
    () => {
        const pageIds = new Set(props.files.data.map((f) => f.id));
        selectedIds.value = selectedIds.value.filter((id) => pageIds.has(id));
    },
);

watch(
    () => props.folders.map((f) => f.id).join(','),
    () => {
        const ids = new Set(props.folders.map((f) => f.id));
        selectedFolderIds.value = selectedFolderIds.value.filter((id) =>
            ids.has(id),
        );
    },
);

const searchInput = ref(props.search ?? '');
let searchTimer: ReturnType<typeof setTimeout> | null = null;

watch(
    () => props.search,
    (val) => {
        searchInput.value = val ?? '';
    },
);

const currentSort = ref(props.sort ?? 'newest');

watch(
    () => props.sort,
    (val) => {
        currentSort.value = val ?? 'newest';
    },
);

function buildParams(): Record<string, string | number> {
    const params: Record<string, string | number> = {};
    if (searchInput.value.trim()) {
        params.q = searchInput.value.trim();
    }
    if (props.currentFolder?.id && !params.q) {
        params.folder = props.currentFolder.id;
    }
    if (currentSort.value && currentSort.value !== 'newest') {
        params.sort = currentSort.value;
    }
    return params;
}

function applySearch(): void {
    router.get('/admin/media', buildParams(), {
        preserveState: true,
        preserveScroll: true,
        replace: true,
    });
}

function onSearchInput(): void {
    if (searchTimer) clearTimeout(searchTimer);
    searchTimer = setTimeout(applySearch, 300);
}

function clearSearch(): void {
    searchInput.value = '';
    applySearch();
}

function changeSort(sort: string): void {
    currentSort.value = sort;
    applySearch();
}

function readableSize(bytes: number): string {
    if (bytes < 1024) return `${bytes} B`;
    if (bytes < 1024 * 1024) return `${(bytes / 1024).toFixed(1)} KB`;
    if (bytes < 1024 * 1024 * 1024)
        return `${(bytes / (1024 * 1024)).toFixed(1)} MB`;
    return `${(bytes / (1024 * 1024 * 1024)).toFixed(2)} GB`;
}

function openRenameFolder(folder: MediaFolderItem): void {
    renameTarget.value = { id: folder.id, name: folder.name };
    renameType.value = 'folder';
    renameOpen.value = true;
}

function openRenameFile(file: MediaFileItem): void {
    renameTarget.value = { id: file.id, name: file.name };
    renameType.value = 'file';
    renameOpen.value = true;
}

function openMoveFolder(folder: MediaFolderItem): void {
    moveTarget.value = {
        type: 'folder',
        id: folder.id,
        name: folder.name,
        currentFolderId: folder.parent_id ?? null,
    };
    moveOpen.value = true;
}

function openMoveFile(file: MediaFileItem): void {
    moveTarget.value = {
        type: 'file',
        id: file.id,
        name: file.name,
        currentFolderId: file.folder_id ?? props.currentFolder?.id ?? null,
    };
    moveOpen.value = true;
}

function openBulkMove(): void {
    if (selectedIds.value.length === 0) return;
    moveTarget.value = {
        type: 'bulk-files',
        ids: [...selectedIds.value],
        name: `${selectedIds.value.length} file(s)`,
        currentFolderId: props.currentFolder?.id ?? null,
    };
    moveOpen.value = true;
}

function openDeleteFolder(folder: MediaFolderItem): void {
    deleteTarget.value = { id: folder.id, name: folder.name };
    deleteType.value = 'folder';
    deleteIsBulk.value = false;
    deleteOpen.value = true;
}

function openDeleteFile(file: MediaFileItem): void {
    deleteTarget.value = { id: file.id, name: file.name };
    deleteType.value = 'file';
    deleteIsBulk.value = false;
    deleteOpen.value = true;
}

function openBulkDelete(): void {
    if (totalSelected.value === 0) return;
    bulkDeleteIds.value = [...selectedIds.value];
    bulkDeleteCount.value = totalSelected.value;
    deleteIsBulk.value = true;
    deleteOpen.value = true;
}

function performBulkDelete(): void {
    const fileIds = [...selectedIds.value];
    const folderIds = [...selectedFolderIds.value];

    const finish = () => {
        deleteOpen.value = false;
        clearSelection();
        bulkDeleteIds.value = [];
    };

    if (fileIds.length > 0 && folderIds.length > 0) {
        router.post(
            '/admin/media/files/bulk-delete',
            { ids: fileIds },
            {
                preserveScroll: true,
                onSuccess: () => {
                    router.post(
                        '/admin/media/folders/bulk-delete',
                        { ids: folderIds },
                        { preserveScroll: true, onSuccess: finish },
                    );
                },
            },
        );
        return;
    }

    if (fileIds.length > 0) {
        router.post(
            '/admin/media/files/bulk-delete',
            { ids: fileIds },
            { preserveScroll: true, onSuccess: finish },
        );
        return;
    }

    if (folderIds.length > 0) {
        router.post(
            '/admin/media/folders/bulk-delete',
            { ids: folderIds },
            { preserveScroll: true, onSuccess: finish },
        );
    }
}

function openFile(file: MediaFileItem): void {
    previewFile.value = file;
    previewOpen.value = true;
}

function deleteFromPreview(file: MediaFileItem): void {
    deleteTarget.value = { id: file.id, name: file.name };
    deleteType.value = 'file';
    deleteIsBulk.value = false;
    deleteOpen.value = true;
    previewOpen.value = false;
}

function onPreviewUpdated(file: MediaFileItem): void {
    previewFile.value = file;
    router.reload({ only: ['files'] });
}
</script>

<template>
    <Head title="Media" />

    <MediaLayout>
        <div class="flex h-full flex-1 flex-col gap-4 p-4">
            <header class="flex flex-wrap items-center justify-between gap-3">
                <MediaBreadcrumbs :items="breadcrumbs" />
                <div class="flex flex-wrap items-center gap-2">
                    <div class="relative">
                        <Search
                            class="pointer-events-none absolute top-1/2 left-2.5 h-4 w-4 -translate-y-1/2 text-muted-foreground"
                        />
                        <Input
                            v-model="searchInput"
                            type="search"
                            placeholder="Search files…"
                            class="h-9 w-56 pr-8 pl-8"
                            @input="onSearchInput"
                            @keydown.enter.prevent="applySearch"
                        />
                        <button
                            v-if="searchInput"
                            type="button"
                            class="absolute top-1/2 right-2 -translate-y-1/2 rounded p-0.5 text-muted-foreground hover:bg-accent"
                            @click="clearSearch"
                        >
                            <X class="h-3.5 w-3.5" />
                        </button>
                    </div>
                    <Button
                        type="button"
                        variant="outline"
                        size="sm"
                        @click="newFolderOpen = true"
                    >
                        <FolderPlus class="h-4 w-4" />
                        New folder
                    </Button>
                    <DropdownMenu>
                        <DropdownMenuTrigger
                            class="inline-flex h-9 items-center gap-1.5 rounded-md border border-input bg-background px-3 text-sm font-medium hover:bg-accent"
                        >
                            <ArrowUpDown class="h-4 w-4" />
                            {{
                                {
                                    newest: 'Newest',
                                    oldest: 'Oldest',
                                    name: 'Name (A→Z)',
                                    name_desc: 'Name (Z→A)',
                                    largest: 'Largest',
                                    smallest: 'Smallest',
                                }[currentSort]
                            }}
                        </DropdownMenuTrigger>
                        <DropdownMenuContent align="end">
                            <DropdownMenuItem @click="changeSort('newest')">
                                Newest first
                            </DropdownMenuItem>
                            <DropdownMenuItem @click="changeSort('oldest')">
                                Oldest first
                            </DropdownMenuItem>
                            <DropdownMenuItem @click="changeSort('name')">
                                Name (A → Z)
                            </DropdownMenuItem>
                            <DropdownMenuItem @click="changeSort('name_desc')">
                                Name (Z → A)
                            </DropdownMenuItem>
                            <DropdownMenuItem @click="changeSort('largest')">
                                Largest first
                            </DropdownMenuItem>
                            <DropdownMenuItem @click="changeSort('smallest')">
                                Smallest first
                            </DropdownMenuItem>
                        </DropdownMenuContent>
                    </DropdownMenu>
                    <Button as-child variant="outline" size="sm">
                        <Link href="/admin/media/trash">
                            <Trash2 class="h-4 w-4" />
                            Trash
                        </Link>
                    </Button>
                </div>
            </header>

            <div
                class="flex flex-wrap items-center gap-3 text-xs text-muted-foreground"
            >
                <span class="inline-flex items-center gap-1.5">
                    <span class="h-2 w-2 rounded-full bg-emerald-500"></span>
                    {{ files.total }} file(s)
                </span>
                <span class="inline-flex items-center gap-1.5">
                    <span class="h-2 w-2 rounded-full bg-amber-500"></span>
                    {{ folders.length }} folder(s)
                </span>
                <span
                    v-if="totalSize > 0"
                    class="inline-flex items-center gap-1.5"
                >
                    <span class="h-2 w-2 rounded-full bg-blue-500"></span>
                    {{ readableSize(totalSize) }} total
                </span>
                <span v-if="search" class="inline-flex items-center gap-1.5">
                    <span class="h-2 w-2 rounded-full bg-violet-500"></span>
                    Search: "{{ search }}"
                </span>
            </div>

            <div
                v-if="totalSelected > 0"
                class="flex flex-wrap items-center justify-between gap-2 rounded-lg border border-primary/40 bg-primary/5 px-3 py-2 text-sm"
            >
                <div class="flex items-center gap-3">
                    <span class="font-medium">
                        {{ totalSelected }} selected
                    </span>
                    <button
                        type="button"
                        class="text-xs text-primary hover:underline"
                        @click="toggleSelectAll"
                    >
                        {{
                            allOnPageSelected
                                ? 'Deselect this page'
                                : 'Select all on this page'
                        }}
                    </button>
                </div>
                <div class="flex items-center gap-2">
                    <Button
                        v-if="selectedIds.length > 0"
                        type="button"
                        size="sm"
                        variant="outline"
                        @click="openBulkMove"
                    >
                        Move {{ selectedIds.length }} file(s)…
                    </Button>
                    <Button
                        type="button"
                        size="sm"
                        variant="destructive"
                        @click="openBulkDelete"
                    >
                        <Trash2 class="h-4 w-4" />
                        Delete
                    </Button>
                    <Button
                        type="button"
                        size="sm"
                        variant="ghost"
                        @click="clearSelection"
                    >
                        Clear
                    </Button>
                </div>
            </div>

            <div class="flex flex-1 flex-col gap-4 md:flex-row md:gap-6">
                <aside class="shrink-0 md:w-64">
                    <p
                        class="mb-2 px-2 text-xs font-medium tracking-wide text-muted-foreground uppercase"
                    >
                        Folders
                    </p>
                    <FolderTree
                        :tree="tree"
                        :current-folder-id="currentFolder?.id ?? null"
                    />
                </aside>

                <main class="flex min-w-0 flex-1 flex-col gap-4">
                    <div
                        v-if="
                            !currentFolder &&
                            folders.length === 0 &&
                            files.total === 0
                        "
                        class="flex flex-col items-center gap-3 rounded-lg border border-dashed border-border bg-muted/30 p-10 text-center"
                    >
                        <FolderPlus class="h-8 w-8 text-muted-foreground" />
                        <div class="flex flex-col gap-1">
                            <p class="text-sm font-medium">
                                Create your first album
                            </p>
                            <p class="text-xs text-muted-foreground">
                                Start by creating a folder, then upload images,
                                PDFs, video, or any other file type into it.
                            </p>
                        </div>
                        <Button
                            type="button"
                            size="sm"
                            @click="newFolderOpen = true"
                        >
                            <FolderPlus class="h-4 w-4" />
                            New folder
                        </Button>
                    </div>

                    <Uploader v-else :folder-id="currentFolder?.id ?? null" />

                    <section
                        v-if="folders.length > 0"
                        class="flex flex-col gap-2"
                    >
                        <h3
                            class="text-xs font-medium tracking-wide text-muted-foreground uppercase"
                        >
                            Folders
                        </h3>
                        <div
                            class="grid grid-cols-1 gap-3 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4"
                        >
                            <FolderCard
                                v-for="folder in folders"
                                :key="folder.id"
                                :folder="folder"
                                :selected="isFolderSelected(folder.id)"
                                @rename="openRenameFolder"
                                @move="openMoveFolder"
                                @delete="openDeleteFolder"
                                @toggle-select="toggleSelectFolder"
                            />
                        </div>
                    </section>

                    <section class="flex flex-col gap-2">
                        <h3
                            class="text-xs font-medium tracking-wide text-muted-foreground uppercase"
                        >
                            Files ({{ files.total }})
                        </h3>
                        <div
                            v-if="files.data.length > 0"
                            class="grid grid-cols-1 gap-3 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4"
                        >
                            <FileCard
                                v-for="file in files.data"
                                :key="file.id"
                                :file="file"
                                :selected="isSelected(file.id)"
                                @open="openFile"
                                @toggle-select="toggleSelect"
                                @rename="openRenameFile"
                                @move="openMoveFile"
                                @delete="openDeleteFile"
                            />
                        </div>
                        <p
                            v-else
                            class="rounded-lg border border-dashed border-border p-12 text-center text-sm text-muted-foreground"
                        >
                            No files in this folder yet. Drop files above to
                            upload.
                        </p>

                        <nav
                            v-if="files.last_page > 1"
                            class="flex flex-wrap items-center justify-center gap-1 pt-2 text-sm"
                        >
                            <Link
                                v-for="link in files.links"
                                :key="link.label"
                                :href="link.url ?? ''"
                                :class="[
                                    'rounded px-3 py-1',
                                    link.active
                                        ? 'bg-primary text-primary-foreground'
                                        : link.url
                                          ? 'hover:bg-accent'
                                          : 'cursor-default text-muted-foreground',
                                ]"
                                v-html="link.label"
                                preserve-scroll
                            />
                        </nav>
                    </section>
                </main>
            </div>
        </div>

        <NewFolderDialog
            v-model:open="newFolderOpen"
            :parent-id="currentFolder?.id ?? null"
        />

        <RenameDialog
            v-model:open="renameOpen"
            :target="renameTarget"
            :type="renameType"
        />

        <DeleteConfirmDialog
            v-if="!deleteIsBulk"
            v-model:open="deleteOpen"
            :target="deleteTarget"
            :type="deleteType"
        />

        <Dialog v-else v-model:open="deleteOpen">
            <DialogContent>
                <DialogHeader>
                    <DialogTitle>
                        Delete {{ bulkDeleteCount }} item(s)?
                    </DialogTitle>
                    <DialogDescription>
                        Selected folders and files will be moved to trash and
                        can be restored later.
                    </DialogDescription>
                </DialogHeader>
                <DialogFooter>
                    <Button
                        type="button"
                        variant="outline"
                        @click="deleteOpen = false"
                    >
                        Cancel
                    </Button>
                    <Button
                        type="button"
                        variant="destructive"
                        @click="performBulkDelete"
                    >
                        Move to trash
                    </Button>
                </DialogFooter>
            </DialogContent>
        </Dialog>

        <MoveDialog
            v-model:open="moveOpen"
            :target="moveTarget"
            :tree="tree"
            @moved="clearSelection"
        />

        <FilePreviewModal
            v-model:open="previewOpen"
            :file="previewFile"
            @delete="deleteFromPreview"
            @updated="onPreviewUpdated"
        />
    </MediaLayout>
</template>
