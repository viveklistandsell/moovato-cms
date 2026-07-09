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
import WidgetImageField from '@/widgets/shared/WidgetImageField.vue';

type Stat = { value: string; line1: string; line2: string };

type Settings = {
    theme: 'dark' | 'light';
    bg_image_path: string | null;
    bg_image_url: string | null;
};

type Data = {
    stats: Stat[];
};

const settings = defineModel<Settings>('settings', { required: true });
const data = defineModel<Data>('data', { required: true });

function addStat(): void {
    data.value.stats = [
        ...(data.value.stats ?? []),
        { value: '', line1: '', line2: '' },
    ];
}

function removeStat(index: number): void {
    data.value.stats = (data.value.stats ?? []).filter((_, i) => i !== index);
}
</script>

<template>
    <div class="space-y-6">
        <div class="grid gap-4 md:grid-cols-2">
            <div class="grid gap-2">
                <Label>Theme</Label>
                <Select
                    :model-value="settings.theme"
                    @update:model-value="
                        (v) => (settings.theme = v as Settings['theme'])
                    "
                >
                    <SelectTrigger><SelectValue /></SelectTrigger>
                    <SelectContent>
                        <SelectItem value="dark">Dark</SelectItem>
                        <SelectItem value="light">Light</SelectItem>
                    </SelectContent>
                </Select>
            </div>
            <div class="grid gap-2">
                <Label>Background image (optional)</Label>
                <WidgetImageField
                    :path="settings.bg_image_path"
                    :url="settings.bg_image_url"
                    aspect-class="aspect-[16/6] w-full"
                    @update="
                        (v) => {
                            settings.bg_image_path = v.path;
                            settings.bg_image_url = v.url;
                        }
                    "
                />
            </div>
        </div>

        <!-- Stats -->
        <div class="space-y-3">
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
                class="grid gap-3 rounded-md border bg-muted/30 p-3 md:grid-cols-[120px_1fr_auto]"
            >
                <Input v-model="stat.value" placeholder="500+" />
                <div class="space-y-2">
                    <Input
                        v-model="stat.line1"
                        placeholder="Umzüge in Berlin"
                    />
                    <Input
                        v-model="stat.line2"
                        placeholder="erfolgreich durchgeführt"
                    />
                </div>
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
