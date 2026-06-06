<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import { Save } from 'lucide-vue-next';
import Heading from '@/components/Heading.vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Label } from '@/components/ui/label';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';

type Settings = {
    privacy_page_id: number | null;
    terms_page_id: number | null;
    imprint_page_id: number | null;
};

type PageOption = { id: number; title: string };

const props = defineProps<{
    settings: Settings;
    pageOptions: PageOption[];
}>();

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Dashboard', href: '/dashboard' },
            { title: 'Settings', href: '/admin/settings/identity' },
            { title: 'Legal pages', href: '/admin/settings/legal' },
        ],
    },
});

const form = useForm({
    privacy_page_id: props.settings.privacy_page_id,
    terms_page_id: props.settings.terms_page_id,
    imprint_page_id: props.settings.imprint_page_id,
});

function submit(): void {
    form.put('/admin/settings/legal', { preserveScroll: true });
}

const legalFields = [
    {
        field: 'privacy_page_id' as const,
        label: 'Privacy policy',
        hint: 'Wires the footer "Privacy Policy" link.',
    },
    {
        field: 'terms_page_id' as const,
        label: 'Terms & conditions',
        hint: 'Wires the footer "Terms" link.',
    },
    {
        field: 'imprint_page_id' as const,
        label: 'Imprint (Impressum)',
        hint: 'Legally required in Germany.',
    },
];

function selectValueFor(field: keyof Settings): string {
    return form[field] === null ? 'none' : String(form[field]);
}

function onSelect(field: keyof Settings, v: string): void {
    form[field] = v === 'none' ? null : Number(v);
}
</script>

<template>
    <Head title="Legal pages" />

    <div class="flex flex-col gap-6 p-4">
        <Heading
            title="Legal pages"
            description="Pick the published pages used for Privacy, Terms and Impressum. The footer links route here automatically."
        />

        <form class="flex flex-col gap-4" @submit.prevent="submit">
            <Card>
                <CardHeader>
                    <CardTitle>Page selectors</CardTitle>
                </CardHeader>
                <CardContent class="space-y-3">
                    <div
                        v-for="legal in legalFields"
                        :key="legal.field"
                        class="space-y-1"
                    >
                        <Label :for="legal.field">{{ legal.label }}</Label>
                        <Select
                            :model-value="selectValueFor(legal.field)"
                            @update:model-value="(v) => onSelect(legal.field, v as string)"
                        >
                            <SelectTrigger :id="legal.field">
                                <SelectValue placeholder="— none —" />
                            </SelectTrigger>
                            <SelectContent class="max-h-72">
                                <SelectItem value="none">— none —</SelectItem>
                                <SelectItem
                                    v-for="p in pageOptions"
                                    :key="p.id"
                                    :value="String(p.id)"
                                >
                                    {{ p.title }}
                                </SelectItem>
                            </SelectContent>
                        </Select>
                        <p class="text-xs text-muted-foreground">{{ legal.hint }}</p>
                    </div>
                </CardContent>
            </Card>

            <div class="flex justify-end">
                <Button type="submit" :disabled="form.processing">
                    <Save class="size-4" />
                    Save legal pages
                </Button>
            </div>
        </form>
    </div>
</template>
