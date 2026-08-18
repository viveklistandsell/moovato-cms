<script setup lang="ts">

import { router, useForm } from '@inertiajs/vue3';
import { Image as ImageIcon, Loader2, Trash2, Upload, X } from 'lucide-vue-next';
import { computed, onBeforeUnmount, onMounted, ref, watch } from 'vue';
import { Button } from '@/components/ui/button';
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';

type GalleryItem = { id: number; name: string; url: string | null };

const props = defineProps<{
    open: boolean;
    companyId: number;
    gallery: GalleryItem[];
    locale: string;
    lazyLoaded: boolean;
}>();

const emit = defineEmits<{
    (e: 'update:open', value: boolean): void;
    (e: 'lazyLoad'): void;
}>();

const de = {
    title: 'Galerie bearbeiten',
    description: 'Fotos für die öffentliche Firmenseite. Sie können mehrere Bilder gleichzeitig auswählen und den Papierkorb klicken, um ein Foto zu entfernen.',
    add: 'Fotos hinzufügen',
    remove: 'Foto entfernen',
    empty: 'Noch keine Fotos in der Galerie.',
    loading: 'Galerie wird geladen…',
    confirm_delete: 'Dieses Foto wirklich entfernen?',
    uploading: 'Wird hochgeladen…',
    upload_progress: 'Hochladen: {p}%',
    close: 'Schließen',
    count: (n: number) => `${n} Fotos`,
    drop_hint: 'Bilder hierher ziehen oder',
    pick_file: 'auswählen',
    hint: 'JPG, PNG oder WebP · max. 5 MB pro Datei · bis zu 20 auf einmal',
    err_too_large: '„{name}" ist zu groß. Maximale Dateigröße: 5 MB.',
    err_bad_type: '„{name}" hat einen nicht unterstützten Dateityp.',
    err_too_many: 'Maximal 20 Dateien pro Upload.',
    err_dismiss: 'Ausblenden',
} as const;

const en = {
    title: 'Edit gallery',
    description: 'Photos shown on the public company page. Pick multiple images at once, click the trash icon to remove.',
    add: 'Add photos',
    remove: 'Remove photo',
    empty: 'No photos in the gallery yet.',
    loading: 'Loading gallery…',
    confirm_delete: 'Remove this photo?',
    uploading: 'Uploading…',
    upload_progress: 'Uploading: {p}%',
    close: 'Close',
    count: (n: number) => `${n} photos`,
    drop_hint: 'Drag images here or',
    pick_file: 'browse',
    hint: 'JPG, PNG or WebP · max 5 MB per file · up to 20 at once',
    err_too_large: '"{name}" is too big. Maximum file size is 5 MB.',
    err_bad_type: '"{name}" has an unsupported file type.',
    err_too_many: 'Maximum of 20 files per upload.',
    err_dismiss: 'Dismiss',
} as const;

const t = computed(() => (props.locale === 'de' ? de : en));

const MAX_FILES_PER_BATCH = 20;
const MAX_FILE_BYTES = 5 * 1024 * 1024;
const ALLOWED_EXTS = ['jpg', 'jpeg', 'png', 'webp'] as const;

const uploadForm = useForm<{ images: File[] }>({ images: [] });
const fileInput = ref<HTMLInputElement | null>(null);
const isDragging = ref(false);
const clientErrors = ref<string[]>([]);
const previewUrls = ref<string[]>([]);

function extOf(name: string): string {
    const dot = name.lastIndexOf('.');
    return dot >= 0 ? name.slice(dot + 1).toLowerCase() : '';
}

function releasePreviews(): void {
    for (const u of previewUrls.value) URL.revokeObjectURL(u);
    previewUrls.value = [];
}

function validateBatch(files: File[]): { ok: File[]; errors: string[] } {
    const ok: File[] = [];
    const errors: string[] = [];

    if (files.length > MAX_FILES_PER_BATCH) {
        errors.push(t.value.err_too_many);
        files = files.slice(0, MAX_FILES_PER_BATCH);
    }

    for (const f of files) {
        const ext = extOf(f.name);
        if (!ALLOWED_EXTS.includes(ext as typeof ALLOWED_EXTS[number])) {
            errors.push(t.value.err_bad_type.replace('{name}', f.name));
            continue;
        }
        if (f.size > MAX_FILE_BYTES) {
            errors.push(t.value.err_too_large.replace('{name}', f.name));
            continue;
        }
        ok.push(f);
    }
    return { ok, errors };
}

function submitBatch(files: File[]): void {
    releasePreviews();
    previewUrls.value = files.map((f) => URL.createObjectURL(f));
    uploadForm.images = files;
    uploadForm.post(`/company-portal/${props.companyId}/gallery`, {
        forceFormData: true,
        preserveScroll: true,
        onSuccess: () => {
            router.reload({ only: ['gallery'] });
        },
        onFinish: () => {
            uploadForm.reset();
            releasePreviews();
            if (fileInput.value) fileInput.value.value = '';
        },
    });
}

function onFilePicked(event: Event): void {
    const input = event.target as HTMLInputElement;
    const list = input.files;
    if (!list || list.length === 0) return;

    const { ok, errors } = validateBatch(Array.from(list));
    clientErrors.value = errors;
    if (ok.length > 0) submitBatch(ok);
    else if (fileInput.value) fileInput.value.value = '';
}

function onDrop(event: DragEvent): void {
    event.preventDefault();
    isDragging.value = false;
    const list = event.dataTransfer?.files;
    if (!list || list.length === 0) return;

    const { ok, errors } = validateBatch(Array.from(list));
    clientErrors.value = errors;
    if (ok.length > 0) submitBatch(ok);
}

function onDragOver(event: DragEvent): void {
    event.preventDefault();
    isDragging.value = true;
}

function onDragLeave(): void {
    isDragging.value = false;
}

function clearErrors(): void {
    clientErrors.value = [];
}

function deleteItem(item: GalleryItem): void {
    if (!confirm(t.value.confirm_delete)) return;
    router.delete(`/company-portal/${props.companyId}/gallery/${item.id}`, {
        preserveScroll: true,
        onFinish: () => router.reload({ only: ['gallery'] }),
    });
}

function close(): void {
    emit('update:open', false);
}

onMounted(() => {
    if (props.open && !props.lazyLoaded) emit('lazyLoad');
});

watch(
    () => props.open,
    (open) => {
        if (open && !props.lazyLoaded) emit('lazyLoad');
    },
);

onBeforeUnmount(() => {
    releasePreviews();
});
</script>

<template>
    <Dialog :open="open" @update:open="close">
        <DialogContent class="sm:max-w-2xl">
            <DialogHeader>
                <DialogTitle>{{ t.title }}</DialogTitle>
                <DialogDescription>{{ t.description }}</DialogDescription>
            </DialogHeader>

            <div class="space-y-4">
                <!-- Upload dropzone (multi-select) -->
                <div
                    class="rounded-lg border-2 border-dashed p-5 text-center transition-colors"
                    :class="isDragging
                        ? 'border-[var(--orange)] bg-[var(--orange-soft)]'
                        : 'border-[var(--linen)] bg-[var(--paper)]'"
                    @dragover.prevent="onDragOver"
                    @dragleave.prevent="onDragLeave"
                    @drop="onDrop"
                >
                    <Upload class="mx-auto size-6 text-[var(--slate-light)]" />
                    <p class="mt-2 text-sm text-[var(--slate)]">
                        {{ t.drop_hint }}
                        <label
                            class="cursor-pointer font-semibold text-[var(--orange)] hover:underline"
                            :class="{ 'pointer-events-none opacity-60': uploadForm.processing }"
                        >
                            {{ t.pick_file }}
                            <input
                                ref="fileInput"
                                type="file"
                                accept=".jpg,.jpeg,.png,.webp"
                                multiple
                                :disabled="uploadForm.processing"
                                class="sr-only"
                                @change="onFilePicked"
                            />
                        </label>
                    </p>
                    <p class="mt-1 text-[11px] text-[var(--slate-light)]">
                        {{ t.hint }} · {{ t.count(gallery.length) }}
                    </p>
                </div>

                <!-- Client-side rejection callouts (bad type, oversize, too many) -->
                <div
                    v-if="clientErrors.length > 0"
                    class="rounded-md border border-[var(--orange)]/25 bg-[var(--orange-soft)] p-2.5 text-xs text-[var(--midnight)]"
                >
                    <div class="flex items-start justify-between gap-2">
                        <ul class="min-w-0 flex-1 space-y-0.5">
                            <li v-for="(e, i) in clientErrors" :key="i" class="flex items-start gap-1.5">
                                <span class="mt-0.5 size-1.5 shrink-0 rounded-full bg-[var(--orange)]"></span>
                                <span>{{ e }}</span>
                            </li>
                        </ul>
                        <button
                            type="button"
                            class="shrink-0 rounded-md p-1 text-[var(--slate-light)] hover:bg-white hover:text-[var(--orange)]"
                            :aria-label="t.err_dismiss"
                            @click="clearErrors"
                        >
                            <X class="size-3.5" />
                        </button>
                    </div>
                </div>

                <!-- Backend validation errors (e.g. broken file after client OK) -->
                <p v-if="uploadForm.errors.images" class="text-xs text-[var(--orange)]">
                    {{ uploadForm.errors.images }}
                </p>

                <!-- Upload progress bar (only while multipart is in flight) -->
                <div
                    v-if="uploadForm.progress && uploadForm.progress.percentage !== undefined && uploadForm.progress.percentage < 100"
                    class="space-y-1"
                >
                    <div class="h-1.5 w-full overflow-hidden rounded-full bg-[var(--linen)]">
                        <div
                            class="h-full rounded-full bg-[var(--orange)] transition-all"
                            :style="{ width: `${uploadForm.progress.percentage}%` }"
                        ></div>
                    </div>
                    <p class="text-center text-[11px] text-[var(--slate)]">
                        {{ t.upload_progress.replace('{p}', String(uploadForm.progress.percentage)) }}
                    </p>
                </div>

                <!-- Live preview of the currently-uploading batch -->
                <div v-if="previewUrls.length > 0" class="grid grid-cols-4 gap-2 rounded-md border border-dashed border-[var(--orange)]/30 bg-[var(--orange-soft)]/30 p-2 sm:grid-cols-6">
                    <div
                        v-for="(url, i) in previewUrls"
                        :key="i"
                        class="relative aspect-square overflow-hidden rounded-sm bg-[var(--paper)]"
                    >
                        <img :src="url" alt="" class="size-full object-cover" />
                        <div class="absolute inset-0 flex items-center justify-center bg-black/40">
                            <Loader2 class="size-5 animate-spin text-white" />
                        </div>
                    </div>
                </div>

                <!-- Gallery grid -->
                <div v-if="!lazyLoaded" class="flex items-center justify-center gap-2 rounded-md border border-dashed border-[var(--linen)] p-6 text-sm text-[var(--slate)]">
                    <Loader2 class="size-4 animate-spin" />
                    {{ t.loading }}
                </div>

                <div v-else-if="gallery.length === 0" class="flex flex-col items-center gap-2 rounded-md border border-dashed border-[var(--linen)] p-8 text-center text-sm text-[var(--slate)]">
                    <ImageIcon class="size-6 text-[var(--slate)]/50" />
                    {{ t.empty }}
                </div>

                <div v-else class="grid grid-cols-2 gap-3 sm:grid-cols-3">
                    <div
                        v-for="item in gallery"
                        :key="item.id"
                        class="group relative aspect-square overflow-hidden rounded-md border border-[var(--linen)] bg-[var(--paper)]"
                    >
                        <img
                            v-if="item.url"
                            :src="item.url"
                            :alt="item.name"
                            class="size-full object-cover"
                        />
                        <button
                            type="button"
                            class="absolute right-1.5 top-1.5 flex size-8 items-center justify-center rounded-full bg-black/50 text-white opacity-0 backdrop-blur transition-opacity hover:bg-[var(--orange)] group-hover:opacity-100"
                            :title="t.remove"
                            @click="deleteItem(item)"
                        >
                            <Trash2 class="size-4" />
                        </button>
                    </div>
                </div>
            </div>

            <DialogFooter>
                <Button type="button" variant="ghost" :disabled="uploadForm.processing" @click="close">
                    <X class="size-4" />
                    {{ t.close }}
                </Button>
            </DialogFooter>
        </DialogContent>
    </Dialog>
</template>
