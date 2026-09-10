<script setup lang="ts">
import RichTextEditor from '@/components/common/RichTextEditor.vue';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';

type Data = {
    heading: string;
    subheading: string;
    body: string;
    intro: string;
    outro: string;
    button_label: string;
    button_url: string;
};

const data = defineModel<Data>('data', { required: true });
defineModel<Record<string, unknown>>('settings', { required: true });
</script>

<template>
    <div class="grid gap-6 md:grid-cols-2">
        <div class="space-y-4">
            <div class="grid gap-2">
                <Label>Heading</Label>
                <Textarea v-model="data.heading" :rows="2" />
            </div>
            <div class="grid gap-2">
                <Label>Subheading</Label>
                <Input
                    v-model="data.subheading"
                    placeholder="Was bedeutet ein Rundum-Umzug?"
                />
            </div>
            <div class="grid gap-2">
                <Label>Body (left column)</Label>
                <RichTextEditor v-model="data.body" placeholder="Body" />
            </div>
        </div>

        <div class="space-y-4">
            <div class="grid gap-2">
                <Label>Intro (right column, add a bullet list for checkmarks)</Label>
                <RichTextEditor v-model="data.intro" placeholder="Intro" />
            </div>
            <div class="grid gap-2">
                <Label>Outro (right column)</Label>
                <Textarea v-model="data.outro" :rows="2" />
            </div>
            <div class="grid grid-cols-2 gap-2">
                <div class="grid gap-1">
                    <Label class="text-xs">Button label</Label>
                    <Input
                        v-model="data.button_label"
                        placeholder="Mehr erfahren"
                    />
                </div>
                <div class="grid gap-1">
                    <Label class="text-xs">Button URL</Label>
                    <Input v-model="data.button_url" placeholder="#leistungen" />
                </div>
            </div>
        </div>
    </div>
</template>
