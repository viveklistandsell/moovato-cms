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

type Point = { title: string; description: string };

type Data = {
    heading: string;
    primary_label: string;
    primary_url: string;
    points: Point[];
    image_alt: string;
};

const settings = defineModel<Settings>('settings', { required: true });
const data = defineModel<Data>('data', { required: true });

function addPoint(): void {
    data.value.points = [
        ...(data.value.points ?? []),
        { title: '', description: '' },
    ];
}

function removePoint(index: number): void {
    data.value.points = (data.value.points ?? []).filter((_, i) => i !== index);
}
</script>

<template>
    <div class="grid gap-6 md:grid-cols-2">
        <div class="space-y-4">
            <div class="grid gap-2">
                <Label>Heading</Label>
                <Textarea v-model="data.heading" :rows="2" />
            </div>

            <div class="grid grid-cols-2 gap-2">
                <div class="grid gap-1">
                    <Label class="text-xs">Button label</Label>
                    <Input
                        v-model="data.primary_label"
                        placeholder="Kostenloses Angebot anfordern"
                    />
                </div>
                <div class="grid gap-1">
                    <Label class="text-xs">Button URL</Label>
                    <Input v-model="data.primary_url" placeholder="#angebot" />
                </div>
            </div>
        </div>

        <div class="space-y-4">
            <WidgetImageField
                label="Background image"
                aspect-class="aspect-[16/7] w-full"
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
                <Label>Background image alt text</Label>
                <Input
                    v-model="data.image_alt"
                    placeholder="Moovato Umzugsteam in Berlin"
                />
            </div>

            <div class="space-y-2">
                <div class="flex items-center justify-between">
                    <Label class="text-sm font-semibold">Checklist points</Label>
                    <Button
                        type="button"
                        variant="outline"
                        size="sm"
                        @click="addPoint"
                    >
                        <Plus class="size-4" />
                        Add point
                    </Button>
                </div>
                <div
                    v-for="(point, i) in data.points ?? []"
                    :key="i"
                    class="space-y-2 rounded-md border bg-muted/30 p-3"
                >
                    <div class="flex items-center gap-2">
                        <Input
                            v-model="point.title"
                            placeholder="Erfahrene Profis für Umzug und Entrümpelung"
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
                    <RichTextEditor
                        v-model="point.description"
                        placeholder="Beschreibung"
                    />
                </div>
            </div>
        </div>
    </div>
</template>
