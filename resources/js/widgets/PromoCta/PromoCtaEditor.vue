<script setup lang="ts">
import { Plus, Trash2 } from 'lucide-vue-next';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';

type Data = {
    heading: string;
    features: string[];
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

function addFeature(): void {
    data.value.features = [...(data.value.features ?? []), ''];
}

function removeFeature(index: number): void {
    data.value.features = (data.value.features ?? []).filter(
        (_, i) => i !== index,
    );
}
</script>

<template>
    <div class="space-y-6">
        <div class="grid gap-2">
            <Label>Heading</Label>
            <Input v-model="data.heading" placeholder="Warum Moovato?" />
        </div>

        <div class="space-y-2">
            <div class="flex items-center justify-between">
                <Label class="text-sm font-semibold">Features</Label>
                <Button
                    type="button"
                    variant="outline"
                    size="sm"
                    @click="addFeature"
                >
                    <Plus class="size-4" />
                    Add
                </Button>
            </div>
            <div
                v-for="(_, i) in data.features ?? []"
                :key="i"
                class="flex gap-2"
            >
                <Input
                    v-model="data.features[i]"
                    placeholder="Festpreisgarantie"
                />
                <Button
                    type="button"
                    variant="ghost"
                    size="icon"
                    @click="removeFeature(i)"
                >
                    <Trash2 class="size-4 text-destructive" />
                </Button>
            </div>
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
