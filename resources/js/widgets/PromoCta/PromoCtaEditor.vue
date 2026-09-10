<script setup lang="ts">
import RichTextEditor from '@/components/common/RichTextEditor.vue';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';

type Data = {
    heading: string;
    description: string;
    button_label: string;
    button_url: string;
    badge_number: string;
    badge_unit: string;
    badge_label: string;
    call_label: string;
    phone: string;
};

const data = defineModel<Data>('data', { required: true });
defineModel<Record<string, unknown>>('settings', { required: true });
</script>

<template>
    <div class="space-y-6">
        <div class="grid gap-2">
            <Label>Heading</Label>
            <Input v-model="data.heading" placeholder="Warum Moovato?" />
        </div>

        <div class="grid gap-2">
            <Label>Description</Label>
            <RichTextEditor
                v-model="data.description"
                placeholder="Beschreibung"
            />
        </div>

        <div class="grid gap-4 md:grid-cols-2">
            <div class="grid gap-2">
                <Label>Button label</Label>
                <Input
                    v-model="data.button_label"
                    placeholder="Mehr erfahren"
                />
            </div>
            <div class="grid gap-2">
                <Label>Button URL</Label>
                <Input v-model="data.button_url" placeholder="#leistungen" />
            </div>
        </div>

        <div class="grid gap-4 md:grid-cols-3">
            <div class="grid gap-2">
                <Label>Badge number</Label>
                <Input v-model="data.badge_number" placeholder="24" />
            </div>
            <div class="grid gap-2">
                <Label>Badge unit</Label>
                <Input v-model="data.badge_unit" placeholder="Stunden" />
            </div>
            <div class="grid gap-2">
                <Label>Badge label</Label>
                <Input v-model="data.badge_label" placeholder="Service" />
            </div>
        </div>

        <div class="grid gap-4 md:grid-cols-2">
            <div class="grid gap-2">
                <Label>Call label</Label>
                <Input v-model="data.call_label" placeholder="Rufen Sie an" />
            </div>
            <div class="grid gap-2">
                <Label>Phone</Label>
                <Input v-model="data.phone" placeholder="030 1234 5678" />
            </div>
        </div>
    </div>
</template>
