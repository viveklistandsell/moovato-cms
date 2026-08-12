<script setup lang="ts">
/**
 * About / Description edit modal. One textarea per language.
 * Empty strings clear the row (about is purely optional, unlike
 * name which is required and where blank looks accidental).
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
import { Textarea } from '@/components/ui/textarea';

type Translation = {
    lang: string;
    about: string;
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
    title: 'Über uns bearbeiten',
    description: 'Kurze, ehrliche Beschreibung des Unternehmens — was Sie machen, wie lange, was Sie besonders macht.',
    save: 'Speichern',
    cancel: 'Abbrechen',
    lang: (code: string) => `Sprache: ${code.toUpperCase()}`,
    placeholder: 'Beschreiben Sie das Unternehmen kurz…',
} as const;
const en = {
    title: 'Edit about / description',
    description: 'Short, honest description of the company — what you do, how long you have done it, what makes you stand out.',
    save: 'Save',
    cancel: 'Cancel',
    lang: (code: string) => `Language: ${code.toUpperCase()}`,
    placeholder: 'Describe the company briefly…',
} as const;
const t = computed(() => (props.locale === 'de' ? de : en));

const form = useForm<{ translations: Translation[] }>({
    translations: props.translations.map((row) => ({ ...row })),
});

watch(
    () => [props.open, props.translations] as const,
    ([open]) => {
        if (open) {
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
    form.post(`/company-portal/${props.companyId}/about`, {
        preserveScroll: true,
        onSuccess: () => close(),
    });
}

function aboutError(idx: number): string | undefined {
    return form.errors[`translations.${idx}.about` as keyof typeof form.errors];
}
</script>

<template>
    <Dialog :open="open" @update:open="close">
        <DialogContent class="sm:max-w-lg">
            <DialogHeader>
                <DialogTitle>{{ t.title }}</DialogTitle>
                <DialogDescription>{{ t.description }}</DialogDescription>
            </DialogHeader>

            <form class="space-y-4" @submit.prevent="submit">
                <div
                    v-for="(row, idx) in form.translations"
                    :key="row.lang"
                    class="rounded-md border border-[var(--linen)] p-3"
                >
                    <p class="mb-2 text-xs font-semibold uppercase tracking-wide text-[var(--slate)]">
                        {{ t.lang(row.lang) }}
                    </p>
                    <Textarea
                        v-model="row.about"
                        rows="5"
                        :placeholder="t.placeholder"
                        :class="aboutError(idx) ? 'border-red-400' : ''"
                    />
                    <p v-if="aboutError(idx)" class="mt-1 text-xs text-red-600">
                        {{ aboutError(idx) }}
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
