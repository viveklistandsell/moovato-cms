<script setup lang="ts">
import { Plus, Trash2 } from 'lucide-vue-next';
import RichTextEditor from '@/components/common/RichTextEditor.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Switch } from '@/components/ui/switch';
import { Textarea } from '@/components/ui/textarea';
import WidgetImageField from '@/widgets/shared/WidgetImageField.vue';

type Settings = {
    read_more_enabled: boolean;
    image_card_enabled: boolean;
    image_card_read_more_enabled: boolean;
    image_path: string | null;
    image_url: string | null;
};

type Data = {
    heading: string;
    subheading: string;
    body: string;
    intro: string;
    points: string[];
    outro: string;
    image_alt: string;
    image_card_heading: string;
    image_card_body: string;
};

const settings = defineModel<Settings>('settings', { required: true });
const data = defineModel<Data>('data', { required: true });

function addPoint(): void {
    data.value.points = [...(data.value.points ?? []), ''];
}

function removePoint(index: number): void {
    data.value.points = (data.value.points ?? []).filter((_, i) => i !== index);
}
</script>

<template>
    <div class="grid gap-6 md:grid-cols-2">
        <div class="space-y-4 md:col-span-2">
            <div
                class="flex items-center justify-between rounded-md border p-3"
            >
                <Label for="textcols-read-more">Enable Read more / Read less</Label>
                <Switch
                    id="textcols-read-more"
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
                <Textarea v-model="data.heading" :rows="2" />
            </div>
            <div class="grid gap-2">
                <Label>Subheading</Label>
                <Input
                    v-model="data.subheading"
                    placeholder="Was bedeutet ein Rundum-Umzug?"
                />
            </div>
            <div class="grid gap-2">
                <Label>Body (left column)</Label>
                <RichTextEditor v-model="data.body" placeholder="Body" />
            </div>
        </div>

        <div class="space-y-4">
            <div class="grid gap-2">
                <Label>Intro (right column)</Label>
                <Textarea v-model="data.intro" :rows="2" />
            </div>
            <div class="space-y-2">
                <div class="flex items-center justify-between">
                    <Label class="text-sm font-semibold">Checklist</Label>
                    <Button
                        type="button"
                        variant="outline"
                        size="sm"
                        @click="addPoint"
                    >
                        <Plus class="size-4" />
                        Add
                    </Button>
                </div>
                <div
                    v-for="(_, i) in data.points ?? []"
                    :key="i"
                    class="flex gap-2"
                >
                    <Input
                        v-model="data.points[i]"
                        placeholder="Klare Festpreise statt versteckter Kosten"
                    />
                    <Button
                        type="button"
                        variant="ghost"
                        size="icon"
                        @click="removePoint(i)"
                    >
                        <Trash2 class="size-4 text-destructive" />
                    </Button>
                </div>
            </div>
            <div class="grid gap-2">
                <Label>Outro (right column)</Label>
                <Textarea v-model="data.outro" :rows="2" />
            </div>
        </div>

        <div class="space-y-4 rounded-md border p-4 md:col-span-2">
            <div class="flex items-center justify-between">
                <Label for="textcols-image-card" class="text-sm font-semibold"
                    >Additional image card section</Label
                >
                <Switch
                    id="textcols-image-card"
                    :model-value="settings.image_card_enabled"
                    @update:model-value="
                        (v) => (settings.image_card_enabled = v)
                    "
                />
            </div>

            <div v-if="settings.image_card_enabled" class="grid gap-4 md:grid-cols-2">
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
                        <Label for="textcols-card-read-more"
                            >Enable Read more / Read less</Label
                        >
                        <Switch
                            id="textcols-card-read-more"
                            :model-value="settings.image_card_read_more_enabled"
                            @update:model-value="
                                (v) =>
                                    (settings.image_card_read_more_enabled = v)
                            "
                        />
                    </div>
                </div>
                <div class="space-y-4">
                    <div class="grid gap-2">
                        <Label>Heading</Label>
                        <Input
                            v-model="data.image_card_heading"
                            placeholder="Festpreis statt Stundenzettel"
                        />
                    </div>
                    <div class="grid gap-2">
                        <Label>Body</Label>
                        <RichTextEditor
                            v-model="data.image_card_body"
                            placeholder="Body"
                        />
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
