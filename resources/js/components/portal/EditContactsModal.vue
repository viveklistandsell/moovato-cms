<script setup lang="ts">
/**
 * Contact Details edit modal — repeater of phone / email / whatsapp
 * rows. Website is edited in its own dedicated modal (not touched
 * here).
 */
import { useForm } from '@inertiajs/vue3';
import { Check, Loader2, Mail, MessageCircle, Phone, Plus, Trash2, X } from 'lucide-vue-next';
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
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';

type ContactType = 'phone' | 'email' | 'whatsapp';
type Contact = {
    type: ContactType;
    value: string;
    label: string;
};

const props = defineProps<{
    open: boolean;
    companyId: number;
    contacts: Contact[];
    locale: string;
}>();

const emit = defineEmits<{
    (e: 'update:open', value: boolean): void;
}>();

const de = {
    title: 'Kontaktdaten bearbeiten',
    description: 'Telefon, E-Mail und WhatsApp-Nummern. Die Website hat einen eigenen Editor.',
    label_type: 'Typ',
    label_value: 'Wert',
    label_label: 'Bezeichnung (optional)',
    placeholder_value: (type: ContactType) => ({
        phone: 'z. B. 030 12345678',
        email: 'z. B. info@firma.de',
        whatsapp: 'z. B. +49 170 1234567',
    } as Record<ContactType, string>)[type],
    placeholder_label: 'z. B. Hauptbüro',
    add: 'Kontakt hinzufügen',
    remove: 'Entfernen',
    save: 'Speichern',
    cancel: 'Abbrechen',
    empty: 'Noch keine Kontakte. Fügen Sie einen ersten Kontakt hinzu.',
    type_labels: {
        phone: 'Telefon',
        email: 'E-Mail',
        whatsapp: 'WhatsApp',
    } as Record<ContactType, string>,
} as const;
const en = {
    title: 'Edit contact details',
    description: 'Phone, email and WhatsApp numbers. Website has its own dedicated editor.',
    label_type: 'Type',
    label_value: 'Value',
    label_label: 'Label (optional)',
    placeholder_value: (type: ContactType) => ({
        phone: 'e.g. +49 30 12345678',
        email: 'e.g. info@example.com',
        whatsapp: 'e.g. +49 170 1234567',
    } as Record<ContactType, string>)[type],
    placeholder_label: 'e.g. Main office',
    add: 'Add contact',
    remove: 'Remove',
    save: 'Save',
    cancel: 'Cancel',
    empty: 'No contacts yet. Add the first one.',
    type_labels: {
        phone: 'Phone',
        email: 'Email',
        whatsapp: 'WhatsApp',
    } as Record<ContactType, string>,
} as const;
const t = computed(() => (props.locale === 'de' ? de : en));

const form = useForm<{ contacts: Contact[] }>({
    contacts: props.contacts.map((c) => ({ ...c })),
});

watch(
    () => [props.open, props.contacts] as const,
    ([open]) => {
        if (open) {
            form.contacts = props.contacts.map((c) => ({ ...c }));
            form.clearErrors();
        }
    },
    { immediate: true },
);

function addRow(): void {
    form.contacts = [
        ...form.contacts,
        { type: 'phone', value: '', label: '' },
    ];
}

function removeRow(idx: number): void {
    form.contacts = form.contacts.filter((_, i) => i !== idx);
}

function close(): void {
    emit('update:open', false);
}

function submit(): void {
    form.post(`/company-portal/${props.companyId}/contacts`, {
        preserveScroll: true,
        onSuccess: () => close(),
    });
}

function rowError(idx: number, field: 'value' | 'label' | 'type'): string | undefined {
    return form.errors[`contacts.${idx}.${field}` as keyof typeof form.errors];
}

const ICONS = { phone: Phone, email: Mail, whatsapp: MessageCircle } as const;
</script>

<template>
    <Dialog :open="open" @update:open="close">
        <DialogContent class="sm:max-w-xl">
            <DialogHeader>
                <DialogTitle>{{ t.title }}</DialogTitle>
                <DialogDescription>{{ t.description }}</DialogDescription>
            </DialogHeader>

            <form class="space-y-4" @submit.prevent="submit">
                <div v-if="form.contacts.length === 0" class="rounded-md border border-dashed border-[var(--linen)] p-4 text-center text-sm text-[var(--slate)]">
                    {{ t.empty }}
                </div>

                <div
                    v-for="(row, idx) in form.contacts"
                    :key="idx"
                    class="rounded-md border border-[var(--linen)] p-3"
                >
                    <div class="mb-2 flex items-center justify-between gap-2">
                        <div class="flex items-center gap-2 text-xs font-semibold text-[var(--slate)]">
                            <component :is="ICONS[row.type]" class="size-3.5" />
                            {{ t.type_labels[row.type] }}
                        </div>
                        <Button
                            type="button"
                            variant="ghost"
                            size="icon-sm"
                            class="text-red-600 hover:text-red-700"
                            :title="t.remove"
                            @click="removeRow(idx)"
                        >
                            <Trash2 class="size-4" />
                        </Button>
                    </div>

                    <div class="grid gap-2">
                        <div>
                            <label class="mb-1 block text-xs text-[var(--slate)]">
                                {{ t.label_type }}
                            </label>
                            <Select
                                :model-value="row.type"
                                @update:model-value="(v) => (row.type = String(v) as ContactType)"
                            >
                                <SelectTrigger>
                                    <SelectValue />
                                </SelectTrigger>
                                <SelectContent>
                                    <SelectItem value="phone">{{ t.type_labels.phone }}</SelectItem>
                                    <SelectItem value="email">{{ t.type_labels.email }}</SelectItem>
                                    <SelectItem value="whatsapp">{{ t.type_labels.whatsapp }}</SelectItem>
                                </SelectContent>
                            </Select>
                        </div>

                        <div>
                            <label class="mb-1 block text-xs text-[var(--slate)]">
                                {{ t.label_value }}
                            </label>
                            <Input
                                v-model="row.value"
                                :placeholder="t.placeholder_value(row.type)"
                                :class="rowError(idx, 'value') ? 'border-red-400' : ''"
                            />
                            <p v-if="rowError(idx, 'value')" class="mt-1 text-xs text-red-600">
                                {{ rowError(idx, 'value') }}
                            </p>
                        </div>

                        <div>
                            <label class="mb-1 block text-xs text-[var(--slate)]">
                                {{ t.label_label }}
                            </label>
                            <Input
                                v-model="row.label"
                                :placeholder="t.placeholder_label"
                            />
                        </div>
                    </div>
                </div>

                <Button type="button" variant="outline" size="sm" @click="addRow">
                    <Plus class="size-4" />
                    {{ t.add }}
                </Button>

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
