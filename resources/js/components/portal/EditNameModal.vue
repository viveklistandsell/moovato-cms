<script setup lang="ts">
/**
 * Business Name edit modal for the portal profile page.
 *
 * One row per language (from the languages prop). Name is required
 * per row shown; permalink auto-fills from the name if left blank
 * (server-side Str::slug).
 *
 * PUBLIC endpoint — no auth. See CompanyProfileController::updateName
 * for the security caveat.
 */
import { useForm } from '@inertiajs/vue3';
import { Check, Loader2, X } from 'lucide-vue-next';
import { computed, watch } from 'vue';
import { Button } from '@/components/ui/button';
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';

type Translation = {
    lang: string;
    name: string;
    permalink: string;
};

const props = defineProps<{
    open: boolean;
    companyId: number;
    translations: Translation[];
    locale: string;
}>();

const emit = defineEmits<{
    (e: 'update:open', value: boolean): void;
}>();

const de = {
    title: 'Unternehmensname bearbeiten',
    description: 'Aktualisieren Sie den Firmennamen für jede Sprache. Die URL wird automatisch aus dem Namen erzeugt, wenn Sie das Feld leer lassen.',
    label_name: 'Name',
    label_permalink: 'URL-Slug',
    placeholder_name: 'Firmenname eingeben',
    placeholder_permalink: 'wird-aus-dem-namen-erzeugt',
    save: 'Speichern',
    cancel: 'Abbrechen',
    lang: (code: string) => `Sprache: ${code.toUpperCase()}`,
} as const;
const en = {
    title: 'Edit business name',
    description: 'Update the company name for each language. The URL is auto-generated from the name if you leave the slug field blank.',
    label_name: 'Name',
    label_permalink: 'URL slug',
    placeholder_name: 'Enter company name',
    placeholder_permalink: 'auto-generated-from-name',
    save: 'Save',
    cancel: 'Cancel',
    lang: (code: string) => `Language: ${code.toUpperCase()}`,
} as const;
const t = computed(() => (props.locale === 'de' ? de : en));

/**
 * Inertia form. Cloning `props.translations` so the local form state
 * doesn't mutate the parent's payload while the user is typing.
 */
const form = useForm<{ translations: Translation[] }>({
    translations: props.translations.map((row) => ({ ...row })),
});

watch(
    () => [props.open, props.translations] as const,
    ([open]) => {
        if (open) {
            form.reset();
            form.translations = props.translations.map((row) => ({ ...row }));
            form.clearErrors();
        }
    },
    { immediate: true },
);

function close(): void {
    emit('update:open', false);
}

function submit(): void {
    form.post(`/company-portal/${props.companyId}/name`, {
        preserveScroll: true,
        onSuccess: () => close(),
    });
}

function nameError(idx: number): string | undefined {
    return form.errors[`translations.${idx}.name` as keyof typeof form.errors];
}
function permalinkError(idx: number): string | undefined {
    return form.errors[`translations.${idx}.permalink` as keyof typeof form.errors];
}
</script>

<template>
    <Dialog :open="open" @update:open="close">
        <DialogContent class="sm:max-w-lg">
            <DialogHeader>
                <DialogTitle>{{ t.title }}</DialogTitle>
                <DialogDescription>{{ t.description }}</DialogDescription>
            </DialogHeader>

            <form class="space-y-6" @submit.prevent="submit">
                <div
                    v-for="(row, idx) in form.translations"
                    :key="row.lang"
                    class="rounded-md border border-[var(--linen)] p-3"
                >
                    <p class="mb-2 text-xs font-semibold uppercase tracking-wide text-[var(--slate)]">
                        {{ t.lang(row.lang) }}
                    </p>

                    <div class="space-y-3">
                        <div>
                            <label :for="`name-${row.lang}`" class="mb-1 block text-xs text-[var(--slate)]">
                                {{ t.label_name }}
                            </label>
                            <Input
                                :id="`name-${row.lang}`"
                                v-model="row.name"
                                :placeholder="t.placeholder_name"
                                :class="nameError(idx) ? 'border-red-400' : ''"
                            />
                            <p v-if="nameError(idx)" class="mt-1 text-xs text-red-600">
                                {{ nameError(idx) }}
                            </p>
                        </div>
                        <div>
                            <label :for="`permalink-${row.lang}`" class="mb-1 block text-xs text-[var(--slate)]">
                                {{ t.label_permalink }}
                            </label>
                            <Input
                                :id="`permalink-${row.lang}`"
                                v-model="row.permalink"
                                :placeholder="t.placeholder_permalink"
                                :class="permalinkError(idx) ? 'border-red-400' : ''"
                            />
                            <p v-if="permalinkError(idx)" class="mt-1 text-xs text-red-600">
                                {{ permalinkError(idx) }}
                            </p>
                        </div>
                    </div>
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
