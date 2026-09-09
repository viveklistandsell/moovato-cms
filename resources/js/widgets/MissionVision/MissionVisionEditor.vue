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
    image_alt: string;
    heading: string;
    subheading: string;
    body: string;
    button_label: string;
    button_url: string;
    panel_one_heading: string;
    panel_one_body: string;
    panel_one_points: string[];
    panel_two_heading: string;
    panel_two_body: string;
    panel_two_points: string[];
};

const settings = defineModel<Settings>('settings', { required: true });
const data = defineModel<Data>('data', { required: true });

function addPoint(key: 'panel_one_points' | 'panel_two_points'): void {
    data.value[key] = [...(data.value[key] ?? []), ''];
}

function removePoint(
    key: 'panel_one_points' | 'panel_two_points',
    index: number,
): void {
    data.value[key] = (data.value[key] ?? []).filter((_, i) => i !== index);
}
</script>

<template>
    <div class="space-y-6">
        <div class="grid gap-4 md:grid-cols-2">
            <WidgetImageField
                label="Hero image"
                aspect-class="aspect-[16/9] w-full"
                :path="settings.image_path"
                :url="settings.image_url"
                @update="
                    (v) => {
                        settings.image_path = v.path;
                        settings.image_url = v.url;
                    }
                "
            />
            <div class="space-y-4">
                <div class="grid gap-2">
                    <Label>Image alt text</Label>
                    <Input
                        v-model="data.image_alt"
                        placeholder="Moovato Team beim Handschlag nach einem erfolgreichen Umzug in Berlin"
                    />
                </div>
                <div class="grid gap-2">
                    <Label>Heading</Label>
                    <Textarea v-model="data.heading" :rows="2" />
                </div>
                <div class="grid gap-2">
                    <Label>Subheading</Label>
                    <Input v-model="data.subheading" />
                </div>
            </div>
        </div>

        <div class="grid gap-4 rounded-md border p-4 md:grid-cols-[2fr_1fr_auto]">
            <div class="grid gap-2">
                <Label>Body (below image)</Label>
                <RichTextEditor v-model="data.body" placeholder="Body" />
            </div>
            <div class="grid gap-2">
                <Label>Button label</Label>
                <Input v-model="data.button_label" placeholder="Mehr erfahren" />
            </div>
            <div class="grid gap-2">
                <Label>Button URL</Label>
                <Input v-model="data.button_url" placeholder="#leistungen" />
            </div>
        </div>

        <div class="grid gap-4 md:grid-cols-2">
            <div class="space-y-3 rounded-md border p-4">
                <Label class="text-sm font-semibold">Panel one</Label>
                <div class="grid gap-2">
                    <Label class="text-xs">Heading</Label>
                    <Input
                        v-model="data.panel_one_heading"
                        placeholder="Unser Auftrag"
                    />
                </div>
                <div class="grid gap-2">
                    <Label class="text-xs">Body</Label>
                    <Textarea v-model="data.panel_one_body" :rows="4" />
                </div>
                <div class="space-y-2">
                    <div class="flex items-center justify-between">
                        <Label class="text-xs">Checklist</Label>
                        <Button
                            type="button"
                            variant="outline"
                            size="sm"
                            @click="addPoint('panel_one_points')"
                        >
                            <Plus class="size-4" />
                            Add
                        </Button>
                    </div>
                    <div
                        v-for="(_, i) in data.panel_one_points ?? []"
                        :key="i"
                        class="flex gap-2"
                    >
                        <Input
                            v-model="data.panel_one_points[i]"
                            placeholder="Maßgeschneiderte Umzugslösungen"
                        />
                        <Button
                            type="button"
                            variant="ghost"
                            size="icon"
                            @click="removePoint('panel_one_points', i)"
                        >
                            <Trash2 class="size-4 text-destructive" />
                        </Button>
                    </div>
                </div>
            </div>

            <div class="space-y-3 rounded-md border p-4">
                <Label class="text-sm font-semibold">Panel two</Label>
                <div class="grid gap-2">
                    <Label class="text-xs">Heading</Label>
                    <Input
                        v-model="data.panel_two_heading"
                        placeholder="Unsere Vision"
                    />
                </div>
                <div class="grid gap-2">
                    <Label class="text-xs">Body</Label>
                    <Textarea v-model="data.panel_two_body" :rows="4" />
                </div>
                <div class="space-y-2">
                    <div class="flex items-center justify-between">
                        <Label class="text-xs">Checklist</Label>
                        <Button
                            type="button"
                            variant="outline"
                            size="sm"
                            @click="addPoint('panel_two_points')"
                        >
                            <Plus class="size-4" />
                            Add
                        </Button>
                    </div>
                    <div
                        v-for="(_, i) in data.panel_two_points ?? []"
                        :key="i"
                        class="flex gap-2"
                    >
                        <Input
                            v-model="data.panel_two_points[i]"
                            placeholder="Innovativer Ansatz"
                        />
                        <Button
                            type="button"
                            variant="ghost"
                            size="icon"
                            @click="removePoint('panel_two_points', i)"
                        >
                            <Trash2 class="size-4 text-destructive" />
                        </Button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
