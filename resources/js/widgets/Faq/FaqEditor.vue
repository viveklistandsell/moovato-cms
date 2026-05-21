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
import { Textarea } from '@/components/ui/textarea';

type Item = { question: string; answer: string };

type Settings = {
    layout: 'accordion' | 'grid';
    first_open: boolean;
};

type Data = {
    heading: string;
    items: Item[];
};

const settings = defineModel<Settings>('settings', { required: true });
const data = defineModel<Data>('data', { required: true });

function addItem(): void {
    data.value.items = [...(data.value.items ?? []), { question: '', answer: '' }];
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
                <Label>Layout</Label>
                <Select
                    :model-value="settings.layout"
                    @update:model-value="(v) => (settings.layout = v as Settings['layout'])"
                >
                    <SelectTrigger><SelectValue /></SelectTrigger>
                    <SelectContent>
                        <SelectItem value="accordion">Accordion</SelectItem>
                        <SelectItem value="grid">Grid</SelectItem>
                    </SelectContent>
                </Select>
            </div>
            <div class="flex items-center justify-between md:col-span-2">
                <Label for="faq-first-open">Open first item by default</Label>
                <Switch
                    id="faq-first-open"
                    :model-value="settings.first_open"
                    @update:model-value="(v) => (settings.first_open = v)"
                />
            </div>
        </div>

        <div class="space-y-3">
            <div class="flex items-center justify-between">
                <Label class="text-sm font-semibold">Questions</Label>
                <Button type="button" variant="outline" size="sm" @click="addItem">
                    <Plus class="size-4" />
                    Add question
                </Button>
            </div>
            <div
                v-for="(item, i) in data.items ?? []"
                :key="i"
                class="grid gap-2 rounded-md border bg-muted/30 p-3"
            >
                <div class="flex items-center gap-2">
                    <Input v-model="item.question" placeholder="Question" class="flex-1" />
                    <Button type="button" variant="ghost" size="icon" @click="removeItem(i)">
                        <Trash2 class="size-4 text-destructive" />
                    </Button>
                </div>
                <Textarea v-model="item.answer" :rows="3" placeholder="Answer" />
            </div>
        </div>
    </div>
</template>
