<script setup lang="ts">
import { Plus, Trash2 } from 'lucide-vue-next';
import RichTextEditor from '@/components/common/RichTextEditor.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Switch } from '@/components/ui/switch';
import WidgetImageField from '@/widgets/shared/WidgetImageField.vue';

type Item = { question: string; answer: string };

type Settings = {
    image_path: string | null;
    image_url: string | null;
    first_open: boolean;
};

type Data = {
    vertical_label: string;
    image_alt: string;
    eyebrow: string;
    heading_lead: string;
    heading_highlight: string;
    heading_tail: string;
    items: Item[];
    button_label: string;
    button_url: string;
};

const settings = defineModel<Settings>('settings', { required: true });
const data = defineModel<Data>('data', { required: true });

function addItem(): void {
    data.value.items = [
        ...(data.value.items ?? []),
        { question: '', answer: '' },
    ];
}

function removeItem(index: number): void {
    data.value.items = (data.value.items ?? []).filter((_, i) => i !== index);
}
</script>

<template>
    <div class="space-y-6">
        <div class="grid gap-6 md:grid-cols-2">
            <div class="space-y-4">
                <WidgetImageField
                    label="Image"
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
                        placeholder="Moovato Umzugswagen vor einem Berliner Altbau"
                    />
                </div>
                <div class="grid gap-2">
                    <Label>Vertical label</Label>
                    <Input
                        v-model="data.vertical_label"
                        placeholder="Umzugsservice Berlin"
                    />
                </div>
            </div>

            <div class="space-y-4">
                <div class="grid gap-2">
                    <Label>Eyebrow</Label>
                    <Input v-model="data.eyebrow" placeholder="Check FAQ" />
                </div>
                <div class="grid gap-2">
                    <Label>Heading — lead</Label>
                    <Input
                        v-model="data.heading_lead"
                        placeholder="Häufig gestellte"
                    />
                </div>
                <div class="grid gap-2">
                    <Label>Heading — highlight word</Label>
                    <Input
                        v-model="data.heading_highlight"
                        placeholder="Fragen"
                    />
                </div>
                <div class="grid gap-2">
                    <Label>Heading — tail</Label>
                    <Input
                        v-model="data.heading_tail"
                        placeholder="rund um Ihren Umzug."
                    />
                </div>
                <div
                    class="flex items-center justify-between rounded-md border p-3"
                >
                    <Label>Open first item by default</Label>
                    <Switch
                        :model-value="settings.first_open"
                        @update:model-value="(v) => (settings.first_open = v)"
                    />
                </div>
                <div class="grid gap-2">
                    <Label>Button label (optional)</Label>
                    <Input
                        v-model="data.button_label"
                        placeholder="Weitere Fragen? Kontaktieren Sie uns"
                    />
                </div>
                <div class="grid gap-2">
                    <Label>Button URL</Label>
                    <Input v-model="data.button_url" placeholder="#kontakt" />
                </div>
            </div>
        </div>

        <div class="space-y-3">
            <div class="flex items-center justify-between">
                <Label class="text-sm font-semibold">Questions</Label>
                <Button
                    type="button"
                    variant="outline"
                    size="sm"
                    @click="addItem"
                >
                    <Plus class="size-4" />
                    Add question
                </Button>
            </div>
            <div
                v-for="(item, i) in data.items ?? []"
                :key="i"
                class="grid gap-3 rounded-md border bg-muted/30 p-3 md:grid-cols-[1fr_auto]"
            >
                <div class="space-y-2">
                    <Input v-model="item.question" placeholder="Question" />
                    <RichTextEditor v-model="item.answer" placeholder="Answer" />
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
