<script setup lang="ts">
import { Plus, Trash2 } from 'lucide-vue-next';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';

type Settings = Record<string, never>;

type Data = {
    eyebrow: string;
    heading: string;
    lead: string;
    benefits: string[];
    form_title: string;
    form_subtitle: string;
    success_title: string;
    success_text: string;
};

defineModel<Settings>('settings', { required: true });
const data = defineModel<Data>('data', { required: true });

function addBenefit(): void {
    data.value.benefits = [...(data.value.benefits ?? []), ''];
}

function removeBenefit(index: number): void {
    data.value.benefits = (data.value.benefits ?? []).filter(
        (_, i) => i !== index,
    );
}
</script>

<template>
    <div class="space-y-6">
        <p
            class="rounded-md border bg-muted/30 px-3 py-2 text-xs text-muted-foreground"
        >
            The form fields (steps, inputs) are fixed. Only the surrounding text
            below is editable.
        </p>

        <div class="grid gap-4 md:grid-cols-2">
            <div class="grid gap-2">
                <Label>Eyebrow</Label>
                <Input v-model="data.eyebrow" placeholder="Umzugsformular" />
            </div>
            <div class="grid gap-2">
                <Label>Heading</Label>
                <Input
                    v-model="data.heading"
                    placeholder="Kostenloses Angebot in 60 Sekunden"
                />
            </div>
        </div>

        <div class="grid gap-2">
            <Label>Lead</Label>
            <Textarea v-model="data.lead" :rows="2" />
        </div>

        <!-- Benefits -->
        <div class="space-y-3">
            <div class="flex items-center justify-between">
                <Label class="text-sm font-semibold">Benefits</Label>
                <Button
                    type="button"
                    variant="outline"
                    size="sm"
                    @click="addBenefit"
                >
                    <Plus class="size-4" />
                    Add benefit
                </Button>
            </div>
            <div
                v-for="(_, i) in data.benefits ?? []"
                :key="i"
                class="flex gap-2"
            >
                <Input
                    v-model="data.benefits[i]"
                    placeholder="Kostenlos & unverbindlich"
                />
                <Button
                    type="button"
                    variant="ghost"
                    size="icon"
                    @click="removeBenefit(i)"
                >
                    <Trash2 class="size-4 text-destructive" />
                </Button>
            </div>
        </div>

        <div class="grid gap-4 md:grid-cols-2">
            <div class="grid gap-2">
                <Label>Form title</Label>
                <Input
                    v-model="data.form_title"
                    placeholder="Jetzt Festpreis anfordern"
                />
            </div>
            <div class="grid gap-2">
                <Label>Form subtitle</Label>
                <Input
                    v-model="data.form_subtitle"
                    placeholder="Schnell, kostenlos und unverbindlich."
                />
            </div>
        </div>

        <div class="grid gap-2">
            <Label>Success title</Label>
            <Input v-model="data.success_title" placeholder="Vielen Dank!" />
        </div>
        <div class="grid gap-2">
            <Label>Success text</Label>
            <Textarea v-model="data.success_text" :rows="2" />
        </div>
    </div>
</template>
