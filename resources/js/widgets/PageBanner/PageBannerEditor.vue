<script setup lang="ts">
import { Plus, Trash2 } from 'lucide-vue-next';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import WidgetImageField from '@/widgets/shared/WidgetImageField.vue';

type Crumb = { label: string; url: string };

type Settings = {
    image_path: string | null;
    image_url: string | null;
};

type Data = {
    crumbs: Crumb[];
    heading: string;
    primary_label: string;
    primary_url: string;
    points: string[];
    image_alt: string;
};

const settings = defineModel<Settings>('settings', { required: true });
const data = defineModel<Data>('data', { required: true });

function addCrumb(): void {
    data.value.crumbs = [...(data.value.crumbs ?? []), { label: '', url: '' }];
}

function removeCrumb(index: number): void {
    data.value.crumbs = (data.value.crumbs ?? []).filter((_, i) => i !== index);
}

function addPoint(): void {
    data.value.points = [...(data.value.points ?? []), ''];
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

            <div class="space-y-2">
                <div class="flex items-center justify-between">
                    <Label class="text-sm font-semibold">Breadcrumbs</Label>
                    <Button
                        type="button"
                        variant="outline"
                        size="sm"
                        @click="addCrumb"
                    >
                        <Plus class="size-4" />
                        Add
                    </Button>
                </div>
                <div
                    v-for="(crumb, i) in data.crumbs ?? []"
                    :key="i"
                    class="flex gap-2"
                >
                    <Input v-model="crumb.label" placeholder="Über uns" />
                    <Input
                        v-model="crumb.url"
                        placeholder="/ (leave empty for current)"
                    />
                    <Button
                        type="button"
                        variant="ghost"
                        size="icon"
                        @click="removeCrumb(i)"
                    >
                        <Trash2 class="size-4 text-destructive" />
                    </Button>
                </div>
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
                    v-for="(_, i) in data.points ?? []"
                    :key="i"
                    class="flex gap-2"
                >
                    <Input
                        v-model="data.points[i]"
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
            </div>
        </div>
    </div>
</template>
