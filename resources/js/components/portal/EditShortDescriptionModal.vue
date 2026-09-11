<script setup lang="ts">
/**
 * Short Description edit modal. Per-language one-liner (max 500
 * chars) shown on cards + search results. Structurally identical
 * to EditAboutModal but with a smaller textarea + character counter.
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

type Translation = { lang: string; short_description: string };

const MAX_LEN = 500;

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
    title: 'Kurzbeschreibung bearbeiten',
    description: 'Ein bis zwei Sätze, die Ihr Unternehmen auf Kartenansichten und in Suchergebnissen zusammenfassen.',
    save: 'Speichern',
    cancel: 'Abbrechen',
    lang: (code: string) => `Sprache: ${code.toUpperCase()}`,
    placeholder: 'z. B. Umzüge in Berlin — pünktlich, sorgfältig, fair.',
    chars_left: (n: number) => `${n} Zeichen verbleibend`,
} as const;
const en = {
    title: 'Edit short description',
    description: 'One or two sentences that summarise your business on card views and search results.',
    save: 'Save',
    cancel: 'Cancel',
    lang: (code: string) => `Language: ${code.toUpperCase()}`,
    placeholder: 'e.g. Berlin moving service — on time, careful, fair pricing.',
    chars_left: (n: number) => `${n} characters remaining`,
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
    form.post(`/company-portal/${props.companyId}/short-description`, {
        preserveScroll: true,
        onSuccess: () => close(),
    });
}

function rowError(idx: number): string | undefined {
    return form.errors[`translations.${idx}.short_description` as keyof typeof form.errors];
}

function charsLeft(row: Translation): number {
    return Math.max(0, MAX_LEN - (row.short_description ?? '').length);
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
                        v-model="row.short_description"
                        rows="3"
                        :maxlength="MAX_LEN"
                        :placeholder="t.placeholder"
                        :class="rowError(idx) ? 'border-red-400' : ''"
                    />
                    <div class="mt-1 flex items-center justify-between">
                        <p v-if="rowError(idx)" class="text-xs text-red-600">
                            {{ rowError(idx) }}
                        </p>
                        <p v-else class="text-xs text-[var(--slate)]/70"></p>
                        <p class="text-xs text-[var(--slate)]/70">
                            {{ t.chars_left(charsLeft(row)) }}
                        </p>
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
