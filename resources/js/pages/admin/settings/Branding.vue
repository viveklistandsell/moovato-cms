<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import { Image as ImageIcon, Save } from 'lucide-vue-next';
import { ref } from 'vue';
import Heading from '@/components/Heading.vue';
import { setBreadcrumbs } from '@/composables/common/useBreadcrumbs';
import { useT } from '@/composables/useT';

const t = useT();
setBreadcrumbs(() => [
    { title: t('sidebar.dashboard'), href: '/dashboard' },
    { title: t('sidebar.settings'), href: '/admin/settings/identity' },
    { title: t('sidebar.branding'), href: '/admin/settings/branding' },
]);
import InputError from '@/components/InputError.vue';
import MediaPicker from '@/components/common/MediaPicker.vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';

type Settings = {
    logo_light_path: string | null;
    logo_dark_path: string | null;
    favicon_path: string | null;
    theme_color: string | null;
};

const props = defineProps<{ settings: Settings }>();

// Breadcrumbs set dynamically above via setBreadcrumbs().
defineOptions({});

const form = useForm({
    logo_light_path: props.settings.logo_light_path ?? '',
    logo_dark_path: props.settings.logo_dark_path ?? '',
    favicon_path: props.settings.favicon_path ?? '',
    theme_color: props.settings.theme_color ?? '#0f172a',
});

function submit(): void {
    form.put('/admin/settings/branding', { preserveScroll: true });
}

// One MediaPicker shared by all three image fields.
const pickerOpen = ref(false);
const pickerTarget = ref<
    'logo_light_path' | 'logo_dark_path' | 'favicon_path' | null
>(null);
const previewUrls = ref<Record<string, string>>({});

function openPicker(field: NonNullable<typeof pickerTarget.value>): void {
    pickerTarget.value = field;
    pickerOpen.value = true;
}

function onPicked(file: {
    path: string;
    url: string;
    name: string;
    thumb_path?: string | null;
    thumb_url?: string | null;
}): void {
    if (!pickerTarget.value) return;
    const target = pickerTarget.value;
    const path = file.thumb_path && file.thumb_path !== '' ? file.thumb_path : file.path;
    const url = file.thumb_url && file.thumb_url !== '' ? file.thumb_url : file.url;
    (form as unknown as Record<string, string>)[target] = path;
    previewUrls.value = { ...previewUrls.value, [target]: url };
    pickerTarget.value = null;
}

function preview(field: string, stored: string): string | null {
    if (previewUrls.value[field]) return previewUrls.value[field];
    if (!stored) return null;
    return `/storage/${stored.replace(/^\/+/, '')}`;
}

function clear(field: NonNullable<typeof pickerTarget.value>): void {
    (form as unknown as Record<string, string>)[field] = '';
    const next = { ...previewUrls.value };
    delete next[field];
    previewUrls.value = next;
}

const imageFields = [
    { field: 'logo_light_path' as const, label: 'Logo (light)' },
    { field: 'logo_dark_path' as const, label: 'Logo (dark)' },
    { field: 'favicon_path' as const, label: 'Favicon · 32×32 / 180×180' },
];
</script>

<template>
    <Head :title="t('settings.branding_title')" />

    <div class="flex flex-col gap-6 p-4">
        <Heading
            :title="t('settings.branding_title')"
            :description="t('settings.branding_description')"
        />

        <form class="flex flex-col gap-4" @submit.prevent="submit">
            <Card>
                <CardHeader>
                    <CardTitle>Logos &amp; favicon</CardTitle>
                </CardHeader>
                <CardContent class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                    <div v-for="img in imageFields" :key="img.field" class="space-y-2">
                        <Label>{{ img.label }}</Label>
                        <div
                            class="flex aspect-video items-center justify-center overflow-hidden rounded-md border bg-muted"
                        >
                            <img
                                v-if="preview(img.field, form[img.field])"
                                :src="preview(img.field, form[img.field]) ?? ''"
                                :alt="img.label"
                                class="size-full object-contain"
                            />
                            <ImageIcon
                                v-else
                                class="size-8 text-muted-foreground/40"
                            />
                        </div>
                        <div class="flex gap-2">
                            <Button
                                type="button"
                                size="sm"
                                variant="outline"
                                class="flex-1"
                                @click="openPicker(img.field)"
                            >
                                Pick image
                            </Button>
                            <Button
                                v-if="form[img.field]"
                                type="button"
                                size="sm"
                                variant="ghost"
                                class="text-rose-600 hover:text-rose-700"
                                @click="clear(img.field)"
                            >
                                Clear
                            </Button>
                        </div>
                    </div>
                </CardContent>
            </Card>

            <Card>
                <CardHeader>
                    <CardTitle>Theme colour</CardTitle>
                </CardHeader>
                <CardContent class="space-y-1">
                    <Label for="theme_color">Hex value</Label>
                    <div class="flex items-center gap-3">
                        <input
                            id="theme_color"
                            v-model="form.theme_color"
                            type="color"
                            class="h-9 w-12 cursor-pointer rounded border bg-transparent"
                        />
                        <Input
                            v-model="form.theme_color"
                            placeholder="#0f172a"
                            class="max-w-[160px] font-mono"
                        />
                    </div>
                    <p class="text-xs text-muted-foreground">
                        Drives the browser tab colour and the PWA manifest.
                    </p>
                    <InputError :message="form.errors.theme_color" />
                </CardContent>
            </Card>

            <div class="flex justify-end">
                <Button type="submit" :disabled="form.processing">
                    <Save class="size-4" />
                    Save branding
                </Button>
            </div>
        </form>

        <MediaPicker v-model:open="pickerOpen" accept="image" @pick="onPicked" />
    </div>
</template>
