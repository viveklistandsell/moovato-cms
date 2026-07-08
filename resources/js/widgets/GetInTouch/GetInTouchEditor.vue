<script setup lang="ts">
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import WidgetImageField from '@/widgets/shared/WidgetImageField.vue';

type Settings = {
    image_path: string | null;
    image_url: string | null;
};

type Data = {
    eyebrow: string;
    heading: string;
    lead: string;
    image_alt: string;
    primary_label: string;
    primary_url: string;
    secondary_label: string;
    secondary_url: string;
};

const settings = defineModel<Settings>('settings', { required: true });
const data = defineModel<Data>('data', { required: true });
</script>

<template>
    <div class="space-y-6">
        <div class="grid gap-2">
            <Label>Eyebrow</Label>
            <Input
                v-model="data.eyebrow"
                placeholder="Ihr Umzugsunternehmen in Berlin"
            />
        </div>

        <div class="grid gap-2">
            <Label>Heading</Label>
            <Input
                v-model="data.heading"
                placeholder="Lassen Sie uns sprechen."
            />
        </div>

        <div class="grid gap-2">
            <Label>Lead</Label>
            <Textarea v-model="data.lead" :rows="2" />
        </div>

        <div class="grid gap-2">
            <Label>Image (optional)</Label>
            <WidgetImageField
                :path="settings.image_path"
                :url="settings.image_url"
                aspect-class="aspect-[4/3] w-full max-w-[320px]"
                @update="
                    (v) => {
                        settings.image_path = v.path;
                        settings.image_url = v.url;
                    }
                "
            />
            <p class="text-xs text-muted-foreground">
                Leave empty to keep the decorative megaphone illustration.
            </p>
        </div>

        <div class="grid gap-2">
            <Label>Image alt text</Label>
            <Input
                v-model="data.image_alt"
                placeholder="Moovato Umzugsteam in Berlin"
            />
        </div>

        <div class="grid gap-4 md:grid-cols-2">
            <div class="grid gap-2">
                <Label>Primary button label</Label>
                <Input
                    v-model="data.primary_label"
                    placeholder="Jetzt anfragen"
                />
            </div>
            <div class="grid gap-2">
                <Label>Primary button URL</Label>
                <Input v-model="data.primary_url" placeholder="#kontakt" />
            </div>
        </div>

        <div class="grid gap-4 md:grid-cols-2">
            <div class="grid gap-2">
                <Label>Secondary button label</Label>
                <Input
                    v-model="data.secondary_label"
                    placeholder="Termin buchen"
                />
            </div>
            <div class="grid gap-2">
                <Label>Secondary button URL</Label>
                <Input v-model="data.secondary_url" placeholder="#angebot" />
            </div>
        </div>
    </div>
</template>
