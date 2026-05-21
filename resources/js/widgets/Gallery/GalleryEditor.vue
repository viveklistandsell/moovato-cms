<script setup lang="ts">
import { Plus, Trash2 } from 'lucide-vue-next';
import { ref } from 'vue';
import MediaPicker from '@/components/common/MediaPicker.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';

type GalleryImage = { path: string; url: string };

type Settings = {
    layout: 'grid' | 'masonry' | 'carousel';
    columns: 2 | 3 | 4;
    gap: 'sm' | 'md' | 'lg';
    images: GalleryImage[];
};

type Data = {
    heading: string;
    // Per-caption per image index, keyed by string index for JSON-friendliness.
    captions: Record<string, string>;
};

const settings = defineModel<Settings>('settings', { required: true });
const data = defineModel<Data>('data', { required: true });

const pickerOpen = ref(false);

function onPick(file: { path: string; url: string; name: string }): void {
    settings.value.images = [
        ...(settings.value.images ?? []),
        { path: file.path, url: file.url },
    ];
}

function removeImage(index: number): void {
    settings.value.images = (settings.value.images ?? []).filter(
        (_, i) => i !== index,
    );
    // Compact captions so indices realign.
    const next: Record<string, string> = {};
    settings.value.images.forEach((_img, i) => {
        const prev = data.value.captions?.[String(i >= index ? i + 1 : i)];
        if (prev) next[String(i)] = prev;
    });
    data.value.captions = next;
}

function captionFor(index: number): string {
    return data.value.captions?.[String(index)] ?? '';
}

function setCaption(index: number, value: string): void {
    data.value.captions = { ...(data.value.captions ?? {}), [String(index)]: value };
}
</script>

<template>
    <div class="space-y-6">
        <div class="grid gap-4 md:grid-cols-4">
            <div class="grid gap-2 md:col-span-4">
                <Label>Heading</Label>
                <Input v-model="data.heading" />
            </div>
            <div class="grid gap-2">
                <Label>Layout</Label>
                <Select
                    :model-value="settings.layout"
                    @update:model-value="(v) => (settings.layout = v as Settings['layout'])"
                >
                    <SelectTrigger><SelectValue /></SelectTrigger>
                    <SelectContent>
                        <SelectItem value="grid">Grid</SelectItem>
                        <SelectItem value="masonry">Masonry</SelectItem>
                        <SelectItem value="carousel">Carousel</SelectItem>
                    </SelectContent>
                </Select>
            </div>
            <div class="grid gap-2">
                <Label>Columns</Label>
                <Select
                    :model-value="String(settings.columns)"
                    @update:model-value="(v) => (settings.columns = Number(v) as Settings['columns'])"
                >
                    <SelectTrigger><SelectValue /></SelectTrigger>
                    <SelectContent>
                        <SelectItem value="2">2</SelectItem>
                        <SelectItem value="3">3</SelectItem>
                        <SelectItem value="4">4</SelectItem>
                    </SelectContent>
                </Select>
            </div>
            <div class="grid gap-2">
                <Label>Gap</Label>
                <Select
                    :model-value="settings.gap"
                    @update:model-value="(v) => (settings.gap = v as Settings['gap'])"
                >
                    <SelectTrigger><SelectValue /></SelectTrigger>
                    <SelectContent>
                        <SelectItem value="sm">Small</SelectItem>
                        <SelectItem value="md">Medium</SelectItem>
                        <SelectItem value="lg">Large</SelectItem>
                    </SelectContent>
                </Select>
            </div>
            <div class="flex items-end">
                <Button
                    type="button"
                    variant="outline"
                    size="sm"
                    class="w-full"
                    @click="pickerOpen = true"
                >
                    <Plus class="size-4" />
                    Add image
                </Button>
            </div>
        </div>

        <div
            v-if="(settings.images ?? []).length > 0"
            class="grid grid-cols-2 gap-3 sm:grid-cols-3 lg:grid-cols-4"
        >
            <div
                v-for="(img, i) in settings.images"
                :key="i"
                class="space-y-2 rounded-md border bg-muted/30 p-2"
            >
                <div class="aspect-square overflow-hidden rounded-sm bg-muted">
                    <img :src="img.url" alt="" class="size-full object-cover" />
                </div>
                <Input
                    :model-value="captionFor(i)"
                    placeholder="Caption"
                    @update:model-value="(v) => setCaption(i, v as string)"
                />
                <Button
                    type="button"
                    variant="ghost"
                    size="sm"
                    class="w-full"
                    @click="removeImage(i)"
                >
                    <Trash2 class="size-4 text-destructive" />
                </Button>
            </div>
        </div>
        <p v-else class="text-sm text-muted-foreground">
            No images yet — click <strong>Add image</strong> to pick from the media library.
        </p>

        <MediaPicker v-model:open="pickerOpen" @pick="onPick" />
    </div>
</template>
