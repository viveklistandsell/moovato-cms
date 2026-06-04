<script setup lang="ts">
import { Plus, Trash2 } from 'lucide-vue-next';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';

type Stat = { value: string; label: string };

type Settings = {
    variant: 'light' | 'dark';
};

type Data = {
    eyebrow: string;
    heading: string;
    stats: Stat[];
};

const settings = defineModel<Settings>('settings', { required: true });
const data = defineModel<Data>('data', { required: true });

function addStat(): void {
    data.value.stats = [...(data.value.stats ?? []), { value: '', label: '' }];
}

function removeStat(index: number): void {
    data.value.stats = (data.value.stats ?? []).filter((_, i) => i !== index);
}
</script>

<template>
    <div class="space-y-4">
        <div class="grid gap-4 md:grid-cols-3">
            <div class="grid gap-2 md:col-span-2">
                <Label>Heading (optional)</Label>
                <Input v-model="data.heading" placeholder="Moovato in Zahlen" />
            </div>
            <div class="grid gap-1">
                <Label class="text-xs">Variant</Label>
                <select
                    v-model="settings.variant"
                    class="h-9 rounded-md border bg-background px-2 text-sm"
                >
                    <option value="dark">Dark</option>
                    <option value="light">Light</option>
                </select>
            </div>
        </div>
        <div class="grid gap-2">
            <Label>Eyebrow (optional)</Label>
            <Input v-model="data.eyebrow" placeholder="Zahlen & Fakten" />
        </div>

        <div class="space-y-2">
            <div class="flex items-center justify-between">
                <Label class="text-sm font-semibold">Stats</Label>
                <Button
                    type="button"
                    variant="outline"
                    size="sm"
                    @click="addStat"
                >
                    <Plus class="size-4" />
                    Add stat
                </Button>
            </div>
            <div
                v-for="(stat, i) in data.stats ?? []"
                :key="i"
                class="flex gap-2"
            >
                <Input v-model="stat.value" class="w-32" placeholder="12.500+" />
                <Input v-model="stat.label" placeholder="Umzüge in Berlin" />
                <Button
                    type="button"
                    variant="ghost"
                    size="icon"
                    @click="removeStat(i)"
                >
                    <Trash2 class="size-4 text-destructive" />
                </Button>
            </div>
        </div>
    </div>
</template>
