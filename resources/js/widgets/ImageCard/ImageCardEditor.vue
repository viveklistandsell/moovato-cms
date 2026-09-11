<script setup lang="ts">
import RichTextEditor from '@/components/common/RichTextEditor.vue';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Switch } from '@/components/ui/switch';
import WidgetImageField from '@/widgets/shared/WidgetImageField.vue';

type Settings = {
    image_path: string | null;
    image_url: string | null;
    read_more_enabled: boolean;
};

type Data = {
    heading: string;
    body: string;
    image_alt: string;
};

const settings = defineModel<Settings>('settings', { required: true });
const data = defineModel<Data>('data', { required: true });
</script>

<template>
    <div class="grid gap-4 md:grid-cols-2">
        <div class="space-y-4">
            <WidgetImageField
                label="Image"
                aspect-class="aspect-[4/3] w-full"
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
                    placeholder="Glückliches Paar mit Umzugskartons in ihrer neuen Wohnung in Berlin"
                />
            </div>
            <div
                class="flex items-center justify-between rounded-md border p-3"
            >
                <Label for="imagecard-read-more"
                    >Enable Read more / Read less</Label
                >
                <Switch
                    id="imagecard-read-more"
                    :model-value="settings.read_more_enabled"
                    @update:model-value="
                        (v) => (settings.read_more_enabled = v)
                    "
                />
            </div>
        </div>
        <div class="space-y-4">
            <div class="grid gap-2">
                <Label>Heading</Label>
                <Input
                    v-model="data.heading"
                    placeholder="Festpreis statt Stundenzettel"
                />
            </div>
            <div class="grid gap-2">
                <Label>Body</Label>
                <RichTextEditor v-model="data.body" placeholder="Body" />
            </div>
        </div>
    </div>
</template>
