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
                    <CardTitle>Title &amp; description</CardTitle>
                </CardHeader>
                <CardContent class="space-y-3">
                    <div class="space-y-1">
                        <Label for="default_meta_title_template">Default title template</Label>
                        <Input
                            id="default_meta_title_template"
                            v-model="form.default_meta_title_template"
                            placeholder="%page% — %site%"
                        />
                        <p class="text-xs text-muted-foreground">
                            Tokens: <code>%page%</code>, <code>%site%</code>. Preview:
                            <strong>{{ titlePreview }}</strong>
                        </p>
                        <InputError :message="form.errors.default_meta_title_template" />
                    </div>
                    <div class="space-y-1">
                        <Label>Default meta description (per language)</Label>
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
                                        {{ (form.translations[languages.findIndex((l) => l.code === code)].default_meta_description ?? '').length }} / 160 recommended
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
                    <CardTitle>Default OG image · 1200×630</CardTitle>
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
                            Pick image
                        </Button>
                        <Button
                            v-if="form.default_og_image_path"
                            type="button"
                            size="sm"
                            variant="ghost"
                            class="text-rose-600 hover:text-rose-700"
                            @click="clearOg"
                        >
                            Clear
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
                            <span class="block font-medium">Allow search indexing</span>
                            <span class="block text-xs text-muted-foreground">
                                Off = ships <code>noindex,nofollow</code> sitewide. Handy on staging.
                            </span>
                        </span>
                    </label>
                </CardContent>
            </Card>

            <div class="flex justify-end">
                <Button type="submit" :disabled="form.processing">
                    <Save class="size-4" />
                    Save SEO defaults
                </Button>
            </div>
        </form>

        <MediaPicker v-model:open="pickerOpen" accept="image" @pick="onPicked" />
    </div>
</template>
