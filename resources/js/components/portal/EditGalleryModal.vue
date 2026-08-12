<script setup lang="ts">
/**
 * Gallery editor. Shows current gallery images with a per-item
 * delete button, plus a single-file upload input to append. Each
 * upload creates its own MediaFile row + a company_media pivot.
 */
import { router, useForm } from '@inertiajs/vue3';
import { Check, Image as ImageIcon, Loader2, Trash2, Upload, X } from 'lucide-vue-next';
import { computed, onMounted, ref, watch } from 'vue';
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
    description: 'Fotos für die öffentliche Firmenseite. Ein Bild pro Upload; klicken Sie auf den Papierkorb, um ein Foto zu entfernen.',
    add: 'Foto hinzufügen',
    remove: 'Foto entfernen',
    empty: 'Noch keine Fotos in der Galerie.',
    loading: 'Galerie wird geladen…',
    confirm_delete: 'Dieses Foto wirklich entfernen?',
    uploading: 'Wird hochgeladen…',
    close: 'Schließen',
    count: (n: number) => `${n} Fotos`,
} as const;
const en = {
    title: 'Edit gallery',
    description: 'Photos shown on the public company page. One file per upload; click the trash icon to remove.',
    add: 'Add photo',
    remove: 'Remove photo',
    empty: 'No photos in the gallery yet.',
    loading: 'Loading gallery…',
    confirm_delete: 'Remove this photo?',
    uploading: 'Uploading…',
    close: 'Close',
    count: (n: number) => `${n} photos`,
} as const;
const t = computed(() => (props.locale === 'de' ? de : en));

const uploadForm = useForm<{ image: File | null }>({ image: null });
const fileInput = ref<HTMLInputElement | null>(null);

onMounted(() => {
    if (props.open && !props.lazyLoaded) emit('lazyLoad');
});

watch(
    () => props.open,
    (open) => {
        if (open && !props.lazyLoaded) emit('lazyLoad');
    },
);

function onFilePicked(event: Event): void {
    const file = (event.target as HTMLInputElement).files?.[0] ?? null;
    if (!file) return;

    uploadForm.image = file;
    uploadForm.post(`/company-portal/${props.companyId}/gallery`, {
        forceFormData: true,
        preserveScroll: true,
        onFinish: () => {
            uploadForm.reset();
            router.reload({ only: ['gallery'] });
            if (fileInput.value) fileInput.value.value = '';
        },
    });
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
</script>

<template>
    <Dialog :open="open" @update:open="close">
        <DialogContent class="sm:max-w-2xl">
            <DialogHeader>
                <DialogTitle>{{ t.title }}</DialogTitle>
                <DialogDescription>{{ t.description }}</DialogDescription>
            </DialogHeader>

            <div class="space-y-4">
                <!-- Upload row -->
                <div class="flex flex-wrap items-center justify-between gap-3 rounded-md border border-[var(--orange)]/60 bg-[var(--orange-soft)]/30 p-3">
                    <p class="text-xs text-[var(--slate)]">
                        {{ t.count(gallery.length) }}
                    </p>
                    <label class="inline-flex cursor-pointer items-center gap-1.5 rounded-md bg-[var(--orange)] px-3 py-1.5 text-xs font-semibold text-white hover:bg-[color-mix(in_srgb,var(--orange)_85%,black)]">
                        <Loader2 v-if="uploadForm.processing" class="size-3.5 animate-spin" />
                        <Upload v-else class="size-3.5" />
                        {{ uploadForm.processing ? t.uploading : t.add }}
                        <input
                            ref="fileInput"
                            type="file"
                            accept=".jpg,.jpeg,.png,.webp"
                            :disabled="uploadForm.processing"
                            class="sr-only"
                            @change="onFilePicked"
                        />
                    </label>
                </div>
                <p v-if="uploadForm.errors.image" class="text-xs text-red-600">
                    {{ uploadForm.errors.image }}
                </p>

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
                            class="absolute right-1.5 top-1.5 flex size-8 items-center justify-center rounded-full bg-black/50 text-white opacity-0 backdrop-blur transition-opacity hover:bg-red-600 group-hover:opacity-100"
                            :title="t.remove"
                            @click="deleteItem(item)"
                        >
                            <Trash2 class="size-4" />
                        </button>
                    </div>
                </div>
            </div>

            <DialogFooter>
                <Button type="button" variant="ghost" @click="close">
                    <X class="size-4" />
                    {{ t.close }}
                </Button>
            </DialogFooter>
        </DialogContent>
    </Dialog>
</template>
