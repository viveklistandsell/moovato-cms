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

type Settings = {
    highlight?: 'basic' | 'premium' | 'gold' | null;
    cta_url?: string;
    plans?: unknown;
};

type Data = {
    eyebrow: string;
    heading: string;
    cta_label: string;
};

const settings = defineModel<Settings>('settings', { required: true });
const data = defineModel<Data>('data', { required: true });
</script>

<template>
    <div class="space-y-6">
        <div class="grid gap-4 md:grid-cols-2">
            <div class="grid gap-2">
                <Label>Eyebrow</Label>
                <Input v-model="data.eyebrow" placeholder="Für Umzugsunternehmen" />
            </div>
            <div class="grid gap-2">
                <Label>Heading</Label>
                <Input
                    v-model="data.heading"
                    placeholder="Werden Sie Moovato-Partner"
                />
            </div>
        </div>

        <div class="grid gap-4 md:grid-cols-3">
            <div class="grid gap-2">
                <Label>Highlighted plan</Label>
                <Select
                    :model-value="settings.highlight ?? 'premium'"
                    @update:model-value="
                        (v) => (settings.highlight = v as Settings['highlight'])
                    "
                >
                    <SelectTrigger><SelectValue /></SelectTrigger>
                    <SelectContent>
                        <SelectItem value="basic">Basic</SelectItem>
                        <SelectItem value="premium">Premium</SelectItem>
                        <SelectItem value="gold">Gold</SelectItem>
                    </SelectContent>
                </Select>
            </div>
            <div class="grid gap-2">
                <Label>CTA label</Label>
                <Input v-model="data.cta_label" placeholder="Partner werden" />
            </div>
            <div class="grid gap-2">
                <Label>CTA URL</Label>
                <Input v-model="settings.cta_url" placeholder="/partner/register" />
            </div>
        </div>

        <p class="text-sm text-muted-foreground">
            Plan cards (price, features, limits) are pulled live from
            Admin &gt; Plans — they can't be edited here. This keeps the
            public pricing table always in sync with what partners see on
            their own plan page. The preview shows sample cards; the live
            page shows the real plans.
        </p>
    </div>
</template>
