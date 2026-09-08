<script setup lang="ts">
import { Plus, Trash2 } from 'lucide-vue-next';
import RichTextEditor from '@/components/common/RichTextEditor.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import WidgetImageField from '@/widgets/shared/WidgetImageField.vue';

type Settings = {
    image_path: string | null;
    image_url: string | null;
    image_side: 'left' | 'right';
};

type Data = {
    eyebrow: string;
    heading: string;
    body: string;
    list_title: string;
    points: string[];
    button_label: string;
    button_url: string;
};

const settings = defineModel<Settings>('settings', { required: true });
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
            <WidgetImageField
                label="Image"
                aspect-class="aspect-[4/3] w-full"
                :path="settings.image_path"
                :url="settings.image_url"
                @update="
                    (v) => {
                        settings.image_path = v.path;
                        settings.image_url = v.url;
                    }
                "
            />
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
            <div class="grid gap-2">
                <Label>Eyebrow (optional)</Label>
                <Input v-model="data.eyebrow" placeholder="Unsere Leistung" />
            </div>
            <div class="grid gap-2">
                <Label>Heading</Label>
                <Textarea v-model="data.heading" :rows="2" />
            </div>
            <div class="grid gap-2">
                <Label>Body</Label>
                <RichTextEditor v-model="data.body" placeholder="Body" />
            </div>
        </div>

        <div class="space-y-4">
            <div class="grid gap-2">
                <Label>List title (optional)</Label>
                <Input
                    v-model="data.list_title"
                    placeholder="Enthaltene Leistungen"
                />
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
                        placeholder="Küchenmontage"
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
                        placeholder="Service anfragen"
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
