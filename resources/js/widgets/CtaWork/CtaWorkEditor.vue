<script setup lang="ts">
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import WidgetImageField from '@/widgets/shared/WidgetImageField.vue';

type Settings = {
    media_image_path: string | null;
    media_image_url: string | null;
};

type Data = {
    heading: string;
    subtext: string;
    media_image_alt: string;
    button_label: string;
    button_url: string;
};

const settings = defineModel<Settings>('settings', { required: true });
const data = defineModel<Data>('data', { required: true });
</script>

<template>
    <div class="grid gap-6 md:grid-cols-2">
        <div class="space-y-4">
            <div class="grid gap-2">
                <Label>Heading</Label>
                <Textarea
                    v-model="data.heading"
                    :rows="2"
                    placeholder="Bereit für Ihren stressfreien Umzug mit Moovato?"
                />
            </div>
            <div class="grid gap-2">
                <Label>Subtext</Label>
                <Textarea
                    v-model="data.subtext"
                    :rows="2"
                    placeholder="Erhalten Sie in wenigen Minuten ein unverbindliches Angebot…"
                />
            </div>
            <div class="grid grid-cols-2 gap-2">
                <div class="grid gap-1">
                    <Label class="text-xs">Button label</Label>
                    <Input
                        v-model="data.button_label"
                        placeholder="Jetzt Angebot anfordern"
                    />
                </div>
                <div class="grid gap-1">
                    <Label class="text-xs">Button URL</Label>
                    <Input v-model="data.button_url" placeholder="/kontakt" />
                </div>
            </div>
            <div class="grid gap-2">
                <Label>Alt-Text (Deko-Bild)</Label>
                <Input
                    v-model="data.media_image_alt"
                    placeholder="Umzugskartons und Sackkarre für Ihren Umzug mit Moovato"
                />
            </div>
        </div>

        <div class="space-y-4">
            <div class="grid gap-2">
                <Label>Deko-Bild (rechte Spalte)</Label>
                <WidgetImageField
                    :path="settings.media_image_path"
                    :url="settings.media_image_url"
                    aspect-class="aspect-square w-full max-w-[220px]"
                    @update="
                        (v) => {
                            settings.media_image_path = v.path;
                            settings.media_image_url = v.url;
                        }
                    "
                />
            </div>
        </div>
    </div>
</template>
