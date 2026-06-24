<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import { Save } from 'lucide-vue-next';
import { computed } from 'vue';
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
import { setBreadcrumbs } from '@/composables/common/useBreadcrumbs';
import { useT } from '@/composables/useT';

const t = useT();
setBreadcrumbs(() => [
    { title: t('sidebar.dashboard'), href: '/dashboard' },
    { title: t('sidebar.settings'), href: '/admin/settings/identity' },
    { title: t('sidebar.legal_pages'), href: '/admin/settings/legal' },
]);

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

// Breadcrumbs set dynamically above via setBreadcrumbs().
defineOptions({});

const form = useForm({
    privacy_page_id: props.settings.privacy_page_id,
    terms_page_id: props.settings.terms_page_id,
    imprint_page_id: props.settings.imprint_page_id,
});

function submit(): void {
    form.put('/admin/settings/legal', { preserveScroll: true });
}

type LegalField = 'privacy_page_id' | 'terms_page_id' | 'imprint_page_id';
const legalFields = computed<Array<{ field: LegalField; label: string; hint: string }>>(() => [
    {
        field: 'privacy_page_id',
        label: t('settings.legal_privacy_label'),
        hint: t('settings.legal_privacy_hint'),
    },
    {
        field: 'terms_page_id',
        label: t('settings.legal_terms_label'),
        hint: t('settings.legal_terms_hint'),
    },
    {
        field: 'imprint_page_id',
        label: t('settings.legal_imprint_label'),
        hint: t('settings.legal_imprint_hint'),
    },
]);

function selectValueFor(field: keyof Settings): string {
    return form[field] === null ? 'none' : String(form[field]);
}

function onSelect(field: keyof Settings, v: string): void {
    form[field] = v === 'none' ? null : Number(v);
}
</script>

<template>
    <Head :title="t('settings.legal_title')" />

    <div class="flex flex-col gap-6 p-4">
        <Heading
            :title="t('settings.legal_title')"
            :description="t('settings.legal_description')"
        />

        <form class="flex flex-col gap-4" @submit.prevent="submit">
            <Card>
                <CardHeader>
                    <CardTitle>{{ t('settings.legal_card_title') }}</CardTitle>
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
                                <SelectValue :placeholder="t('settings.legal_none')" />
                            </SelectTrigger>
                            <SelectContent class="max-h-72">
                                <SelectItem value="none">{{ t('settings.legal_none') }}</SelectItem>
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
                    {{ t('settings.legal_save') }}
                </Button>
            </div>
        </form>
    </div>
</template>
