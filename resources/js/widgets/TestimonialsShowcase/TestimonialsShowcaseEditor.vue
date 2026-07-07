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
    rating: number;
    rating_text: string;
    quote: string;
    name: string;
    role: string;
    image_path: string | null;
    image_url: string | null;
};

type Settings = Record<string, never>;

type Data = {
    eyebrow: string;
    heading: string;
    button_label: string;
    button_url: string;
    items: Item[];
};

defineModel<Settings>('settings', { required: true });
const data = defineModel<Data>('data', { required: true });

function addItem(): void {
    data.value.items = [
        ...(data.value.items ?? []),
        {
            rating: 5,
            rating_text: '5,0 Bewertung',
            quote: '',
            name: '',
            role: '',
            image_path: null,
            image_url: null,
        },
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
                <Label>Eyebrow</Label>
                <Input v-model="data.eyebrow" placeholder="Kundenstimmen" />
            </div>
            <div class="grid gap-2">
                <Label>Heading</Label>
                <Input
                    v-model="data.heading"
                    placeholder="Echte Bewertungen von Umzügen in Berlin."
                />
            </div>
            <div class="grid gap-2">
                <Label>Button label</Label>
                <Input
                    v-model="data.button_label"
                    placeholder="Alle Bewertungen"
                />
            </div>
            <div class="grid gap-2">
                <Label>Button URL</Label>
                <Input v-model="data.button_url" placeholder="/bewertungen" />
            </div>
        </div>

        <div class="space-y-3">
            <div class="flex items-center justify-between">
                <Label class="text-sm font-semibold">Testimonials</Label>
                <Button
                    type="button"
                    variant="outline"
                    size="sm"
                    @click="addItem"
                >
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
                    :path="item.image_path"
                    :url="item.image_url"
                    aspect-class="size-24 rounded-full mx-auto"
                    @update="
                        (v) => {
                            item.image_path = v.path;
                            item.image_url = v.url;
                        }
                    "
                />
                <div class="space-y-2">
                    <Textarea
                        v-model="item.quote"
                        :rows="3"
                        placeholder="Quote"
                    />
                    <div class="grid grid-cols-2 gap-2">
                        <Input v-model="item.name" placeholder="Name" />
                        <Input
                            v-model="item.role"
                            placeholder="Privatumzug, Berlin-Mitte"
                        />
                    </div>
                    <div class="grid grid-cols-2 gap-2">
                        <Select
                            :model-value="String(item.rating)"
                            @update:model-value="
                                (v) => (item.rating = Number(v))
                            "
                        >
                            <SelectTrigger><SelectValue /></SelectTrigger>
                            <SelectContent>
                                <SelectItem
                                    v-for="n in 5"
                                    :key="n"
                                    :value="String(n)"
                                >
                                    {{ n }} ★
                                </SelectItem>
                            </SelectContent>
                        </Select>
                        <Input
                            v-model="item.rating_text"
                            placeholder="5,0 Bewertung"
                        />
                    </div>
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
