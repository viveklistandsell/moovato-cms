<script setup lang="ts">
/**
 * FAQ editor. Repeater of Q&A rows, each row has one question +
 * one answer per language. Backend replaces the whole set on save
 * (delete-then-insert), so removing a row deletes the FAQ + all
 * its translations.
 */
import { useForm } from '@inertiajs/vue3';
import { Check, ChevronDown, ChevronRight, HelpCircle, Loader2, Plus, Trash2, X } from 'lucide-vue-next';
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
import { Input } from '@/components/ui/input';
import { Textarea } from '@/components/ui/textarea';

type FaqTranslation = { lang: string; question: string; answer: string };
type Faq = {
    id?: number;
    sort_order: number;
    translations: FaqTranslation[];
};

const props = defineProps<{
    open: boolean;
    companyId: number;
    faqs: Faq[];
    languages: string[];
    locale: string;
    lazyLoaded: boolean;
}>();

const emit = defineEmits<{
    (e: 'update:open', value: boolean): void;
    (e: 'lazyLoad'): void;
}>();

const de = {
    title: 'FAQ bearbeiten',
    description: 'Häufig gestellte Fragen mit Antworten pro Sprache. Zum Bearbeiten auf eine Frage klicken; neue Fragen unten hinzufügen.',
    add: 'Frage hinzufügen',
    remove: 'Diese Frage entfernen',
    empty: 'Noch keine FAQs. Fügen Sie die erste hinzu.',
    loading: 'FAQs werden geladen…',
    row_label: (n: number) => `Frage #${n}`,
    lang_label: (code: string) => `Sprache: ${code.toUpperCase()}`,
    label_question: 'Frage',
    label_answer: 'Antwort',
    placeholder_question: 'z. B. Was macht Ihr Unternehmen besonders?',
    placeholder_answer: 'Ausführliche Antwort, die auf der öffentlichen Seite erscheint.',
    save: 'Speichern',
    cancel: 'Abbrechen',
    empty_question: '(Neue Frage — zum Bearbeiten aufklappen)',
    expand_all: 'Alle aufklappen',
    collapse_all: 'Alle einklappen',
} as const;
const en = {
    title: 'Edit FAQ',
    description: 'Frequently asked questions with per-language answers. Click a question to edit; add a new one below.',
    add: 'Add question',
    remove: 'Remove this question',
    empty: 'No FAQs yet. Add the first one.',
    loading: 'Loading FAQs…',
    row_label: (n: number) => `Question #${n}`,
    lang_label: (code: string) => `Language: ${code.toUpperCase()}`,
    label_question: 'Question',
    label_answer: 'Answer',
    placeholder_question: 'e.g. What makes your business stand out?',
    placeholder_answer: 'Detailed answer shown on the public page.',
    save: 'Save',
    cancel: 'Cancel',
    empty_question: '(New question — click to expand)',
    expand_all: 'Expand all',
    collapse_all: 'Collapse all',
} as const;
const t = computed(() => (props.locale === 'de' ? de : en));

const form = useForm<{ faqs: Faq[] }>({
    faqs: cloneFaqs(props.faqs),
});

function cloneFaqs(faqs: Faq[]): Faq[] {
    return faqs.map((f) => ({
        ...f,
        translations: f.translations.map((tr) => ({ ...tr })),
    }));
}

const expanded = ref<Set<number>>(new Set());

function isExpanded(idx: number): boolean {
    return expanded.value.has(idx);
}

function toggle(idx: number): void {
    const next = new Set(expanded.value);
    next.has(idx) ? next.delete(idx) : next.add(idx);
    expanded.value = next;
}

function expandAll(): void {
    expanded.value = new Set(form.faqs.map((_, i) => i));
}

function collapseAll(): void {
    expanded.value = new Set();
}

function previewFor(faq: Faq): string {
    for (const tr of faq.translations) {
        const q = (tr.question ?? '').trim();
        if (q !== '') return q;
    }

    return t.value.empty_question;
}

onMounted(() => {
    if (props.open && !props.lazyLoaded) emit('lazyLoad');
});

watch(
    () => [props.open, props.faqs] as const,
    ([open]) => {
        if (open) {
            form.faqs = cloneFaqs(props.faqs);
            form.clearErrors();
            expanded.value = new Set();
            if (!props.lazyLoaded) emit('lazyLoad');
        }
    },
);

watch(
    () => form.errors,
    (errs) => {
        const errKeys = Object.keys(errs);
        if (errKeys.length === 0) return;
        const next = new Set(expanded.value);
        errKeys.forEach((key) => {
            const match = key.match(/^faqs\.(\d+)\./);
            if (match) next.add(Number(match[1]));
        });
        expanded.value = next;
    },
    { deep: true },
);

function addFaq(): void {
    form.faqs = [
        ...form.faqs,
        {
            sort_order: form.faqs.length + 1,
            translations: props.languages.map((code) => ({
                lang: code,
                question: '',
                answer: '',
            })),
        },
    ];
    const newIdx = form.faqs.length - 1;
    expanded.value = new Set([...expanded.value, newIdx]);
}

function removeFaq(idx: number): void {
    form.faqs = form.faqs.filter((_, i) => i !== idx);
    const next = new Set<number>();
    for (const i of expanded.value) {
        if (i < idx) next.add(i);
        else if (i > idx) next.add(i - 1);
    }
    expanded.value = next;
}

function close(): void {
    emit('update:open', false);
}

function submit(): void {
    form.post(`/company-portal/${props.companyId}/faqs`, {
        preserveScroll: true,
        onSuccess: () => close(),
    });
}

function fieldError(faqIdx: number, trIdx: number, key: 'question' | 'answer'): string | undefined {
    return form.errors[`faqs.${faqIdx}.translations.${trIdx}.${key}` as keyof typeof form.errors];
}
</script>

<template>
    <Dialog :open="open" @update:open="close">
        <DialogContent class="sm:max-w-2xl">
            <DialogHeader>
                <DialogTitle>{{ t.title }}</DialogTitle>
                <DialogDescription>{{ t.description }}</DialogDescription>
            </DialogHeader>

            <form class="space-y-3" @submit.prevent="submit">
                <div v-if="!lazyLoaded" class="flex items-center justify-center gap-2 rounded-md border border-dashed border-[var(--linen)] p-6 text-sm text-[var(--slate)]">
                    <Loader2 class="size-4 animate-spin" />
                    {{ t.loading }}
                </div>

                <div v-else-if="form.faqs.length === 0" class="rounded-md border border-dashed border-[var(--linen)] p-6 text-center text-sm text-[var(--slate)]">
                    {{ t.empty }}
                </div>

                <template v-else>
                    <div v-if="form.faqs.length > 1" class="flex items-center justify-end gap-1">
                        <Button type="button" variant="ghost" size="sm" @click="expandAll">
                            <ChevronDown class="size-3.5" />
                            {{ t.expand_all }}
                        </Button>
                        <Button type="button" variant="ghost" size="sm" @click="collapseAll">
                            <ChevronRight class="size-3.5" />
                            {{ t.collapse_all }}
                        </Button>
                    </div>

                    <div
                        v-for="(faq, faqIdx) in form.faqs"
                        :key="faqIdx"
                        class="rounded-md border border-[var(--linen)]"
                    >
                        <!-- Header — click to toggle expand -->
                        <button
                            type="button"
                            class="flex w-full items-center gap-2 p-3 text-left hover:bg-[var(--paper)]"
                            @click="toggle(faqIdx)"
                        >
                            <ChevronDown v-if="isExpanded(faqIdx)" class="size-4 shrink-0 text-[var(--slate)]" />
                            <ChevronRight v-else class="size-4 shrink-0 text-[var(--slate)]" />
                            <HelpCircle class="size-3.5 shrink-0 text-[var(--slate)]" />
                            <div class="min-w-0 flex-1">
                                <p class="text-xs font-semibold uppercase tracking-wide text-[var(--slate)]">
                                    {{ t.row_label(faqIdx + 1) }}
                                </p>
                                <p class="mt-0.5 truncate text-sm text-[var(--midnight)]">
                                    {{ previewFor(faq) }}
                                </p>
                            </div>
                            <Button
                                type="button"
                                as-child
                                variant="ghost"
                                size="icon-sm"
                                class="shrink-0 text-red-600 hover:text-red-700"
                                :title="t.remove"
                                @click.stop="removeFaq(faqIdx)"
                            >
                                <span>
                                    <Trash2 class="size-4" />
                                </span>
                            </Button>
                        </button>

                        <!-- Body — only when expanded -->
                        <div v-if="isExpanded(faqIdx)" class="space-y-3 border-t border-[var(--linen)] p-3">
                            <div
                                v-for="(tr, trIdx) in faq.translations"
                                :key="tr.lang"
                                class="rounded-md bg-[var(--paper)] p-2"
                            >
                                <p class="mb-1.5 text-[10px] font-semibold uppercase tracking-wide text-[var(--slate)]">
                                    {{ t.lang_label(tr.lang) }}
                                </p>
                                <div class="space-y-2">
                                    <div>
                                        <label class="mb-1 block text-xs text-[var(--slate)]">
                                            {{ t.label_question }}
                                        </label>
                                        <Input
                                            v-model="tr.question"
                                            :placeholder="t.placeholder_question"
                                            :class="fieldError(faqIdx, trIdx, 'question') ? 'border-red-400' : ''"
                                        />
                                        <p v-if="fieldError(faqIdx, trIdx, 'question')" class="mt-1 text-xs text-red-600">
                                            {{ fieldError(faqIdx, trIdx, 'question') }}
                                        </p>
                                    </div>
                                    <div>
                                        <label class="mb-1 block text-xs text-[var(--slate)]">
                                            {{ t.label_answer }}
                                        </label>
                                        <Textarea
                                            v-model="tr.answer"
                                            rows="3"
                                            :placeholder="t.placeholder_answer"
                                            :class="fieldError(faqIdx, trIdx, 'answer') ? 'border-red-400' : ''"
                                        />
                                        <p v-if="fieldError(faqIdx, trIdx, 'answer')" class="mt-1 text-xs text-red-600">
                                            {{ fieldError(faqIdx, trIdx, 'answer') }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </template>

                <Button v-if="lazyLoaded" type="button" variant="outline" size="sm" @click="addFaq">
                    <Plus class="size-4" />
                    {{ t.add }}
                </Button>

                <DialogFooter>
                    <Button type="button" variant="ghost" @click="close">
                        <X class="size-4" />
                        {{ t.cancel }}
                    </Button>
                    <Button type="submit" :disabled="form.processing || !lazyLoaded">
                        <Loader2 v-if="form.processing" class="size-4 animate-spin" />
                        <Check v-else class="size-4" />
                        {{ t.save }}
                    </Button>
                </DialogFooter>
            </form>
        </DialogContent>
    </Dialog>
</template>
