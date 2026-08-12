<script setup lang="ts" generic="TValue extends string | number | null">
/**
 * Reusable single-field edit modal for the portal profile page.
 *
 * Used for simple sections that only need one input — Business
 * Website, Founded Year, Employees, etc. For multi-field sections
 * (translations, contacts repeater), write a bespoke modal.
 *
 * PUBLIC endpoint by design — the parent picks the URL, this
 * component just POSTs. See CompanyProfileController for the
 * security caveat.
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

const props = defineProps<{
    open: boolean;
    endpoint: string;
    fieldName: string;
    value: TValue;
    type: 'text' | 'number' | 'url';
    title: string;
    description?: string;
    label: string;
    placeholder?: string;
    min?: number;
    max?: number;
    locale: string;
}>();

const emit = defineEmits<{
    (e: 'update:open', value: boolean): void;
}>();

const de = { save: 'Speichern', cancel: 'Abbrechen' } as const;
const en = { save: 'Save', cancel: 'Cancel' } as const;
const tr = computed(() => (props.locale === 'de' ? de : en));

const form = useForm<Record<string, TValue>>({
    [props.fieldName]: props.value,
} as Record<string, TValue>);

watch(
    () => [props.open, props.value] as const,
    ([open]) => {
        if (open) {
            form[props.fieldName] = props.value;
            form.clearErrors();
        }
    },
    { immediate: true },
);

function close(): void {
    emit('update:open', false);
}

function submit(): void {
    form.post(props.endpoint, {
        preserveScroll: true,
        onSuccess: () => close(),
    });
}

const error = computed(() => form.errors[props.fieldName]);
</script>

<template>
    <Dialog :open="open" @update:open="close">
        <DialogContent class="sm:max-w-md">
            <DialogHeader>
                <DialogTitle>{{ title }}</DialogTitle>
                <DialogDescription v-if="description">
                    {{ description }}
                </DialogDescription>
            </DialogHeader>

            <form class="space-y-4" @submit.prevent="submit">
                <div>
                    <label :for="fieldName" class="mb-1 block text-xs font-medium text-[var(--slate)]">
                        {{ label }}
                    </label>
                    <Input
                        :id="fieldName"
                        v-model="form[fieldName]"
                        :type="type"
                        :placeholder="placeholder"
                        :min="min"
                        :max="max"
                        :class="error ? 'border-red-400' : ''"
                    />
                    <p v-if="error" class="mt-1 text-xs text-red-600">
                        {{ error }}
                    </p>
                </div>

                <DialogFooter>
                    <Button type="button" variant="ghost" @click="close">
                        <X class="size-4" />
                        {{ tr.cancel }}
                    </Button>
                    <Button type="submit" :disabled="form.processing">
                        <Loader2 v-if="form.processing" class="size-4 animate-spin" />
                        <Check v-else class="size-4" />
                        {{ tr.save }}
                    </Button>
                </DialogFooter>
            </form>
        </DialogContent>
    </Dialog>
</template>
