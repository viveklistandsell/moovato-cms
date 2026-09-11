<script setup lang="ts">
import { Plus, Trash2 } from 'lucide-vue-next';
import RichTextEditor from '@/components/common/RichTextEditor.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import WidgetImageField from '@/widgets/shared/WidgetImageField.vue';

type Item = {
    title: string;
    url: string;
    icon: string;
    image_alt: string;
    image_path: string | null;
    image_url: string | null;
};

type Settings = {
    bg_image_path: string | null;
    bg_image_url: string | null;
};

type Data = {
    eyebrow: string;
    heading: string;
    description: string;
    step_label: string;
    button_label: string;
    button_url: string;
    items: Item[];
};

const settings = defineModel<Settings>('settings', { required: true });
const data = defineModel<Data>('data', { required: true });

function addItem(): void {
    data.value.items = [
        ...(data.value.items ?? []),
        {
            title: '',
            url: '',
            icon: '',
            image_alt: '',
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
                <Input v-model="data.eyebrow" placeholder="Unser Ablauf" />
            </div>
            <div class="grid gap-2">
                <Label>Heading</Label>
                <Input
                    v-model="data.heading"
                    placeholder="So läuft Ihr Umzug ab."
                />
            </div>
            <div class="grid gap-2 md:col-span-2">
                <Label>Description</Label>
                <RichTextEditor v-model="data.description" placeholder="Description" />
            </div>
            <div class="grid gap-2">
                <Label>Step label</Label>
                <Input v-model="data.step_label" placeholder="Schritt" />
            </div>
            <div class="grid grid-cols-2 gap-2">
                <div class="grid gap-1">
                    <Label class="text-xs">Button label</Label>
                    <Input
                        v-model="data.button_label"
                        placeholder="Alle Leistungen"
                    />
                </div>
                <div class="grid gap-1">
                    <Label class="text-xs">Button URL</Label>
                    <Input
                        v-model="data.button_url"
                        placeholder="/leistungen"
                    />
                </div>
            </div>
            <div class="grid gap-2 md:col-span-2">
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

        <div class="space-y-3">
            <div class="flex items-center justify-between">
                <Label class="text-sm font-semibold">Steps</Label>
                <Button
                    type="button"
                    variant="outline"
                    size="sm"
                    @click="addItem"
                >
                    <Plus class="size-4" />
                    Add step
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
                    aspect-class="aspect-[4/3] w-full"
                    @update="
                        (v) => {
                            item.image_path = v.path;
                            item.image_url = v.url;
                        }
                    "
                />
                <div class="space-y-2">
                    <Input
                        v-model="item.title"
                        placeholder="Anfrage & Beratung"
                    />
                    <div class="grid grid-cols-2 gap-2">
                        <Input v-model="item.url" placeholder="#anfrage" />
                        <Input
                            v-model="item.icon"
                            placeholder="Icon (lucide, z.B. Truck)"
                        />
                    </div>
                    <Input v-model="item.image_alt" placeholder="Alt text" />
                    <p class="text-xs text-muted-foreground">
                        Set a lucide icon name to show an icon instead of the
                        image. Leave empty to use the uploaded image.
                    </p>
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
