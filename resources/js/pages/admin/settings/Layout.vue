<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import { Save } from 'lucide-vue-next';
import Heading from '@/components/Heading.vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent } from '@/components/ui/card';
import { Switch } from '@/components/ui/switch';
import { setBreadcrumbs } from '@/composables/common/useBreadcrumbs';
import { useT } from '@/composables/useT';

const t = useT();
setBreadcrumbs(() => [
    { title: t('sidebar.dashboard'), href: '/dashboard' },
    { title: t('sidebar.settings'), href: '/admin/settings/identity' },
    { title: t('sidebar.layout_toggles'), href: '/admin/settings/layout' },
]);

type Settings = {
    header_sticky: boolean;
    show_language_switcher: boolean;
    show_back_to_top: boolean;
    footer_copyright_auto_year: boolean;
};

const props = defineProps<{ settings: Settings }>();

// Breadcrumbs set dynamically above via setBreadcrumbs().
defineOptions({});

const form = useForm({
    header_sticky: props.settings.header_sticky,
    show_language_switcher: props.settings.show_language_switcher,
    show_back_to_top: props.settings.show_back_to_top,
    footer_copyright_auto_year: props.settings.footer_copyright_auto_year,
});

function submit(): void {
    form.put('/admin/settings/layout', { preserveScroll: true });
}

const toggles = [
    {
        field: 'header_sticky' as const,
        label: 'Sticky header',
        hint: 'Header stays pinned while scrolling.',
    },
    {
        field: 'show_language_switcher' as const,
        label: 'Show language switcher',
        hint: 'EN / DE toggle in the header.',
    },
    {
        field: 'show_back_to_top' as const,
        label: 'Show back-to-top button',
        hint: 'Floating arrow on long pages.',
    },
    {
        field: 'footer_copyright_auto_year' as const,
        label: 'Auto-update footer year',
        hint: 'Always shows the current year.',
    },
];
</script>

<template>
    <Head :title="t('settings.layout_title')" />

    <div class="flex flex-col gap-6 p-4">
        <Heading
            :title="t('settings.layout_title')"
            :description="t('settings.layout_description')"
        />

        <form class="flex flex-col gap-4" @submit.prevent="submit">
            <Card>
                <CardContent class="space-y-3 pt-6">
                    <label
                        v-for="t in toggles"
                        :key="t.field"
                        class="flex cursor-pointer items-center gap-3 rounded-md border p-3 text-sm"
                    >
                        <Switch
                            :model-value="form[t.field]"
                            @update:model-value="(v) => (form[t.field] = v as boolean)"
                        />
                        <span class="flex-1">
                            <span class="block font-medium">{{ t.label }}</span>
                            <span class="block text-xs text-muted-foreground">
                                {{ t.hint }}
                            </span>
                        </span>
                    </label>
                </CardContent>
            </Card>

            <div class="flex justify-end">
                <Button type="submit" :disabled="form.processing">
                    <Save class="size-4" />
                    Save layout
                </Button>
            </div>
        </form>
    </div>
</template>
