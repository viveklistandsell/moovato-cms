<script setup lang="ts">
import RichTextEditor from '@/components/common/RichTextEditor.vue';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';

type Settings = {
    variant: 'brand' | 'muted' | 'dark';
    alignment: 'left' | 'center';
};

type Data = {
    eyebrow: string;
    title: string;
    description: string;
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
                <Input v-model="data.eyebrow" />
            </div>
            <div class="grid gap-2">
                <Label>Title</Label>
                <Input v-model="data.title" />
            </div>
            <div class="grid gap-2">
                <Label>Description</Label>
                <RichTextEditor
                    v-model="data.description"
                    placeholder="Beschreibung"
                />
            </div>
        </div>
        <div class="space-y-4">
            <div class="grid grid-cols-2 gap-2">
                <div class="grid gap-1">
                    <Label class="text-xs">Primary label</Label>
                    <Input v-model="data.primary_label" />
                </div>
                <div class="grid gap-1">
                    <Label class="text-xs">Primary URL</Label>
                    <Input v-model="data.primary_url" />
                </div>
            </div>
            <div class="grid grid-cols-2 gap-2">
                <div class="grid gap-1">
                    <Label class="text-xs">Secondary label</Label>
                    <Input v-model="data.secondary_label" />
                </div>
                <div class="grid gap-1">
                    <Label class="text-xs">Secondary URL</Label>
                    <Input v-model="data.secondary_url" />
                </div>
            </div>
            <div class="grid gap-2">
                <Label>Variant</Label>
                <Select
                    :model-value="settings.variant"
                    @update:model-value="
                        (v) => (settings.variant = v as Settings['variant'])
                    "
                >
                    <SelectTrigger><SelectValue /></SelectTrigger>
                    <SelectContent>
                        <SelectItem value="brand">Brand</SelectItem>
                        <SelectItem value="muted">Muted</SelectItem>
                        <SelectItem value="dark">Dark</SelectItem>
                    </SelectContent>
                </Select>
            </div>
            <div class="grid gap-2">
                <Label>Alignment</Label>
                <Select
                    :model-value="settings.alignment"
                    @update:model-value="
                        (v) => (settings.alignment = v as Settings['alignment'])
                    "
                >
                    <SelectTrigger><SelectValue /></SelectTrigger>
                    <SelectContent>
                        <SelectItem value="left">Left</SelectItem>
                        <SelectItem value="center">Center</SelectItem>
                    </SelectContent>
                </Select>
            </div>
        </div>
    </div>
</template>
