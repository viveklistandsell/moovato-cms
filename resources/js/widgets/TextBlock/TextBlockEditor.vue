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
    width: 'narrow' | 'wide' | 'full';
    alignment: 'left' | 'center' | 'right';
    background: 'none' | 'muted' | 'brand';
};

type Data = { heading: string; body: string };

const settings = defineModel<Settings>('settings', { required: true });
const data = defineModel<Data>('data', { required: true });
</script>

<template>
    <div class="grid gap-6 md:grid-cols-2">
        <div class="space-y-4 md:col-span-2">
            <div class="grid gap-2">
                <Label>Heading</Label>
                <Input v-model="data.heading" placeholder="Section heading" />
            </div>
            <div class="grid gap-2">
                <Label>Body</Label>
                <RichTextEditor
                    :model-value="data.body"
                    placeholder="Body text"
                    @update:model-value="(v) => (data.body = v)"
                />
            </div>
        </div>

        <div class="grid gap-2">
            <Label>Width</Label>
            <Select
                :model-value="settings.width"
                @update:model-value="
                    (v) => (settings.width = v as Settings['width'])
                "
            >
                <SelectTrigger><SelectValue /></SelectTrigger>
                <SelectContent>
                    <SelectItem value="narrow">Narrow (prose)</SelectItem>
                    <SelectItem value="wide">Wide</SelectItem>
                    <SelectItem value="full">Full width</SelectItem>
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
                    <SelectItem value="right">Right</SelectItem>
                </SelectContent>
            </Select>
        </div>
        <div class="grid gap-2 md:col-span-2">
            <Label>Background</Label>
            <Select
                :model-value="settings.background"
                @update:model-value="
                    (v) => (settings.background = v as Settings['background'])
                "
            >
                <SelectTrigger><SelectValue /></SelectTrigger>
                <SelectContent>
                    <SelectItem value="none">None</SelectItem>
                    <SelectItem value="muted">Muted</SelectItem>
                    <SelectItem value="brand">Brand tint</SelectItem>
                </SelectContent>
            </Select>
        </div>
    </div>
</template>
