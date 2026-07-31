<script setup lang="ts">
import { Plus, Trash2 } from 'lucide-vue-next';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import WidgetImageField from '@/widgets/shared/WidgetImageField.vue';

type Pill = { icon: string; label: string };

type Settings = {
    image_path: string | null;
    image_url: string | null;
};

type Data = {
    badge: string;
    title_lead: string;
    title_highlight: string;
    title_tail: string;
    image_alt: string;
    features: string[];
    primary_label: string;
    primary_url: string;
    secondary_label: string;
    secondary_url: string;
    pills: Pill[];
    avatars: string[];
    trust_title: string;
    trust_subtitle: string;
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

function addPill(): void {
    data.value.pills = [
        ...(data.value.pills ?? []),
        { icon: 'Box', label: '' },
    ];
}

function removePill(index: number): void {
    data.value.pills = (data.value.pills ?? []).filter((_, i) => i !== index);
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
        <!-- LEFT: content -->
        <div class="space-y-4">
            <div class="grid gap-2">
                <Label>Badge</Label>
                <Input
                    v-model="data.badge"
                    placeholder="Moovato Umzugsservice · Berlin"
                />
            </div>

            <div class="grid gap-2">
                <Label>Title — lead</Label>
                <Input
                    v-model="data.title_lead"
                    placeholder="Dein modernes & effizientes"
                />
            </div>

            <div class="grid grid-cols-2 gap-2">
                <div class="grid gap-1">
                    <Label class="text-xs">Highlight word</Label>
                    <Input
                        v-model="data.title_highlight"
                        placeholder="UMZUGS"
                    />
                </div>
                <div class="grid gap-1">
                    <Label class="text-xs">Title — tail (italic)</Label>
                    <Input
                        v-model="data.title_tail"
                        placeholder="Unternehmen in Berlin"
                    />
                </div>
            </div>

            <!-- Feature checklist -->
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
                        placeholder="Festpreisgarantie"
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
                    <Label class="text-xs">Primary CTA label</Label>
                    <Input
                        v-model="data.primary_label"
                        placeholder="Angebot anfordern"
                    />
                </div>
                <div class="grid gap-1">
                    <Label class="text-xs">Primary CTA URL</Label>
                    <Input v-model="data.primary_url" placeholder="#angebot" />
                </div>
            </div>

            <div class="grid grid-cols-2 gap-2">
                <div class="grid gap-1">
                    <Label class="text-xs">Secondary CTA label</Label>
                    <Input
                        v-model="data.secondary_label"
                        placeholder="Unsere Leistungen"
                    />
                </div>
                <div class="grid gap-1">
                    <Label class="text-xs">Secondary CTA URL</Label>
                    <Input
                        v-model="data.secondary_url"
                        placeholder="#leistungen"
                    />
                </div>
            </div>
        </div>

        <!-- RIGHT: visual -->
        <div class="space-y-4">
            <WidgetImageField
                label="Hero image"
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
                <Input v-model="data.image_alt" placeholder="Moovato Mover" />
            </div>

            <!-- Floating pills -->
            <div class="space-y-2">
                <div class="flex items-center justify-between">
                    <Label class="text-sm font-semibold"
                        >Floating pills (max 4)</Label
                    >
                    <Button
                        type="button"
                        variant="outline"
                        size="sm"
                        @click="addPill"
                    >
                        <Plus class="size-4" />
                        Add
                    </Button>
                </div>
                <div
                    v-for="(pill, i) in data.pills ?? []"
                    :key="i"
                    class="grid grid-cols-[120px_1fr_auto] gap-2"
                >
                    <Input v-model="pill.icon" placeholder="Icon (lucide)" />
                    <Input v-model="pill.label" placeholder="Privatumzug" />
                    <Button
                        type="button"
                        variant="ghost"
                        size="icon"
                        @click="removePill(i)"
                    >
                        <Trash2 class="size-4 text-destructive" />
                    </Button>
                </div>
            </div>

            <!-- Trust strip -->
            <div class="grid gap-2">
                <Label>Trust title</Label>
                <Input
                    v-model="data.trust_title"
                    placeholder="4,9 ★ · 1.200+ Umzüge"
                />
            </div>
            <div class="grid gap-2">
                <Label>Trust subtitle</Label>
                <Input
                    v-model="data.trust_subtitle"
                    placeholder="in Berlin durchgeführt"
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
    </div>
</template>
