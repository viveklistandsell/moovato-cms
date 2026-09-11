<script setup lang="ts">
import { Plus, Trash2 } from 'lucide-vue-next';
import RichTextEditor from '@/components/common/RichTextEditor.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import WidgetImageField from '@/widgets/shared/WidgetImageField.vue';

type City = {
    path: string | null;
    url: string | null;
    alt: string;
    name: string;
    link: string;
};

type Settings = {
    cities: City[];
};

type Data = {
    title: string;
    subtitle: string;
    description: string;
    card_prefix: string;
    search_placeholder: string;
    search_url: string;
};

const settings = defineModel<Settings>('settings', { required: true });
const data = defineModel<Data>('data', { required: true });

function addCity(): void {
    settings.value.cities = [
        ...(settings.value.cities ?? []),
        { path: null, url: null, alt: '', name: '', link: '#' },
    ];
}

function removeCity(index: number): void {
    settings.value.cities = (settings.value.cities ?? []).filter(
        (_, i) => i !== index,
    );
}
</script>

<template>
    <div class="space-y-6">
        <div class="grid gap-4 md:grid-cols-2">
            <div class="grid gap-2 md:col-span-2">
                <Label>Title</Label>
                <Input
                    v-model="data.title"
                    placeholder="Finden Sie die besten Umzugsunternehmen in Ihrer Nähe"
                />
            </div>
            <div class="grid gap-2 md:col-span-2">
                <Label>Subtitle</Label>
                <Textarea v-model="data.subtitle" :rows="2" />
            </div>
            <div class="grid gap-2 md:col-span-2">
                <Label>Description</Label>
                <RichTextEditor
                    v-model="data.description"
                    placeholder="Beschreibung"
                />
            </div>
            <div class="grid gap-2">
                <Label>Card prefix</Label>
                <Input
                    v-model="data.card_prefix"
                    placeholder="Top 10 Umzugsunternehmen in"
                />
            </div>
            <div class="grid grid-cols-2 gap-2">
                <div class="grid gap-1">
                    <Label class="text-xs">Search placeholder</Label>
                    <Input
                        v-model="data.search_placeholder"
                        placeholder="Stadt oder Umzugsfirma suchen"
                    />
                </div>
                <div class="grid gap-1">
                    <Label class="text-xs">Search URL</Label>
                    <Input v-model="data.search_url" placeholder="#" />
                </div>
            </div>
        </div>

        <div class="space-y-3">
            <div class="flex items-center justify-between">
                <Label class="text-sm font-semibold">City cards</Label>
                <Button
                    type="button"
                    variant="outline"
                    size="sm"
                    @click="addCity"
                >
                    <Plus class="size-4" />
                    Add city
                </Button>
            </div>
            <div
                v-for="(city, i) in settings.cities ?? []"
                :key="i"
                class="grid gap-3 rounded-md border bg-muted/30 p-3 md:grid-cols-[160px_1fr_auto]"
            >
                <WidgetImageField
                    aspect-class="aspect-[3/2] w-full"
                    :path="city.path"
                    :url="city.url"
                    @update="
                        (v) => {
                            city.path = v.path;
                            city.url = v.url;
                        }
                    "
                />
                <div class="space-y-2">
                    <Input v-model="city.name" placeholder="Berlin" />
                    <Input v-model="city.alt" placeholder="Image alt text" />
                    <Input
                        v-model="city.link"
                        placeholder="/umzugsunternehmen/berlin"
                    />
                </div>
                <Button
                    type="button"
                    variant="ghost"
                    size="icon"
                    @click="removeCity(i)"
                >
                    <Trash2 class="size-4 text-destructive" />
                </Button>
            </div>
        </div>
    </div>
</template>
