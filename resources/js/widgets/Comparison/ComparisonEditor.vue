<script setup lang="ts">
import { Plus, Trash2 } from 'lucide-vue-next';
import RichTextEditor from '@/components/common/RichTextEditor.vue';
import { Button } from '@/components/ui/button';
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

type Row = { label: string; left: string | boolean; right: string | boolean };
type Tab = {
    label: string;
    mode: 'value' | 'check';
    col_left: string;
    col_right: string;
    use_logo: boolean;
    rows: Row[];
};
type Data = {
    eyebrow: string;
    heading: string;
    description: string;
    tabs: Tab[];
};

defineModel<Record<string, unknown>>('settings', { required: true });
const data = defineModel<Data>('data', { required: true });

function addTab(): void {
    data.value.tabs = [
        ...(data.value.tabs ?? []),
        {
            label: '',
            mode: 'check',
            col_left: 'Kein Vergleich',
            col_right: 'Moovato',
            use_logo: true,
            rows: [],
        },
    ];
}

function removeTab(index: number): void {
    data.value.tabs = (data.value.tabs ?? []).filter((_, i) => i !== index);
}

function onModeChange(tabIndex: number, mode: Tab['mode']): void {
    const tab = data.value.tabs[tabIndex];
    tab.mode = mode;
    tab.rows = (tab.rows ?? []).map((row) =>
        mode === 'check'
            ? {
                  ...row,
                  left: typeof row.left === 'boolean' ? row.left : false,
                  right: typeof row.right === 'boolean' ? row.right : true,
              }
            : {
                  ...row,
                  left: typeof row.left === 'string' ? row.left : '',
                  right: typeof row.right === 'string' ? row.right : '',
              },
    );
}

function addRow(tabIndex: number): void {
    const tab = data.value.tabs[tabIndex];
    const isCheck = tab.mode === 'check';
    tab.rows = [
        ...(tab.rows ?? []),
        {
            label: '',
            left: isCheck ? false : '',
            right: isCheck ? true : '',
        },
    ];
}

function removeRow(tabIndex: number, rowIndex: number): void {
    const tab = data.value.tabs[tabIndex];
    tab.rows = (tab.rows ?? []).filter((_, i) => i !== rowIndex);
}
</script>

<template>
    <div class="space-y-6">
        <div class="grid gap-4 md:grid-cols-2">
            <div class="grid gap-2">
                <Label>Eyebrow</Label>
                <Input v-model="data.eyebrow" />
            </div>
            <div class="grid gap-2">
                <Label>Heading</Label>
                <Input v-model="data.heading" />
            </div>
            <div class="grid gap-2 md:col-span-2">
                <Label>Description</Label>
                <RichTextEditor
                    v-model="data.description"
                    placeholder="Beschreibung"
                />
            </div>
        </div>

        <div class="space-y-4">
            <div class="flex items-center justify-between">
                <Label class="text-sm font-semibold">Tabs</Label>
                <Button
                    type="button"
                    variant="outline"
                    size="sm"
                    @click="addTab"
                >
                    <Plus class="size-4" />
                    Add tab
                </Button>
            </div>

            <div
                v-for="(tab, ti) in data.tabs ?? []"
                :key="ti"
                class="space-y-3 rounded-md border bg-muted/30 p-4"
            >
                <div class="grid gap-3 md:grid-cols-2">
                    <div class="grid gap-2">
                        <Label class="text-xs">Tab label</Label>
                        <Input
                            v-model="tab.label"
                            placeholder="Leistungsvergleich"
                        />
                    </div>
                    <div class="grid gap-2">
                        <Label class="text-xs">Mode</Label>
                        <Select
                            :model-value="tab.mode"
                            @update:model-value="
                                (v) => onModeChange(ti, v as Tab['mode'])
                            "
                        >
                            <SelectTrigger><SelectValue /></SelectTrigger>
                            <SelectContent>
                                <SelectItem value="check"
                                    >Check / Cross</SelectItem
                                >
                                <SelectItem value="value"
                                    >Text values</SelectItem
                                >
                            </SelectContent>
                        </Select>
                    </div>
                    <div class="grid gap-2">
                        <Label class="text-xs">Left column label</Label>
                        <Input v-model="tab.col_left" />
                    </div>
                    <div class="grid gap-2">
                        <Label class="text-xs">Right column label</Label>
                        <Input v-model="tab.col_right" :disabled="tab.use_logo" />
                    </div>
                </div>

                <label class="flex items-center gap-2 text-xs">
                    <Switch
                        :model-value="tab.use_logo"
                        @update:model-value="(v) => (tab.use_logo = v)"
                    />
                    Show Moovato logo in right column header
                </label>

                <div class="space-y-2">
                    <div class="flex items-center justify-between">
                        <Label class="text-xs font-semibold">Rows</Label>
                        <Button
                            type="button"
                            variant="outline"
                            size="sm"
                            @click="addRow(ti)"
                        >
                            <Plus class="size-4" />
                            Add row
                        </Button>
                    </div>
                    <div
                        v-for="(row, ri) in tab.rows ?? []"
                        :key="ri"
                        class="flex items-center gap-3"
                    >
                        <Input
                            v-model="row.label"
                            placeholder="Zeile"
                            class="flex-1"
                        />
                        <template v-if="tab.mode === 'check'">
                            <label class="flex items-center gap-1 text-xs">
                                <Switch
                                    :model-value="!!row.left"
                                    @update:model-value="(v) => (row.left = v)"
                                />
                                {{ tab.col_left || 'Links' }}
                            </label>
                            <label class="flex items-center gap-1 text-xs">
                                <Switch
                                    :model-value="!!row.right"
                                    @update:model-value="(v) => (row.right = v)"
                                />
                                {{ tab.col_right || 'Rechts' }}
                            </label>
                        </template>
                        <template v-else>
                            <Input
                                :model-value="(row.left as string)"
                                placeholder="Links"
                                class="w-28"
                                @update:model-value="
                                    (v) => (row.left = String(v))
                                "
                            />
                            <Input
                                :model-value="(row.right as string)"
                                placeholder="Rechts"
                                class="w-28"
                                @update:model-value="
                                    (v) => (row.right = String(v))
                                "
                            />
                        </template>
                        <Button
                            type="button"
                            variant="ghost"
                            size="icon"
                            @click="removeRow(ti, ri)"
                        >
                            <Trash2 class="size-4 text-destructive" />
                        </Button>
                    </div>
                </div>

                <div class="flex justify-end">
                    <Button
                        type="button"
                        variant="ghost"
                        size="sm"
                        @click="removeTab(ti)"
                    >
                        <Trash2 class="size-4 text-destructive" />
                        Remove tab
                    </Button>
                </div>
            </div>
        </div>
    </div>
</template>
