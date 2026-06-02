<script setup lang="ts">
import { computed, ref, watch } from 'vue';
import {
    Dialog,
    DialogContent,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';
import { Button } from '@/components/ui/button';
import { Download, Trash2 } from 'lucide-vue-next';
import type { MediaFileItem } from './FileCard.vue';

const props = defineProps<{
    open: boolean;
    file: MediaFileItem | null;
}>();

const emit = defineEmits<{
    (e: 'update:open', value: boolean): void;
    (e: 'delete', file: MediaFileItem): void;
}>();

const isImage = computed(
    () => props.file?.mime_type.startsWith('image/') ?? false,
);
const isVideo = computed(
    () => props.file?.mime_type.startsWith('video/') ?? false,
);
const isAudio = computed(
    () => props.file?.mime_type.startsWith('audio/') ?? false,
);
const isPdf = computed(() => props.file?.mime_type.includes('pdf') ?? false);

const SPREADSHEET_EXTS = ['xlsx', 'xls', 'csv', 'ods', 'tsv'];
const TEXT_EXTS = [
    'txt',
    'md',
    'json',
    'log',
    'xml',
    'yml',
    'yaml',
    'html',
    'css',
    'js',
    'ts',
    'php',
    'py',
];

const isSpreadsheet = computed(() => {
    const ext = (props.file?.extension ?? '').toLowerCase();
    return (
        SPREADSHEET_EXTS.includes(ext) ||
        (props.file?.mime_type.includes('spreadsheet') ?? false) ||
        props.file?.mime_type === 'text/csv'
    );
});

const isText = computed(() => {
    const ext = (props.file?.extension ?? '').toLowerCase();
    return (
        TEXT_EXTS.includes(ext) ||
        (props.file?.mime_type.startsWith('text/') ?? false)
    );
});

const dialogSizeClass = computed(() => {
    if (!props.file) return 'sm:max-w-md';
    if (isImage.value || isVideo.value || isPdf.value) {
        return 'sm:max-w-[95vw] md:max-w-[90vw] lg:max-w-[85vw]';
    }
    if (isSpreadsheet.value) {
        return 'sm:max-w-[95vw] md:max-w-6xl';
    }
    if (isText.value) {
        return 'sm:max-w-3xl';
    }
    if (isAudio.value) {
        return 'sm:max-w-lg';
    }
    return 'sm:max-w-md';
});

const previewBoxClass = computed(() => {
    if (!props.file) return 'max-h-[60vh]';
    if (isImage.value || isVideo.value || isPdf.value) {
        return 'min-h-[60vh] max-h-[80vh]';
    }
    if (isSpreadsheet.value || isText.value) {
        return 'min-h-[40vh] max-h-[70vh]';
    }
    return 'max-h-[50vh]';
});

const sheets = ref<{ name: string; html: string }[]>([]);
const activeSheetIndex = ref(0);
const textContent = ref('');
const previewLoading = ref(false);
const previewError = ref<string | null>(null);

watch(
    [() => props.open, () => props.file?.id],
    ([open, id]) => {
        if (!open || !id) {
            sheets.value = [];
            textContent.value = '';
            previewError.value = null;
            return;
        }

        if (isSpreadsheet.value) {
            void loadSpreadsheet();
        } else if (isText.value && !isSpreadsheet.value) {
            void loadText();
        }
    },
    { immediate: true },
);

async function loadSpreadsheet(): Promise<void> {
    if (!props.file) return;
    previewLoading.value = true;
    previewError.value = null;
    sheets.value = [];

    try {
        const [{ read, utils }, response] = await Promise.all([
            import('xlsx'),
            fetch(props.file.url),
        ]);

        if (!response.ok) {
            throw new Error('Failed to fetch file');
        }

        const buffer = await response.arrayBuffer();
        const workbook = read(buffer, { type: 'array' });

        sheets.value = workbook.SheetNames.map((name) => ({
            name,
            html: utils.sheet_to_html(workbook.Sheets[name], {
                editable: false,
            }),
        }));
        activeSheetIndex.value = 0;
    } catch (e) {
        previewError.value = e instanceof Error ? e.message : 'Preview failed';
    } finally {
        previewLoading.value = false;
    }
}

async function loadText(): Promise<void> {
    if (!props.file) return;
    previewLoading.value = true;
    previewError.value = null;
    textContent.value = '';

    try {
        const response = await fetch(props.file.url);
        if (!response.ok) {
            throw new Error('Failed to fetch file');
        }
        const text = await response.text();
        textContent.value =
            text.length > 100_000
                ? text.slice(0, 100_000) + '\n\n…(truncated)'
                : text;
    } catch (e) {
        previewError.value = e instanceof Error ? e.message : 'Preview failed';
    } finally {
        previewLoading.value = false;
    }
}
</script>

<template>
    <Dialog :open="open" @update:open="emit('update:open', $event)">
        <DialogContent :class="dialogSizeClass">
            <DialogHeader>
                <DialogTitle class="truncate">
                    {{ file?.name }}
                </DialogTitle>
            </DialogHeader>

            <div
                v-if="file"
                class="flex flex-col overflow-auto rounded bg-muted/40"
                :class="previewBoxClass"
            >
                <div
                    v-if="isImage"
                    class="flex flex-1 items-center justify-center"
                >
                    <img
                        :src="file.medium_url ?? file.url"
                        :alt="file.name"
                        class="max-h-[80vh] max-w-full object-contain"
                    />
                </div>
                <video
                    v-else-if="isVideo"
                    :src="file.url"
                    controls
                    class="max-h-[80vh] w-full"
                />
                <audio
                    v-else-if="isAudio"
                    :src="file.url"
                    controls
                    class="w-full p-6"
                />
                <iframe
                    v-else-if="isPdf"
                    :src="file.url"
                    class="h-[80vh] min-h-[60vh] w-full flex-1 bg-white"
                ></iframe>

                <div v-else-if="isSpreadsheet" class="flex flex-col">
                    <p
                        v-if="previewLoading"
                        class="p-6 text-center text-sm text-muted-foreground"
                    >
                        Loading spreadsheet…
                    </p>
                    <p
                        v-else-if="previewError"
                        class="p-6 text-center text-sm text-destructive"
                    >
                        Could not preview: {{ previewError }}
                    </p>
                    <template v-else-if="sheets.length > 0">
                        <div
                            v-if="sheets.length > 1"
                            class="flex flex-wrap gap-1 border-b border-border bg-background p-2"
                        >
                            <button
                                v-for="(sheet, idx) in sheets"
                                :key="sheet.name"
                                type="button"
                                class="rounded px-3 py-1 text-xs font-medium"
                                :class="
                                    idx === activeSheetIndex
                                        ? 'bg-primary text-primary-foreground'
                                        : 'hover:bg-accent'
                                "
                                @click="activeSheetIndex = idx"
                            >
                                {{ sheet.name }}
                            </button>
                        </div>
                        <div
                            class="spreadsheet-preview overflow-auto bg-white p-4 text-sm text-black"
                            v-html="sheets[activeSheetIndex]?.html ?? ''"
                        ></div>
                    </template>
                </div>

                <div v-else-if="isText" class="flex flex-col bg-white">
                    <p
                        v-if="previewLoading"
                        class="p-6 text-center text-sm text-muted-foreground"
                    >
                        Loading…
                    </p>
                    <p
                        v-else-if="previewError"
                        class="p-6 text-center text-sm text-destructive"
                    >
                        Could not preview: {{ previewError }}
                    </p>
                    <pre
                        v-else
                        class="overflow-auto p-4 font-mono text-xs break-words whitespace-pre-wrap text-black"
                        >{{ textContent }}</pre
                    >
                </div>

                <div
                    v-else
                    class="flex flex-col items-center gap-2 p-12 text-sm text-muted-foreground"
                >
                    <p>
                        No preview available for
                        <strong>{{ file.mime_type }}</strong>
                    </p>
                    <p>Download to view.</p>
                </div>
            </div>

            <div
                v-if="file"
                class="flex items-center justify-between gap-2 border-t border-border pt-3 text-sm"
            >
                <p class="text-muted-foreground">
                    {{ file.mime_type }} ·
                    {{ Math.round(file.size / 1024).toLocaleString() }}&nbsp;KB
                </p>
                <div class="flex items-center gap-2">
                    <Button as-child variant="outline" size="sm">
                        <a
                            :href="`/admin/media/files/${file.id}/download`"
                            class="inline-flex items-center gap-1.5"
                        >
                            <Download class="h-4 w-4" />
                            Download
                        </a>
                    </Button>
                    <Button
                        type="button"
                        variant="destructive"
                        size="sm"
                        @click="emit('delete', file)"
                    >
                        <Trash2 class="h-4 w-4" />
                        Delete
                    </Button>
                </div>
            </div>
        </DialogContent>
    </Dialog>
</template>

<style scoped>
.spreadsheet-preview :deep(table) {
    border-collapse: collapse;
    width: max-content;
    min-width: 100%;
}
.spreadsheet-preview :deep(th),
.spreadsheet-preview :deep(td) {
    border: 1px solid #e5e7eb;
    padding: 4px 8px;
    text-align: left;
    vertical-align: top;
    white-space: nowrap;
}
.spreadsheet-preview :deep(tr:first-child td),
.spreadsheet-preview :deep(thead td) {
    background: #f9fafb;
    font-weight: 600;
}
</style>
