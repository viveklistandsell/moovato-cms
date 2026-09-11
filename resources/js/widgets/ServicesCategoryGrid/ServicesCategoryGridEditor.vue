<script setup lang="ts">
import RichTextEditor from '@/components/common/RichTextEditor.vue';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import { Switch } from '@/components/ui/switch';
import { Textarea } from '@/components/ui/textarea';

/**
 * Parent Categories Grid — the widget always renders every parent
 * service category (rows from `service_parent_categories`). There is no
 * source picker: content editors manage which parents exist on the
 * dedicated "Elternkategorien" admin page.
 */
type Settings = {
    only_featured: boolean;
    only_popular: boolean;
    max_items: number;
    columns: 2 | 3 | 4 | 5 | 6;
    show_description: boolean;
    categories?: unknown;
};

type Data = {
    eyebrow: string;
    heading: string;
    subheading: string;
    description: string;
};

const settings = defineModel<Settings>('settings', { required: true });
const data = defineModel<Data>('data', { required: true });
</script>

<template>
    <div class="space-y-6">
        <div class="grid gap-4 md:grid-cols-2">
            <div class="grid gap-2">
                <Label>Eyebrow</Label>
                <Input
                    v-model="data.eyebrow"
                    placeholder="Unsere Services"
                />
            </div>
            <div class="grid gap-2">
                <Label>Heading</Label>
                <Input
                    v-model="data.heading"
                    placeholder="Alle Dienstleistungen im Überblick"
                />
            </div>
            <div class="grid gap-2 md:col-span-2">
                <Label>Subheading</Label>
                <Textarea
                    v-model="data.subheading"
                    :rows="2"
                    placeholder="Wähle eine Kategorie, um passende Anbieter in deiner Stadt zu sehen."
                />
            </div>
            <div class="grid gap-2 md:col-span-2">
                <Label>Description</Label>
                <RichTextEditor
                    v-model="data.description"
                    placeholder="Beschreibung"
                />
            </div>
        </div>

        <!-- Layout ------------------------------------------------------- -->
        <div class="grid gap-4 md:grid-cols-2">
            <div class="grid gap-2">
                <Label>Columns</Label>
                <Select
                    :model-value="String(settings.columns)"
                    @update:model-value="
                        (v) =>
                            (settings.columns = Number(
                                v,
                            ) as Settings['columns'])
                    "
                >
                    <SelectTrigger><SelectValue /></SelectTrigger>
                    <SelectContent>
                        <SelectItem value="2">2</SelectItem>
                        <SelectItem value="3">3</SelectItem>
                        <SelectItem value="4">4</SelectItem>
                        <SelectItem value="5">5</SelectItem>
                        <SelectItem value="6">6</SelectItem>
                    </SelectContent>
                </Select>
            </div>

            <div class="grid gap-2">
                <Label>Max items (0 = no cap)</Label>
                <Input
                    v-model.number="settings.max_items"
                    type="number"
                    min="0"
                    max="100"
                />
            </div>
        </div>

        <!-- Toggles ------------------------------------------------------- -->
        <div class="grid gap-3 rounded-md border bg-muted/30 p-4">
            <div class="flex items-center justify-between">
                <div>
                    <Label class="text-sm font-medium">Only featured</Label>
                    <p class="text-xs text-muted-foreground">
                        Restrict tiles to parent categories flagged as
                        featured.
                    </p>
                </div>
                <Switch v-model="settings.only_featured" />
            </div>
            <div class="flex items-center justify-between">
                <div>
                    <Label class="text-sm font-medium">Only popular</Label>
                    <p class="text-xs text-muted-foreground">
                        Restrict tiles to parent categories flagged as popular.
                    </p>
                </div>
                <Switch v-model="settings.only_popular" />
            </div>
            <div class="flex items-center justify-between">
                <div>
                    <Label class="text-sm font-medium">
                        Show short description
                    </Label>
                    <p class="text-xs text-muted-foreground">
                        Renders the parent's short description under each tile
                        (defaults to icon + name only).
                    </p>
                </div>
                <Switch v-model="settings.show_description" />
            </div>
        </div>
    </div>
</template>
