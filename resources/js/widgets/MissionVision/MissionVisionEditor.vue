<script setup lang="ts">
import RichTextEditor from '@/components/common/RichTextEditor.vue';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import WidgetImageField from '@/widgets/shared/WidgetImageField.vue';

type Settings = {
    image_path: string | null;
    image_url: string | null;
};

type Data = {
    image_alt: string;
    heading: string;
    subheading: string;
    body: string;
    button_label: string;
    button_url: string;
    panel_one_heading: string;
    panel_one_body: string;
    panel_two_heading: string;
    panel_two_body: string;
};

const settings = defineModel<Settings>('settings', { required: true });
const data = defineModel<Data>('data', { required: true });
</script>

<template>
    <div class="space-y-6">
        <div class="grid gap-4 md:grid-cols-2">
            <WidgetImageField
                label="Hero image"
                aspect-class="aspect-[16/9] w-full"
                :path="settings.image_path"
                :url="settings.image_url"
                @update="
                    (v) => {
                        settings.image_path = v.path;
                        settings.image_url = v.url;
                    }
                "
            />
            <div class="space-y-4">
                <div class="grid gap-2">
                    <Label>Image alt text</Label>
                    <Input
                        v-model="data.image_alt"
                        placeholder="Moovato Team beim Handschlag nach einem erfolgreichen Umzug in Berlin"
                    />
                </div>
                <div class="grid gap-2">
                    <Label>Heading</Label>
                    <Textarea v-model="data.heading" :rows="2" />
                </div>
                <div class="grid gap-2">
                    <Label>Subheading</Label>
                    <Input v-model="data.subheading" />
                </div>
            </div>
        </div>

        <div class="grid gap-4 rounded-md border p-4 md:grid-cols-[2fr_1fr_auto]">
            <div class="grid gap-2">
                <Label>Body (below image)</Label>
                <RichTextEditor v-model="data.body" placeholder="Body" />
            </div>
            <div class="grid gap-2">
                <Label>Button label</Label>
                <Input v-model="data.button_label" placeholder="Mehr erfahren" />
            </div>
            <div class="grid gap-2">
                <Label>Button URL</Label>
                <Input v-model="data.button_url" placeholder="#leistungen" />
            </div>
        </div>

        <div class="grid gap-4 md:grid-cols-2">
            <div class="space-y-3 rounded-md border p-4">
                <Label class="text-sm font-semibold">Panel one</Label>
                <div class="grid gap-2">
                    <Label class="text-xs">Heading</Label>
                    <Input
                        v-model="data.panel_one_heading"
                        placeholder="Unser Auftrag"
                    />
                </div>
                <div class="grid gap-2">
                    <Label class="text-xs"
                        >Body (add a bullet list for checkmarks)</Label
                    >
                    <RichTextEditor
                        v-model="data.panel_one_body"
                        placeholder="Body"
                    />
                </div>
            </div>

            <div class="space-y-3 rounded-md border p-4">
                <Label class="text-sm font-semibold">Panel two</Label>
                <div class="grid gap-2">
                    <Label class="text-xs">Heading</Label>
                    <Input
                        v-model="data.panel_two_heading"
                        placeholder="Unsere Vision"
                    />
                </div>
                <div class="grid gap-2">
                    <Label class="text-xs"
                        >Body (add a bullet list for checkmarks)</Label
                    >
                    <RichTextEditor
                        v-model="data.panel_two_body"
                        placeholder="Body"
                    />
                </div>
            </div>
        </div>
    </div>
</template>
