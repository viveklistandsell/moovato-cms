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
import RichTextEditor from '@/components/common/RichTextEditor.vue';
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
import MediaPicker from '@/components/common/MediaPicker.vue';
import SeoMetaFields from '@/components/admin/seo/SeoMetaFields.vue';
import FlagImage from '@/components/common/FlagImage.vue';
import { setBreadcrumbs } from '@/composables/common/useBreadcrumbs';
import { useT } from '@/composables/useT';
import { slugify } from '@/lib/slug';

const t = useT();

type Translation = {
    name: string;
    permalink: string;
    short_description: string | null;
    content: string | null;
    meta_title?: string | null;
    meta_description?: string | null;
    schema?: string | null;
    meta_image?: string | null;
};

type Post = {
    id: number;
    image: string | null;
    image_url: string | null;
    user_id: number | null;
    user_name?: string | null;
    category_ids: number[];
    tag_ids: number[];
    status: string;
    sort_order: number;
    reading_time: number;
    view_count?: number;
    is_sticky: boolean;
    is_featured: boolean;
    translations: Record<string, Translation>;
};

type Language = {
    code: string;
    name: string;
    native_name: string;
    flag: string | null;
    is_default: boolean;
};

type Option = { id: number; name: string };

const props = defineProps<{
    post: Post | null;
    languages: Language[];
    urlPrefixes: Record<string, string>;
    categoryOptions: Option[];
    tagOptions: Option[];
    currentUserId: number | null;
    nextSortOrder: number;
}>();

const isEdit = computed(() => props.post !== null);

setBreadcrumbs(() => [
    { title: t('sidebar.dashboard'), href: '/dashboard' },
    { title: t('sidebar.blog_posts'), href: '/admin/blog/posts' },
    {
        title: isEdit.value ? t('blog.posts_edit') : t('blog.posts_create'),
        href: '#',
    },
]);

const initialTranslations: Record<string, Translation> = Object.fromEntries(
    props.languages.map((lang) => {
        const existing = props.post?.translations[lang.code];
        return [
            lang.code,
            {
                name: existing?.name ?? '',
                permalink: existing?.permalink ?? '',
                short_description: existing?.short_description ?? '',
                content: existing?.content ?? '',
                meta_title: existing?.meta_title ?? '',
                meta_description: existing?.meta_description ?? '',
                schema: typeof existing?.schema === 'string'
                    ? existing.schema
                    : (existing?.schema ? JSON.stringify(existing.schema, null, 2) : ''),
                meta_image: existing?.meta_image ?? '',
            },
        ];
    }),
);

const form = useForm({
    image: null as File | null,
    image_path: (props.post?.image ?? '') as string,
    remove_image: false,
    category_ids: props.post?.category_ids ?? [],
    tag_ids: props.post?.tag_ids ?? [],
    status: props.post?.status ?? 'published',
    sort_order: props.post?.sort_order ?? props.nextSortOrder,
    reading_time: props.post?.reading_time ?? 0,
    is_sticky: props.post?.is_sticky ?? false,
    is_featured: props.post?.is_featured ?? false,
    translations: initialTranslations,
});

const activeLocale = ref(
    props.languages.find((l) => l.is_default)?.code ??
        props.languages[0]?.code ??
        'de',
);

const seoActiveLocale = ref(
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

function onPermalinkInput(code: string) {
    permalinkTouched.value[code] = true;
}

// Auto reading-time calc: words / 200 (avg adult reading speed), rounded up.
const wordCounts = ref<Record<string, number>>(
    Object.fromEntries(props.languages.map((l) => [l.code, 0])),
);
const readingTimeTouched = ref(
    props.post?.reading_time !== undefined && props.post.reading_time > 0,
);

function recomputeReadingTime(): void {
    if (readingTimeTouched.value) {
        return;
    }
    const total = Object.values(wordCounts.value).reduce(
        (sum, n) => sum + n,
        0,
    );
    form.reading_time = Math.max(1, Math.ceil(total / 200));
}

function onWordCount(code: string, count: number): void {
    wordCounts.value[code] = count;
    recomputeReadingTime();
}

function onReadingTimeInput(): void {
    readingTimeTouched.value = true;
}

const totalWords = computed(() =>
    Object.values(wordCounts.value).reduce((sum, n) => sum + n, 0),
);

const imagePreview = ref<string | null>(props.post?.image_url ?? null);

function removeImage(): void {
    form.image = null;
    form.image_path = '';
    form.remove_image = true;
    imagePreview.value = null;
}

// In-form media picker modal. Click "Upload" to open the library popup,
// pick an existing image or upload a new one, and the chosen file's path
// is applied to image_path + preview. The form stays open the whole time.
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
        // Inertia + multipart upload + PUT requires _method spoofing in the
        // FormData body (the X-HTTP-Method-Override header is ignored when
        // the request is multipart/form-data).
        form.transform((data) => ({ ...data, _method: 'put' })).post(
            `/admin/blog/posts/${props.post!.id}`,
            { forceFormData: true },
        );
    } else {
        form.post('/admin/blog/posts', { forceFormData: true });
    }
}

function saveAndStay(): void {
    if (!isEdit.value) return;
    form.transform((data) => ({ ...data, _method: 'put' })).post(
        `/admin/blog/posts/${props.post!.id}?stay=1`,
        {
            forceFormData: true,
            preserveScroll: true,
            preserveState: true,
        },
    );
}

function livePermalinkUrl(code: string): string | null {
    const slug = form.translations[code]?.permalink ?? '';
    if (!slug) return null;
    const prefix = props.urlPrefixes[code] ?? '';
    return `${prefix}${slug}`;
}

const errorFor = (code: string, field: keyof Translation) =>
    form.errors[`translations.${code}.${field}` as keyof typeof form.errors] as
        | string
        | undefined;
</script>

<template>
    <Head :title="isEdit ? t('blog.posts_edit') : t('blog.new_post')" />

    <div class="flex flex-col gap-6 p-4">
        <div class="flex items-start justify-between gap-4">
            <div class="flex items-center gap-3">
                <Button as-child variant="ghost" size="sm">
                    <Link href="/admin/blog/posts">
                        <ArrowLeft class="size-4" />
                    </Link>
                </Button>
                <Heading
                    :title="isEdit ? t('blog.posts_edit') : t('blog.posts_create')"
                    :description="
                        isEdit
                            ? t('blog.edit_description')
                            : t('blog.create_description')
                    "
                />
            </div>
        </div>

        <form
            class="grid grid-cols-1 gap-6 lg:grid-cols-3"
            @submit.prevent="submit"
        >
            <!-- LEFT: just translations -->
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
                                        <Label :for="`name-${code}`">
                                            {{ t('blog.title_label') }}
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
                                            :id="`name-${code}`"
                                            v-model="
                                                form.translations[code].name
                                            "
                                            :placeholder="t('blog.title_placeholder')"
                                        />
                                        <InputError
                                            :message="errorFor(code, 'name')"
                                        />
                                    </div>

                                    <div class="grid gap-2">
                                        <Label :for="`permalink-${code}`">
                                            {{ t('blog.permalink_label') }}
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
                                                :placeholder="t('blog.permalink_placeholder')"
                                                class="h-9 w-full min-w-0 rounded-r-md border border-input bg-transparent px-3 py-1 text-base shadow-xs transition-[color,box-shadow] outline-none placeholder:text-muted-foreground focus-visible:border-ring focus-visible:ring-[3px] focus-visible:ring-ring/50 md:text-sm dark:bg-input/30"
                                                @input="onPermalinkInput(code)"
                                            />
                                        </div>
                                        <p
                                            class="text-xs text-muted-foreground"
                                        >
                                            {{ t('pages.preview_label') }}:
                                            <a
                                                v-if="livePermalinkUrl(code)"
                                                :href="livePermalinkUrl(code)!"
                                                target="_blank"
                                                rel="noopener"
                                                class="inline-flex items-center gap-1 font-medium text-[var(--orange)] underline underline-offset-4 hover:text-[color-mix(in_srgb,var(--orange)_80%,black)]"
                                            >
                                                {{ urlPrefixes[code]
                                                }}{{ form.translations[code].permalink }}
                                                <ExternalLink class="size-3" />
                                            </a>
                                            <span v-else class="text-[var(--orange)]">
                                                {{ urlPrefixes[code]
                                                }}{{ t('blog.permalink_placeholder') }}
                                            </span>
                                        </p>
                                        <InputError
                                            :message="
                                                errorFor(code, 'permalink')
                                            "
                                        />
                                    </div>

                                    <div class="grid gap-2">
                                        <Label
                                            :for="`short-description-${code}`"
                                            >{{ t('blog.short_description_label') }}</Label
                                        >
                                        <Textarea
                                            :id="`short-description-${code}`"
                                            v-model="
                                                form.translations[code]
                                                    .short_description as string
                                            "
                                            :rows="2"
                                            :placeholder="t('blog.short_description_placeholder')"
                                        />
                                        <InputError
                                            :message="
                                                errorFor(
                                                    code,
                                                    'short_description',
                                                )
                                            "
                                        />
                                    </div>

                                    <div class="grid gap-2">
                                        <Label>
                                            {{ t('blog.content_label') }}
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
                                        <RichTextEditor
                                            :model-value="
                                                form.translations[code].content
                                            "
                                            :placeholder="t('blog.content_placeholder', { code: code.toUpperCase() })"
                                            @update:model-value="
                                                (v) =>
                                                    (form.translations[
                                                        code
                                                    ].content = v)
                                            "
                                            @word-count="
                                                (n) => onWordCount(code, n)
                                            "
                                        />
                                        <p
                                            class="text-xs text-muted-foreground"
                                        >
                                            {{ t('blog.words_count', { count: wordCounts[code] ?? 0 }) }}
                                            <span v-if="!readingTimeTouched">
                                                {{ t('blog.reading_time_estimate', { minutes: form.reading_time }) }}
                                            </span>
                                        </p>
                                        <InputError
                                            :message="errorFor(code, 'content')"
                                        />
                                    </div>
                                </div>
                            </template>
                        </LocaleTabs>
                    </CardContent>
                </Card>
                <Card>
                    <CardHeader>
                        <CardTitle>{{ t('blog.seo_title') }}</CardTitle>
                    </CardHeader>
                    <CardContent>
                        <div class="mb-4 inline-flex rounded-md border border-input p-0.5">
                            <button
                                v-for="lang in languages"
                                :key="lang.code"
                                type="button"
                                class="inline-flex items-center gap-2 rounded px-3 py-1 text-xs font-semibold uppercase tracking-wider transition-colors"
                                :class="seoActiveLocale === lang.code
                                    ? 'bg-[var(--orange)] text-white'
                                    : 'text-muted-foreground hover:text-foreground'"
                                @click="seoActiveLocale = lang.code"
                            >
                                <FlagImage :code="lang.flag ?? lang.code" size="xs" />
                                {{ lang.code }}
                            </button>
                        </div>

                        <template v-for="lang in languages" :key="`seo-${lang.code}`">
                            <div v-if="seoActiveLocale === lang.code">
                                <SeoMetaFields
                                    :meta-title="form.translations[lang.code].meta_title ?? ''"
                                    :meta-description="form.translations[lang.code].meta_description ?? ''"
                                    :schema="form.translations[lang.code].schema ?? ''"
                                        :meta-image="form.translations[lang.code].meta_image ?? ''"
                                        :locale="lang.code"
                                        :errors="{
                                            meta_title: errorFor(lang.code, 'meta_title'),
                                            meta_description: errorFor(lang.code, 'meta_description'),
                                            schema: errorFor(lang.code, 'schema'),
                                            meta_image: errorFor(lang.code, 'meta_image'),
                                        }"
                                        @update:meta-title="(v) => (form.translations[lang.code].meta_title = v)"
                                        @update:meta-description="(v) => (form.translations[lang.code].meta_description = v)"
                                        @update:schema="(v) => (form.translations[lang.code].schema = v)"
                                        @update:meta-image="(v) => (form.translations[lang.code].meta_image = v)"
                                    />
                            </div>
                        </template>
                    </CardContent>
                </Card>
            </div>

            <!-- RIGHT: publish, blog status, blog image, settings, stats -->
            <div class="space-y-6">
                <Card>
                    <CardHeader>
                        <CardTitle>{{ t('blog.publish_title') }}</CardTitle>
                    </CardHeader>
                    <CardContent>
                        <div class="flex items-center gap-2">
                            <Button
                                type="submit"
                                :disabled="form.processing"
                                class="flex-1"
                            >
                                <Save class="size-4" />
                                {{ t('blog.save_exit') }}
                            </Button>
                            <Button
                                v-if="isEdit"
                                type="button"
                                variant="secondary"
                                :disabled="form.processing"
                                class="flex-1"
                                @click="saveAndStay"
                            >
                                <Save class="size-4" />
                                {{ t('common.save') }}
                            </Button>
                            <Button
                                as-child
                                type="button"
                                variant="outline"
                                class="flex-1"
                            >
                                <Link href="/admin/blog/posts">
                                    <X class="size-4" />
                                    {{ t('blog.cancel') }}
                                </Link>
                            </Button>
                        </div>
                        <p
                            v-if="form.progress"
                            class="mt-3 text-xs text-muted-foreground"
                        >
                            {{ t('blog.uploading', { percent: form.progress.percentage ?? 0 }) }}
                        </p>
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader>
                        <CardTitle>
                            {{ t('blog.blog_status') }}
                            <span class="text-destructive">*</span>
                        </CardTitle>
                    </CardHeader>
                    <CardContent>
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
                        <InputError
                            class="mt-2"
                            :message="form.errors.status"
                        />
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader>
                        <CardTitle>{{ t('blog.blog_image') }}</CardTitle>
                        <CardDescription>
                            {{ t('blog.image_hint') }}
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
                                {{ imagePreview ? t('blog.image_replace') : t('blog.image_upload') }}
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
                                {{ t('blog.image_remove') }}
                            </Button>
                        </div>
                        <p class="text-xs text-muted-foreground">
                            {{ t('blog.image_picker_hint') }}
                        </p>
                        <InputError :message="form.errors.image" />
                        <InputError :message="form.errors.image_path" />
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader>
                        <CardTitle>{{ t('blog.settings_title') }}</CardTitle>
                    </CardHeader>
                    <CardContent class="space-y-4">
                        <div class="grid gap-2">
                            <Label>{{ t('blog.categories_label') }}</Label>
                            <MultiSelect
                                :model-value="form.category_ids"
                                :options="categoryOptions"
                                :placeholder="t('blog.categories_placeholder')"
                                :search-placeholder="t('blog.categories_search')"
                                :empty-text="t('blog.categories_empty')"
                                @update:model-value="
                                    (v) => (form.category_ids = v)
                                "
                            />
                            <InputError :message="form.errors.category_ids" />
                        </div>

                        <div class="grid gap-2">
                            <Label>{{ t('blog.tags_label') }}</Label>
                            <MultiSelect
                                :model-value="form.tag_ids"
                                :options="tagOptions"
                                :placeholder="t('blog.tags_placeholder')"
                                :search-placeholder="t('blog.tags_search')"
                                :empty-text="t('blog.tags_empty')"
                                @update:model-value="(v) => (form.tag_ids = v)"
                            />
                            <InputError :message="form.errors.tag_ids" />
                        </div>

                        <div
                            class="flex items-center justify-between rounded-md border p-3"
                        >
                            <Label
                                for="is_sticky"
                                class="cursor-pointer font-medium"
                                >{{ t('blog.sticky_label') }}</Label
                            >
                            <Switch
                                id="is_sticky"
                                :model-value="form.is_sticky"
                                @update:model-value="
                                    (v) => (form.is_sticky = !!v)
                                "
                            />
                        </div>

                        <div
                            class="flex items-center justify-between rounded-md border p-3"
                        >
                            <Label
                                for="is_featured"
                                class="cursor-pointer font-medium"
                                >{{ t('blog.featured_label') }}</Label
                            >
                            <Switch
                                id="is_featured"
                                :model-value="form.is_featured"
                                @update:model-value="
                                    (v) => (form.is_featured = !!v)
                                "
                            />
                        </div>

                        <div class="grid gap-2">
                            <Label for="reading_time">
                                {{ t('blog.reading_time_label') }}
                                <span
                                    v-if="!readingTimeTouched"
                                    class="ml-1 text-[10px] font-normal tracking-wide text-muted-foreground uppercase"
                                    >{{ t('blog.auto_label') }}</span
                                >
                            </Label>
                            <Input
                                id="reading_time"
                                v-model.number="form.reading_time"
                                type="number"
                                min="0"
                                @input="onReadingTimeInput"
                            />
                            <p
                                v-if="!readingTimeTouched"
                                class="text-xs text-muted-foreground"
                            >
                                {{ t('blog.reading_time_hint', { words: totalWords }) }}
                            </p>
                            <InputError :message="form.errors.reading_time" />
                        </div>

                        <div class="grid gap-2">
                            <Label for="sort_order">{{ t('blog.sort_order_label') }}</Label>
                            <Input
                                id="sort_order"
                                v-model.number="form.sort_order"
                                type="number"
                                min="1"
                            />
                            <p class="text-xs text-muted-foreground">
                                {{ t('blog.sort_order_hint') }}
                            </p>
                            <InputError :message="form.errors.sort_order" />
                        </div>
                    </CardContent>
                </Card>

                <Card v-if="isEdit">
                    <CardHeader>
                        <CardTitle>{{ t('blog.stats_title') }}</CardTitle>
                    </CardHeader>
                    <CardContent class="space-y-2 text-sm">
                        <div class="flex justify-between">
                            <span class="text-muted-foreground">{{ t('blog.views_label') }}</span>
                            <span class="font-medium">{{
                                post?.view_count?.toLocaleString() ?? 0
                            }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-muted-foreground">{{ t('blog.author_label') }}</span>
                            <span class="font-medium">{{
                                post?.user_name ?? '—'
                            }}</span>
                        </div>
                    </CardContent>
                </Card>
            </div>
        </form>

        <MediaPicker v-model:open="pickerOpen" @pick="onMediaPicked" />
    </div>
</template>
