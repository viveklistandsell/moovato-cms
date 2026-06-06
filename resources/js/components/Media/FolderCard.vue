<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { Eye, Folder, MoreVertical, Trash2 } from 'lucide-vue-next';
import { ref } from 'vue';
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';

export type MediaFolderItem = {
    id: number;
    name: string;
    parent_id: number | null;
    path: string;
    thumb_url?: string | null;
    file_count?: number;
    subfolder_count?: number;
};

const props = defineProps<{
    folder: MediaFolderItem;
    selected?: boolean;
    isDropTarget?: boolean;
    droppable?: boolean;
}>();

const emit = defineEmits<{
    (e: 'rename', folder: MediaFolderItem): void;
    (e: 'move', folder: MediaFolderItem): void;
    (e: 'delete', folder: MediaFolderItem): void;
    (e: 'toggle-select', folder: MediaFolderItem): void;
    (e: 'dragover', folder: MediaFolderItem, event: DragEvent): void;
    (e: 'dragleave', folder: MediaFolderItem): void;
    (e: 'drop', folder: MediaFolderItem, event: DragEvent): void;
}>();

const coverFailed = ref(false);

function onDragOver(event: DragEvent): void {
    if (props.droppable === false) return;
    emit('dragover', props.folder, event);
}

function onDragLeave(): void {
    if (props.droppable === false) return;
    emit('dragleave', props.folder);
}

function onDrop(event: DragEvent): void {
    if (props.droppable === false) return;
    emit('drop', props.folder, event);
}
</script>

<template>
    <div
        class="group relative flex flex-col overflow-hidden rounded-xl border border-border bg-card shadow-sm transition-all hover:-translate-y-0.5 hover:border-primary/60 hover:shadow-md"
        :class="{
            'border-primary ring-2 ring-primary': selected,
            '!border-emerald-500 ring-2 ring-emerald-500 scale-[1.02]':
                isDropTarget,
        }"
        @dragover="onDragOver"
        @dragleave="onDragLeave"
        @drop="onDrop"
    >
        <label
            class="absolute left-2 top-2 z-10 flex h-6 w-6 cursor-pointer items-center justify-center rounded-md border border-border bg-background/80 text-foreground shadow-sm transition-opacity"
            :class="
                selected ? 'opacity-100' : 'opacity-0 group-hover:opacity-100'
            "
            @click.stop
        >
            <input
                type="checkbox"
                class="h-3.5 w-3.5 cursor-pointer accent-primary"
                :checked="selected"
                @change="emit('toggle-select', folder)"
            />
        </label>

        <Link
            :href="`/admin/media?folder=${folder.id}`"
            class="flex flex-1 flex-col"
        >
            <div
                class="relative flex aspect-video items-center justify-center bg-amber-50 dark:bg-amber-950/30"
            >
                <img
                    v-if="folder.thumb_url && !coverFailed"
                    :src="folder.thumb_url"
                    :alt="folder.name"
                    class="h-full w-full object-cover"
                    loading="lazy"
                    @error="coverFailed = true"
                />
                <Folder
                    v-else
                    class="h-12 w-12 text-amber-500"
                    :stroke-width="1.5"
                />
            </div>
            <div class="flex items-center gap-2 px-3 py-2.5">
                <Folder class="h-4 w-4 shrink-0 text-amber-500" />
                <span
                    class="flex-1 truncate text-sm font-medium text-foreground"
                >
                    {{ folder.name }}
                </span>
                <span
                    v-if="
                        (folder.file_count ?? 0) +
                            (folder.subfolder_count ?? 0) >
                        0
                    "
                    class="shrink-0 rounded-full bg-muted px-2 py-0.5 text-[10px] font-medium text-muted-foreground"
                >
                    {{
                        (folder.file_count ?? 0) +
                        (folder.subfolder_count ?? 0)
                    }}
                </span>
            </div>
        </Link>

        <div
            class="absolute right-2 top-2 flex items-center gap-1 opacity-0 transition-opacity group-hover:opacity-100"
        >
            <Link
                :href="`/admin/media?folder=${folder.id}`"
                class="rounded-md border border-border bg-background/90 p-1.5 text-foreground shadow-sm hover:bg-accent"
                title="Open folder"
            >
                <Eye class="h-3.5 w-3.5" />
            </Link>
            <button
                type="button"
                class="rounded-md border border-border bg-background/90 p-1.5 text-destructive shadow-sm hover:bg-destructive hover:text-destructive-foreground"
                title="Delete folder"
                @click.stop="emit('delete', folder)"
            >
                <Trash2 class="h-3.5 w-3.5" />
            </button>
            <DropdownMenu>
                <DropdownMenuTrigger
                    class="rounded-md border border-border bg-background/90 p-1.5 text-foreground shadow-sm hover:bg-accent"
                    title="More"
                >
                    <MoreVertical class="h-3.5 w-3.5" />
                </DropdownMenuTrigger>
                <DropdownMenuContent align="end">
                    <DropdownMenuItem @click="emit('rename', folder)">
                        Rename
                    </DropdownMenuItem>
                    <DropdownMenuItem @click="emit('move', folder)">
                        Move…
                    </DropdownMenuItem>
                    <DropdownMenuItem
                        class="text-destructive focus:text-destructive"
                        @click="emit('delete', folder)"
                    >
                        Delete
                    </DropdownMenuItem>
                </DropdownMenuContent>
            </DropdownMenu>
        </div>
    </div>
</template>
