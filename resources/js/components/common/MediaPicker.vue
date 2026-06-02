<script setup lang="ts">
import {
    ArrowUpDown,
    ChevronLeft,
    Eye,
    Folder,
    FolderPlus,
    Home,
    Image as ImageIcon,
    Loader2,
    Search,
    Trash2,
    X,
} from 'lucide-vue-next';
import { computed, ref, watch } from 'vue';
import MediaDetailsModal, {
    type PickerFile as DetailFile,
} from '@/components/common/MediaDetailsModal.vue';
import Uploader from '@/components/Media/Uploader.vue';
import { Button } from '@/components/ui/button';
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
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';

type PickerFile = {
    id: number;
    name: string;
    original_name: string;
    path: string;
    thumb_path: string | null;
    medium_path: string | null;
    url: string;
    thumb_url: string | null;
    medium_url: string | null;
    mime_type: string;
    extension?: string;
    size: number;
    folder_id: number | null;
    uploader?: string | null;
    created_at?: string | null;
    updated_at?: string | null;
    alt_text?: string | null;
    title?: string | null;
    caption?: string | null;
    description?: string | null;
};

type PickerFolder = {
    id: number;
    name: string;
    parent_id: number | null;
    thumb_url: string | null;
    file_count: number;
    subfolder_count: number;
};

type Crumb = { id: number; name: string };

type SortKey = 'newest' | 'oldest' | 'name' | 'name_desc';

const SORT_LABELS: Record<SortKey, string> = {
    newest: 'Newest',
    oldest: 'Oldest',
    name: 'Name A–Z',
    name_desc: 'Name Z–A',
};

const open = defineModel<boolean>('open', { required: true });

// `accept` narrows the listing by MIME family. Defaults to 'image' so the
// many existing image-only callers (page/blog featured image, widget image
// fields) keep their current behavior.
const props = withDefaults(
    defineProps<{
        accept?: 'image' | 'video' | 'any';
    }>(),
    { accept: 'image' },
);

const emit = defineEmits<{
    (
        e: 'pick',
        file: {
            path: string;
            url: string;
            name: string;
            thumb_path?: string | null;
            thumb_url?: string | null;
        },
    ): void;
}>();

const search = ref('');
const folderId = ref<number | null>(null);
const sort = ref<SortKey>('newest');
const breadcrumb = ref<Crumb[]>([]);
const parentFolderId = ref<number | null>(null);

const files = ref<PickerFile[]>([]);
const folders = ref<PickerFolder[]>([]);
const stats = ref({ file_count: 0, folder_count: 0 });
const loading = ref(false);

const newFolderOpen = ref(false);
const newFolderName = ref('');
const newFolderError = ref<string | null>(null);
const creatingFolder = ref(false);

// === Drag & drop (move files into folders) ===
const draggedFileId = ref<number | null>(null);
const dropTargetFolderId = ref<number | null | 'up'>(null);

function onFileDragStart(event: DragEvent, file: PickerFile): void {
    draggedFileId.value = file.id;
    if (event.dataTransfer) {
        event.dataTransfer.effectAllowed = 'move';
        event.dataTransfer.setData('text/plain', String(file.id));
    }
}

function onFolderDragOver(event: DragEvent, folder: PickerFolder): void {
    if (draggedFileId.value === null) return;
    event.preventDefault();
    if (event.dataTransfer) event.dataTransfer.dropEffect = 'move';
    dropTargetFolderId.value = folder.id;
}

function onUpDragOver(event: DragEvent): void {
    if (draggedFileId.value === null || folderId.value === null) return;
    event.preventDefault();
    if (event.dataTransfer) event.dataTransfer.dropEffect = 'move';
    dropTargetFolderId.value = 'up';
}

function onDragLeave(): void {
    dropTargetFolderId.value = null;
}

function onDragEnd(): void {
    draggedFileId.value = null;
    dropTargetFolderId.value = null;
}

async function moveFiles(
    ids: number[],
    targetFolderId: number | null,
): Promise<void> {
    // Optimistic removal — drop the moved files from the current view
    // immediately so the user sees instant feedback. fetchPage() will
    // reconcile (folder thumb_url and file_count for the target folder).
    const movedSet = new Set(ids);
    const snapshot = files.value;
    files.value = files.value.filter((f) => !movedSet.has(f.id));
    stats.value = {
        ...stats.value,
        file_count: Math.max(0, stats.value.file_count - ids.length),
    };

    try {
        const fd = new FormData();
        ids.forEach((id) => fd.append('ids[]', String(id)));
        if (targetFolderId !== null) {
            fd.append('folder_id', String(targetFolderId));
        }
        const res = await fetch('/admin/media/picker/files/move', {
            method: 'POST',
            headers: {
                Accept: 'application/json',
                'X-CSRF-TOKEN': csrfToken.value,
                'X-Requested-With': 'XMLHttpRequest',
            },
            credentials: 'same-origin',
            body: fd,
        });
        if (!res.ok) {
            // Roll back optimistic removal on failure.
            files.value = snapshot;
            stats.value = {
                ...stats.value,
                file_count: snapshot.length,
            };
            return;
        }
        await fetchPage();
    } catch {
        files.value = snapshot;
    }
}

async function deleteFolderFromCard(folder: PickerFolder): Promise<void> {
    if (
        !confirm(
            `Move folder "${folder.name}" to trash? Files inside are moved too. You can restore from Media › Trash.`,
        )
    ) {
        return;
    }
    try {
        const res = await fetch(`/admin/media/picker/folders/${folder.id}`, {
            method: 'DELETE',
            headers: {
                Accept: 'application/json',
                'X-CSRF-TOKEN': csrfToken.value,
                'X-Requested-With': 'XMLHttpRequest',
            },
            credentials: 'same-origin',
        });
        if (!res.ok) return;
        folders.value = folders.value.filter((f) => f.id !== folder.id);
        stats.value = {
            ...stats.value,
            folder_count: Math.max(0, stats.value.folder_count - 1),
        };
    } catch {
        /* swallow */
    }
}

function onFolderDrop(event: DragEvent, folder: PickerFolder): void {
    event.preventDefault();
    const id = draggedFileId.value;
    onDragEnd();
    if (id === null) return;
    moveFiles([id], folder.id);
}

function onUpDrop(event: DragEvent): void {
    event.preventDefault();
    const id = draggedFileId.value;
    onDragEnd();
    if (id === null) return;
    moveFiles([id], parentFolderId.value);
}

// Details modal state
const detailsOpen = ref(false);
const detailsFile = ref<PickerFile | null>(null);

function openDetails(file: PickerFile): void {
    detailsFile.value = file;
    detailsOpen.value = true;
}

function onDetailsNavigate(file: DetailFile): void {
    detailsFile.value = file as PickerFile;
}

function onDetailsUpdated(updated: DetailFile): void {
    // Splice the updated record back into the grid so UI stays in sync.
    const idx = files.value.findIndex((f) => f.id === updated.id);
    if (idx !== -1) {
        files.value[idx] = updated as PickerFile;
    }
    detailsFile.value = updated as PickerFile;
}

function onDetailsDeleted(deleted: DetailFile): void {
    files.value = files.value.filter((f) => f.id !== deleted.id);
    stats.value = {
        ...stats.value,
        file_count: Math.max(0, stats.value.file_count - 1),
    };
}

function onDetailsUse(file: DetailFile): void {
    const f = file as PickerFile;
    emit('pick', {
        path: f.path,
        url: f.url,
        name: f.alt_text ?? f.title ?? f.name,
        thumb_path: f.thumb_path,
        thumb_url: f.thumb_url,
    });
    open.value = false;
}

async function deleteFromCard(file: PickerFile): Promise<void> {
    if (
        !confirm(
            `Move "${file.name}" to trash? You can restore it from Media › Trash.`,
        )
    ) {
        return;
    }
    try {
        const res = await fetch(`/admin/media/picker/files/${file.id}`, {
            method: 'DELETE',
            headers: {
                Accept: 'application/json',
                'X-CSRF-TOKEN': csrfToken.value,
                'X-Requested-With': 'XMLHttpRequest',
            },
            credentials: 'same-origin',
        });
        if (!res.ok) return;
        files.value = files.value.filter((f) => f.id !== file.id);
        stats.value = {
            ...stats.value,
            file_count: Math.max(0, stats.value.file_count - 1),
        };
    } catch {
        /* swallow */
    }
}

// After upload completes, refetch and auto-open Details on the newest file
// so the user can fill in alt text / title / caption right away.
async function handleUploaded(): Promise<void> {
    const before = new Set(files.value.map((f) => f.id));
    await fetchPage();
    const fresh = files.value.find((f) => !before.has(f.id));
    if (fresh) {
        openDetails(fresh);
    }
}

const csrfToken = computed(() => {
    if (typeof document === 'undefined') return '';
    return (
        document.querySelector<HTMLMetaElement>('meta[name="csrf-token"]')
            ?.content ?? ''
    );
});

async function fetchPage(): Promise<void> {
    loading.value = true;
    try {
        const params = new URLSearchParams();
        if (search.value.trim() !== '') {
            params.set('q', search.value.trim());
        } else if (folderId.value !== null) {
            params.set('folder', String(folderId.value));
        }
        params.set('sort', sort.value);
        if (props.accept !== 'image') {
            // Image is the server default; only send the param when it differs
            // so URLs stay short for the common case.
            params.set('accept', props.accept);
        }

        const res = await fetch(`/admin/media/picker?${params.toString()}`, {
            headers: { Accept: 'application/json' },
            credentials: 'same-origin',
        });
        if (!res.ok) {
            throw new Error(`Picker fetch failed (${res.status})`);
        }
        const json = await res.json();
        files.value = json.files ?? [];
        folders.value = json.folders ?? [];
        stats.value = json.stats ?? { file_count: 0, folder_count: 0 };
        breadcrumb.value = json.breadcrumb ?? [];
        parentFolderId.value = json.parent_folder_id ?? null;
    } catch {
        files.value = [];
        folders.value = [];
        stats.value = { file_count: 0, folder_count: 0 };
    } finally {
        loading.value = false;
    }
}

watch(open, (isOpen) => {
    if (isOpen) {
        search.value = '';
        folderId.value = null;
        sort.value = 'newest';
        breadcrumb.value = [];
        parentFolderId.value = null;
        fetchPage();
    }
});

let searchTimer: ReturnType<typeof setTimeout> | null = null;
watch(search, () => {
    if (!open.value) return;
    if (searchTimer !== null) clearTimeout(searchTimer);
    searchTimer = setTimeout(() => fetchPage(), 250);
});

watch(sort, () => {
    if (open.value) fetchPage();
});

function enterFolder(f: PickerFolder): void {
    folderId.value = f.id;
    search.value = '';
    fetchPage();
}

function goUp(): void {
    folderId.value = parentFolderId.value;
    search.value = '';
    fetchPage();
}

function jumpToRoot(): void {
    folderId.value = null;
    search.value = '';
    fetchPage();
}

function jumpToCrumb(idx: number): void {
    folderId.value = breadcrumb.value[idx]?.id ?? null;
    search.value = '';
    fetchPage();
}

function pickFile(f: PickerFile): void {
    emit('pick', {
        path: f.path,
        url: f.url,
        name: f.name,
        thumb_path: f.thumb_path,
        thumb_url: f.thumb_url,
    });
    open.value = false;
}

function openTrash(): void {
    window.open('/admin/media/trash', '_blank', 'noopener');
}

function openNewFolder(): void {
    newFolderName.value = '';
    newFolderError.value = null;
    newFolderOpen.value = true;
}

async function submitNewFolder(): Promise<void> {
    const name = newFolderName.value.trim();
    if (name === '') {
        newFolderError.value = 'Name is required.';
        return;
    }
    creatingFolder.value = true;
    newFolderError.value = null;
    try {
        const fd = new FormData();
        fd.append('name', name);
        if (folderId.value !== null) {
            fd.append('parent_id', String(folderId.value));
        }
        const res = await fetch('/admin/media/picker/folders', {
            method: 'POST',
            headers: {
                Accept: 'application/json',
                'X-CSRF-TOKEN': csrfToken.value,
                'X-Requested-With': 'XMLHttpRequest',
            },
            credentials: 'same-origin',
            body: fd,
        });
        if (!res.ok) {
            const body = await res.json().catch(() => null);
            throw new Error(body?.message ?? `Create failed (${res.status})`);
        }
        newFolderOpen.value = false;
        await fetchPage();
    } catch (e) {
        newFolderError.value = e instanceof Error ? e.message : 'Create failed';
    } finally {
        creatingFolder.value = false;
    }
}

function readableSize(bytes: number): string {
    if (bytes < 1024) return `${bytes} B`;
    if (bytes < 1024 * 1024) return `${(bytes / 1024).toFixed(1)} KB`;
    return `${(bytes / (1024 * 1024)).toFixed(1)} MB`;
}

const folderLabel = computed(() =>
    breadcrumb.value.length > 0
        ? breadcrumb.value[breadcrumb.value.length - 1].name
        : 'All media',
);
</script>

<template>
    <Dialog v-model:open="open">
        <DialogContent
            class="flex h-[92vh] w-[95vw] max-w-[1280px] flex-col gap-0 overflow-hidden p-0 sm:max-w-[1280px]"
        >
            <!-- Header -->
            <DialogHeader
                class="flex flex-row items-start justify-between gap-3 border-b px-6 pt-6 pb-4"
            >
                <div class="space-y-1">
                    <DialogTitle class="flex items-center gap-2 text-base">
                        <ImageIcon class="size-5 text-primary" />
                        Pick image — {{ folderLabel }}
                    </DialogTitle>
                    <DialogDescription class="text-xs">
                        Browse the media library or upload a new file. Click any
                        image to use it as the featured image.
                    </DialogDescription>
                </div>
                <div class="flex items-center gap-2">
                    <div class="relative">
                        <Search
                            class="pointer-events-none absolute top-1/2 left-2.5 size-4 -translate-y-1/2 text-muted-foreground"
                        />
                        <Input
                            v-model="search"
                            type="search"
                            placeholder="Search files…"
                            class="h-9 w-56 pl-8"
                        />
                    </div>
                    <Button
                        type="button"
                        variant="outline"
                        size="sm"
                        @click="openNewFolder"
                    >
                        <FolderPlus class="size-4" />
                        New folder
                    </Button>
                    <DropdownMenu>
                        <DropdownMenuTrigger as-child>
                            <Button type="button" variant="outline" size="sm">
                                <ArrowUpDown class="size-4" />
                                {{ SORT_LABELS[sort] }}
                            </Button>
                        </DropdownMenuTrigger>
                        <DropdownMenuContent align="end">
                            <DropdownMenuItem @click="sort = 'newest'">
                                Newest
                            </DropdownMenuItem>
                            <DropdownMenuItem @click="sort = 'oldest'">
                                Oldest
                            </DropdownMenuItem>
                            <DropdownMenuItem @click="sort = 'name'">
                                Name A–Z
                            </DropdownMenuItem>
                            <DropdownMenuItem @click="sort = 'name_desc'">
                                Name Z–A
                            </DropdownMenuItem>
                        </DropdownMenuContent>
                    </DropdownMenu>
                    <Button
                        type="button"
                        variant="outline"
                        size="sm"
                        @click="openTrash"
                    >
                        <Trash2 class="size-4" />
                        Trash
                    </Button>
                </div>
            </DialogHeader>

            <!-- Stats + breadcrumb row -->
            <div
                class="flex flex-wrap items-center justify-between gap-3 border-b bg-muted/30 px-6 py-2 text-xs"
            >
                <div class="flex items-center gap-4">
                    <span class="inline-flex items-center gap-1.5">
                        <span class="size-2 rounded-full bg-emerald-500" />
                        {{ stats.file_count }} file<span
                            v-if="stats.file_count !== 1"
                            >s</span
                        >
                    </span>
                    <span class="inline-flex items-center gap-1.5">
                        <span class="size-2 rounded-full bg-amber-500" />
                        {{ stats.folder_count }} folder<span
                            v-if="stats.folder_count !== 1"
                            >s</span
                        >
                    </span>
                    <Loader2
                        v-if="loading"
                        class="size-3 animate-spin text-muted-foreground"
                    />
                </div>

                <nav
                    class="flex flex-wrap items-center gap-1 text-muted-foreground"
                >
                    <button
                        type="button"
                        class="inline-flex items-center gap-1 hover:text-foreground"
                        @click="jumpToRoot"
                    >
                        <Home class="size-3" />
                        All media
                    </button>
                    <template
                        v-for="(crumb, idx) in breadcrumb"
                        :key="crumb.id"
                    >
                        <span>/</span>
                        <button
                            type="button"
                            class="hover:text-foreground"
                            :class="
                                idx === breadcrumb.length - 1
                                    ? 'font-medium text-foreground'
                                    : ''
                            "
                            @click="jumpToCrumb(idx)"
                        >
                            {{ crumb.name }}
                        </button>
                    </template>
                </nav>
            </div>

            <!-- Body -->
            <div class="flex-1 overflow-y-auto px-6 py-4">
                <Uploader
                    :folder-id="folderId"
                    :on-uploaded="handleUploaded"
                    class="mb-5"
                />

                <div
                    v-if="folderId !== null && search.trim() === ''"
                    class="mb-3"
                >
                    <Button
                        type="button"
                        variant="ghost"
                        size="sm"
                        :class="
                            dropTargetFolderId === 'up'
                                ? 'border border-primary/60 bg-primary/10'
                                : ''
                        "
                        @click="goUp"
                        @dragover="onUpDragOver"
                        @dragleave="onDragLeave"
                        @drop="onUpDrop"
                    >
                        <ChevronLeft class="size-4" />
                        Up one level
                        <span
                            v-if="dropTargetFolderId === 'up'"
                            class="ml-1 text-[10px] tracking-wider text-primary uppercase"
                            >Drop to move</span
                        >
                    </Button>
                </div>

                <section v-if="folders.length > 0" class="mb-6">
                    <h3
                        class="mb-2 text-xs font-medium tracking-wide text-muted-foreground uppercase"
                    >
                        Folders
                    </h3>
                    <div
                        class="grid grid-cols-2 gap-3 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5"
                    >
                        <div
                            v-for="folder in folders"
                            :key="folder.id"
                            class="group relative flex flex-col overflow-hidden rounded-xl border bg-card shadow-sm transition-all hover:-translate-y-0.5 hover:border-primary/60 hover:shadow-md"
                            :class="
                                dropTargetFolderId === folder.id
                                    ? 'border-primary ring-2 ring-primary/40'
                                    : 'border-border'
                            "
                            @dragover="onFolderDragOver($event, folder)"
                            @dragleave="onDragLeave"
                            @drop="onFolderDrop($event, folder)"
                        >
                            <!-- Hover actions: open + delete -->
                            <div
                                class="absolute top-2 right-2 z-10 flex items-center gap-1 opacity-0 transition-opacity group-hover:opacity-100"
                            >
                                <button
                                    type="button"
                                    class="cursor-pointer rounded-md border border-border bg-background/90 p-1.5 text-foreground shadow-sm hover:bg-accent"
                                    title="Open folder"
                                    @click.stop="enterFolder(folder)"
                                >
                                    <Eye class="size-3.5" />
                                </button>
                                <button
                                    type="button"
                                    class="cursor-pointer rounded-md border border-border bg-background/90 p-1.5 text-destructive shadow-sm hover:bg-destructive hover:text-destructive-foreground"
                                    title="Move folder to trash"
                                    @click.stop="deleteFolderFromCard(folder)"
                                >
                                    <Trash2 class="size-3.5" />
                                </button>
                            </div>

                            <button
                                type="button"
                                class="flex flex-1 cursor-pointer flex-col text-left focus-visible:outline-none"
                                @click="enterFolder(folder)"
                            >
                                <div
                                    class="flex aspect-[4/3] items-center justify-center overflow-hidden bg-amber-50 dark:bg-amber-950/30"
                                >
                                    <img
                                        v-if="folder.thumb_url"
                                        :src="folder.thumb_url"
                                        :alt="folder.name"
                                        class="size-full cursor-pointer object-cover"
                                        loading="lazy"
                                    />
                                    <Folder
                                        v-else
                                        class="size-12 text-amber-500"
                                    />
                                </div>
                                <div
                                    class="flex items-center justify-between gap-2 px-3 py-2"
                                >
                                    <span
                                        class="inline-flex items-center gap-1.5 truncate text-sm"
                                        :title="folder.name"
                                    >
                                        <Folder
                                            class="size-3.5 shrink-0 text-amber-500"
                                        />
                                        {{ folder.name }}
                                    </span>
                                    <span
                                        class="inline-flex h-5 min-w-5 items-center justify-center rounded-full bg-muted px-1.5 text-[10px] text-muted-foreground"
                                    >
                                        {{ folder.file_count }}
                                    </span>
                                </div>
                            </button>
                        </div>
                    </div>
                </section>

                <section>
                    <h3
                        class="mb-2 text-xs font-medium tracking-wide text-muted-foreground uppercase"
                    >
                        Files ({{ stats.file_count }})
                    </h3>

                    <div
                        v-if="files.length === 0 && !loading"
                        class="rounded-lg border border-dashed border-border p-12 text-center text-sm text-muted-foreground"
                    >
                        <p v-if="search.trim() !== ''">
                            No images match "{{ search }}".
                        </p>
                        <p v-else>
                            No files in this folder yet. Drop a file above to
                            upload.
                        </p>
                    </div>

                    <div
                        v-else
                        class="grid grid-cols-2 gap-3 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6"
                    >
                        <div
                            v-for="file in files"
                            :key="file.id"
                            class="group relative flex flex-col overflow-hidden rounded-xl border border-border bg-card shadow-sm transition-all hover:-translate-y-0.5 hover:border-primary hover:shadow-md"
                            :class="
                                draggedFileId === file.id ? 'opacity-40' : ''
                            "
                            draggable="true"
                            @dragstart="onFileDragStart($event, file)"
                            @dragend="onDragEnd"
                        >
                            <!-- Hover actions: preview + delete -->
                            <div
                                class="absolute top-2 right-2 z-10 flex items-center gap-1 opacity-0 transition-opacity group-hover:opacity-100"
                            >
                                <button
                                    type="button"
                                    class="cursor-pointer rounded-md border border-border bg-background/90 p-1.5 text-foreground shadow-sm hover:bg-accent"
                                    title="View details"
                                    @click.stop="openDetails(file)"
                                >
                                    <Eye class="size-3.5" />
                                </button>
                                <button
                                    type="button"
                                    class="cursor-pointer rounded-md border border-border bg-background/90 p-1.5 text-destructive shadow-sm hover:bg-destructive hover:text-destructive-foreground"
                                    title="Move to trash"
                                    @click.stop="deleteFromCard(file)"
                                >
                                    <Trash2 class="size-3.5" />
                                </button>
                            </div>

                            <button
                                type="button"
                                class="flex flex-1 cursor-pointer flex-col text-left focus-visible:outline-none"
                                @click="pickFile(file)"
                            >
                                <div
                                    class="flex aspect-square cursor-pointer items-center justify-center overflow-hidden bg-muted"
                                >
                                    <img
                                        v-if="file.thumb_url || file.url"
                                        :src="file.thumb_url || file.url"
                                        :alt="file.alt_text ?? file.name"
                                        class="size-full cursor-pointer object-cover transition-transform group-hover:scale-105"
                                        loading="lazy"
                                    />
                                    <ImageIcon
                                        v-else
                                        class="size-8 text-muted-foreground/50"
                                    />
                                </div>
                                <div class="flex flex-col gap-0.5 px-2.5 py-2">
                                    <p
                                        class="truncate text-xs font-medium"
                                        :title="file.title ?? file.name"
                                    >
                                        {{ file.title ?? file.name }}
                                    </p>
                                    <p
                                        class="text-[10px] text-muted-foreground"
                                    >
                                        {{ readableSize(file.size) }}
                                    </p>
                                </div>
                            </button>
                        </div>
                    </div>
                </section>
            </div>

            <!-- Footer -->
            <div
                class="flex items-center justify-end gap-2 border-t bg-muted/40 px-6 py-3"
            >
                <Button
                    type="button"
                    variant="outline"
                    size="sm"
                    @click="open = false"
                >
                    <X class="size-4" />
                    Close
                </Button>
            </div>
        </DialogContent>
    </Dialog>

    <!-- New folder sub-dialog -->
    <Dialog v-model:open="newFolderOpen">
        <DialogContent class="max-w-md">
            <DialogHeader>
                <DialogTitle>New folder</DialogTitle>
                <DialogDescription>
                    Create a folder inside
                    <strong>{{ folderLabel }}</strong
                    >.
                </DialogDescription>
            </DialogHeader>
            <div class="grid gap-2 pt-1">
                <Label for="new-folder-name">Folder name</Label>
                <Input
                    id="new-folder-name"
                    v-model="newFolderName"
                    placeholder="My folder"
                    autocomplete="off"
                    @keydown.enter.prevent="submitNewFolder"
                />
                <p v-if="newFolderError" class="text-xs text-destructive">
                    {{ newFolderError }}
                </p>
            </div>
            <DialogFooter>
                <Button
                    type="button"
                    variant="outline"
                    @click="newFolderOpen = false"
                >
                    Cancel
                </Button>
                <Button
                    type="button"
                    :disabled="creatingFolder"
                    @click="submitNewFolder"
                >
                    <Loader2
                        v-if="creatingFolder"
                        class="size-4 animate-spin"
                    />
                    <FolderPlus v-else class="size-4" />
                    Create
                </Button>
            </DialogFooter>
        </DialogContent>
    </Dialog>

    <!-- Media Details sub-modal -->
    <MediaDetailsModal
        v-model:open="detailsOpen"
        :file="detailsFile"
        :siblings="files"
        :selectable="true"
        @navigate="onDetailsNavigate"
        @updated="onDetailsUpdated"
        @deleted="onDetailsDeleted"
        @use="onDetailsUse"
    />
</template>
