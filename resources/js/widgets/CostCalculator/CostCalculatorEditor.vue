<script setup lang="ts">
import { Plus, Trash2 } from 'lucide-vue-next';
import RichTextEditor from '@/components/common/RichTextEditor.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';

type LocationOption = { name: string; lat: number | string; lng: number | string };

type Settings = {
    currency: string;
    base_price: number | string;
    price_per_sqm: number | string;
    price_per_km: number | string;
    spread_percent: number | string;
    locations: LocationOption[];
};

type Data = {
    title: string;
    from_label: string;
    from_placeholder: string;
    to_label: string;
    to_placeholder: string;
    area_label: string;
    area_placeholder: string;
    area_unit: string;
    calculate_label: string;
    recalculate_label: string;
    result_title: string;
    volume_label: string;
    distance_label: string;
    empty_text: string;
    error_text: string;
    cta_text: string;
    cta_label: string;
    cta_url: string;
};

const settings = defineModel<Settings>('settings', { required: true });
const data = defineModel<Data>('data', { required: true });

function addLocation(): void {
    settings.value.locations = [
        ...(settings.value.locations ?? []),
        { name: '', lat: '', lng: '' },
    ];
}

function removeLocation(index: number): void {
    settings.value.locations = (settings.value.locations ?? []).filter(
        (_, i) => i !== index,
    );
}
</script>

<template>
    <div class="space-y-6">
        <div class="grid gap-3 md:grid-cols-2">
            <div class="grid gap-2">
                <Label>Title</Label>
                <Input v-model="data.title" placeholder="Umzugskostenrechner" />
            </div>
            <div class="grid gap-2">
                <Label>Area unit</Label>
                <Input v-model="data.area_unit" placeholder="m²" />
            </div>
        </div>

        <div class="space-y-3 rounded-md border bg-muted/30 p-3">
            <Label class="text-sm font-semibold">Form fields</Label>
            <div class="grid gap-2 md:grid-cols-2">
                <div class="grid gap-1">
                    <Label class="text-xs">From label</Label>
                    <Input v-model="data.from_label" placeholder="Umzug von" />
                </div>
                <div class="grid gap-1">
                    <Label class="text-xs">From placeholder</Label>
                    <Input
                        v-model="data.from_placeholder"
                        placeholder="Berlin, Deutschland"
                    />
                </div>
                <div class="grid gap-1">
                    <Label class="text-xs">To label</Label>
                    <Input v-model="data.to_label" placeholder="Umzug nach" />
                </div>
                <div class="grid gap-1">
                    <Label class="text-xs">To placeholder</Label>
                    <Input
                        v-model="data.to_placeholder"
                        placeholder="München, Deutschland"
                    />
                </div>
                <div class="grid gap-1">
                    <Label class="text-xs">Area label</Label>
                    <Input
                        v-model="data.area_label"
                        placeholder="Fläche des aktuellen Wohnorts"
                    />
                </div>
                <div class="grid gap-1">
                    <Label class="text-xs">Area placeholder</Label>
                    <Input v-model="data.area_placeholder" placeholder="80" />
                </div>
                <div class="grid gap-1">
                    <Label class="text-xs">Calculate button</Label>
                    <Input
                        v-model="data.calculate_label"
                        placeholder="Preis berechnen"
                    />
                </div>
                <div class="grid gap-1">
                    <Label class="text-xs">Recalculate button</Label>
                    <Input
                        v-model="data.recalculate_label"
                        placeholder="Erneut berechnen"
                    />
                </div>
            </div>
            <div class="grid gap-1">
                <Label class="text-xs">Validation message</Label>
                <Textarea
                    v-model="data.error_text"
                    :rows="2"
                    placeholder="Bitte wählen Sie Start- und Zielort aus der Liste…"
                />
            </div>
        </div>

        <div class="space-y-3 rounded-md border bg-muted/30 p-3">
            <Label class="text-sm font-semibold">
                Result (shown after calculating)
            </Label>
            <div class="grid gap-2 md:grid-cols-3">
                <div class="grid gap-1">
                    <Label class="text-xs">Result title</Label>
                    <Input
                        v-model="data.result_title"
                        placeholder="Geschätzte Kosten"
                    />
                </div>
                <div class="grid gap-1">
                    <Label class="text-xs">Volume label</Label>
                    <Input v-model="data.volume_label" placeholder="Volumen" />
                </div>
                <div class="grid gap-1">
                    <Label class="text-xs">Distance label</Label>
                    <Input
                        v-model="data.distance_label"
                        placeholder="Entfernung"
                    />
                </div>
            </div>
            <div class="grid gap-1">
                <Label class="text-xs">Placeholder before calculating</Label>
                <Textarea
                    v-model="data.empty_text"
                    :rows="2"
                    placeholder="Geben Sie Start, Ziel und Wohnfläche ein…"
                />
            </div>
            <div class="grid gap-1">
                <Label class="text-xs">CTA text</Label>
                <RichTextEditor
                    v-model="data.cta_text"
                    placeholder="Möchten Sie einen detaillierten Preis?"
                />
            </div>
            <div class="grid gap-2 md:grid-cols-2">
                <div class="grid gap-1">
                    <Label class="text-xs">CTA label</Label>
                    <Input
                        v-model="data.cta_label"
                        placeholder="Kostenloses Angebot anfordern"
                    />
                </div>
                <div class="grid gap-1">
                    <Label class="text-xs">CTA URL</Label>
                    <Input v-model="data.cta_url" placeholder="#" />
                </div>
            </div>
        </div>

        <div class="space-y-3 rounded-md border bg-muted/30 p-3">
            <Label class="text-sm font-semibold">Pricing formula</Label>
            <div class="grid gap-2 md:grid-cols-5">
                <div class="grid gap-1">
                    <Label class="text-xs">Currency</Label>
                    <Input v-model="settings.currency" placeholder="€" />
                </div>
                <div class="grid gap-1">
                    <Label class="text-xs">Base price</Label>
                    <Input
                        v-model="settings.base_price"
                        type="number"
                        min="0"
                        step="1"
                    />
                </div>
                <div class="grid gap-1">
                    <Label class="text-xs">Price per m²</Label>
                    <Input
                        v-model="settings.price_per_sqm"
                        type="number"
                        min="0"
                        step="0.1"
                    />
                </div>
                <div class="grid gap-1">
                    <Label class="text-xs">Price per km</Label>
                    <Input
                        v-model="settings.price_per_km"
                        type="number"
                        min="0"
                        step="0.1"
                    />
                </div>
                <div class="grid gap-1">
                    <Label class="text-xs">Spread %</Label>
                    <Input
                        v-model="settings.spread_percent"
                        type="number"
                        min="0"
                        max="100"
                        step="1"
                    />
                </div>
            </div>
        </div>

        <div class="space-y-2">
            <div class="flex items-center justify-between">
                <Label class="text-sm font-semibold">
                    Locations (name, latitude, longitude)
                </Label>
                <Button
                    type="button"
                    variant="outline"
                    size="sm"
                    @click="addLocation"
                >
                    <Plus class="size-4" />
                    Add location
                </Button>
            </div>
            <div
                v-for="(place, i) in settings.locations ?? []"
                :key="i"
                class="grid grid-cols-[1fr_110px_110px_auto] gap-2"
            >
                <Input v-model="place.name" placeholder="Berlin, Deutschland" />
                <Input
                    v-model="place.lat"
                    type="number"
                    step="0.0001"
                    placeholder="52.52"
                />
                <Input
                    v-model="place.lng"
                    type="number"
                    step="0.0001"
                    placeholder="13.405"
                />
                <Button
                    type="button"
                    variant="ghost"
                    size="icon"
                    @click="removeLocation(i)"
                >
                    <Trash2 class="size-4 text-destructive" />
                </Button>
            </div>
        </div>
    </div>
</template>
