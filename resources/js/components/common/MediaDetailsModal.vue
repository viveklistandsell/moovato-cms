<script setup lang="ts">
import {
    Check,
    ChevronLeft,
    ChevronRight,
    Copy,
    Download,
    Loader2,
    Save,
    Trash2,
    X,
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

export type PickerFile = {
    id: number;
    name: string;
    original_name: string;
    path: string;
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

const open = defineModel<boolean>('open', { required: true });

const props = defineProps<{
    /** The currently-focused file. */
    file: PickerFile | null;
    /** When provided, enables the prev/next nav arrows. */
    siblings?: PickerFile[];
    /** Show the "Use this image" button (picker mode). Defaults to true. */
    selectable?: boolean;
}>();

const emit = defineEmits<{
    (e: 'navigate', file: PickerFile): void;
    (e: 'updated', file: PickerFile): void;
    (e: 'deleted', file: PickerFile): void;
    (e: 'use', file: PickerFile): void;
}>();

const selectable = computed(() => props.selectable !== false);

// Editable buffer
const form = ref({
    name: '',
    alt_text: '',
    title: '',
    caption: '',
    description: '',
});

const saving = ref(false);
const deleting = ref(false);
const copied = ref(false);
const saveError = ref<string | null>(null);

watch(
    () => props.file?.id,
    () => {
        if (props.file === null) return;
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

const currentIndex = computed(() => {
    if (props.file === null || !props.siblings) return -1;
    return props.siblings.findIndex((s) => s.id === props.file!.id);
});

const hasPrev = computed(() => currentIndex.value > 0);
const hasNext = computed(
    () =>
        props.siblings !== undefined
        && currentIndex.value !== -1
        && currentIndex.value < props.siblings.length - 1,
);

function navPrev(): void {
    if (!hasPrev.value || !props.siblings) return;
    emit('navigate', props.siblings[currentIndex.value - 1]);
}

function navNext(): void {
    if (!hasNext.value || !props.siblings) return;
    emit('navigate', props.siblings[currentIndex.value + 1]);
}

const csrfToken = computed(() => {
    if (typeof document === 'undefined') return '';
    return (
        document
            .querySelector<HTMLMetaElement>('meta[name="csrf-token"]')
            ?.content ?? ''
    );
});

const absoluteUrl = computed(() => {
    if (props.file === null) return '';
    // Build a fully-qualified URL so it's useful to copy/share outside the
    // current page context.
    if (typeof window === 'undefined') return props.file.url;
    return `${window.location.origin}${props.file.url}`;
});

function copyUrl(): void {
    if (props.file === null) return;
    if (typeof navigator === 'undefined' || !navigator.clipboard) return;
    navigator.clipboard
        .writeText(absoluteUrl.value)
        .then(() => {
            copied.value = true;
            setTimeout(() => (copied.value = false), 1500);
        })
        .catch(() => {
            /* ignore */
        });
}

function downloadFile(): void {
    if (props.file === null) return;
    const a = document.createElement('a');
    a.href = props.file.url;
    a.download = props.file.original_name || props.file.name;
    a.rel = 'noopener';
    document.body.appendChild(a);
    a.click();
    a.remove();
}

async function save(): Promise<void> {
    if (props.file === null) return;
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
            emit('updated', json.file as PickerFile);
        }
    } catch (e) {
        saveError.value = e instanceof Error ? e.message : 'Save failed';
    } finally {
        saving.value = false;
    }
}

async function deletePermanently(): Promise<void> {
    if (props.file === null) return;
    if (
        !confirm(
            `Delete "${props.file.name}" permanently? This cannot be undone.`,
        )
    ) {
        return;
    }
    deleting.value = true;
    try {
        const res = await fetch(
            `/admin/media/picker/files/${props.file.id}/force`,
            {
                method: 'DELETE',
                headers: {
                    Accept: 'application/json',
                    'X-CSRF-TOKEN': csrfToken.value,
                    'X-Requested-With': 'XMLHttpRequest',
                },
                credentials: 'same-origin',
            },
        );
        if (!res.ok) {
            throw new Error(`Delete failed (${res.status})`);
        }
        emit('deleted', props.file);
        open.value = false;
    } catch (e) {
        saveError.value = e instanceof Error ? e.message : 'Delete failed';
    } finally {
        deleting.value = false;
    }
}

function use(): void {
    if (props.file === null) return;
    emit('use', props.file);
    open.value = false;
}

function readableSize(bytes: number): string {
    if (bytes < 1024) return `${bytes} B`;
    if (bytes < 1024 * 1024) return `${(bytes / 1024).toFixed(1)} KB`;
    return `${(bytes / (1024 * 1024)).toFixed(2)} MB`;
}

function formatTimestamp(iso: string | null | undefined): string {
    if (!iso) return '—';
    const d = new Date(iso);
    return d.toLocaleString();
}
</script>

<template>
    <Dialog v-model:open="open">
        <DialogContent
            class="flex h-[92vh] w-[95vw] max-w-[1280px] flex-col gap-0 overflow-hidden p-0 sm:max-w-[1280px]"
        >
            <DialogHeader
                class="flex flex-row items-start justify-between gap-3 border-b px-6 pb-4 pt-6"
            >
                <div class="space-y-1">
                    <DialogTitle class="text-base">Media Details</DialogTitle>
                    <DialogDescription class="text-xs">
                        Edit metadata, copy the public URL, download the
                        original, or remove the file.
                    </DialogDescription>
                </div>
                <div class="flex items-center gap-1">
                    <Button
                        v-if="siblings"
                        type="button"
                        variant="outline"
                        size="icon"
                        :disabled="!hasPrev"
                        @click="navPrev"
                    >
                        <ChevronLeft class="size-4" />
                    </Button>
                    <Button
                        v-if="siblings"
                        type="button"
                        variant="outline"
                        size="icon"
                        :disabled="!hasNext"
                        @click="navNext"
                    >
                        <ChevronRight class="size-4" />
                    </Button>
                    <Button
                        type="button"
                        variant="outline"
                        size="icon"
                        @click="open = false"
                    >
                        <X class="size-4" />
                    </Button>
                </div>
            </DialogHeader>

            <div
                v-if="file"
                class="flex flex-1 flex-col gap-0 overflow-hidden md:flex-row"
            >
                <!-- LEFT: preview -->
                <div
                    class="flex flex-1 items-center justify-center overflow-auto bg-muted/40 p-6"
                >
                    <img
                        v-if="file.mime_type.startsWith('image/')"
                        :src="file.url"
                        :alt="file.alt_text ?? file.name"
                        class="max-h-full max-w-full rounded-md object-contain shadow-md"
                    />
                    <div
                        v-else
                        class="rounded-md border border-dashed p-12 text-center text-sm text-muted-foreground"
                    >
                        Preview not available for {{ file.mime_type }}.
                    </div>
                </div>

                <!-- RIGHT: metadata -->
                <div
                    class="flex w-full shrink-0 flex-col gap-4 overflow-y-auto border-t bg-background p-6 md:w-[420px] md:border-l md:border-t-0"
                >
                    <!-- Stats -->
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
                                    @focus="(e: FocusEvent) => (e.target as HTMLInputElement).select()"
                                />
                            </dd>
                        </div>
                        <div class="flex items-baseline gap-2">
                            <dt class="font-semibold">File Type:</dt>
                            <dd class="text-muted-foreground">
                                {{
                                    (
                                        file.extension || file.mime_type
                                    ).toUpperCase()
                                }}
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
                        <Button
                            type="button"
                            size="sm"
                            @click="downloadFile"
                        >
                            <Download class="size-4" />
                            Download
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

                    <!-- Editable fields -->
                    <div class="grid gap-3">
                        <div class="grid gap-1.5">
                            <Label for="md-alt">Alt Text</Label>
                            <Input
                                id="md-alt"
                                v-model="form.alt_text"
                                placeholder="Describe the image for screen readers"
                            />
                        </div>
                        <div class="grid gap-1.5">
                            <Label for="md-title">Title</Label>
                            <Input
                                id="md-title"
                                v-model="form.title"
                                placeholder="Image title"
                            />
                        </div>
                        <div class="grid gap-1.5">
                            <Label for="md-caption">Caption</Label>
                            <Textarea
                                id="md-caption"
                                v-model="form.caption"
                                :rows="2"
                                placeholder="Short caption shown beneath the image"
                            />
                        </div>
                        <div class="grid gap-1.5">
                            <Label for="md-desc">Description</Label>
                            <Textarea
                                id="md-desc"
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

            <!-- Footer -->
            <div
                class="flex flex-wrap items-center justify-between gap-2 border-t bg-muted/40 px-6 py-3"
            >
                <Button
                    type="button"
                    variant="destructive"
                    size="sm"
                    :disabled="deleting"
                    @click="deletePermanently"
                >
                    <Loader2 v-if="deleting" class="size-4 animate-spin" />
                    <Trash2 v-else class="size-4" />
                    Delete Permanently
                </Button>
                <div class="flex items-center gap-2">
                    <Button
                        type="button"
                        :disabled="saving"
                        @click="save"
                    >
                        <Loader2 v-if="saving" class="size-4 animate-spin" />
                        <Save v-else class="size-4" />
                        Save
                    </Button>
                    <Button
                        v-if="selectable"
                        type="button"
                        variant="default"
                        @click="use"
                    >
                        <Check class="size-4" />
                        Use this image
                    </Button>
                </div>
            </div>
        </DialogContent>
    </Dialog>
</template>
