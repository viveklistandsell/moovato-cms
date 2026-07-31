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
import WidgetImageField from '@/widgets/shared/WidgetImageField.vue';

type Settings = {
    image_path: string | null;
    image_url: string | null;
    aspect: '16/9' | '4/3' | '1/1' | 'auto';
    width: 'narrow' | 'wide' | 'full';
    rounded: boolean;
    link_url: string | null;
};

type Data = { alt: string; caption: string };

const settings = defineModel<Settings>('settings', { required: true });
const data = defineModel<Data>('data', { required: true });
</script>

<template>
    <div class="grid gap-6 md:grid-cols-2">
        <div class="space-y-4">
            <WidgetImageField
                label="Image"
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
                <Label>Link URL (optional)</Label>
                <Input
                    :model-value="settings.link_url ?? ''"
                    placeholder="https://…"
                    @update:model-value="
                        (v) => (settings.link_url = (v as string) || null)
                    "
                />
            </div>
        </div>
        <div class="space-y-4">
            <div class="grid gap-2">
                <Label>Alt text</Label>
                <Input v-model="data.alt" />
            </div>
            <div class="grid gap-2">
                <Label>Caption</Label>
                <Input v-model="data.caption" />
            </div>
            <div class="grid grid-cols-2 gap-2">
                <div class="grid gap-1">
                    <Label class="text-xs">Aspect</Label>
                    <Select
                        :model-value="settings.aspect"
                        @update:model-value="
                            (v) => (settings.aspect = v as Settings['aspect'])
                        "
                    >
                        <SelectTrigger><SelectValue /></SelectTrigger>
                        <SelectContent>
                            <SelectItem value="16/9">16:9</SelectItem>
                            <SelectItem value="4/3">4:3</SelectItem>
                            <SelectItem value="1/1">1:1</SelectItem>
                            <SelectItem value="auto">Auto</SelectItem>
                        </SelectContent>
                    </Select>
                </div>
                <div class="grid gap-1">
                    <Label class="text-xs">Width</Label>
                    <Select
                        :model-value="settings.width"
                        @update:model-value="
                            (v) => (settings.width = v as Settings['width'])
                        "
                    >
                        <SelectTrigger><SelectValue /></SelectTrigger>
                        <SelectContent>
                            <SelectItem value="narrow">Narrow</SelectItem>
                            <SelectItem value="wide">Wide</SelectItem>
                            <SelectItem value="full">Full</SelectItem>
                        </SelectContent>
                    </Select>
                </div>
            </div>
            <div class="flex items-center justify-between">
                <Label for="img-rounded">Rounded corners</Label>
                <Switch
                    id="img-rounded"
                    :model-value="settings.rounded"
                    @update:model-value="(v) => (settings.rounded = v)"
                />
            </div>
        </div>
    </div>
</template>
