<script setup lang="ts">
import {
    Archive,
    Eye,
    File as FileIcon,
    FileAudio,
    FileSpreadsheet,
    FileText,
    FileType,
    FileVideo,
    Image as ImageIcon,
    MoreVertical,
    Presentation,
    Trash2,
} from 'lucide-vue-next';
import { computed } from 'vue';
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';

export type MediaFileItem = {
    id: number;
    name: string;
    original_name: string;
    mime_type: string;
    extension: string;
    size: number;
    url: string;
    thumb_url: string | null;
    medium_url: string | null;
    metadata: Record<string, unknown> | null;
    folder_id?: number | null;
    path?: string;
    created_at: string | null;
};

const props = defineProps<{
    file: MediaFileItem;
    selected?: boolean;
}>();

const emit = defineEmits<{
    (e: 'open', file: MediaFileItem): void;
    (e: 'toggle-select', file: MediaFileItem): void;
    (e: 'rename', file: MediaFileItem): void;
    (e: 'move', file: MediaFileItem): void;
    (e: 'delete', file: MediaFileItem): void;
}>();

type Kind =
    | 'image'
    | 'video'
    | 'audio'
    | 'pdf'
    | 'word'
    | 'excel'
    | 'powerpoint'
    | 'text'
    | 'archive'
    | 'other';

const kind = computed<Kind>(() => {
    const mime = props.file.mime_type ?? '';
    const ext = (props.file.extension ?? '').toLowerCase();

    if (mime.startsWith('image/')) return 'image';
    if (mime.startsWith('video/')) return 'video';
    if (mime.startsWith('audio/')) return 'audio';
    if (mime.includes('pdf') || ext === 'pdf') return 'pdf';
    if (
        ['doc', 'docx', 'odt', 'rtf'].includes(ext) ||
        mime.includes('msword') ||
        mime.includes('wordprocessingml')
    ) {
        return 'word';
    }
    if (
        ['xls', 'xlsx', 'csv', 'tsv', 'ods'].includes(ext) ||
        mime.includes('spreadsheet') ||
        mime === 'text/csv'
    ) {
        return 'excel';
    }
    if (
        ['ppt', 'pptx', 'odp', 'key'].includes(ext) ||
        mime.includes('presentation')
    ) {
        return 'powerpoint';
    }
    if (['zip', 'tar', 'gz', '7z', 'rar'].includes(ext)) return 'archive';
    if (
        mime.startsWith('text/') ||
        [
            'md',
            'txt',
            'json',
            'log',
            'xml',
            'yml',
            'yaml',
            'js',
            'ts',
            'css',
            'html',
            'php',
            'py',
        ].includes(ext)
    ) {
        return 'text';
    }
    return 'other';
});

const tileClasses = computed(() => {
    switch (kind.value) {
        case 'image':
            return 'bg-emerald-50 text-emerald-600 dark:bg-emerald-950/40 dark:text-emerald-400';
        case 'video':
            return 'bg-violet-50 text-violet-600 dark:bg-violet-950/40 dark:text-violet-400';
        case 'audio':
            return 'bg-pink-50 text-pink-600 dark:bg-pink-950/40 dark:text-pink-400';
        case 'pdf':
            return 'bg-red-50 text-red-600 dark:bg-red-950/40 dark:text-red-400';
        case 'word':
            return 'bg-blue-50 text-blue-600 dark:bg-blue-950/40 dark:text-blue-400';
        case 'excel':
            return 'bg-green-50 text-green-700 dark:bg-green-950/40 dark:text-green-400';
        case 'powerpoint':
            return 'bg-orange-50 text-orange-600 dark:bg-orange-950/40 dark:text-orange-400';
        case 'archive':
            return 'bg-amber-50 text-amber-700 dark:bg-amber-950/40 dark:text-amber-400';
        case 'text':
            return 'bg-slate-100 text-slate-600 dark:bg-slate-800 dark:text-slate-300';
        default:
            return 'bg-muted text-muted-foreground';
    }
});

const badgeClasses = computed(() => {
    switch (kind.value) {
        case 'image':
            return 'bg-emerald-600';
        case 'video':
            return 'bg-violet-600';
        case 'audio':
            return 'bg-pink-600';
        case 'pdf':
            return 'bg-red-600';
        case 'word':
            return 'bg-blue-600';
        case 'excel':
            return 'bg-green-600';
        case 'powerpoint':
            return 'bg-orange-600';
        case 'archive':
            return 'bg-amber-600';
        case 'text':
            return 'bg-slate-500';
        default:
            return 'bg-muted-foreground';
    }
});

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
    <div
        class="group relative flex flex-col overflow-hidden rounded-xl border border-border bg-card text-left shadow-sm transition-all hover:-translate-y-0.5 hover:border-primary/60 hover:shadow-md"
        :class="{ 'border-primary ring-2 ring-primary': selected }"
    >
        <label
            class="absolute top-2 left-2 z-10 flex h-6 w-6 cursor-pointer items-center justify-center rounded-md border border-border bg-background/80 text-foreground shadow-sm transition-opacity"
            :class="
                selected ? 'opacity-100' : 'opacity-0 group-hover:opacity-100'
            "
            @click.stop
        >
            <input
                type="checkbox"
                class="h-3.5 w-3.5 cursor-pointer accent-primary"
                :checked="selected"
                @change="emit('toggle-select', file)"
            />
        </label>

        <div
            class="absolute top-2 right-2 z-10 flex items-center gap-1 opacity-0 transition-opacity group-hover:opacity-100"
        >
            <button
                type="button"
                class="rounded-md border border-border bg-background/90 p-1.5 text-foreground shadow-sm hover:bg-accent"
                title="Preview"
                @click.stop="emit('open', file)"
            >
                <Eye class="h-3.5 w-3.5" />
            </button>
            <button
                type="button"
                class="rounded-md border border-border bg-background/90 p-1.5 text-destructive shadow-sm hover:bg-destructive hover:text-destructive-foreground"
                title="Delete"
                @click.stop="emit('delete', file)"
            >
                <Trash2 class="h-3.5 w-3.5" />
            </button>
            <DropdownMenu>
                <DropdownMenuTrigger
                    class="rounded-md border border-border bg-background/90 p-1.5 text-foreground shadow-sm hover:bg-accent"
                    title="More"
                    @click.stop
                >
                    <MoreVertical class="h-3.5 w-3.5" />
                </DropdownMenuTrigger>
                <DropdownMenuContent align="end">
                    <DropdownMenuItem @click="emit('open', file)">
                        Preview
                    </DropdownMenuItem>
                    <DropdownMenuItem @click="emit('rename', file)">
                        Rename
                    </DropdownMenuItem>
                    <DropdownMenuItem @click="emit('move', file)">
                        Move…
                    </DropdownMenuItem>
                    <DropdownMenuItem
                        class="text-destructive focus:text-destructive"
                        @click="emit('delete', file)"
                    >
                        Delete
                    </DropdownMenuItem>
                </DropdownMenuContent>
            </DropdownMenu>
        </div>

        <button
            type="button"
            class="flex flex-1 flex-col text-left focus-visible:outline-none"
            @click="emit('open', file)"
        >
            <div
                class="relative flex aspect-square items-center justify-center transition-colors"
                :class="tileClasses"
            >
                <img
                    v-if="kind === 'image' && file.thumb_url"
                    :src="file.thumb_url"
                    :alt="file.name"
                    class="h-full w-full object-cover"
                    loading="lazy"
                />
                <ImageIcon v-else-if="kind === 'image'" class="h-12 w-12" />
                <FileVideo v-else-if="kind === 'video'" class="h-12 w-12" />
                <FileAudio v-else-if="kind === 'audio'" class="h-12 w-12" />
                <FileText v-else-if="kind === 'pdf'" class="h-12 w-12" />
                <FileType v-else-if="kind === 'word'" class="h-12 w-12" />
                <FileSpreadsheet
                    v-else-if="kind === 'excel'"
                    class="h-12 w-12"
                />
                <Presentation
                    v-else-if="kind === 'powerpoint'"
                    class="h-12 w-12"
                />
                <Archive v-else-if="kind === 'archive'" class="h-12 w-12" />
                <FileText v-else-if="kind === 'text'" class="h-12 w-12" />
                <FileIcon v-else class="h-12 w-12" />

                <span
                    class="absolute right-2 bottom-2 rounded px-1.5 py-0.5 text-[10px] font-semibold tracking-wide text-white uppercase shadow-sm"
                    :class="badgeClasses"
                >
                    {{
                        file.extension || file.mime_type.split('/')[1] || 'file'
                    }}
                </span>
            </div>

            <div class="flex flex-col gap-0.5 px-3 py-2.5">
                <p
                    class="truncate text-sm font-medium text-foreground"
                    :title="file.name"
                >
                    {{ file.name }}
                </p>
                <p class="text-[11px] text-muted-foreground">
                    {{ readableSize(file.size) }}
                </p>
            </div>
        </button>
    </div>
</template>
