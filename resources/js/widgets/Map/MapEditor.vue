<script setup lang="ts">
import { Plus, Trash2 } from 'lucide-vue-next';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import WidgetImageField from '@/widgets/shared/WidgetImageField.vue';

type Pin = {
    x: number;
    y: number;
    city: string;
    label: string;
    flag_path: string | null;
    flag_url: string | null;
};

type Settings = {
    image_path: string | null;
    image_url: string | null;
    pins: Pin[];
};

type Data = {
    eyebrow: string;
    heading: string;
    subheading: string;
};

const settings = defineModel<Settings>('settings', { required: true });
const data = defineModel<Data>('data', { required: true });

function addPin(): void {
    settings.value.pins = [
        ...(settings.value.pins ?? []),
        { x: 50, y: 50, city: '', label: '', flag_path: null, flag_url: null },
    ];
}

function removePin(index: number): void {
    settings.value.pins = (settings.value.pins ?? []).filter(
        (_, i) => i !== index,
    );
}
</script>

<template>
    <div class="space-y-6">
        <div class="grid gap-4 md:grid-cols-2">
            <div class="grid gap-2">
                <Label>Eyebrow</Label>
                <Input v-model="data.eyebrow" placeholder="Einsatzgebiete" />
            </div>
            <div class="grid gap-2">
                <Label>Heading</Label>
                <Input
                    v-model="data.heading"
                    placeholder="Moovato bringt Sie überall hin"
                />
            </div>
            <div class="grid gap-2 md:col-span-2">
                <Label>Subheading</Label>
                <Textarea v-model="data.subheading" :rows="2" />
            </div>
        </div>

        <WidgetImageField
            label="Map image (dark dotted map works best)"
            aspect-class="aspect-[2/1] w-full"
            :path="settings.image_path"
            :url="settings.image_url"
            @update="
                (v) => {
                    settings.image_path = v.path;
                    settings.image_url = v.url;
                }
            "
        />

        <div class="space-y-3">
            <div class="flex items-center justify-between">
                <Label class="text-sm font-semibold">Location pins</Label>
                <Button
                    type="button"
                    variant="outline"
                    size="sm"
                    @click="addPin"
                >
                    <Plus class="size-4" />
                    Add pin
                </Button>
            </div>
            <p class="text-xs text-muted-foreground">
                X / Y are percentages (0–100) of the map width and height, so
                pins stay positioned at every screen size.
            </p>
            <div
                v-for="(pin, i) in settings.pins ?? []"
                :key="i"
                class="grid gap-3 rounded-md border bg-muted/30 p-3 md:grid-cols-[140px_1fr_auto]"
            >
                <WidgetImageField
                    aspect-class="aspect-square w-full"
                    :path="pin.flag_path"
                    :url="pin.flag_url"
                    @update="
                        (v) => {
                            pin.flag_path = v.path;
                            pin.flag_url = v.url;
                        }
                    "
                />
                <div class="space-y-2">
                    <Input
                        v-model="pin.city"
                        placeholder="City (e.g. Berlin)"
                    />
                    <Input
                        v-model="pin.label"
                        placeholder="Label (e.g. Umzugsservice verfügbar)"
                    />
                    <div class="grid grid-cols-2 gap-2">
                        <div class="grid gap-1">
                            <Label class="text-xs">X %</Label>
                            <Input
                                type="number"
                                min="0"
                                max="100"
                                :model-value="pin.x"
                                @update:model-value="
                                    (v) => (pin.x = Number(v) || 0)
                                "
                            />
                        </div>
                        <div class="grid gap-1">
                            <Label class="text-xs">Y %</Label>
                            <Input
                                type="number"
                                min="0"
                                max="100"
                                :model-value="pin.y"
                                @update:model-value="
                                    (v) => (pin.y = Number(v) || 0)
                                "
                            />
                        </div>
                    </div>
                </div>
                <Button
                    type="button"
                    variant="ghost"
                    size="icon"
                    @click="removePin(i)"
                >
                    <Trash2 class="size-4 text-destructive" />
                </Button>
            </div>
        </div>
    </div>
</template>
