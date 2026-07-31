<script setup lang="ts">
import { Plus, Trash2 } from 'lucide-vue-next';
import RichTextEditor from '@/components/common/RichTextEditor.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import WidgetImageField from '@/widgets/shared/WidgetImageField.vue';

type Feature = { title: string; description: string };

type Settings = {
    image_path: string | null;
    image_url: string | null;
};

type Data = {
    eyebrow: string;
    heading: string;
    body: string;
    image_alt: string;
    stat_value: string;
    stat_label: string;
    features: Feature[];
};

const settings = defineModel<Settings>('settings', { required: true });
const data = defineModel<Data>('data', { required: true });

function addFeature(): void {
    data.value.features = [
        ...(data.value.features ?? []),
        { title: '', description: '' },
    ];
}

function removeFeature(index: number): void {
    data.value.features = (data.value.features ?? []).filter(
        (_, i) => i !== index,
    );
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
            <div class="grid gap-2">
                <Label>Image alt text</Label>
                <Input
                    v-model="data.image_alt"
                    placeholder="Moovato Mitarbeiter trägt einen Karton"
                />
            </div>
            <div class="grid grid-cols-2 gap-2">
                <div class="grid gap-1">
                    <Label class="text-xs">Stat value</Label>
                    <Input v-model="data.stat_value" placeholder="1.5k+" />
                </div>
                <div class="grid gap-1">
                    <Label class="text-xs">Stat label</Label>
                    <Input v-model="data.stat_label" placeholder="Kunden" />
                </div>
            </div>
            <div class="grid gap-2">
                <Label>Eyebrow (optional)</Label>
                <Input v-model="data.eyebrow" placeholder="Vertrauen" />
            </div>
            <div class="grid gap-2">
                <Label>Heading</Label>
                <Textarea v-model="data.heading" :rows="2" />
            </div>
            <div class="grid gap-2">
                <Label>Body</Label>
                <Textarea v-model="data.body" :rows="3" />
            </div>
        </div>

        <div class="space-y-3">
            <div class="flex items-center justify-between">
                <Label class="text-sm font-semibold">Numbered features</Label>
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
                v-for="(feature, i) in data.features ?? []"
                :key="i"
                class="grid gap-2 rounded-md border bg-muted/30 p-3"
            >
                <div class="flex gap-2">
                    <Input v-model="feature.title" placeholder="Title" />
                    <Button
                        type="button"
                        variant="ghost"
                        size="icon"
                        @click="removeFeature(i)"
                    >
                        <Trash2 class="size-4 text-destructive" />
                    </Button>
                </div>
                <RichTextEditor
                    v-model="feature.description"
                    placeholder="Description"
                />
            </div>
        </div>
    </div>
</template>
