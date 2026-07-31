<script setup lang="ts">
import { Plus, Trash2 } from 'lucide-vue-next';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';

type Settings = Record<string, never>;

type Data = {
    heading: string;
    subheading: string;
    body: string;
    intro: string;
    points: string[];
    outro: string;
};

defineModel<Settings>('settings', { required: true });
const data = defineModel<Data>('data', { required: true });

function addPoint(): void {
    data.value.points = [...(data.value.points ?? []), ''];
}

function removePoint(index: number): void {
    data.value.points = (data.value.points ?? []).filter((_, i) => i !== index);
}
</script>

<template>
    <div class="grid gap-6 md:grid-cols-2">
        <div class="space-y-4">
            <div class="grid gap-2">
                <Label>Heading</Label>
                <Textarea v-model="data.heading" :rows="2" />
            </div>
            <div class="grid gap-2">
                <Label>Subheading</Label>
                <Input
                    v-model="data.subheading"
                    placeholder="Was bedeutet ein Rundum-Umzug?"
                />
            </div>
            <div class="grid gap-2">
                <Label>Body (left column)</Label>
                <Textarea v-model="data.body" :rows="6" />
                <p class="text-xs text-muted-foreground">
                    Leerzeile = neuer Absatz.
                </p>
            </div>
        </div>

        <div class="space-y-4">
            <div class="grid gap-2">
                <Label>Intro (right column)</Label>
                <Textarea v-model="data.intro" :rows="2" />
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
                        placeholder="Klare Festpreise statt versteckter Kosten"
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
            <div class="grid gap-2">
                <Label>Outro (right column)</Label>
                <Textarea v-model="data.outro" :rows="2" />
            </div>
        </div>
    </div>
</template>
