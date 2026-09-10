<script setup lang="ts">
import { Plus, Trash2 } from 'lucide-vue-next';
import RichTextEditor from '@/components/common/RichTextEditor.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';

type Step = { icon: string; title: string; description: string };

type Data = {
    eyebrow: string;
    heading: string;
    steps: Step[];
    button_label: string;
    button_url: string;
};

defineModel<Record<string, unknown>>('settings', { required: true });
const data = defineModel<Data>('data', { required: true });

function addStep(): void {
    data.value.steps = [
        ...(data.value.steps ?? []),
        { icon: 'Circle', title: '', description: '' },
    ];
}

function removeStep(index: number): void {
    data.value.steps = (data.value.steps ?? []).filter((_, i) => i !== index);
}
</script>

<template>
    <div class="space-y-6">
        <div class="grid gap-4 md:grid-cols-2">
            <div class="grid gap-2">
                <Label>Eyebrow</Label>
                <Input v-model="data.eyebrow" />
            </div>
            <div class="grid gap-2">
                <Label>Heading</Label>
                <Input v-model="data.heading" />
            </div>
            <div class="grid gap-1">
                <Label class="text-xs">Button label (optional)</Label>
                <Input
                    v-model="data.button_label"
                    placeholder="Kostenloses Angebot anfordern"
                />
            </div>
            <div class="grid gap-1">
                <Label class="text-xs">Button URL</Label>
                <Input v-model="data.button_url" placeholder="#angebot" />
            </div>
        </div>

        <div class="space-y-3">
            <div class="flex items-center justify-between">
                <Label class="text-sm font-semibold">Steps</Label>
                <Button
                    type="button"
                    variant="outline"
                    size="sm"
                    @click="addStep"
                >
                    <Plus class="size-4" />
                    Add step
                </Button>
            </div>
            <div
                v-for="(step, i) in data.steps ?? []"
                :key="i"
                class="grid gap-3 rounded-md border bg-muted/30 p-3 md:grid-cols-[120px_1fr_auto]"
            >
                <Input v-model="step.icon" placeholder="Icon (lucide)" />
                <div class="space-y-2">
                    <Input v-model="step.title" placeholder="Title" />
                    <RichTextEditor
                        v-model="step.description"
                        placeholder="Description"
                    />
                </div>
                <Button
                    type="button"
                    variant="ghost"
                    size="icon"
                    @click="removeStep(i)"
                >
                    <Trash2 class="size-4 text-destructive" />
                </Button>
            </div>
        </div>
    </div>
</template>
