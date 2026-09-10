<script setup lang="ts">
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import { Textarea } from '@/components/ui/textarea';

type Settings = {
    alignment: 'left' | 'center';
    background: 'orange-soft' | 'midnight' | 'paper' | 'none';
};

type Data = {
    eyebrow: string;
    title: string;
    subtitle: string;
    placeholder: string;
    button_label: string;
    no_match_hint: string;
};

const settings = defineModel<Settings>('settings', { required: true });
const data = defineModel<Data>('data', { required: true });
</script>

<template>
    <div class="space-y-6">
        <div class="grid gap-4 md:grid-cols-2">
            <div class="grid gap-2">
                <Label>Ausrichtung</Label>
                <Select
                    :model-value="settings.alignment"
                    @update:model-value="(v) => (settings.alignment = v as Settings['alignment'])"
                >
                    <SelectTrigger><SelectValue /></SelectTrigger>
                    <SelectContent>
                        <SelectItem value="center">Zentriert</SelectItem>
                        <SelectItem value="left">Linksbündig</SelectItem>
                    </SelectContent>
                </Select>
            </div>
            <div class="grid gap-2">
                <Label>Hintergrund</Label>
                <Select
                    :model-value="settings.background"
                    @update:model-value="(v) => (settings.background = v as Settings['background'])"
                >
                    <SelectTrigger><SelectValue /></SelectTrigger>
                    <SelectContent>
                        <SelectItem value="orange-soft">Orange (weich)</SelectItem>
                        <SelectItem value="midnight">Midnight (dunkel)</SelectItem>
                        <SelectItem value="paper">Papier</SelectItem>
                        <SelectItem value="none">Ohne (transparent)</SelectItem>
                    </SelectContent>
                </Select>
            </div>
        </div>

        <div class="grid gap-4">
            <div class="grid gap-2">
                <Label>Eyebrow</Label>
                <Input v-model="data.eyebrow" placeholder="Finden Sie Ihre Region" />
            </div>
            <div class="grid gap-2">
                <Label>Titel</Label>
                <Input v-model="data.title" placeholder="Umzugsunternehmen in Ihrer Nähe" />
            </div>
            <div class="grid gap-2">
                <Label>Untertitel</Label>
                <Textarea v-model="data.subtitle" :rows="2" placeholder="Kurze Erklärung, was der Suchbereich tut" />
            </div>
            <div class="grid gap-4 md:grid-cols-2">
                <div class="grid gap-2">
                    <Label>Platzhalter im Suchfeld</Label>
                    <Input v-model="data.placeholder" placeholder="z. B. Berlin, Neckarstadt, Bayern, Deutschland …" />
                </div>
                <div class="grid gap-2">
                    <Label>Button-Beschriftung</Label>
                    <Input v-model="data.button_label" placeholder="Suchen" />
                </div>
            </div>
            <div class="grid gap-2">
                <Label>Hinweis bei keinem Treffer</Label>
                <Input v-model="data.no_match_hint" placeholder="Kein passender Ort — wir suchen trotzdem für Sie." />
            </div>
        </div>
    </div>
</template>
