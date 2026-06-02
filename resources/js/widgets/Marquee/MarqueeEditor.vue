<script setup lang="ts">
import { Plus, Trash2 } from 'lucide-vue-next';
import { Button } from '@/components/ui/button';
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

type Settings = {
    speed: 'slow' | 'normal' | 'fast';
    reverse: boolean;
};

type Data = {
    items: string[];
};

const settings = defineModel<Settings>('settings', { required: true });
const data = defineModel<Data>('data', { required: true });

function addItem(): void {
    data.value.items = [...(data.value.items ?? []), ''];
}

function removeItem(index: number): void {
    data.value.items = (data.value.items ?? []).filter((_, i) => i !== index);
}
</script>

<template>
    <div class="space-y-6">
        <div class="grid gap-4 md:grid-cols-2">
            <div class="grid gap-2">
                <Label>Speed</Label>
                <Select
                    :model-value="settings.speed"
                    @update:model-value="
                        (v) => (settings.speed = v as Settings['speed'])
                    "
                >
                    <SelectTrigger><SelectValue /></SelectTrigger>
                    <SelectContent>
                        <SelectItem value="slow">Slow</SelectItem>
                        <SelectItem value="normal">Normal</SelectItem>
                        <SelectItem value="fast">Fast</SelectItem>
                    </SelectContent>
                </Select>
            </div>

            <div
                class="flex items-center justify-between rounded-md border bg-muted/30 px-4 py-3"
            >
                <Label class="mb-0">Reverse direction</Label>
                <Switch
                    :model-value="settings.reverse"
                    @update:model-value="(v) => (settings.reverse = v)"
                />
            </div>
        </div>

        <div class="space-y-3">
            <div class="flex items-center justify-between">
                <Label class="text-sm font-semibold">Words</Label>
                <Button
                    type="button"
                    variant="outline"
                    size="sm"
                    @click="addItem"
                >
                    <Plus class="size-4" />
                    Add word
                </Button>
            </div>
            <div v-for="(_, i) in data.items ?? []" :key="i" class="flex gap-2">
                <Input v-model="data.items[i]" placeholder="Graphic Design" />
                <Button
                    type="button"
                    variant="ghost"
                    size="icon"
                    @click="removeItem(i)"
                >
                    <Trash2 class="size-4 text-destructive" />
                </Button>
            </div>
        </div>
    </div>
</template>
