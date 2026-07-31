<script setup lang="ts">
import { Plus, Trash2 } from 'lucide-vue-next';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import WidgetImageField from '@/widgets/shared/WidgetImageField.vue';

type Settings = {
    images: { path: string; url: string }[];
    image_side: 'left' | 'right';
    marker_style: 'check' | 'chevron';
};

type Data = {
    eyebrow: string;
    heading: string;
    body: string;
    points: string[];
    alts: string[];
    button_label: string;
    button_url: string;
};

const settings = defineModel<Settings>('settings', { required: true });
const data = defineModel<Data>('data', { required: true });

const slots = [0, 1, 2];

function setImage(i: number, v: { path: string | null; url: string | null }): void {
    const next = [...(settings.value.images ?? [])];
    next[i] = { path: v.path ?? '', url: v.url ?? '' };
    settings.value.images = next;
}

function setAlt(i: number, value: string): void {
    const next = [...(data.value.alts ?? [])];
    next[i] = value;
    data.value.alts = next;
}

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
            <div class="grid grid-cols-2 gap-2">
                <div class="grid gap-1">
                    <Label class="text-xs">Image side</Label>
                    <select
                        v-model="settings.image_side"
                        class="h-9 rounded-md border bg-background px-2 text-sm"
                    >
                        <option value="left">Left</option>
                        <option value="right">Right</option>
                    </select>
                </div>
                <div class="grid gap-1">
                    <Label class="text-xs">Marker</Label>
                    <select
                        v-model="settings.marker_style"
                        class="h-9 rounded-md border bg-background px-2 text-sm"
                    >
                        <option value="check">Check</option>
                        <option value="chevron">Chevron</option>
                    </select>
                </div>
            </div>

            <Label class="text-sm font-semibold">Collage images (3)</Label>
            <div
                v-for="i in slots"
                :key="i"
                class="grid gap-2 rounded-md border bg-muted/30 p-2"
            >
                <WidgetImageField
                    :label="`Image ${i + 1}`"
                    aspect-class="aspect-video w-full"
                    :path="settings.images?.[i]?.path ?? null"
                    :url="settings.images?.[i]?.url ?? null"
                    @update="(v) => setImage(i, v)"
                />
                <Input
                    :model-value="data.alts?.[i] ?? ''"
                    placeholder="Alt-Text"
                    @update:model-value="(v) => setAlt(i, v as string)"
                />
            </div>
        </div>

        <div class="space-y-4">
            <div class="grid gap-2">
                <Label>Eyebrow (optional)</Label>
                <Input v-model="data.eyebrow" placeholder="Über uns" />
            </div>
            <div class="grid gap-2">
                <Label>Heading</Label>
                <Textarea v-model="data.heading" :rows="2" />
            </div>
            <div class="grid gap-2">
                <Label>Body</Label>
                <Textarea v-model="data.body" :rows="3" />
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
                        placeholder="Professionelle Verpackung"
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
                    <Label class="text-xs">Button label (optional)</Label>
                    <Input
                        v-model="data.button_label"
                        placeholder="Angebot anfordern"
                    />
                </div>
                <div class="grid gap-1">
                    <Label class="text-xs">Button URL</Label>
                    <Input v-model="data.button_url" placeholder="#kontakt" />
                </div>
            </div>
        </div>
    </div>
</template>
