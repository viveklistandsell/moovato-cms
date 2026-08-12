<script setup lang="ts">
/**
 * Business Address edit modal — street + postal code.
 *
 * City / district pickers are their own follow-up phase (they need
 * cascading dropdowns from the countries / states / cities /
 * districts data). Admins who need to change those still use the
 * full admin edit form.
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
    companyId: number;
    street: string;
    postalCode: string;
    cityName: string | null;
    locale: string;
}>();

const emit = defineEmits<{
    (e: 'update:open', value: boolean): void;
}>();

const de = {
    title: 'Geschäftsadresse bearbeiten',
    description: 'Straße und Postleitzahl. Stadt und Bezirk werden weiterhin über den vollständigen Editor gepflegt.',
    label_street: 'Straße & Nummer',
    label_postal: 'Postleitzahl',
    label_city: 'Stadt (nicht bearbeitbar)',
    placeholder_street: 'z. B. Torstraße 108',
    placeholder_postal: '10119',
    save: 'Speichern',
    cancel: 'Abbrechen',
} as const;
const en = {
    title: 'Edit business address',
    description: 'Street and postal code. City and district still live in the full admin editor.',
    label_street: 'Street & number',
    label_postal: 'Postal code',
    label_city: 'City (not editable)',
    placeholder_street: 'e.g. 108 Main St',
    placeholder_postal: '10119',
    save: 'Save',
    cancel: 'Cancel',
} as const;
const t = computed(() => (props.locale === 'de' ? de : en));

const form = useForm<{ street: string; postal_code: string }>({
    street: props.street,
    postal_code: props.postalCode,
});

watch(
    () => [props.open, props.street, props.postalCode] as const,
    ([open]) => {
        if (open) {
            form.street = props.street;
            form.postal_code = props.postalCode;
            form.clearErrors();
        }
    },
    { immediate: true },
);

function close(): void {
    emit('update:open', false);
}

function submit(): void {
    form.post(`/company-portal/${props.companyId}/address`, {
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
                <div>
                    <label for="street" class="mb-1 block text-xs font-medium text-[var(--slate)]">
                        {{ t.label_street }}
                    </label>
                    <Input
                        id="street"
                        v-model="form.street"
                        :placeholder="t.placeholder_street"
                        :class="form.errors.street ? 'border-red-400' : ''"
                    />
                    <p v-if="form.errors.street" class="mt-1 text-xs text-red-600">
                        {{ form.errors.street }}
                    </p>
                </div>

                <div>
                    <label for="postal_code" class="mb-1 block text-xs font-medium text-[var(--slate)]">
                        {{ t.label_postal }}
                    </label>
                    <Input
                        id="postal_code"
                        v-model="form.postal_code"
                        :placeholder="t.placeholder_postal"
                        :class="form.errors.postal_code ? 'border-red-400' : ''"
                    />
                    <p v-if="form.errors.postal_code" class="mt-1 text-xs text-red-600">
                        {{ form.errors.postal_code }}
                    </p>
                </div>

                <div v-if="cityName">
                    <label class="mb-1 block text-xs font-medium text-[var(--slate)]">
                        {{ t.label_city }}
                    </label>
                    <Input :model-value="cityName" disabled />
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
