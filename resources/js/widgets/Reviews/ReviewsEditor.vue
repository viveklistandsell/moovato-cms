<script setup lang="ts">
import { Plus, Trash2 } from 'lucide-vue-next';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';

type Testimonial = {
    quote: string;
    author: string;
    role: string;
    avatar_url: string;
    rating: number;
};

type Data = {
    heading: string;
    badge: string;
    rating_value: string;
    rating_label: string;
    rating_sub: string;
    cta_label: string;
    cta_url: string;
    testimonials: Testimonial[];
};

defineModel<Record<string, unknown>>('settings', { required: true });
const data = defineModel<Data>('data', { required: true });

function addItem(): void {
    data.value.testimonials = [
        ...(data.value.testimonials ?? []),
        { quote: '', author: '', role: '', avatar_url: '', rating: 5 },
    ];
}

function removeItem(index: number): void {
    data.value.testimonials = (data.value.testimonials ?? []).filter(
        (_, i) => i !== index,
    );
}
</script>

<template>
    <div class="space-y-6">
        <div class="grid gap-2">
            <Label>Heading</Label>
            <Input v-model="data.heading" />
        </div>
        <div class="grid gap-2">
            <Label>Badge</Label>
            <Input v-model="data.badge" />
        </div>

        <div class="grid gap-4 md:grid-cols-2">
            <div class="grid gap-2">
                <Label>Rating value</Label>
                <Input v-model="data.rating_value" placeholder="4,9" />
            </div>
            <div class="grid gap-2">
                <Label>Rating label</Label>
                <Input v-model="data.rating_label" />
            </div>
            <div class="grid gap-2 md:col-span-2">
                <Label>Rating subtitle</Label>
                <Input v-model="data.rating_sub" />
            </div>
            <div class="grid gap-2">
                <Label>Button label</Label>
                <Input v-model="data.cta_label" />
            </div>
            <div class="grid gap-2">
                <Label>Button link</Label>
                <Input v-model="data.cta_url" />
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
                v-for="(item, i) in data.testimonials ?? []"
                :key="i"
                class="space-y-2 rounded-md border bg-muted/30 p-3"
            >
                <Textarea v-model="item.quote" :rows="2" placeholder="Quote" />
                <div class="grid gap-2 md:grid-cols-3">
                    <Input v-model="item.author" placeholder="Author" />
                    <Input v-model="item.role" placeholder="Role / location" />
                    <Input
                        v-model.number="item.rating"
                        type="number"
                        min="1"
                        max="5"
                        placeholder="Rating (1-5)"
                    />
                </div>
                <div class="flex items-center gap-3">
                    <Input
                        v-model="item.avatar_url"
                        placeholder="Avatar image URL (optional)"
                        class="flex-1"
                    />
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
    </div>
</template>
