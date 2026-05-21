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
        <div class="space-y-4">
            <div class="grid gap-2">
                <Label>Eyebrow</Label>
                <Input v-model="data.eyebrow" placeholder="Welcome" />
            </div>
            <div class="grid gap-2">
                <Label>Title</Label>
                <Input v-model="data.title" placeholder="Headline" />
            </div>
            <div class="grid gap-2">
                <Label>Subtitle</Label>
                <Textarea v-model="data.subtitle" :rows="3" />
            </div>
            <div class="grid grid-cols-2 gap-2">
                <div class="grid gap-1">
                    <Label class="text-xs">Primary CTA label</Label>
                    <Input v-model="data.primary_label" />
                </div>
                <div class="grid gap-1">
                    <Label class="text-xs">Primary CTA URL</Label>
                    <Input v-model="data.primary_url" placeholder="/contact" />
                </div>
            </div>
            <div class="grid grid-cols-2 gap-2">
                <div class="grid gap-1">
                    <Label class="text-xs">Secondary CTA label</Label>
                    <Input v-model="data.secondary_label" />
                </div>
                <div class="grid gap-1">
                    <Label class="text-xs">Secondary CTA URL</Label>
                    <Input v-model="data.secondary_url" />
                </div>
            </div>
        </div>

        <div class="space-y-4">
            <WidgetImageField
                label="Background image"
                :path="settings.image_path"
                :url="settings.image_url"
                @update="(v) => { settings.image_path = v.path; settings.image_url = v.url; }"
            />
            <div class="grid gap-2">
                <Label>Alignment</Label>
                <Select
                    :model-value="settings.alignment"
                    @update:model-value="(v) => (settings.alignment = v as Settings['alignment'])"
                >
                    <SelectTrigger><SelectValue /></SelectTrigger>
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
                    @update:model-value="(v) => (settings.height = v as Settings['height'])"
                >
                    <SelectTrigger><SelectValue /></SelectTrigger>
                    <SelectContent>
                        <SelectItem value="sm">Small</SelectItem>
                        <SelectItem value="md">Medium</SelectItem>
                        <SelectItem value="lg">Large</SelectItem>
                        <SelectItem value="xl">Extra large</SelectItem>
                    </SelectContent>
                </Select>
            </div>
            <div class="flex items-center justify-between">
                <Label for="hero-overlay">Dark overlay over image</Label>
                <Switch
                    id="hero-overlay"
                    :model-value="settings.overlay"
                    @update:model-value="(v) => (settings.overlay = v)"
                />
            </div>
        </div>
    </div>
</template>
