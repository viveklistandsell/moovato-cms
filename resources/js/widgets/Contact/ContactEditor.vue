<script setup lang="ts">
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';

type Settings = Record<string, never>;

type Data = {
    eyebrow: string;
    heading_lead: string;
    heading_highlight: string;
    email_label: string;
    email: string;
    phone: string;
    location_label: string;
    address: string;
    name_field_label: string;
    email_field_label: string;
    message_field_label: string;
    message_placeholder: string;
    submit_label: string;
    success_title: string;
    success_text: string;
};

defineModel<Settings>('settings', { required: true });
const data = defineModel<Data>('data', { required: true });
</script>

<template>
    <div class="space-y-6">
        <p
            class="rounded-md border bg-muted/30 px-3 py-2 text-xs text-muted-foreground"
        >
            The form fields (Name, Email, Message) are fixed. Only the
            surrounding text below is editable.
        </p>

        <div class="grid gap-4 md:grid-cols-3">
            <div class="grid gap-2">
                <Label>Eyebrow</Label>
                <Input v-model="data.eyebrow" placeholder="Kontakt" />
            </div>
            <div class="grid gap-2">
                <Label>Heading (line 1)</Label>
                <Input
                    v-model="data.heading_lead"
                    placeholder="Lassen Sie uns Ihren"
                />
            </div>
            <div class="grid gap-2">
                <Label>Heading highlight</Label>
                <Input
                    v-model="data.heading_highlight"
                    placeholder="Umzug planen."
                />
            </div>
        </div>

        <div class="grid gap-4 md:grid-cols-2">
            <div class="grid gap-2">
                <Label>Email label</Label>
                <Input
                    v-model="data.email_label"
                    placeholder="Schreiben Sie uns"
                />
            </div>
            <div class="grid gap-2">
                <Label>Email</Label>
                <Input v-model="data.email" placeholder="hallo@moovato.de" />
            </div>
        </div>

        <div class="grid gap-4 md:grid-cols-2">
            <div class="grid gap-2">
                <Label>Phone</Label>
                <Input v-model="data.phone" placeholder="+49 30 1234 5678" />
            </div>
            <div class="grid gap-2">
                <Label>Location label</Label>
                <Input v-model="data.location_label" placeholder="Standort" />
            </div>
        </div>

        <div class="grid gap-2">
            <Label>Address</Label>
            <Textarea
                v-model="data.address"
                :rows="2"
                placeholder="Moovato Umzüge, Musterstraße 12, 10115 Berlin"
            />
        </div>

        <div class="grid gap-4 md:grid-cols-3">
            <div class="grid gap-2">
                <Label>Name field label</Label>
                <Input v-model="data.name_field_label" placeholder="Name" />
            </div>
            <div class="grid gap-2">
                <Label>Email field label</Label>
                <Input v-model="data.email_field_label" placeholder="E-Mail" />
            </div>
            <div class="grid gap-2">
                <Label>Message field label</Label>
                <Input
                    v-model="data.message_field_label"
                    placeholder="Nachricht"
                />
            </div>
        </div>

        <div class="grid gap-4 md:grid-cols-2">
            <div class="grid gap-2">
                <Label>Message placeholder</Label>
                <Input
                    v-model="data.message_placeholder"
                    placeholder="Erzählen Sie uns kurz von Ihrem Umzug"
                />
            </div>
            <div class="grid gap-2">
                <Label>Submit button label</Label>
                <Input v-model="data.submit_label" placeholder="Jetzt senden" />
            </div>
        </div>

        <div class="grid gap-2">
            <Label>Success title</Label>
            <Input v-model="data.success_title" placeholder="Vielen Dank!" />
        </div>
        <div class="grid gap-2">
            <Label>Success text</Label>
            <Textarea v-model="data.success_text" :rows="2" />
        </div>
    </div>
</template>
