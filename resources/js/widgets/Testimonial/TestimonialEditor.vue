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
import { Textarea } from '@/components/ui/textarea';
import WidgetImageField from '@/widgets/shared/WidgetImageField.vue';

type Item = {
    quote: string;
    author: string;
    role: string;
    avatar_path: string | null;
    avatar_url: string | null;
};

type Settings = {
    layout: 'grid' | 'carousel' | 'single';
    columns: 1 | 2 | 3;
};

type Data = {
    heading: string;
    items: Item[];
};

const settings = defineModel<Settings>('settings', { required: true });
const data = defineModel<Data>('data', { required: true });

function addItem(): void {
    data.value.items = [
        ...(data.value.items ?? []),
        { quote: '', author: '', role: '', avatar_path: null, avatar_url: null },
    ];
}

function removeItem(index: number): void {
    data.value.items = (data.value.items ?? []).filter((_, i) => i !== index);
}
</script>

<template>
    <div class="space-y-6">
        <div class="grid gap-4 md:grid-cols-3">
            <div class="grid gap-2 md:col-span-3">
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
                        <SelectItem value="grid">Grid</SelectItem>
                        <SelectItem value="carousel">Carousel</SelectItem>
                        <SelectItem value="single">Single</SelectItem>
                    </SelectContent>
                </Select>
            </div>
            <div class="grid gap-2 md:col-span-2">
                <Label>Columns (grid only)</Label>
                <Select
                    :model-value="String(settings.columns)"
                    @update:model-value="(v) => (settings.columns = Number(v) as Settings['columns'])"
                >
                    <SelectTrigger><SelectValue /></SelectTrigger>
                    <SelectContent>
                        <SelectItem value="1">1</SelectItem>
                        <SelectItem value="2">2</SelectItem>
                        <SelectItem value="3">3</SelectItem>
                    </SelectContent>
                </Select>
            </div>
        </div>

        <div class="space-y-3">
            <div class="flex items-center justify-between">
                <Label class="text-sm font-semibold">Testimonials</Label>
                <Button type="button" variant="outline" size="sm" @click="addItem">
                    <Plus class="size-4" />
                    Add testimonial
                </Button>
            </div>
            <div
                v-for="(item, i) in data.items ?? []"
                :key="i"
                class="grid gap-3 rounded-md border bg-muted/30 p-3 md:grid-cols-[140px_1fr_auto]"
            >
                <WidgetImageField
                    :path="item.avatar_path"
                    :url="item.avatar_url"
                    aspect-class="size-24 rounded-full mx-auto"
                    @update="(v) => { item.avatar_path = v.path; item.avatar_url = v.url; }"
                />
                <div class="space-y-2">
                    <Textarea v-model="item.quote" :rows="3" placeholder="Quote" />
                    <div class="grid grid-cols-2 gap-2">
                        <Input v-model="item.author" placeholder="Author" />
                        <Input v-model="item.role" placeholder="Role" />
                    </div>
                </div>
                <Button type="button" variant="ghost" size="icon" @click="removeItem(i)">
                    <Trash2 class="size-4 text-destructive" />
                </Button>
            </div>
        </div>
    </div>
</template>
