<script setup lang="ts">
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';

type Settings = Record<string, never>;

type Data = {
    eyebrow: string;
    heading: string;
    lead: string;
    primary_label: string;
    primary_url: string;
    secondary_label: string;
    secondary_url: string;
};

defineModel<Settings>('settings', { required: true });
const data = defineModel<Data>('data', { required: true });
</script>

<template>
    <div class="space-y-6">
        <div class="grid gap-2">
            <Label>Eyebrow</Label>
            <Input
                v-model="data.eyebrow"
                placeholder="Ihr Umzugsunternehmen in Berlin"
            />
        </div>

        <div class="grid gap-2">
            <Label>Heading</Label>
            <Input
                v-model="data.heading"
                placeholder="Lassen Sie uns sprechen."
            />
        </div>

        <div class="grid gap-2">
            <Label>Lead</Label>
            <Textarea v-model="data.lead" :rows="2" />
        </div>

        <div class="grid gap-4 md:grid-cols-2">
            <div class="grid gap-2">
                <Label>Primary button label</Label>
                <Input
                    v-model="data.primary_label"
                    placeholder="Jetzt anfragen"
                />
            </div>
            <div class="grid gap-2">
                <Label>Primary button URL</Label>
                <Input v-model="data.primary_url" placeholder="#kontakt" />
            </div>
        </div>

        <div class="grid gap-4 md:grid-cols-2">
            <div class="grid gap-2">
                <Label>Secondary button label</Label>
                <Input
                    v-model="data.secondary_label"
                    placeholder="Termin buchen"
                />
            </div>
            <div class="grid gap-2">
                <Label>Secondary button URL</Label>
                <Input v-model="data.secondary_url" placeholder="#angebot" />
            </div>
        </div>
    </div>
</template>
