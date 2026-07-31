<script setup lang="ts">
import { Plus, Trash2 } from 'lucide-vue-next';
import RichTextEditor from '@/components/common/RichTextEditor.vue';
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

type Item = { icon: string; title: string; description: string };

type Settings = {
    columns: 2 | 3 | 4;
    icon_style: 'badge' | 'outline' | 'plain';
};

type Data = {
    heading: string;
    subheading: string;
    items: Item[];
};

const settings = defineModel<Settings>('settings', { required: true });
const data = defineModel<Data>('data', { required: true });

function addItem(): void {
    data.value.items = [
        ...(data.value.items ?? []),
        { icon: 'Zap', title: '', description: '' },
    ];
}

function removeItem(index: number): void {
    data.value.items = (data.value.items ?? []).filter((_, i) => i !== index);
}
</script>

<template>
    <div class="space-y-6">
        <div class="grid gap-4 md:grid-cols-2">
            <div class="grid gap-2">
                <Label>Heading</Label>
                <Input v-model="data.heading" />
            </div>
            <div class="grid gap-2">
                <Label>Subheading</Label>
                <Input v-model="data.subheading" />
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
                <Label>Icon style</Label>
                <Select
                    :model-value="settings.icon_style"
                    @update:model-value="
                        (v) =>
                            (settings.icon_style = v as Settings['icon_style'])
                    "
                >
                    <SelectTrigger><SelectValue /></SelectTrigger>
                    <SelectContent>
                        <SelectItem value="badge">Badge</SelectItem>
                        <SelectItem value="outline">Outline</SelectItem>
                        <SelectItem value="plain">Plain</SelectItem>
                    </SelectContent>
                </Select>
            </div>
        </div>

        <div class="space-y-3">
            <div class="flex items-center justify-between">
                <Label class="text-sm font-semibold">Feature items</Label>
                <Button
                    type="button"
                    variant="outline"
                    size="sm"
                    @click="addItem"
                >
                    <Plus class="size-4" />
                    Add item
                </Button>
            </div>
            <div
                v-for="(item, i) in data.items ?? []"
                :key="i"
                class="grid gap-3 rounded-md border bg-muted/30 p-3 md:grid-cols-[120px_1fr_auto]"
            >
                <Input v-model="item.icon" placeholder="Icon (lucide)" />
                <div class="space-y-2">
                    <Input v-model="item.title" placeholder="Title" />
                    <RichTextEditor
                        v-model="item.description"
                        placeholder="Description"
                    />
                </div>
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
