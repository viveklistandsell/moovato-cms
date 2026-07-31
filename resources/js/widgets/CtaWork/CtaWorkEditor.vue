<script setup lang="ts">
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import WidgetImageField from '@/widgets/shared/WidgetImageField.vue';

type Settings = {
    bg_image_path: string | null;
    bg_image_url: string | null;
    inline_image_path: string | null;
    inline_image_url: string | null;
};

type Data = {
    title_before: string;
    title_after: string;
    inline_image_alt: string;
    button_label: string;
    button_url: string;
};

const settings = defineModel<Settings>('settings', { required: true });
const data = defineModel<Data>('data', { required: true });
</script>

<template>
    <div class="grid gap-6 md:grid-cols-2">
        <div class="space-y-4">
            <div class="grid grid-cols-2 gap-2">
                <div class="grid gap-1">
                    <Label class="text-xs">Title before image</Label>
                    <Input v-model="data.title_before" placeholder="Jetzt" />
                </div>
                <div class="grid gap-1">
                    <Label class="text-xs">Title after image</Label>
                    <Input
                        v-model="data.title_after"
                        placeholder="Umzug starten"
                    />
                </div>
            </div>
            <div class="grid grid-cols-2 gap-2">
                <div class="grid gap-1">
                    <Label class="text-xs">Button label</Label>
                    <Input
                        v-model="data.button_label"
                        placeholder="Kontakt aufnehmen"
                    />
                </div>
                <div class="grid gap-1">
                    <Label class="text-xs">Button URL</Label>
                    <Input v-model="data.button_url" placeholder="/kontakt" />
                </div>
            </div>
            <div class="grid gap-2">
                <Label>Inline image alt text</Label>
                <Input
                    v-model="data.inline_image_alt"
                    placeholder="Moovato Umzugsteam in Berlin"
                />
            </div>
        </div>

        <div class="space-y-4">
            <div class="grid gap-2">
                <Label>Background image</Label>
                <WidgetImageField
                    :path="settings.bg_image_path"
                    :url="settings.bg_image_url"
                    aspect-class="aspect-[16/7] w-full"
                    @update="
                        (v) => {
                            settings.bg_image_path = v.path;
                            settings.bg_image_url = v.url;
                        }
                    "
                />
            </div>
            <div class="grid gap-2">
                <Label>Inline title image (optional)</Label>
                <WidgetImageField
                    :path="settings.inline_image_path"
                    :url="settings.inline_image_url"
                    aspect-class="aspect-[2/1] w-full max-w-[200px]"
                    @update="
                        (v) => {
                            settings.inline_image_path = v.path;
                            settings.inline_image_url = v.url;
                        }
                    "
                />
            </div>
        </div>
    </div>
</template>
