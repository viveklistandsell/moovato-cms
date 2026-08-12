<script setup lang="ts">
/**
 * Google Ratings edit modal — rating (0–5) + review count together.
 * These fields only make sense as a pair (a rating without a count
 * has no context), so they share one modal.
 */
import { useForm } from '@inertiajs/vue3';
import { Check, Loader2, Star, X } from 'lucide-vue-next';
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

const props = defineProps<{
    open: boolean;
    companyId: number;
    googleRating: number | string | null;
    googleReviewCount: number;
    locale: string;
}>();

const emit = defineEmits<{
    (e: 'update:open', value: boolean): void;
}>();

const de = {
    title: 'Google-Bewertungen bearbeiten',
    description: 'Aktuelle Google-Bewertung (0–5 Sterne) und Anzahl der Google-Rezensionen. Beide Felder werden auf der öffentlichen Firmenseite angezeigt.',
    label_rating: 'Google-Bewertung (0–5)',
    label_count: 'Anzahl Google-Bewertungen',
    placeholder_rating: 'z. B. 4,8',
    placeholder_count: 'z. B. 245',
    save: 'Speichern',
    cancel: 'Abbrechen',
    hint_rating: 'Ganze oder Dezimalzahl, z. B. 4.5',
} as const;
const en = {
    title: 'Edit Google ratings',
    description: 'Current Google star rating (0–5) and total number of Google reviews. Both show on the public company page.',
    label_rating: 'Google rating (0–5)',
    label_count: 'Number of Google reviews',
    placeholder_rating: 'e.g. 4.8',
    placeholder_count: 'e.g. 245',
    save: 'Save',
    cancel: 'Cancel',
    hint_rating: 'Whole or decimal, e.g. 4.5',
} as const;
const t = computed(() => (props.locale === 'de' ? de : en));

const form = useForm<{ google_rating: number | string | null; google_review_count: number | null }>({
    google_rating: props.googleRating,
    google_review_count: props.googleReviewCount,
});

watch(
    () => [props.open, props.googleRating, props.googleReviewCount] as const,
    ([open]) => {
        if (open) {
            form.google_rating = props.googleRating;
            form.google_review_count = props.googleReviewCount;
            form.clearErrors();
        }
    },
    { immediate: true },
);

function close(): void {
    emit('update:open', false);
}

function submit(): void {
    form.post(`/company-portal/${props.companyId}/google`, {
        preserveScroll: true,
        onSuccess: () => close(),
    });
}
</script>

<template>
    <Dialog :open="open" @update:open="close">
        <DialogContent class="sm:max-w-md">
            <DialogHeader>
                <DialogTitle>
                    <span class="inline-flex items-center gap-2">
                        <Star class="size-4 fill-amber-400 text-amber-500" />
                        {{ t.title }}
                    </span>
                </DialogTitle>
                <DialogDescription>{{ t.description }}</DialogDescription>
            </DialogHeader>

            <form class="space-y-4" @submit.prevent="submit">
                <div>
                    <label for="google_rating" class="mb-1 block text-xs font-medium text-[var(--slate)]">
                        {{ t.label_rating }}
                    </label>
                    <Input
                        id="google_rating"
                        v-model="form.google_rating"
                        type="number"
                        step="0.1"
                        min="0"
                        max="5"
                        :placeholder="t.placeholder_rating"
                        :class="form.errors.google_rating ? 'border-red-400' : ''"
                    />
                    <p v-if="form.errors.google_rating" class="mt-1 text-xs text-red-600">
                        {{ form.errors.google_rating }}
                    </p>
                    <p v-else class="mt-1 text-xs text-[var(--slate)]/70">
                        {{ t.hint_rating }}
                    </p>
                </div>

                <div>
                    <label for="google_review_count" class="mb-1 block text-xs font-medium text-[var(--slate)]">
                        {{ t.label_count }}
                    </label>
                    <Input
                        id="google_review_count"
                        v-model="form.google_review_count"
                        type="number"
                        min="0"
                        :placeholder="t.placeholder_count"
                        :class="form.errors.google_review_count ? 'border-red-400' : ''"
                    />
                    <p v-if="form.errors.google_review_count" class="mt-1 text-xs text-red-600">
                        {{ form.errors.google_review_count }}
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
