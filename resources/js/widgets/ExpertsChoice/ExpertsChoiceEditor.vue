<script setup lang="ts">
import { Plus, Trash2 } from 'lucide-vue-next';
import RichTextEditor from '@/components/common/RichTextEditor.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';

type Card = { icon: string; title: string; description: string };

type Settings = Record<string, never>;

type Data = {
    eyebrow: string;
    heading: string;
    body: string;
    points: string[];
    button_label: string;
    button_url: string;
    since_label: string;
    cards: Card[];
};

defineModel<Settings>('settings', { required: true });
const data = defineModel<Data>('data', { required: true });

function addPoint(): void {
    data.value.points = [...(data.value.points ?? []), ''];
}

function removePoint(index: number): void {
    data.value.points = (data.value.points ?? []).filter((_, i) => i !== index);
}

function addCard(): void {
    data.value.cards = [
        ...(data.value.cards ?? []),
        { icon: 'BadgeCheck', title: '', description: '' },
    ];
}

function removeCard(index: number): void {
    data.value.cards = (data.value.cards ?? []).filter((_, i) => i !== index);
}
</script>

<template>
    <div class="grid gap-6 md:grid-cols-2">
        <div class="space-y-4">
            <div class="grid gap-2">
                <Label>Eyebrow</Label>
                <Input v-model="data.eyebrow" placeholder="Warum Moovato" />
            </div>
            <div class="grid gap-2">
                <Label>Heading</Label>
                <Input
                    v-model="data.heading"
                    placeholder="Die Wahl der Profis"
                />
            </div>
            <div class="grid gap-2">
                <Label>Body</Label>
                <RichTextEditor v-model="data.body" placeholder="Body" />
            </div>
            <div class="space-y-2">
                <div class="flex items-center justify-between">
                    <Label class="text-sm font-semibold">Checklist</Label>
                    <Button
                        type="button"
                        variant="outline"
                        size="sm"
                        @click="addPoint"
                    >
                        <Plus class="size-4" />
                        Add
                    </Button>
                </div>
                <div
                    v-for="(_, i) in data.points ?? []"
                    :key="i"
                    class="flex gap-2"
                >
                    <Input
                        v-model="data.points[i]"
                        placeholder="Saubere, beschriftete Umzugsfahrzeuge"
                    />
                    <Button
                        type="button"
                        variant="ghost"
                        size="icon"
                        @click="removePoint(i)"
                    >
                        <Trash2 class="size-4 text-destructive" />
                    </Button>
                </div>
            </div>
            <div class="grid grid-cols-2 gap-2">
                <div class="grid gap-1">
                    <Label class="text-xs">Button label</Label>
                    <Input
                        v-model="data.button_label"
                        placeholder="So arbeiten wir"
                    />
                </div>
                <div class="grid gap-1">
                    <Label class="text-xs">Button URL</Label>
                    <Input v-model="data.button_url" placeholder="#ablauf" />
                </div>
            </div>
            <div class="grid gap-1">
                <Label class="text-xs">Since label</Label>
                <Input v-model="data.since_label" placeholder="Seit 2014" />
            </div>
        </div>

        <div class="space-y-3">
            <div class="flex items-center justify-between">
                <Label class="text-sm font-semibold">Feature cards</Label>
                <Button
                    type="button"
                    variant="outline"
                    size="sm"
                    @click="addCard"
                >
                    <Plus class="size-4" />
                    Add card
                </Button>
            </div>
            <div
                v-for="(card, i) in data.cards ?? []"
                :key="i"
                class="grid gap-3 rounded-md border bg-muted/30 p-3 md:grid-cols-[120px_1fr_auto]"
            >
                <Input v-model="card.icon" placeholder="Icon (lucide)" />
                <div class="space-y-2">
                    <Input v-model="card.title" placeholder="Title" />
                    <RichTextEditor
                        v-model="card.description"
                        placeholder="Description"
                    />
                </div>
                <Button
                    type="button"
                    variant="ghost"
                    size="icon"
                    @click="removeCard(i)"
                >
                    <Trash2 class="size-4 text-destructive" />
                </Button>
            </div>
        </div>
    </div>
</template>
