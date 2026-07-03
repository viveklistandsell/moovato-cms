<script setup lang="ts">
import RichTextEditor from '@/components/common/RichTextEditor.vue';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import WidgetImageField from '@/widgets/shared/WidgetImageField.vue';

type Settings = {
    image_path: string | null;
    image_url: string | null;
    inline_image_path: string | null;
    inline_image_url: string | null;
};

type Data = {
    highlight: string;
    heading: string;
    description: string;
    primary_label: string;
    primary_url: string;
    image_alt: string;
    inline_image_alt: string;
};

const settings = defineModel<Settings>('settings', { required: true });
const data = defineModel<Data>('data', { required: true });
</script>

<template>
    <div class="grid gap-6 md:grid-cols-2">
        <div class="space-y-4">
            <div class="grid gap-2">
                <Label>Highlight</Label>
                <Input
                    v-model="data.highlight"
                    placeholder="Ihr zuverlässiges"
                />
            </div>
            <div class="grid gap-2">
                <Label>Heading</Label>
                <Input
                    v-model="data.heading"
                    placeholder="Umzugsunternehmen in Berlin"
                />
            </div>
            <div class="grid gap-2">
                <Label>Description</Label>
                <RichTextEditor
                    v-model="data.description"
                    placeholder="Beschreibung"
                />
            </div>
            <div class="grid grid-cols-2 gap-2">
                <div class="grid gap-1">
                    <Label class="text-xs">Button label</Label>
                    <Input
                        v-model="data.primary_label"
                        placeholder="Mehr erfahren"
                    />
                </div>
                <div class="grid gap-1">
                    <Label class="text-xs">Button URL</Label>
                    <Input v-model="data.primary_url" placeholder="#angebot" />
                </div>
            </div>
        </div>

        <div class="space-y-4">
            <WidgetImageField
                label="Right image"
                aspect-class="aspect-[10/13] w-full max-w-[220px]"
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
                <Label>Right image alt text</Label>
                <Input
                    v-model="data.image_alt"
                    placeholder="Moovato Umzugsteam in Berlin"
                />
            </div>
            <WidgetImageField
                label="Inline title image (small pill)"
                aspect-class="aspect-[2/1] w-full max-w-[160px]"
                :path="settings.inline_image_path"
                :url="settings.inline_image_url"
                @update="
                    (v) => {
                        settings.inline_image_path = v.path;
                        settings.inline_image_url = v.url;
                    }
                "
            />
            <div class="grid gap-2">
                <Label>Inline image alt text</Label>
                <Input
                    v-model="data.inline_image_alt"
                    placeholder="Moovato Team beim Umzug"
                />
            </div>
        </div>
    </div>
</template>
