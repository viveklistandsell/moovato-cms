<script setup lang="ts">
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import { Switch } from '@/components/ui/switch';
import { Textarea } from '@/components/ui/textarea';
import WidgetImageField from '@/widgets/shared/WidgetImageField.vue';

type Settings = {
    image_path: string | null;
    image_url: string | null;
    alignment: 'left' | 'center' | 'right';
    overlay: boolean;
    height: 'sm' | 'md' | 'lg' | 'xl';
};

type Data = {
    eyebrow: string;
    title: string;
    subtitle: string;
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
    <div class="grid gap-6 md:grid-cols-2">
        <!-- LEFT: content fields -->
        <div class="space-y-4">
            <div class="grid gap-2">
                <Label>Eyebrow</Label>
                <Input
                    v-model="data.eyebrow"
                    placeholder="Umzugsunternehmen · Berlin"
                />
            </div>

            <div class="grid gap-2">
                <Label>Title</Label>
                <Input
                    v-model="data.title"
                    placeholder="Ihr Umzug in Berlin — stressfrei & zum Festpreis."
                />
            </div>

            <div class="grid gap-2">
                <Label>Subtitle</Label>
                <Textarea
                    v-model="data.subtitle"
                    :rows="3"
                    placeholder="Privat-, Firmen- oder Fernumzug. Wir packen an — pünktlich, versichert und transparent."
                />
            </div>

            <div class="grid grid-cols-2 gap-2">
                <div class="grid gap-1">
                    <Label class="text-xs">Primary CTA label</Label>
                    <Input
                        v-model="data.primary_label"
                        placeholder="Kostenloses Angebot anfordern"
                    />
                </div>
                <div class="grid gap-1">
                    <Label class="text-xs">Primary CTA URL</Label>
                    <Input
                        v-model="data.primary_url"
                        placeholder="#umzugsformular"
                    />
                </div>
            </div>

            <div class="grid grid-cols-2 gap-2">
                <div class="grid gap-1">
                    <Label class="text-xs">Secondary CTA label</Label>
                    <Input
                        v-model="data.secondary_label"
                        placeholder="030 / 000 000"
                    />
                </div>
                <div class="grid gap-1">
                    <Label class="text-xs">Secondary CTA URL</Label>
                    <Input
                        v-model="data.secondary_url"
                        placeholder="tel:+4930000000"
                    />
                </div>
            </div>
        </div>

        <!-- RIGHT: visual / layout settings -->
        <div class="space-y-4">
            <WidgetImageField
                label="Background image"
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
                <Label>Background image alt text</Label>
                <Input
                    v-model="data.image_alt"
                    placeholder="Moovato Umzugswagen und Umzugsteam in Berlin"
                />
            </div>

            <div class="grid gap-2">
                <Label>Alignment</Label>
                <Select
                    :model-value="settings.alignment"
                    @update:model-value="
                        (v) => (settings.alignment = v as Settings['alignment'])
                    "
                >
                    <SelectTrigger>
                        <SelectValue placeholder="Select alignment" />
                    </SelectTrigger>
                    <SelectContent>
                        <SelectItem value="left">Left</SelectItem>
                        <SelectItem value="center">Center</SelectItem>
                        <SelectItem value="right">Right</SelectItem>
                    </SelectContent>
                </Select>
            </div>

            <div class="grid gap-2">
                <Label>Height</Label>
                <Select
                    :model-value="settings.height"
                    @update:model-value="
                        (v) => (settings.height = v as Settings['height'])
                    "
                >
                    <SelectTrigger>
                        <SelectValue placeholder="Select height" />
                    </SelectTrigger>
                    <SelectContent>
                        <SelectItem value="sm">Small</SelectItem>
                        <SelectItem value="md">Medium</SelectItem>
                        <SelectItem value="lg">Large</SelectItem>
                        <SelectItem value="xl">Extra large</SelectItem>
                    </SelectContent>
                </Select>
            </div>

            <div
                class="flex items-center justify-between rounded-[var(--radius-md)] border border-[var(--color-border)] bg-white px-4 py-3"
            >
                <div>
                    <Label class="mb-0">Dark overlay over image</Label>
                    <p class="mt-0.5 text-xs text-[var(--color-muted)]">
                        Improves text contrast on busy photos.
                    </p>
                </div>
                <Switch
                    :model-value="settings.overlay"
                    @update:model-value="(v) => (settings.overlay = v)"
                />
            </div>
        </div>
    </div>
</template>
