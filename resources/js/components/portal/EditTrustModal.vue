<script setup lang="ts">
/**
 * Trust Badges edit modal — verified + top-rated toggles. Two
 * booleans, both PUBLIC (see security caveat in the controller).
 */
import { useForm } from '@inertiajs/vue3';
import { BadgeCheck, Check, Loader2, Star, X } from 'lucide-vue-next';
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
import { Switch } from '@/components/ui/switch';

const props = defineProps<{
    open: boolean;
    companyId: number;
    verified: boolean;
    isTopRated: boolean;
    locale: string;
}>();

const emit = defineEmits<{
    (e: 'update:open', value: boolean): void;
}>();

const de = {
    title: 'Vertrauens-Abzeichen bearbeiten',
    description: 'Zeigen Sie Besuchern auf einen Blick, dass das Unternehmen manuell überprüft wurde oder besonders gut bewertet ist.',
    verified_label: 'Verifiziert',
    verified_hint: 'Zeigt ein blaues Häkchen neben dem Firmennamen an.',
    top_rated_label: 'Top-bewertet',
    top_rated_hint: 'Zeigt einen dunklen "TOP-RATED"-Chip über der Firma.',
    save: 'Speichern',
    cancel: 'Abbrechen',
} as const;
const en = {
    title: 'Edit trust badges',
    description: 'Signal to visitors at a glance that the company was manually verified or is unusually well-rated.',
    verified_label: 'Verified',
    verified_hint: 'Shows a blue check next to the company name.',
    top_rated_label: 'Top-rated',
    top_rated_hint: 'Shows a dark "TOP-RATED" chip above the company.',
    save: 'Save',
    cancel: 'Cancel',
} as const;
const t = computed(() => (props.locale === 'de' ? de : en));

const form = useForm<{ verified: boolean; is_top_rated: boolean }>({
    verified: props.verified,
    is_top_rated: props.isTopRated,
});

watch(
    () => [props.open, props.verified, props.isTopRated] as const,
    ([open]) => {
        if (open) {
            form.verified = props.verified;
            form.is_top_rated = props.isTopRated;
            form.clearErrors();
        }
    },
    { immediate: true },
);

function close(): void {
    emit('update:open', false);
}

function submit(): void {
    form.post(`/company-portal/${props.companyId}/trust`, {
        preserveScroll: true,
        onSuccess: () => close(),
    });
}
</script>

<template>
    <Dialog :open="open" @update:open="close">
        <DialogContent class="sm:max-w-md">
            <DialogHeader>
                <DialogTitle>{{ t.title }}</DialogTitle>
                <DialogDescription>{{ t.description }}</DialogDescription>
            </DialogHeader>

            <form class="space-y-3" @submit.prevent="submit">
                <div class="flex items-start gap-3 rounded-md border border-[var(--linen)] p-3">
                    <BadgeCheck class="mt-0.5 size-5 shrink-0 text-blue-500" />
                    <div class="flex-1">
                        <p class="text-sm font-medium">{{ t.verified_label }}</p>
                        <p class="mt-0.5 text-xs text-[var(--slate)]">
                            {{ t.verified_hint }}
                        </p>
                    </div>
                    <Switch v-model="form.verified" />
                </div>

                <div class="flex items-start gap-3 rounded-md border border-[var(--linen)] p-3">
                    <Star class="mt-0.5 size-5 shrink-0 fill-amber-400 text-amber-500" />
                    <div class="flex-1">
                        <p class="text-sm font-medium">{{ t.top_rated_label }}</p>
                        <p class="mt-0.5 text-xs text-[var(--slate)]">
                            {{ t.top_rated_hint }}
                        </p>
                    </div>
                    <Switch v-model="form.is_top_rated" />
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
