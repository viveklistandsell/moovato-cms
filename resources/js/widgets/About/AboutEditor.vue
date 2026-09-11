<script setup lang="ts">
import { Plus, Trash2 } from 'lucide-vue-next';
import RichTextEditor from '@/components/common/RichTextEditor.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import WidgetImageField from '@/widgets/shared/WidgetImageField.vue';

type Settings = {
    image_path: string | null;
    image_url: string | null;
};

type Data = {
    eyebrow: string;
    heading: string;
    image_alt: string;
    body: string;
    badge: string;
    features: string[];
    trusted_label: string;
    avatars: string[];
    button_label: string;
    button_url: string;
};

const settings = defineModel<Settings>('settings', { required: true });
const data = defineModel<Data>('data', { required: true });

function addFeature(): void {
    data.value.features = [...(data.value.features ?? []), ''];
}

function removeFeature(index: number): void {
    data.value.features = (data.value.features ?? []).filter(
        (_, i) => i !== index,
    );
}

function addAvatar(): void {
    data.value.avatars = [...(data.value.avatars ?? []), ''];
}

function removeAvatar(index: number): void {
    data.value.avatars = (data.value.avatars ?? []).filter(
        (_, i) => i !== index,
    );
}
</script>

<template>
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
                    placeholder="Moovato Umzugsteam beim Be- und Entladen in Berlin"
                />
            </div>
            <div class="grid gap-2">
                <Label>Floating badge</Label>
                <Input v-model="data.badge" placeholder="Umzug & Montage" />
            </div>
            <div class="grid gap-2">
                <Label>Trusted label</Label>
                <Input
                    v-model="data.trusted_label"
                    placeholder="Zufriedene Kunden"
                />
            </div>
            <div class="space-y-2">
                <div class="flex items-center justify-between">
                    <Label class="text-sm font-semibold">Avatar initials</Label>
                    <Button
                        type="button"
                        variant="outline"
                        size="sm"
                        @click="addAvatar"
                    >
                        <Plus class="size-4" />
                        Add
                    </Button>
                </div>
                <div
                    v-for="(_, i) in data.avatars ?? []"
                    :key="i"
                    class="flex gap-2"
                >
                    <Input
                        v-model="data.avatars[i]"
                        maxlength="3"
                        placeholder="M"
                    />
                    <Button
                        type="button"
                        variant="ghost"
                        size="icon"
                        @click="removeAvatar(i)"
                    >
                        <Trash2 class="size-4 text-destructive" />
                    </Button>
                </div>
            </div>
        </div>

        <div class="space-y-4">
            <div class="grid gap-2">
                <Label>Eyebrow</Label>
                <Input v-model="data.eyebrow" placeholder="Über uns" />
            </div>
            <div class="grid gap-2">
                <Label>Heading</Label>
                <Textarea v-model="data.heading" :rows="3" />
            </div>
            <div class="grid gap-2">
                <Label>Body</Label>
                <RichTextEditor v-model="data.body" placeholder="Body" />
            </div>
            <div class="space-y-2">
                <div class="flex items-center justify-between">
                    <Label class="text-sm font-semibold">Checklist</Label>
                    <Button
                        type="button"
                        variant="outline"
                        size="sm"
                        @click="addFeature"
                    >
                        <Plus class="size-4" />
                        Add
                    </Button>
                </div>
                <div
                    v-for="(_, i) in data.features ?? []"
                    :key="i"
                    class="flex gap-2"
                >
                    <Input
                        v-model="data.features[i]"
                        placeholder="Faire Festpreise"
                    />
                    <Button
                        type="button"
                        variant="ghost"
                        size="icon"
                        @click="removeFeature(i)"
                    >
                        <Trash2 class="size-4 text-destructive" />
                    </Button>
                </div>
            </div>
            <div class="grid grid-cols-2 gap-2">
                <div class="grid gap-1">
                    <Label class="text-xs">Button label</Label>
                    <Input
                        v-model="data.button_label"
                        placeholder="Mehr erfahren"
                    />
                </div>
                <div class="grid gap-1">
                    <Label class="text-xs">Button URL</Label>
                    <Input
                        v-model="data.button_url"
                        placeholder="#leistungen"
                    />
                </div>
            </div>
        </div>
    </div>
</template>
