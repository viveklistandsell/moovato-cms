<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import {
    ArrowLeft,
    ExternalLink,
    Image as ImageIcon,
    Save,
    Upload,
    X,
} from 'lucide-vue-next';
import { computed, ref, watch } from 'vue';
import Heading from '@/components/Heading.vue';
import InputError from '@/components/InputError.vue';
import LocaleTabs from '@/components/common/LocaleTabs.vue';
import MultiSelect from '@/components/common/MultiSelect.vue';
import WidgetsCanvas from '@/components/admin/widgets/WidgetsCanvas.vue';
import type { WidgetInstance, WidgetMeta } from '@/widgets/types';
import { getWidgetEntry } from '@/widgets/registry';
import { Button } from '@/components/ui/button';
import {
    Card,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
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
import { setBreadcrumbs } from '@/composables/common/useBreadcrumbs';
import { useT } from '@/composables/useT';
import MediaPicker from '@/components/common/MediaPicker.vue';
import { localizedUrl } from '@/lib/localizedUrl';
import { slugify } from '@/lib/slug';

const t = useT();

type Translation = {
    title: string;
    permalink: string;
};

type Page = {
    id: number;
    image: string | null;
    image_url: string | null;
    user_id: number | null;
    user_name?: string | null;
    category_ids: number[];
    template: string;
    is_home: boolean;
    status: string;
    translations: Record<string, Translation>;
};

type Language = {
    code: string;
    name: string;
    native_name: string;
    flag: string | null;
    is_default: boolean;
};

type CategoryOption = { id: number; name: string };
type TemplateOption = { value: string; label: string };

const props = defineProps<{
    page: Page | null;
    languages: Language[];
    urlPrefixes: Record<string, string>;
    categoryOptions: CategoryOption[];
    templates: TemplateOption[];
    currentUserId: number | null;
    availableWidgets: WidgetMeta[];
    pageWidgets: WidgetInstance[];
}>();

const isEdit = computed(() => props.page !== null);

setBreadcrumbs(() => [
    { title: t('sidebar.dashboard'), href: '/dashboard' },
    { title: t('sidebar.pages'), href: '/admin/pages' },
    {
        title: isEdit.value ? t('pages.edit_title') : t('pages.create_title'),
        href: '#',
    },
]);

const initialTranslations: Record<string, Translation> = Object.fromEntries(
    props.languages.map((lang) => [
        lang.code,
        props.page?.translations[lang.code] ?? {
            title: '',
            permalink: '',
        },
    ]),
);

const form = useForm({
    image: null,
    image_path: (props.page?.image ?? '') as string,
    remove_image: false,
    category_ids: props.page?.category_ids ?? [],
    template: props.page?.template ?? 'default',
    is_home: props.page?.is_home ?? false,
    status: props.page?.status ?? 'published',
    translations: initialTranslations,
    widgets: [],
});

const editWidgets = ref<WidgetInstance[]>(props.pageWidgets ?? []);

const activeLocale = ref(
    props.languages.find((l) => l.is_default)?.code ??
        props.languages[0]?.code ??
        'de',
);

const permalinkTouched = ref<Record<string, boolean>>(
    Object.fromEntries(props.languages.map((lang) => [lang.code, false])),
);

watch(
    () => form.translations,
    (translations) => {
        for (const code of Object.keys(translations)) {
            const tr = translations[code];
            if (!permalinkTouched.value[code] && tr.title.length > 0) {
                tr.permalink = slugify(tr.title);
            }
        }
    },
    { deep: true },
);

function onPermalinkInput(code: string): void {
    permalinkTouched.value[code] = true;
}

const imagePreview = ref<string | null>(props.page?.image_url ?? null);

function removeImage(): void {
    form.image = null;
    form.image_path = '';
    form.remove_image = true;
    imagePreview.value = null;
}

const pickerOpen = ref(false);

function onMediaPicked(file: {
    path: string;
    url: string;
    name: string;
}): void {
    form.image = null;
    form.image_path = file.path;
    form.remove_image = false;
    imagePreview.value = file.url;
}

function submit(): void {
    if (isEdit.value) {
        const widgets = (
            JSON.parse(JSON.stringify(editWidgets.value)) as WidgetInstance[]
        ).filter((w) => getWidgetEntry(w.type) !== null);

        form.transform((data) => ({ ...data, widgets })).put(
            `/admin/pages/${props.page!.id}`,
        );
    } else {
        form.post('/admin/pages');
    }
}

const previewUrl = computed<string | null>(() => {
    if (!props.page) return null;
    const defaultLocale =
        props.languages.find((l) => l.is_default)?.code ?? 'de';
    const slug =
        props.page.translations[defaultLocale]?.permalink ??
        Object.values(props.page.translations)[0]?.permalink ??
        null;
    return slug ? localizedUrl(defaultLocale, `/${slug}`) : null;
});

const errorFor = (code: string, field: keyof Translation) =>
    form.errors[`translations.${code}.${field}` as keyof typeof form.errors] as
        | string
        | undefined;
</script>

<template>
    <Head :title="isEdit ? t('pages.edit_title') : t('pages.new_page')" />

    <div class="flex flex-col gap-6 p-4">
        <div class="flex items-start justify-between gap-4">
            <div class="flex items-center gap-3">
                <Button as-child variant="ghost" size="sm">
                    <Link href="/admin/pages">
                        <ArrowLeft class="size-4" />
                    </Link>
                </Button>
                <Heading
                    :title="isEdit ? t('pages.edit_title') : t('pages.create_title')"
                    :description="
                        isEdit
                            ? t('pages.edit_description')
                            : t('pages.create_description')
                    "
                />
            </div>
        </div>

        <form
            class="grid grid-cols-1 gap-6 lg:grid-cols-3"
            @submit.prevent="submit"
        >
            <!-- LEFT: translations -->
            <div class="space-y-6 lg:col-span-2">
                <Card>
                    <CardHeader>
                        <CardTitle>{{ t('pages.translations_title') }}</CardTitle>
                        <CardDescription>
                            {{ t('pages.translations_description') }}
                        </CardDescription>
                    </CardHeader>
                    <CardContent>
                        <LocaleTabs
                            v-model="activeLocale"
                            :languages="languages"
                        >
                            <template #default="{ code }">
                                <div class="space-y-5 pt-4">
                                    <div class="grid gap-2">
                                        <Label :for="`title-${code}`">
                                            {{ t('pages.title_label') }}
                                            <span
                                                v-if="
                                                    languages.find(
                                                        (l) => l.code === code,
                                                    )?.is_default
                                                "
                                                class="text-destructive"
                                                >*</span
                                            >
                                        </Label>
                                        <Input
                                            :id="`title-${code}`"
                                            v-model="
                                                form.translations[code].title
                                            "
                                            :placeholder="t('pages.title_placeholder')"
                                        />
                                        <InputError
                                            :message="errorFor(code, 'title')"
                                        />
                                    </div>

                                    <div class="grid gap-2">
                                        <Label :for="`permalink-${code}`">
                                            {{ t('pages.permalink_label') }}
                                            <span
                                                v-if="
                                                    languages.find(
                                                        (l) => l.code === code,
                                                    )?.is_default
                                                "
                                                class="text-destructive"
                                                >*</span
                                            >
                                        </Label>
                                        <div class="flex w-full items-stretch">
                                            <span
                                                class="inline-flex shrink-0 items-center rounded-l-md border border-r-0 border-input bg-muted px-3 text-sm text-muted-foreground"
                                            >
                                                {{ urlPrefixes[code] }}
                                            </span>
                                            <input
                                                :id="`permalink-${code}`"
                                                v-model="
                                                    form.translations[code]
                                                        .permalink
                                                "
                                                :placeholder="t('pages.permalink_placeholder')"
                                                class="h-9 w-full min-w-0 rounded-r-md border border-input bg-transparent px-3 py-1 text-base shadow-xs transition-[color,box-shadow] outline-none placeholder:text-muted-foreground focus-visible:border-ring focus-visible:ring-[3px] focus-visible:ring-ring/50 md:text-sm dark:bg-input/30"
                                                @input="onPermalinkInput(code)"
                                            />
                                        </div>
                                        <p
                                            class="text-xs text-muted-foreground"
                                        >
                                            {{ t('pages.preview_label') }}:
                                            <span class="text-primary">
                                                {{ urlPrefixes[code]
                                                }}{{
                                                    form.translations[code]
                                                        .permalink ||
                                                    t('pages.permalink_placeholder')
                                                }}
                                            </span>
                                        </p>
                                        <InputError
                                            :message="
                                                errorFor(code, 'permalink')
                                            "
                                        />
                                    </div>
                                </div>
                            </template>
                        </LocaleTabs>
                    </CardContent>
                </Card>

                <!-- Widget Builder: available on BOTH create and edit.
                     On create, widgets are part of the main page form (no
                     internal Save button); on edit they have their own
                     Save button that hits the sync endpoint directly. -->
                <Card>
                    <CardHeader>
                        <CardTitle>{{ t('pages.widgets_title') }}</CardTitle>
                        <CardDescription>
                            {{ t('pages.widgets_description') }}
                        </CardDescription>
                    </CardHeader>
                    <CardContent>
                        <WidgetsCanvas
                            v-if="isEdit && page"
                            v-model:widgets="editWidgets"
                            :page-id="page.id"
                            :available-widgets="availableWidgets"
                            :languages="languages"
                        />
                        <WidgetsCanvas
                            v-else
                            v-model:widgets="form.widgets"
                            :available-widgets="availableWidgets"
                            :languages="languages"
                        />
                    </CardContent>
                </Card>
            </div>

            <!-- RIGHT: publish, status, template, image, categories -->
            <div class="space-y-6">
                <Card>
                    <CardHeader>
                        <CardTitle>{{ t('pages.publish_title') }}</CardTitle>
                    </CardHeader>
                    <CardContent>
                        <div class="flex flex-wrap items-center gap-2">
                            <Button
                                type="submit"
                                :disabled="form.processing"
                                class="flex-1"
                            >
                                {{ t('pages.save_exit') }}
                            </Button>
                            <Button
                                v-if="previewUrl"
                                as-child
                                type="button"
                                variant="default"
                            >
                                <a
                                    :href="previewUrl"
                                    target="_blank"
                                    rel="noopener"
                                    class="flex-1"
                                >
                                    <ExternalLink class="size-4" />
                                    {{ t('pages.preview_button') }}
                                </a>
                            </Button>
                            <Button
                                as-child
                                type="button"
                                variant="outline"
                                class="flex-1"
                            >
                                <Link href="/admin/pages">
                                    <X class="size-4" />
                                    {{ t('pages.cancel') }}
                                </Link>
                            </Button>
                        </div>
                        <p
                            v-if="form.progress"
                            class="mt-3 text-xs text-muted-foreground"
                        >
                            {{ t('pages.uploading', { percent: form.progress.percentage ?? 0 }) }}
                        </p>
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader>
                        <CardTitle>
                            {{ t('common.status') }}
                            <span class="text-destructive">*</span>
                        </CardTitle>
                    </CardHeader>
                    <CardContent class="space-y-4">
                        <Select
                            :model-value="form.status"
                            @update:model-value="
                                (v) => (form.status = v as string)
                            "
                        >
                            <SelectTrigger id="status" class="w-full">
                                <SelectValue />
                            </SelectTrigger>
                            <SelectContent>
                                <SelectItem value="published">{{ t('status.published') }}</SelectItem>
                                <SelectItem value="draft">{{ t('status.draft') }}</SelectItem>
                                <SelectItem value="inactive">{{ t('status.inactive') }}</SelectItem>
                            </SelectContent>
                        </Select>
                        <InputError :message="form.errors.status" />

                        <div class="flex items-center justify-between">
                            <Label for="is_home" class="cursor-pointer">
                                {{ t('pages.set_as_homepage') }}
                            </Label>
                            <Switch
                                id="is_home"
                                :model-value="form.is_home"
                                @update:model-value="(v) => (form.is_home = v)"
                            />
                        </div>
                        <p class="text-xs text-muted-foreground">
                            {{ t('pages.homepage_hint') }}
                        </p>
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader>
                        <CardTitle>{{ t('pages.template_title') }}</CardTitle>
                        <CardDescription>
                            {{ t('pages.template_description') }}
                        </CardDescription>
                    </CardHeader>
                    <CardContent>
                        <Select
                            :model-value="form.template"
                            @update:model-value="
                                (v) => (form.template = v as string)
                            "
                        >
                            <SelectTrigger id="template" class="w-full">
                                <SelectValue />
                            </SelectTrigger>
                            <SelectContent>
                                <SelectItem
                                    v-for="tmpl in templates"
                                    :key="tmpl.value"
                                    :value="tmpl.value"
                                >
                                    {{ tmpl.label }}
                                </SelectItem>
                            </SelectContent>
                        </Select>
                        <InputError :message="form.errors.template" />
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader>
                        <CardTitle>{{ t('pages.categories_card_title') }}</CardTitle>
                    </CardHeader>
                    <CardContent>
                        <MultiSelect
                            :model-value="form.category_ids"
                            :options="categoryOptions"
                            :placeholder="t('pages.categories_pick')"
                            :empty-message="t('pages.category_no_items')"
                            @update:model-value="(v) => (form.category_ids = v)"
                        />
                        <InputError
                            class="mt-2"
                            :message="form.errors.category_ids"
                        />
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader>
                        <CardTitle>{{ t('pages.featured_image') }}</CardTitle>
                        <CardDescription>
                            {{ t('pages.featured_image_hint') }}
                        </CardDescription>
                    </CardHeader>
                    <CardContent class="space-y-3">
                        <div
                            class="flex aspect-video w-full items-center justify-center overflow-hidden rounded-md border border-dashed bg-muted"
                        >
                            <img
                                v-if="imagePreview"
                                :src="imagePreview"
                                :alt="t('pages.preview_label')"
                                class="size-full object-cover"
                            />
                            <ImageIcon
                                v-else
                                class="size-8 text-muted-foreground/40"
                            />
                        </div>
                        <div class="flex items-center gap-2">
                            <Button
                                type="button"
                                variant="outline"
                                size="sm"
                                class="flex-1"
                                @click="pickerOpen = true"
                            >
                                <Upload class="size-4" />
                                {{ imagePreview ? t('pages.image_replace') : t('pages.image_upload') }}
                            </Button>
                            <Button
                                v-if="imagePreview"
                                type="button"
                                variant="ghost"
                                size="sm"
                                @click="removeImage"
                            >
                                <X class="size-4" />
                                {{ t('pages.image_remove') }}
                            </Button>
                        </div>
                        <p class="text-xs text-muted-foreground">
                            {{ t('pages.image_picker_hint') }}
                        </p>
                        <InputError :message="form.errors.image" />
                        <InputError :message="form.errors.image_path" />
                    </CardContent>
                </Card>
            </div>
        </form>

        <MediaPicker v-model:open="pickerOpen" @pick="onMediaPicked" />
    </div>
</template>
