<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import { Image as ImageIcon, Save } from 'lucide-vue-next';
import { computed, ref } from 'vue';
import Heading from '@/components/Heading.vue';
import InputError from '@/components/InputError.vue';
import LocaleTabs, { type LocaleOption } from '@/components/common/LocaleTabs.vue';
import MediaPicker from '@/components/common/MediaPicker.vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Switch } from '@/components/ui/switch';
import { Textarea } from '@/components/ui/textarea';
import { setBreadcrumbs } from '@/composables/common/useBreadcrumbs';
import { useT } from '@/composables/useT';

const t = useT();
setBreadcrumbs(() => [
    { title: t('sidebar.dashboard'), href: '/dashboard' },
    { title: t('sidebar.settings'), href: '/admin/settings/identity' },
    { title: t('sidebar.seo_defaults'), href: '/admin/settings/seo' },
]);

type TranslationRow = { default_meta_description: string | null };

type Settings = {
    default_meta_title_template: string | null;
    default_og_image_path: string | null;
    robots_index: boolean;
    translations: Record<string, TranslationRow>;
};

const props = defineProps<{
    settings: Settings;
    languages: LocaleOption[];
}>();

// Breadcrumbs set dynamically above via setBreadcrumbs().
defineOptions({});

const defaultLang = computed<string>(
    () =>
        props.languages.find((l) => l.is_default)?.code ??
        props.languages[0]?.code ??
        'de',
);
const activeLang = ref<string>(defaultLang.value);

const form = useForm({
    default_meta_title_template:
        props.settings.default_meta_title_template ?? '%page% — %site%',
    default_og_image_path: props.settings.default_og_image_path ?? '',
    robots_index: props.settings.robots_index,
    translations: props.languages.map((lang) => ({
        lang: lang.code,
        default_meta_description:
            props.settings.translations[lang.code]?.default_meta_description ?? '',
    })),
});

function submit(): void {
    form.put('/admin/settings/seo', { preserveScroll: true });
}

function descErrorFor(lang: string): string | undefined {
    const idx = form.translations.findIndex((t) => t.lang === lang);
    if (idx < 0) return undefined;
    const errs = form.errors as Record<string, string | undefined>;
    return errs[`translations.${idx}.default_meta_description`];
}

const titlePreview = computed<string>(() =>
    (form.default_meta_title_template || '%page% — %site%')
        .replace(/%page%/g, 'About')
        .replace(/%site%/g, 'Moovato'),
);

const pickerOpen = ref(false);
const ogPreviewUrl = ref<string | null>(null);

function onPicked(file: {
    path: string;
    url: string;
    name: string;
    thumb_path?: string | null;
    thumb_url?: string | null;
}): void {
    form.default_og_image_path =
        file.thumb_path && file.thumb_path !== '' ? file.thumb_path : file.path;
    ogPreviewUrl.value =
        file.thumb_url && file.thumb_url !== '' ? file.thumb_url : file.url;
}

const ogPreview = computed<string | null>(() => {
    if (ogPreviewUrl.value) return ogPreviewUrl.value;
    if (!form.default_og_image_path) return null;
    return `/storage/${form.default_og_image_path.replace(/^\/+/, '')}`;
});

function clearOg(): void {
    form.default_og_image_path = '';
    ogPreviewUrl.value = null;
}
</script>

<template>
    <Head :title="t('settings.seo_title')" />

    <div class="flex flex-col gap-6 p-4">
        <Heading
            :title="t('settings.seo_title')"
            :description="t('settings.seo_description')"
        />

        <form class="flex flex-col gap-4" @submit.prevent="submit">
            <Card>
                <CardHeader>
                    <CardTitle>{{ t('settings.seo_title_desc_card') }}</CardTitle>
                </CardHeader>
                <CardContent class="space-y-3">
                    <div class="space-y-1">
                        <Label for="default_meta_title_template">{{ t('settings.seo_title_template') }}</Label>
                        <Input
                            id="default_meta_title_template"
                            v-model="form.default_meta_title_template"
                            :placeholder="t('settings.seo_title_template_placeholder')"
                        />
                        <p class="text-xs text-muted-foreground" v-html="t('settings.seo_title_template_help', { preview: titlePreview })" />
                        <InputError :message="form.errors.default_meta_title_template" />
                    </div>
                    <div class="space-y-1">
                        <Label>{{ t('settings.seo_meta_desc_label') }}</Label>
                        <LocaleTabs
                            :model-value="activeLang"
                            :languages="languages"
                            @update:model-value="(v) => (activeLang = v)"
                        >
                            <template #default="{ code }">
                                <div class="space-y-1 pt-3">
                                    <Textarea
                                        :id="`meta_desc_${code}`"
                                        v-model="form.translations[languages.findIndex((l) => l.code === code)].default_meta_description"
                                        :rows="3"
                                        maxlength="500"
                                    />
                                    <p class="text-xs text-muted-foreground">
                                        {{ t('settings.seo_chars_recommended', { count: (form.translations[languages.findIndex((l) => l.code === code)].default_meta_description ?? '').length }) }}
                                    </p>
                                    <InputError :message="descErrorFor(code)" />
                                </div>
                            </template>
                        </LocaleTabs>
                    </div>
                </CardContent>
            </Card>

            <Card>
                <CardHeader>
                    <CardTitle>{{ t('settings.seo_og_card') }}</CardTitle>
                </CardHeader>
                <CardContent class="space-y-2">
                    <div
                        class="flex aspect-[1200/630] max-w-md items-center justify-center overflow-hidden rounded-md border bg-muted"
                    >
                        <img
                            v-if="ogPreview"
                            :src="ogPreview"
                            alt="OG image"
                            class="size-full object-cover"
                        />
                        <ImageIcon v-else class="size-10 text-muted-foreground/40" />
                    </div>
                    <div class="flex gap-2">
                        <Button
                            type="button"
                            size="sm"
                            variant="outline"
                            @click="pickerOpen = true"
                        >
                            {{ t('settings.seo_og_pick_image') }}
                        </Button>
                        <Button
                            v-if="form.default_og_image_path"
                            type="button"
                            size="sm"
                            variant="ghost"
                            class="text-rose-600 hover:text-rose-700"
                            @click="clearOg"
                        >
                            {{ t('settings.seo_og_clear') }}
                        </Button>
                    </div>
                </CardContent>
            </Card>

            <Card>
                <CardContent class="pt-6">
                    <label class="flex cursor-pointer items-center gap-3 rounded-md border p-3 text-sm">
                        <Switch
                            :model-value="form.robots_index"
                            @update:model-value="(v) => (form.robots_index = v as boolean)"
                        />
                        <span class="flex-1">
                            <span class="block font-medium">{{ t('settings.seo_allow_indexing') }}</span>
                            <span class="block text-xs text-muted-foreground" v-html="t('settings.seo_allow_indexing_help')" />
                        </span>
                    </label>
                </CardContent>
            </Card>

            <div class="flex justify-end">
                <Button type="submit" :disabled="form.processing">
                    <Save class="size-4" />
                    {{ t('settings.seo_save') }}
                </Button>
            </div>
        </form>

        <MediaPicker v-model:open="pickerOpen" accept="image" @pick="onPicked" />
    </div>
</template>
