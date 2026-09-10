<script setup lang="ts">
import RichTextEditor from '@/components/common/RichTextEditor.vue';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import WidgetImageField from '@/widgets/shared/WidgetImageField.vue';

type Settings = {
    image_path: string | null;
    image_url: string | null;
    image_side: 'left' | 'right';
    card: boolean;
    bg?: 'none' | 'tinted' | 'dark';
};

type Data = {
    eyebrow: string;
    heading: string;
    body: string;
    image_alt: string;
    button_label: string;
    button_url: string;
};

const settings = defineModel<Settings>('settings', { required: true });
const data = defineModel<Data>('data', { required: true });
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
                    placeholder="Moovato Beraterin plant einen Umzug"
                />
            </div>
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
                    <Label class="text-xs">Card style</Label>
                    <select
                        v-model="settings.card"
                        class="h-9 rounded-md border bg-background px-2 text-sm"
                    >
                        <option :value="true">On</option>
                        <option :value="false">Off</option>
                    </select>
                </div>
            </div>
            <div class="grid gap-1">
                <Label class="text-xs">Background</Label>
                <select
                    v-model="settings.bg"
                    class="h-9 rounded-md border bg-background px-2 text-sm"
                >
                    <option value="none">None</option>
                    <option value="tinted">Tinted</option>
                    <option value="dark">Dark</option>
                </select>
            </div>
        </div>

        <div class="space-y-4">
            <div class="grid gap-2">
                <Label>Eyebrow (optional)</Label>
                <Input v-model="data.eyebrow" placeholder="Warum Moovato" />
            </div>
            <div class="grid gap-2">
                <Label>Heading</Label>
                <Textarea v-model="data.heading" :rows="2" />
            </div>
            <div class="grid gap-2">
                <Label>Body</Label>
                <RichTextEditor v-model="data.body" placeholder="Body" />
            </div>
            <div class="grid grid-cols-2 gap-2">
                <div class="grid gap-1">
                    <Label class="text-xs">Button label (optional)</Label>
                    <Input
                        v-model="data.button_label"
                        placeholder="Beratung anfragen"
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
