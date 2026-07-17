<script setup lang="ts">
import { Plus, Trash2 } from 'lucide-vue-next';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import WidgetImageField from '@/widgets/shared/WidgetImageField.vue';

type SelectField = { label: string; options: string[] };
type ToolLink = { label: string; url: string };
type Column = { icon: string; title: string; links: ToolLink[] };

type Settings = {
    avatar_path: string | null;
    avatar_url: string | null;
};

type Data = {
    assistant_title: string;
    assistant_subtitle: string;
    avatar_alt: string;
    selects: SelectField[];
    button_label: string;
    button_url: string;
    tools_title: string;
    columns: Column[];
};

const settings = defineModel<Settings>('settings', { required: true });
const data = defineModel<Data>('data', { required: true });

function addSelect(): void {
    data.value.selects = [
        ...(data.value.selects ?? []),
        { label: '', options: [] },
    ];
}

function removeSelect(index: number): void {
    data.value.selects = (data.value.selects ?? []).filter(
        (_, i) => i !== index,
    );
}

function setOptions(field: SelectField, value: string): void {
    field.options = value.split('\n').map((line) => line.trim());
}

function addColumn(): void {
    data.value.columns = [
        ...(data.value.columns ?? []),
        { icon: 'FileText', title: '', links: [] },
    ];
}

function removeColumn(index: number): void {
    data.value.columns = (data.value.columns ?? []).filter(
        (_, i) => i !== index,
    );
}

function addLink(column: Column): void {
    column.links = [...(column.links ?? []), { label: '', url: '#' }];
}

function removeLink(column: Column, index: number): void {
    column.links = (column.links ?? []).filter((_, i) => i !== index);
}
</script>

<template>
    <div class="space-y-6">
        <!-- Assistant card -->
        <div class="space-y-3 rounded-md border bg-muted/30 p-3">
            <Label class="text-sm font-semibold">Assistant card</Label>
            <div class="grid gap-3 md:grid-cols-2">
                <div class="grid gap-2">
                    <Label>Title</Label>
                    <Input
                        v-model="data.assistant_title"
                        placeholder="Moovato-Assistent"
                    />
                </div>
                <div class="grid gap-2">
                    <Label>Subtitle</Label>
                    <Input
                        v-model="data.assistant_subtitle"
                        placeholder="Suchen Sie etwas? Wir sind hier, um zu helfen!"
                    />
                </div>
            </div>
            <WidgetImageField
                label="Avatar image"
                :path="settings.avatar_path"
                :url="settings.avatar_url"
                @update="
                    (v) => {
                        settings.avatar_path = v.path;
                        settings.avatar_url = v.url;
                    }
                "
            />
            <div class="grid gap-2">
                <Label>Avatar alt</Label>
                <Input
                    v-model="data.avatar_alt"
                    placeholder="Moovato Assistent"
                />
            </div>

            <div class="space-y-2">
                <div class="flex items-center justify-between">
                    <Label class="text-xs font-semibold">Selects</Label>
                    <Button
                        type="button"
                        variant="outline"
                        size="sm"
                        @click="addSelect"
                    >
                        <Plus class="size-4" />
                        Add
                    </Button>
                </div>
                <div
                    v-for="(field, i) in data.selects ?? []"
                    :key="i"
                    class="grid grid-cols-[1fr_1fr_auto] gap-2"
                >
                    <Input v-model="field.label" placeholder="Ich ziehe um" />
                    <Textarea
                        :model-value="(field.options ?? []).join('\n')"
                        :rows="3"
                        placeholder="One option per line"
                        @update:model-value="
                            (v) => setOptions(field, String(v))
                        "
                    />
                    <Button
                        type="button"
                        variant="ghost"
                        size="icon"
                        @click="removeSelect(i)"
                    >
                        <Trash2 class="size-4 text-destructive" />
                    </Button>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-2">
                <div class="grid gap-1">
                    <Label class="text-xs">Button label</Label>
                    <Input v-model="data.button_label" placeholder="Start" />
                </div>
                <div class="grid gap-1">
                    <Label class="text-xs">Button URL</Label>
                    <Input v-model="data.button_url" placeholder="#" />
                </div>
            </div>
        </div>

        <!-- Tools columns -->
        <div class="space-y-3">
            <div class="grid gap-2">
                <Label>Tools heading</Label>
                <Input
                    v-model="data.tools_title"
                    placeholder="Weitere Artikel und Hilfstools"
                />
            </div>

            <div class="flex items-center justify-between">
                <Label class="text-sm font-semibold">Link columns</Label>
                <Button
                    type="button"
                    variant="outline"
                    size="sm"
                    @click="addColumn"
                >
                    <Plus class="size-4" />
                    Add column
                </Button>
            </div>

            <div
                v-for="(column, i) in data.columns ?? []"
                :key="i"
                class="space-y-3 rounded-md border bg-muted/30 p-3"
            >
                <div class="grid grid-cols-[120px_1fr_auto] gap-2">
                    <Input v-model="column.icon" placeholder="Icon (lucide)" />
                    <Input
                        v-model="column.title"
                        placeholder="Umzug Deutschlandweit"
                    />
                    <Button
                        type="button"
                        variant="ghost"
                        size="icon"
                        @click="removeColumn(i)"
                    >
                        <Trash2 class="size-4 text-destructive" />
                    </Button>
                </div>

                <div class="space-y-2 pl-2">
                    <div class="flex items-center justify-between">
                        <Label class="text-xs font-semibold">Links</Label>
                        <Button
                            type="button"
                            variant="outline"
                            size="sm"
                            @click="addLink(column)"
                        >
                            <Plus class="size-4" />
                            Add link
                        </Button>
                    </div>
                    <div
                        v-for="(link, j) in column.links ?? []"
                        :key="j"
                        class="grid grid-cols-[1fr_1fr_auto] gap-2"
                    >
                        <Input v-model="link.label" placeholder="Link label" />
                        <Input v-model="link.url" placeholder="#" />
                        <Button
                            type="button"
                            variant="ghost"
                            size="icon"
                            @click="removeLink(column, j)"
                        >
                            <Trash2 class="size-4 text-destructive" />
                        </Button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
