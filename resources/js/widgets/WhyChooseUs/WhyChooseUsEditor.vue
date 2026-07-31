<script setup lang="ts">
import { Plus, Trash2 } from 'lucide-vue-next';
import RichTextEditor from '@/components/common/RichTextEditor.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import WidgetImageField from '@/widgets/shared/WidgetImageField.vue';

type Card = { icon: string; title: string; description: string };

type Settings = {
    image_path: string | null;
    image_url: string | null;
};

type Data = {
    eyebrow: string;
    heading: string;
    subheading: string;
    image_alt: string;
    cards: Card[];
};

const settings = defineModel<Settings>('settings', { required: true });
const data = defineModel<Data>('data', { required: true });

function addCard(): void {
    data.value.cards = [
        ...(data.value.cards ?? []),
        { icon: 'BadgeCheck', title: '', description: '' },
    ];
}

function removeCard(index: number): void {
    data.value.cards = (data.value.cards ?? []).filter((_, i) => i !== index);
}
</script>

<template>
    <div class="grid gap-6 md:grid-cols-2">
        <div class="space-y-4">
            <div class="grid gap-2">
                <Label>Eyebrow</Label>
                <Input v-model="data.eyebrow" placeholder="Warum Moovato" />
            </div>
            <div class="grid gap-2">
                <Label>Heading</Label>
                <Input
                    v-model="data.heading"
                    placeholder="Darum vertraut Berlin auf Moovato"
                />
            </div>
            <div class="grid gap-2">
                <Label>Subheading</Label>
                <Textarea v-model="data.subheading" :rows="3" />
            </div>

            <WidgetImageField
                label="Sticky image"
                aspect-class="aspect-[4/5] w-full"
                :path="settings.image_path"
                :url="settings.image_url"
                @update="
                    (v) => {
                        settings.image_path = v.path;
                        settings.image_url = v.url;
                    }
                "
            />

            <div class="grid gap-2">
                <Label>Image alt text</Label>
                <Input
                    v-model="data.image_alt"
                    placeholder="Moovato Umzugsteam bei der Arbeit in Berlin"
                />
            </div>
        </div>

        <div class="space-y-3">
            <div class="flex items-center justify-between">
                <Label class="text-sm font-semibold">Cards</Label>
                <Button
                    type="button"
                    variant="outline"
                    size="sm"
                    @click="addCard"
                >
                    <Plus class="size-4" />
                    Add card
                </Button>
            </div>
            <div
                v-for="(card, i) in data.cards ?? []"
                :key="i"
                class="grid gap-3 rounded-md border bg-muted/30 p-3 md:grid-cols-[120px_1fr_auto]"
            >
                <Input v-model="card.icon" placeholder="Icon (lucide)" />
                <div class="space-y-2">
                    <Input v-model="card.title" placeholder="Title" />
                    <RichTextEditor
                        v-model="card.description"
                        placeholder="Description"
                    />
                </div>
                <Button
                    type="button"
                    variant="ghost"
                    size="icon"
                    @click="removeCard(i)"
                >
                    <Trash2 class="size-4 text-destructive" />
                </Button>
            </div>
        </div>
    </div>
</template>
