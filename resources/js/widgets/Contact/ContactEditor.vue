<script setup lang="ts">
import { Plus, Trash2 } from 'lucide-vue-next';
import RichTextEditor from '@/components/common/RichTextEditor.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';

type Settings = {
    map_embed_url: string;
};

type Data = {
    phone_title: string;
    phone: string;
    email_title: string;
    email: string;
    location_title: string;
    address: string;
    address_url: string;
    eyebrow: string;
    heading_lead: string;
    heading_highlight: string;
    description: string;
    name_placeholder: string;
    email_placeholder: string;
    phone_placeholder: string;
    service_placeholder: string;
    services: string[];
    message_placeholder: string;
    submit_label: string;
    success_title: string;
    success_text: string;
    watermark: string;
};

const settings = defineModel<Settings>('settings', { required: true });
const data = defineModel<Data>('data', { required: true });

function addService(): void {
    data.value.services = [...(data.value.services ?? []), ''];
}

function removeService(index: number): void {
    data.value.services = (data.value.services ?? []).filter(
        (_, i) => i !== index,
    );
}
</script>

<template>
    <div class="space-y-6">
        <p
            class="rounded-md border bg-muted/30 px-3 py-2 text-xs text-muted-foreground"
        >
            The form fields (Name, Email, Phone, Service, Message) are fixed.
            Only the surrounding text, contact details and service list are
            editable.
        </p>

        <div class="grid gap-4 md:grid-cols-2">
            <div class="grid gap-2">
                <Label>Phone title</Label>
                <Input
                    v-model="data.phone_title"
                    placeholder="Rufen Sie uns an"
                />
            </div>
            <div class="grid gap-2">
                <Label>Phone</Label>
                <Input v-model="data.phone" placeholder="+49 30 1234 5678" />
            </div>
        </div>

        <div class="grid gap-4 md:grid-cols-2">
            <div class="grid gap-2">
                <Label>Email title</Label>
                <Input
                    v-model="data.email_title"
                    placeholder="E-Mail Adresse"
                />
            </div>
            <div class="grid gap-2">
                <Label>Email</Label>
                <Input v-model="data.email" placeholder="hallo@moovato.de" />
            </div>
        </div>

        <div class="grid gap-4 md:grid-cols-2">
            <div class="grid gap-2">
                <Label>Location title</Label>
                <Input
                    v-model="data.location_title"
                    placeholder="Unser Standort"
                />
            </div>
            <div class="grid gap-2">
                <Label>Address</Label>
                <Input
                    v-model="data.address"
                    placeholder="Musterstraße 12, 10115 Berlin"
                />
            </div>
        </div>

        <div class="grid gap-2">
            <Label>Address link (Google Maps)</Label>
            <Input
                v-model="data.address_url"
                placeholder="https://www.google.com/maps?q=..."
            />
        </div>

        <div class="grid gap-2">
            <Label>Map embed URL</Label>
            <Textarea
                v-model="settings.map_embed_url"
                :rows="2"
                placeholder="https://www.google.com/maps?q=Berlin&output=embed"
            />
        </div>

        <div class="grid gap-4 md:grid-cols-3">
            <div class="grid gap-2">
                <Label>Eyebrow</Label>
                <Input
                    v-model="data.eyebrow"
                    placeholder="Kontakt aufnehmen"
                />
            </div>
            <div class="grid gap-2">
                <Label>Heading (line 1)</Label>
                <Input
                    v-model="data.heading_lead"
                    placeholder="Fordern Sie jetzt ein"
                />
            </div>
            <div class="grid gap-2">
                <Label>Heading highlight</Label>
                <Input
                    v-model="data.heading_highlight"
                    placeholder="kostenloses Angebot an."
                />
            </div>
        </div>

        <div class="grid gap-2">
            <Label>Description</Label>
            <RichTextEditor
                v-model="data.description"
                placeholder="Erzählen Sie uns kurz von Ihrem Umzug ..."
            />
        </div>

        <!-- Services -->
        <div class="space-y-3">
            <div class="flex items-center justify-between">
                <Label class="text-sm font-semibold">Services (dropdown)</Label>
                <Button
                    type="button"
                    variant="outline"
                    size="sm"
                    @click="addService"
                >
                    <Plus class="size-4" />
                    Add service
                </Button>
            </div>
            <div
                v-for="(_, i) in data.services ?? []"
                :key="i"
                class="flex gap-2"
            >
                <Input v-model="data.services[i]" placeholder="Privat" />
                <Button
                    type="button"
                    variant="ghost"
                    size="icon"
                    @click="removeService(i)"
                >
                    <Trash2 class="size-4 text-destructive" />
                </Button>
            </div>
        </div>

        <div class="grid gap-4 md:grid-cols-2">
            <div class="grid gap-2">
                <Label>Name placeholder</Label>
                <Input
                    v-model="data.name_placeholder"
                    placeholder="Ihr Name *"
                />
            </div>
            <div class="grid gap-2">
                <Label>Email placeholder</Label>
                <Input
                    v-model="data.email_placeholder"
                    placeholder="Ihre E-Mail *"
                />
            </div>
        </div>

        <div class="grid gap-4 md:grid-cols-3">
            <div class="grid gap-2">
                <Label>Phone placeholder</Label>
                <Input
                    v-model="data.phone_placeholder"
                    placeholder="Ihre Telefonnummer *"
                />
            </div>
            <div class="grid gap-2">
                <Label>Service placeholder</Label>
                <Input
                    v-model="data.service_placeholder"
                    placeholder="Service auswählen"
                />
            </div>
            <div class="grid gap-2">
                <Label>Message placeholder</Label>
                <Input
                    v-model="data.message_placeholder"
                    placeholder="Ihre Nachricht *"
                />
            </div>
        </div>

        <div class="grid gap-4 md:grid-cols-2">
            <div class="grid gap-2">
                <Label>Submit button label</Label>
                <Input
                    v-model="data.submit_label"
                    placeholder="Anfrage senden"
                />
            </div>
            <div class="grid gap-2">
                <Label>Watermark (bottom text)</Label>
                <Input v-model="data.watermark" placeholder="KONTAKT" />
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
