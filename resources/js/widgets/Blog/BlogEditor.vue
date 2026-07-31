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
import { Textarea } from '@/components/ui/textarea';

type Settings = {
    count: number;
    columns: 2 | 3 | 4;
    category: string;
    posts?: unknown;
};

type Data = {
    eyebrow: string;
    heading: string;
    subheading: string;
    read_more_label: string;
    cta_label: string;
    cta_url: string;
};

const settings = defineModel<Settings>('settings', { required: true });
const data = defineModel<Data>('data', { required: true });
</script>

<template>
    <div class="space-y-6">
        <div class="grid gap-4 md:grid-cols-2">
            <div class="grid gap-2">
                <Label>Eyebrow</Label>
                <Input v-model="data.eyebrow" placeholder="News & Blog" />
            </div>
            <div class="grid gap-2">
                <Label>Heading</Label>
                <Input
                    v-model="data.heading"
                    placeholder="Aktuelles & Tipps rund um Ihren Umzug"
                />
            </div>
            <div class="grid gap-2 md:col-span-2">
                <Label>Subheading</Label>
                <Textarea v-model="data.subheading" :rows="2" />
            </div>
        </div>

        <div class="grid gap-4 md:grid-cols-3">
            <div class="grid gap-2">
                <Label>Number of posts</Label>
                <Input
                    type="number"
                    min="1"
                    max="12"
                    :model-value="settings.count"
                    @update:model-value="
                        (v) => (settings.count = Number(v) || 1)
                    "
                />
            </div>
            <div class="grid gap-2">
                <Label>Columns</Label>
                <Select
                    :model-value="String(settings.columns)"
                    @update:model-value="
                        (v) =>
                            (settings.columns = Number(
                                v,
                            ) as Settings['columns'])
                    "
                >
                    <SelectTrigger><SelectValue /></SelectTrigger>
                    <SelectContent>
                        <SelectItem value="2">2</SelectItem>
                        <SelectItem value="3">3</SelectItem>
                        <SelectItem value="4">4</SelectItem>
                    </SelectContent>
                </Select>
            </div>
            <div class="grid gap-2">
                <Label>Category filter</Label>
                <Input
                    v-model="settings.category"
                    placeholder="Category permalink (optional)"
                />
            </div>
        </div>

        <div class="grid gap-4 md:grid-cols-3">
            <div class="grid gap-2">
                <Label>Card link label</Label>
                <Input
                    v-model="data.read_more_label"
                    placeholder="Mehr lesen"
                />
            </div>
            <div class="grid gap-2">
                <Label>CTA label</Label>
                <Input
                    v-model="data.cta_label"
                    placeholder="Alle Beiträge ansehen"
                />
            </div>
            <div class="grid gap-2">
                <Label>CTA URL</Label>
                <Input v-model="data.cta_url" placeholder="/blog" />
            </div>
        </div>

        <p class="text-sm text-muted-foreground">
            Posts are pulled automatically from the latest published blog
            entries. The preview shows sample cards; the live page shows real
            posts.
        </p>
    </div>
</template>
