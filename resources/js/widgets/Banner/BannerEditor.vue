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

type Settings = {
    variant: 'info' | 'success' | 'warning' | 'danger' | 'brand';
    dismissible: boolean;
    icon: string;
};

type Data = {
    message: string;
    cta_label: string;
    cta_url: string;
};

const settings = defineModel<Settings>('settings', { required: true });
const data = defineModel<Data>('data', { required: true });
</script>

<template>
    <div class="grid gap-6 md:grid-cols-2">
        <div class="space-y-4">
            <div class="grid gap-2">
                <Label>Message</Label>
                <Textarea v-model="data.message" :rows="3" />
            </div>
            <div class="grid grid-cols-2 gap-2">
                <div class="grid gap-1">
                    <Label class="text-xs">CTA label</Label>
                    <Input v-model="data.cta_label" />
                </div>
                <div class="grid gap-1">
                    <Label class="text-xs">CTA URL</Label>
                    <Input v-model="data.cta_url" />
                </div>
            </div>
        </div>
        <div class="space-y-4">
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
                        <SelectItem value="info">Info</SelectItem>
                        <SelectItem value="success">Success</SelectItem>
                        <SelectItem value="warning">Warning</SelectItem>
                        <SelectItem value="danger">Danger</SelectItem>
                        <SelectItem value="brand">Brand</SelectItem>
                    </SelectContent>
                </Select>
            </div>
            <div class="grid gap-2">
                <Label>Lucide icon (optional)</Label>
                <Input v-model="settings.icon" placeholder="Megaphone" />
            </div>
            <div class="flex items-center justify-between">
                <Label for="banner-dismissible">Dismissible</Label>
                <Switch
                    id="banner-dismissible"
                    :model-value="settings.dismissible"
                    @update:model-value="(v) => (settings.dismissible = v)"
                />
            </div>
        </div>
    </div>
</template>
