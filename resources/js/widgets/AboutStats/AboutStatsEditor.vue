<script setup lang="ts">
import { Plus, Trash2 } from 'lucide-vue-next';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import WidgetImageField from '@/widgets/shared/WidgetImageField.vue';

type Stat = { value: string; title: string; description: string };

type Settings = {
    image_path: string | null;
    image_url: string | null;
    image_2_path: string | null;
    image_2_url: string | null;
};

type Data = {
    eyebrow: string;
    heading: string;
    heading_accent: string;
    body: string;
    reviews_label: string;
    avatars: string[];
    image_alt: string;
    image_2_alt: string;
    stats: Stat[];
    button_label: string;
    button_url: string;
};

const settings = defineModel<Settings>('settings', { required: true });
const data = defineModel<Data>('data', { required: true });

function addAvatar(): void {
    data.value.avatars = [...(data.value.avatars ?? []), ''];
}

function removeAvatar(index: number): void {
    data.value.avatars = (data.value.avatars ?? []).filter(
        (_, i) => i !== index,
    );
}

function addStat(): void {
    data.value.stats = [
        ...(data.value.stats ?? []),
        { value: '', title: '', description: '' },
    ];
}

function removeStat(index: number): void {
    data.value.stats = (data.value.stats ?? []).filter((_, i) => i !== index);
}
</script>

<template>
    <div class="grid gap-6 md:grid-cols-2">
        <div class="space-y-4">
            <div class="grid gap-2">
                <Label>Eyebrow</Label>
                <Input v-model="data.eyebrow" placeholder="Über uns" />
            </div>
            <div class="grid gap-2">
                <Label>Heading</Label>
                <Textarea v-model="data.heading" :rows="2" />
            </div>
            <div class="grid gap-2">
                <Label>Heading accent (orange)</Label>
                <Input
                    v-model="data.heading_accent"
                    placeholder="mit einem Team, das wirklich anpackt"
                />
            </div>
            <div class="grid gap-2">
                <Label>Body</Label>
                <Textarea v-model="data.body" :rows="4" />
            </div>
            <div class="grid gap-2">
                <Label>Reviews label</Label>
                <Input
                    v-model="data.reviews_label"
                    placeholder="Basierend auf 204 Bewertungen"
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
            <div class="grid grid-cols-2 gap-2">
                <div class="grid gap-1">
                    <Label class="text-xs">Button label</Label>
                    <Input
                        v-model="data.button_label"
                        placeholder="Mehr über uns"
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

        <div class="space-y-4">
            <WidgetImageField
                label="Image 1"
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
                <Label>Image 1 alt text</Label>
                <Input
                    v-model="data.image_alt"
                    placeholder="Moovato Team verpackt Umzugskartons"
                />
            </div>
            <WidgetImageField
                label="Image 2"
                aspect-class="aspect-[4/5] w-full"
                :path="settings.image_2_path"
                :url="settings.image_2_url"
                @update="
                    (v) => {
                        settings.image_2_path = v.path;
                        settings.image_2_url = v.url;
                    }
                "
            />
            <div class="grid gap-2">
                <Label>Image 2 alt text</Label>
                <Input
                    v-model="data.image_2_alt"
                    placeholder="Lächelnder Moovato Umzugshelfer"
                />
            </div>

            <div class="space-y-2">
                <div class="flex items-center justify-between">
                    <Label class="text-sm font-semibold">Stats</Label>
                    <Button
                        type="button"
                        variant="outline"
                        size="sm"
                        @click="addStat"
                    >
                        <Plus class="size-4" />
                        Add stat
                    </Button>
                </div>
                <div
                    v-for="(stat, i) in data.stats ?? []"
                    :key="i"
                    class="grid gap-2 rounded-md border bg-muted/30 p-3"
                >
                    <div class="flex gap-2">
                        <Input
                            v-model="stat.value"
                            class="w-24"
                            placeholder="98%"
                        />
                        <Input v-model="stat.title" placeholder="Zufriedenheit" />
                        <Button
                            type="button"
                            variant="ghost"
                            size="icon"
                            @click="removeStat(i)"
                        >
                            <Trash2 class="size-4 text-destructive" />
                        </Button>
                    </div>
                    <Input
                        v-model="stat.description"
                        placeholder="Garantierte Zufriedenheit"
                    />
                </div>
            </div>
        </div>
    </div>
</template>
