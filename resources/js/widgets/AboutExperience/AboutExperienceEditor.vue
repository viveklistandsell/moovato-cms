<script setup lang="ts">
import RichTextEditor from '@/components/common/RichTextEditor.vue';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import WidgetImageField from '@/widgets/shared/WidgetImageField.vue';

type Settings = {
    image_path: string | null;
    image_url: string | null;
    image_2_path: string | null;
    image_2_url: string | null;
    founder_path: string | null;
    founder_url: string | null;
};

type Data = {
    eyebrow: string;
    heading: string;
    heading_accent: string;
    body: string;
    experience_value: string;
    experience_label: string;
    image_alt: string;
    image_2_alt: string;
    button_label: string;
    button_url: string;
    founder_name: string;
    founder_role: string;
    founder_alt: string;
};

const settings = defineModel<Settings>('settings', { required: true });
const data = defineModel<Data>('data', { required: true });
</script>

<template>
    <div class="grid gap-6 md:grid-cols-2">
        <div class="space-y-4">
            <WidgetImageField
                label="Image 1 (main)"
                aspect-class="aspect-[4/5] w-full"
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
                <Label>Image 1 alt text</Label>
                <Input
                    v-model="data.image_alt"
                    placeholder="Moovato Kundin trägt einen Umzugskarton"
                />
            </div>
            <WidgetImageField
                label="Image 2 (overlay)"
                aspect-class="aspect-[4/3] w-full"
                :path="settings.image_2_path"
                :url="settings.image_2_url"
                @update="
                    (v) => {
                        settings.image_2_path = v.path;
                        settings.image_2_url = v.url;
                    }
                "
            />
            <div class="grid gap-2">
                <Label>Image 2 alt text</Label>
                <Input
                    v-model="data.image_2_alt"
                    placeholder="Moovato Mitarbeiter prüft Pakete"
                />
            </div>
            <div class="grid grid-cols-2 gap-2">
                <div class="grid gap-1">
                    <Label class="text-xs">Experience value</Label>
                    <Input
                        v-model="data.experience_value"
                        placeholder="25+"
                    />
                </div>
                <div class="grid gap-1">
                    <Label class="text-xs">Experience label</Label>
                    <Input
                        v-model="data.experience_label"
                        placeholder="Jahre Erfahrung"
                    />
                </div>
            </div>
        </div>

        <div class="space-y-4">
            <div class="grid gap-2">
                <Label>Eyebrow</Label>
                <Input v-model="data.eyebrow" placeholder="Über uns" />
            </div>
            <div class="grid gap-2">
                <Label>Heading</Label>
                <Textarea v-model="data.heading" :rows="2" />
            </div>
            <div class="grid gap-2">
                <Label>Heading accent (orange)</Label>
                <Input v-model="data.heading_accent" placeholder="in Berlin" />
            </div>
            <div class="grid gap-2">
                <Label>Body</Label>
                <RichTextEditor v-model="data.body" placeholder="Body" />
            </div>
            <div class="grid grid-cols-2 gap-2">
                <div class="grid gap-1">
                    <Label class="text-xs">Button label</Label>
                    <Input
                        v-model="data.button_label"
                        placeholder="Mehr über uns"
                    />
                </div>
                <div class="grid gap-1">
                    <Label class="text-xs">Button URL</Label>
                    <Input
                        v-model="data.button_url"
                        placeholder="#leistungen"
                    />
                </div>
            </div>

            <div class="space-y-3 rounded-md border bg-muted/30 p-3">
                <Label class="text-sm font-semibold">Founder</Label>
                <WidgetImageField
                    label="Founder photo"
                    aspect-class="size-20 rounded-full"
                    :path="settings.founder_path"
                    :url="settings.founder_url"
                    @update="
                        (v) => {
                            settings.founder_path = v.path;
                            settings.founder_url = v.url;
                        }
                    "
                />
                <div class="grid grid-cols-2 gap-2">
                    <Input
                        v-model="data.founder_name"
                        placeholder="Lukas Hoffmann"
                    />
                    <Input v-model="data.founder_role" placeholder="Gründer" />
                </div>
                <Input
                    v-model="data.founder_alt"
                    placeholder="Porträt von Lukas Hoffmann"
                />
            </div>
        </div>
    </div>
</template>
