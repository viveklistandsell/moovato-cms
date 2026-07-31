<script setup lang="ts">
import {
    Check,
    Copy,
    Download,
    Loader2,
    Save,
    Trash2,
} from 'lucide-vue-next';
import { computed, ref, watch } from 'vue';
import { Button } from '@/components/ui/button';
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import type { MediaFileItem } from './FileCard.vue';

const props = defineProps<{
    open: boolean;
    file: MediaFileItem | null;
}>();

const emit = defineEmits<{
    (e: 'update:open', value: boolean): void;
    (e: 'delete', file: MediaFileItem): void;
    /** Fired after a successful metadata save so the parent list can refresh. */
    (e: 'updated', file: MediaFileItem): void;
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

const previewImageFailed = ref(false);
watch(
    () => props.file?.id,
    () => {
        previewImageFailed.value = false;
    },
);

const SPREADSHEET_EXTS = ['xlsx', 'xls', 'csv', 'ods', 'tsv'];
const TEXT_EXTS = [
    'txt', 'md', 'json', 'log', 'xml', 'yml', 'yaml',
    'html', 'css', 'js', 'ts', 'php', 'py',
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

const previewBoxClass = computed(() => {
    if (!props.file) return 'min-h-[60vh]';
    if (isImage.value || isVideo.value || isPdf.value) {
        return 'min-h-[60vh]';
    }
    if (isSpreadsheet.value || isText.value) {
        return 'min-h-[40vh]';
    }
    return 'min-h-[40vh]';
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

        if (!response.ok) throw new Error('Failed to fetch file');

        const buffer = await response.arrayBuffer();
        const workbook = read(buffer, { type: 'array' });

        sheets.value = workbook.SheetNames.map((name) => ({
            name,
            html: utils.sheet_to_html(workbook.Sheets[name], { editable: false }),
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
        if (!response.ok) throw new Error('Failed to fetch file');
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

// ============================================================
// Metadata sidebar — mirrors the picker's MediaDetailsModal so the
// admin gets the same File Name / URL / Type / Size / Uploaded By /
// timestamps + editable Alt / Title / Caption / Description.
// ============================================================
const form = ref({
    name: '',
    alt_text: '',
    title: '',
    caption: '',
    description: '',
});

const saving = ref(false);
const saveError = ref<string | null>(null);
const copied = ref(false);

watch(
    () => props.file?.id,
    () => {
        if (!props.file) return;
        form.value = {
            name: props.file.name ?? '',
            alt_text: props.file.alt_text ?? '',
            title: props.file.title ?? '',
            caption: props.file.caption ?? '',
            description: props.file.description ?? '',
        };
        saveError.value = null;
        copied.value = false;
    },
    { immediate: true },
);

const csrfToken = computed(() => {
    if (typeof document === 'undefined') return '';
    return (
        document.querySelector<HTMLMetaElement>('meta[name="csrf-token"]')
            ?.content ?? ''
    );
});

const absoluteUrl = computed(() => {
    if (!props.file) return '';
    if (typeof window === 'undefined') return props.file.url;
    return `${window.location.origin}${props.file.url}`;
});

function copyUrl(): void {
    if (!props.file) return;
    if (typeof navigator === 'undefined' || !navigator.clipboard) return;
    navigator.clipboard
        .writeText(absoluteUrl.value)
        .then(() => {
            copied.value = true;
            window.setTimeout(() => (copied.value = false), 1500);
        })
        .catch(() => {
            /* clipboard write rejected — ignore */
        });
}

async function save(): Promise<void> {
    if (!props.file) return;
    saving.value = true;
    saveError.value = null;

    try {
        const fd = new FormData();
        fd.append('_method', 'PATCH');
        fd.append('name', form.value.name);
        fd.append('alt_text', form.value.alt_text);
        fd.append('title', form.value.title);
        fd.append('caption', form.value.caption);
        fd.append('description', form.value.description);

        const res = await fetch(
            `/admin/media/picker/files/${props.file.id}`,
            {
                method: 'POST',
                headers: {
                    Accept: 'application/json',
                    'X-CSRF-TOKEN': csrfToken.value,
                    'X-Requested-With': 'XMLHttpRequest',
                },
                credentials: 'same-origin',
                body: fd,
            },
        );

        if (!res.ok) {
            const body = await res.json().catch(() => null);
            throw new Error(body?.message ?? `Save failed (${res.status})`);
        }

        const json = await res.json();
        if (json.file) {
            emit('updated', json.file as MediaFileItem);
        }
    } catch (e) {
        saveError.value = e instanceof Error ? e.message : 'Save failed';
    } finally {
        saving.value = false;
    }
}

function readableSize(bytes: number): string {
    if (bytes < 1024) return `${bytes} B`;
    if (bytes < 1024 * 1024) return `${(bytes / 1024).toFixed(1)} KB`;
    return `${(bytes / (1024 * 1024)).toFixed(2)} MB`;
}

function formatTimestamp(iso: string | null | undefined): string {
    if (!iso) return '—';
    return new Date(iso).toLocaleString();
}
</script>

<template>
    <Dialog :open="open" @update:open="emit('update:open', $event)">
        <DialogContent
            class="flex h-[92vh] w-[95vw] max-w-[1280px] flex-col gap-0 overflow-hidden p-0 sm:max-w-[1280px]"
        >
            <DialogHeader
                class="flex flex-row items-start justify-between gap-3 border-b px-6 pt-6 pb-4"
            >
                <div class="space-y-1">
                    <DialogTitle class="text-base">Media Details</DialogTitle>
                    <DialogDescription class="text-xs">
                        Edit metadata, copy the public URL, download the
                        original, or remove the file.
                    </DialogDescription>
                </div>
            </DialogHeader>

            <div
                v-if="file"
                class="flex flex-1 flex-col gap-0 overflow-hidden md:flex-row"
            >
                <!-- LEFT: preview content (same as before — image / video / audio
                     / PDF / spreadsheet / text / fallback). -->
                <div
                    class="flex flex-1 items-center justify-center overflow-auto bg-muted/40 p-6"
                    :class="previewBoxClass"
                >
                    <div
                        v-if="isImage"
                        class="flex h-full w-full items-center justify-center"
                    >
                        <img
                            v-if="!previewImageFailed"
                            :src="file.medium_url ?? file.url"
                            :alt="file.alt_text ?? file.name"
                            class="max-h-[80vh] max-w-full rounded-md object-contain shadow-md"
                            @error="previewImageFailed = true"
                        />
                        <div
                            v-else
                            class="flex flex-col items-center justify-center gap-2 p-12 text-center text-sm text-muted-foreground"
                        >
                            <span class="text-base">Preview unavailable</span>
                            <span class="text-xs">
                                The file may have been moved or deleted on disk.
                            </span>
                        </div>
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
                    />

                    <div
                        v-else-if="isSpreadsheet"
                        class="flex w-full flex-col"
                    >
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
                            />
                        </template>
                    </div>

                    <div v-else-if="isText" class="flex w-full flex-col bg-white">
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
                            >{{ textContent }}</pre>
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

                <!-- RIGHT: metadata + editable form, identical shape to the
                     MediaPicker's MediaDetailsModal so admins see the same
                     panel regardless of where they opened the file from. -->
                <div
                    class="flex w-full shrink-0 flex-col gap-4 overflow-y-auto border-t bg-background p-6 md:w-[420px] md:border-t-0 md:border-l"
                >
                    <dl class="space-y-1.5 text-sm">
                        <div class="flex items-baseline gap-2">
                            <dt class="font-semibold">File Name:</dt>
                            <dd
                                class="truncate text-muted-foreground"
                                :title="file.name"
                            >
                                {{ file.name }}
                            </dd>
                        </div>
                        <div>
                            <dt class="font-semibold">File URL:</dt>
                            <dd class="mt-1">
                                <Input
                                    :model-value="absoluteUrl"
                                    readonly
                                    class="h-8 font-mono text-xs"
                                    @focus="
                                        (e: FocusEvent) =>
                                            (e.target as HTMLInputElement).select()
                                    "
                                />
                            </dd>
                        </div>
                        <div class="flex items-baseline gap-2">
                            <dt class="font-semibold">File Type:</dt>
                            <dd class="text-muted-foreground">
                                {{ (file.extension || file.mime_type).toUpperCase() }}
                            </dd>
                        </div>
                        <div class="flex items-baseline gap-2">
                            <dt class="font-semibold">File Size:</dt>
                            <dd class="text-muted-foreground">
                                {{ readableSize(file.size) }}
                            </dd>
                        </div>
                        <div class="flex items-baseline gap-2">
                            <dt class="font-semibold">Uploaded By:</dt>
                            <dd class="text-muted-foreground">
                                {{ file.uploader ?? '—' }}
                            </dd>
                        </div>
                        <div class="flex items-baseline gap-2">
                            <dt class="font-semibold">Created At:</dt>
                            <dd class="text-muted-foreground">
                                {{ formatTimestamp(file.created_at) }}
                            </dd>
                        </div>
                        <div class="flex items-baseline gap-2">
                            <dt class="font-semibold">Updated At:</dt>
                            <dd class="text-muted-foreground">
                                {{ formatTimestamp(file.updated_at) }}
                            </dd>
                        </div>
                    </dl>

                    <div class="flex flex-wrap items-center gap-2">
                        <Button as-child type="button" size="sm">
                            <a
                                :href="`/admin/media/files/${file.id}/download`"
                                class="inline-flex items-center gap-1.5"
                            >
                                <Download class="size-4" />
                                Download
                            </a>
                        </Button>
                        <Button
                            type="button"
                            variant="secondary"
                            size="sm"
                            @click="copyUrl"
                        >
                            <Check v-if="copied" class="size-4" />
                            <Copy v-else class="size-4" />
                            {{ copied ? 'Copied!' : 'Copy URL' }}
                        </Button>
                    </div>

                    <hr class="border-border" />

                    <div class="grid gap-3">
                        <div class="grid gap-1.5">
                            <Label for="fp-alt">Alt Text</Label>
                            <Input
                                id="fp-alt"
                                v-model="form.alt_text"
                                placeholder="Describe the image for screen readers"
                            />
                        </div>
                        <div class="grid gap-1.5">
                            <Label for="fp-title">Title</Label>
                            <Input
                                id="fp-title"
                                v-model="form.title"
                                placeholder="Image title"
                            />
                        </div>
                        <div class="grid gap-1.5">
                            <Label for="fp-caption">Caption</Label>
                            <Textarea
                                id="fp-caption"
                                v-model="form.caption"
                                :rows="2"
                                placeholder="Short caption shown beneath the image"
                            />
                        </div>
                        <div class="grid gap-1.5">
                            <Label for="fp-desc">Description</Label>
                            <Textarea
                                id="fp-desc"
                                v-model="form.description"
                                :rows="4"
                                placeholder="Longer description for SEO or notes"
                            />
                        </div>
                    </div>

                    <p
                        v-if="saveError"
                        class="rounded-md border border-destructive/30 bg-destructive/5 px-3 py-2 text-xs text-destructive"
                    >
                        {{ saveError }}
                    </p>
                </div>
            </div>

            <!-- Footer: destructive action on the left, save on the right —
                 same arrangement as MediaDetailsModal so the two screens feel
                 the same to operate. -->
            <div
                class="flex flex-wrap items-center justify-between gap-2 border-t bg-muted/40 px-6 py-3"
            >
                <Button
                    v-if="file"
                    type="button"
                    variant="destructive"
                    size="sm"
                    @click="emit('delete', file)"
                >
                    <Trash2 class="size-4" />
                    Delete
                </Button>
                <Button
                    v-if="file"
                    type="button"
                    :disabled="saving"
                    @click="save"
                >
                    <Loader2 v-if="saving" class="size-4 animate-spin" />
                    <Save v-else class="size-4" />
                    Save
                </Button>
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
