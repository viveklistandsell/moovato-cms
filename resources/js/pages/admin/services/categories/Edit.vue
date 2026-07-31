<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ArrowLeft, Image as ImageIcon, Save, Upload, X } from 'lucide-vue-next';
import { computed, ref, watch } from 'vue';
import Heading from '@/components/Heading.vue';
import InputError from '@/components/InputError.vue';
import LocaleTabs from '@/components/common/LocaleTabs.vue';
import MediaPicker from '@/components/common/MediaPicker.vue';
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
import { Textarea } from '@/components/ui/textarea';
import { setBreadcrumbs } from '@/composables/common/useBreadcrumbs';
import { useAdminLanguage } from '@/composables/useAdminLocale';
import { useT } from '@/composables/useT';
import { slugify } from '@/lib/slug';

const t = useT();
const adminLang = useAdminLanguage();

type Translation = {
    name: string;
    permalink: string;
    short_description: string | null;
};

type Category = {
    id: number;
    parent_category_id: number | null;
    image: string | null;
    image_url: string | null;
    status: string;
    is_featured: boolean;
    is_popular: boolean;
    sort_order: number;
    translations: Record<string, Translation>;
};

type Language = {
    code: string;
    name: string;
    native_name: string;
    flag: string | null;
    is_default: boolean;
};

type ParentOption = {
    id: number;
    label: string;
    labels: Record<string, string>;
};

function parentOptionLabel(opt: ParentOption): string {
    return opt.labels?.[adminLang.value] ?? opt.label;
}

const props = defineProps<{
    category: Category | null;
    languages: Language[];
    parentOptions: ParentOption[];
    nextSortOrder: number;
}>();

const isEdit = computed(() => props.category !== null);

setBreadcrumbs(() => [
    { title: t('sidebar.dashboard'), href: '/dashboard' },
    { title: t('sidebar.service_categories'), href: '/admin/services/categories' },
    {
        title: isEdit.value
            ? t('service_categories.edit_title')
            : t('service_categories.create_title'),
        href: '#',
    },
]);

const initialTranslations: Record<string, Translation> = Object.fromEntries(
    props.languages.map((lang) => [
        lang.code,
        props.category?.translations[lang.code] ?? {
            name: '',
            permalink: '',
            short_description: '',
        },
    ]),
);

const form = useForm<{
    parent_category_id: number | null;
    image: string;
    status: string;
    is_featured: boolean;
    is_popular: boolean;
    sort_order: number;
    translations: Record<string, Translation>;
}>({
    parent_category_id:
        props.category?.parent_category_id ??
        props.parentOptions[0]?.id ??
        null,
    image: props.category?.image ?? '',
    status: props.category?.status ?? 'published',
    is_featured: props.category?.is_featured ?? false,
    is_popular: props.category?.is_popular ?? false,
    sort_order: props.category?.sort_order ?? props.nextSortOrder,
    translations: initialTranslations,
});

const imagePreview = ref<string | null>(props.category?.image_url ?? null);
const pickerOpen = ref(false);

function onMediaPicked(file: { path: string; url: string; name: string }): void {
    form.image = file.path;
    imagePreview.value = file.url;
}

function removeImage(): void {
    form.image = '';
    imagePreview.value = null;
}

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
            if (!permalinkTouched.value[code] && tr.name.length > 0) {
                tr.permalink = slugify(tr.name);
            }
        }
    },
    { deep: true },
);

function onPermalinkInput(code: string): void {
    permalinkTouched.value[code] = true;
}

function submit(): void {
    if (isEdit.value && props.category) {
        form.put(`/admin/services/categories/${props.category.id}`);
    } else {
        form.post('/admin/services/categories');
    }
}

const errorFor = (code: string, field: keyof Translation) =>
    form.errors[`translations.${code}.${field}` as keyof typeof form.errors] as
        | string
        | undefined;
</script>

<template>
    <Head
        :title="
            isEdit
                ? t('service_categories.edit_title')
                : t('service_categories.create_title')
        "
    />

    <div class="flex flex-col gap-6 p-4">
        <div class="flex items-start justify-between gap-4">
            <div class="flex items-center gap-3">
                <Button as-child variant="ghost" size="sm">
                    <Link href="/admin/services/categories">
                        <ArrowLeft class="size-4" />
                    </Link>
                </Button>
                <Heading
                    :title="
                        isEdit
                            ? t('service_categories.edit_title')
                            : t('service_categories.create_title')
                    "
                    :description="
                        isEdit
                            ? t('service_categories.edit_description')
                            : t('service_categories.create_description')
                    "
                />
            </div>
        </div>

        <form
            class="grid grid-cols-1 gap-6 lg:grid-cols-3"
            @submit.prevent="submit"
        >
            <div class="space-y-6 lg:col-span-2">
                <Card>
                    <CardHeader>
                        <CardTitle>{{ t('service_categories.translations_title') }}</CardTitle>
                        <CardDescription>
                            {{ t('service_categories.translations_description') }}
                        </CardDescription>
                    </CardHeader>
                    <CardContent>
                        <LocaleTabs v-model="activeLocale" :languages="languages">
                            <template #default="{ code }">
                                <div class="space-y-5 pt-4">
                                    <div class="grid gap-2">
                                        <Label :for="`name-${code}`">
                                            {{ t('service_categories.field_name') }}
                                            <span
                                                v-if="languages.find((l) => l.code === code)?.is_default"
                                                class="text-destructive"
                                            >*</span>
                                        </Label>
                                        <Input
                                            :id="`name-${code}`"
                                            v-model="form.translations[code].name"
                                            :placeholder="t('service_categories.field_name_placeholder')"
                                        />
                                        <InputError :message="errorFor(code, 'name')" />
                                    </div>

                                    <div class="grid gap-2">
                                        <Label :for="`permalink-${code}`">
                                            {{ t('service_categories.field_permalink') }}
                                            <span
                                                v-if="languages.find((l) => l.code === code)?.is_default"
                                                class="text-destructive"
                                            >*</span>
                                        </Label>
                                        <Input
                                            :id="`permalink-${code}`"
                                            v-model="form.translations[code].permalink"
                                            :placeholder="t('service_categories.field_permalink_placeholder')"
                                            class="font-mono"
                                            @input="onPermalinkInput(code)"
                                        />
                                        <p class="text-xs text-muted-foreground">
                                            {{ t('service_categories.field_permalink_hint') }}
                                        </p>
                                        <InputError :message="errorFor(code, 'permalink')" />
                                    </div>
                                    <div class="grid gap-2">
                                        <Label :for="`short-description-${code}`">
                                            {{ t('service_categories.field_short_description') }}
                                        </Label>
                                        <Textarea
                                            :id="`short-description-${code}`"
                                            v-model="form.translations[code].short_description as string"
                                            :rows="4"
                                            :placeholder="t('service_categories.field_short_description_placeholder')"
                                        />
                                        <InputError :message="errorFor(code, 'short_description')" />
                                    </div>
                                </div>
                            </template>
                        </LocaleTabs>
                    </CardContent>
                </Card>
            </div>

            <div class="space-y-6">
                <Card>
                    <CardHeader>
                        <CardTitle>{{ t('service_categories.settings_title') }}</CardTitle>
                        <CardDescription>
                            {{ t('service_categories.settings_description') }}
                        </CardDescription>
                    </CardHeader>
                    <CardContent class="space-y-5">
                        <div class="grid gap-2">
                            <Label for="parent_category_id">
                                {{ t('service_categories.field_parent') }}
                                <span class="text-destructive">*</span>
                            </Label>
                            <Select
                                :model-value="
                                    form.parent_category_id === null
                                        ? undefined
                                        : String(form.parent_category_id)
                                "
                                @update:model-value="
                                    (v) =>
                                        (form.parent_category_id =
                                            typeof v === 'string'
                                                ? Number(v)
                                                : null)
                                "
                            >
                                <SelectTrigger id="parent_category_id">
                                    <SelectValue :placeholder="t('service_categories.field_parent_placeholder')" />
                                </SelectTrigger>
                                <SelectContent>
                                    <SelectItem
                                        v-for="opt in parentOptions"
                                        :key="opt.id"
                                        :value="String(opt.id)"
                                    >
                                        {{ parentOptionLabel(opt) }}
                                    </SelectItem>
                                </SelectContent>
                            </Select>
                            <InputError :message="form.errors.parent_category_id" />
                        </div>

                        <div class="grid gap-2">
                            <Label>{{ t('service_categories.field_image') }}</Label>
                            <div
                                class="flex aspect-video w-full items-center justify-center overflow-hidden rounded-md border border-dashed bg-muted"
                            >
                                <img
                                    v-if="imagePreview"
                                    :src="imagePreview"
                                    :alt="form.translations[activeLocale]?.name || ''"
                                    class="size-full object-cover"
                                />
                                <ImageIcon
                                    v-else
                                    class="size-8 text-muted-foreground"
                                />
                            </div>
                            <div class="flex flex-wrap items-center gap-2">
                                <Button
                                    type="button"
                                    variant="outline"
                                    size="sm"
                                    class="flex-1"
                                    @click="pickerOpen = true"
                                >
                                    <Upload class="size-4" />
                                    {{
                                        imagePreview
                                            ? t('service_categories.field_image_replace')
                                            : t('service_categories.field_image_upload')
                                    }}
                                </Button>
                                <Button
                                    v-if="imagePreview"
                                    type="button"
                                    variant="ghost"
                                    size="sm"
                                    class="text-destructive"
                                    @click="removeImage"
                                >
                                    <X class="size-4" />
                                    {{ t('service_categories.field_image_remove') }}
                                </Button>
                            </div>
                            <p class="text-xs text-muted-foreground">
                                {{ t('service_categories.field_image_hint') }}
                            </p>
                            <InputError :message="form.errors.image" />
                        </div>

                        <div class="grid gap-2">
                            <Label for="status">{{ t('table.col_status') }} *</Label>
                            <Select v-model="form.status">
                                <SelectTrigger id="status">
                                    <SelectValue />
                                </SelectTrigger>
                                <SelectContent>
                                    <SelectItem value="published">{{ t('status.published') }}</SelectItem>
                                    <SelectItem value="draft">{{ t('status.draft') }}</SelectItem>
                                    <SelectItem value="inactive">{{ t('status.inactive') }}</SelectItem>
                                </SelectContent>
                            </Select>
                            <InputError :message="form.errors.status" />
                        </div>

                        <div class="grid gap-2">
                            <Label for="sort_order">{{ t('table.col_order') }}</Label>
                            <Input
                                id="sort_order"
                                v-model.number="form.sort_order"
                                type="number"
                                min="0"
                                max="65535"
                            />
                            <InputError :message="form.errors.sort_order" />
                        </div>

                        <div class="flex items-center justify-between rounded-md border p-3">
                            <div>
                                <Label for="is_featured" class="text-sm font-medium">
                                    {{ t('service_categories.field_is_featured') }}
                                </Label>
                                <p class="text-xs text-muted-foreground">
                                    {{ t('service_categories.field_is_featured_hint') }}
                                </p>
                            </div>
                            <Switch id="is_featured" v-model="form.is_featured" />
                        </div>

                        <div class="flex items-center justify-between rounded-md border p-3">
                            <div>
                                <Label for="is_popular" class="text-sm font-medium">
                                    {{ t('service_categories.field_is_popular') }}
                                </Label>
                                <p class="text-xs text-muted-foreground">
                                    {{ t('service_categories.field_is_popular_hint') }}
                                </p>
                            </div>
                            <Switch id="is_popular" v-model="form.is_popular" />
                        </div>
                    </CardContent>
                </Card>

                <div class="flex items-center justify-end gap-2">
                    <Button as-child variant="ghost">
                        <Link href="/admin/services/categories">
                            {{ t('common.cancel') }}
                        </Link>
                    </Button>
                    <Button type="submit" :disabled="form.processing">
                        <Save class="size-4" />
                        {{ isEdit ? t('common.save_changes') : t('common.create') }}
                    </Button>
                </div>
            </div>
        </form>
        <MediaPicker v-model:open="pickerOpen" @pick="onMediaPicked" />
    </div>
</template>
