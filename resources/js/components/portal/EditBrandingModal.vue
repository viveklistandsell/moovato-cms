<script setup lang="ts">
/**
 * Logo & Cover edit modal. Two file inputs with live preview of
 * the current + newly-selected file. Each field can also be
 * removed (checkbox — server deletes the stored file).
 *
 * Files go to `storage/app/public/company-portal/branding/` on the
 * public disk. Old files are auto-deleted when replaced.
 */
import { useForm } from '@inertiajs/vue3';
import { Check, Image as ImageIcon, Loader2, Upload, X } from 'lucide-vue-next';
import { computed, ref, watch } from 'vue';
import { Button } from '@/components/ui/button';
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';

const props = defineProps<{
    open: boolean;
    companyId: number;
    logo: string | null;
    cover: string | null;
    locale: string;
}>();

const emit = defineEmits<{
    (e: 'update:open', value: boolean): void;
}>();

const de = {
    title: 'Logo & Titelbild bearbeiten',
    description: 'Logo (empfohlen quadratisch, max. 2 MB) und Titelbild (Breitformat, max. 5 MB).',
    label_logo: 'Logo',
    label_cover: 'Titelbild',
    pick_file: 'Datei wählen',
    remove: 'Aktuelles Bild entfernen',
    no_image: 'Kein Bild hochgeladen',
    save: 'Speichern',
    cancel: 'Abbrechen',
} as const;
const en = {
    title: 'Edit logo & cover',
    description: 'Logo (square recommended, max 2 MB) and cover image (wide, max 5 MB).',
    label_logo: 'Logo',
    label_cover: 'Cover image',
    pick_file: 'Choose file',
    remove: 'Remove current image',
    no_image: 'No image uploaded',
    save: 'Save',
    cancel: 'Cancel',
} as const;
const t = computed(() => (props.locale === 'de' ? de : en));

// The form has multipart/file inputs; useForm handles that transparently
// when values are File instances.
const form = useForm<{
    logo: File | null;
    cover: File | null;
    remove_logo: boolean;
    remove_cover: boolean;
}>({
    logo: null,
    cover: null,
    remove_logo: false,
    remove_cover: false,
});

// Preview URLs for newly-selected files (before submit).
const logoPreview = ref<string | null>(null);
const coverPreview = ref<string | null>(null);

watch(
    () => props.open,
    (open) => {
        if (open) {
            form.reset();
            form.logo = null;
            form.cover = null;
            form.remove_logo = false;
            form.remove_cover = false;
            logoPreview.value = null;
            coverPreview.value = null;
            form.clearErrors();
        }
    },
);

function assetUrl(path: string | null): string | null {
    if (!path) return null;

    return path.startsWith('http') ? path : `/storage/${path}`;
}

function onLogoChange(event: Event): void {
    const file = (event.target as HTMLInputElement).files?.[0] ?? null;
    form.logo = file;
    form.remove_logo = false;
    logoPreview.value = file ? URL.createObjectURL(file) : null;
}

function onCoverChange(event: Event): void {
    const file = (event.target as HTMLInputElement).files?.[0] ?? null;
    form.cover = file;
    form.remove_cover = false;
    coverPreview.value = file ? URL.createObjectURL(file) : null;
}

function close(): void {
    emit('update:open', false);
}

function submit(): void {
    form.post(`/company-portal/${props.companyId}/branding`, {
        forceFormData: true,
        preserveScroll: true,
        onSuccess: () => close(),
    });
}
</script>

<template>
    <Dialog :open="open" @update:open="close">
        <DialogContent class="sm:max-w-lg">
            <DialogHeader>
                <DialogTitle>{{ t.title }}</DialogTitle>
                <DialogDescription>{{ t.description }}</DialogDescription>
            </DialogHeader>

            <form class="space-y-5" @submit.prevent="submit">
                <!-- LOGO -->
                <div class="space-y-2 rounded-md border border-[var(--linen)] p-3">
                    <label class="block text-xs font-medium text-[var(--slate)]">
                        {{ t.label_logo }}
                    </label>
                    <div class="flex items-center gap-3">
                        <div class="flex size-20 shrink-0 items-center justify-center overflow-hidden rounded-md border border-[var(--linen)] bg-[var(--paper)]">
                            <img
                                v-if="logoPreview"
                                :src="logoPreview"
                                alt=""
                                class="size-full object-contain"
                            />
                            <img
                                v-else-if="logo && !form.remove_logo"
                                :src="assetUrl(logo)!"
                                alt=""
                                class="size-full object-contain"
                            />
                            <ImageIcon v-else class="size-6 text-[var(--slate)]/50" />
                        </div>
                        <div class="flex-1 space-y-1.5">
                            <label class="inline-flex cursor-pointer items-center gap-1.5 rounded-md border border-[var(--orange)] px-3 py-1.5 text-xs font-medium text-[var(--orange)] hover:bg-[var(--orange-soft)]">
                                <Upload class="size-3.5" />
                                {{ t.pick_file }}
                                <input
                                    type="file"
                                    accept=".jpg,.jpeg,.png,.webp,.svg"
                                    class="sr-only"
                                    @change="onLogoChange"
                                />
                            </label>
                            <label v-if="logo" class="flex items-center gap-1.5 text-xs text-[var(--slate)]">
                                <input v-model="form.remove_logo" type="checkbox" />
                                {{ t.remove }}
                            </label>
                        </div>
                    </div>
                    <p v-if="form.errors.logo" class="text-xs text-red-600">
                        {{ form.errors.logo }}
                    </p>
                </div>

                <!-- COVER -->
                <div class="space-y-2 rounded-md border border-[var(--linen)] p-3">
                    <label class="block text-xs font-medium text-[var(--slate)]">
                        {{ t.label_cover }}
                    </label>
                    <div class="space-y-2">
                        <div class="flex aspect-[6/1] w-full items-center justify-center overflow-hidden rounded-md border border-[var(--linen)] bg-[var(--paper)]">
                            <img
                                v-if="coverPreview"
                                :src="coverPreview"
                                alt=""
                                class="size-full object-cover"
                            />
                            <img
                                v-else-if="cover && !form.remove_cover"
                                :src="assetUrl(cover)!"
                                alt=""
                                class="size-full object-cover"
                            />
                            <span v-else class="flex items-center gap-2 text-xs text-[var(--slate)]/70">
                                <ImageIcon class="size-4" />
                                {{ t.no_image }}
                            </span>
                        </div>
                        <div class="flex flex-wrap items-center gap-3">
                            <label class="inline-flex cursor-pointer items-center gap-1.5 rounded-md border border-[var(--orange)] px-3 py-1.5 text-xs font-medium text-[var(--orange)] hover:bg-[var(--orange-soft)]">
                                <Upload class="size-3.5" />
                                {{ t.pick_file }}
                                <input
                                    type="file"
                                    accept=".jpg,.jpeg,.png,.webp"
                                    class="sr-only"
                                    @change="onCoverChange"
                                />
                            </label>
                            <label v-if="cover" class="flex items-center gap-1.5 text-xs text-[var(--slate)]">
                                <input v-model="form.remove_cover" type="checkbox" />
                                {{ t.remove }}
                            </label>
                        </div>
                    </div>
                    <p v-if="form.errors.cover" class="text-xs text-red-600">
                        {{ form.errors.cover }}
                    </p>
                </div>

                <DialogFooter>
                    <Button type="button" variant="ghost" @click="close">
                        <X class="size-4" />
                        {{ t.cancel }}
                    </Button>
                    <Button type="submit" :disabled="form.processing">
                        <Loader2 v-if="form.processing" class="size-4 animate-spin" />
                        <Check v-else class="size-4" />
                        {{ t.save }}
                    </Button>
                </DialogFooter>
            </form>
        </DialogContent>
    </Dialog>
</template>
