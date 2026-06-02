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
import { localizedUrl } from '@/lib/localizedUrl';
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
import { setBreadcrumbs } from '@/composables/common/useBreadcrumbs';
import { slugify } from '@/lib/slug';

type Translation = {
    name: string;
    permalink: string;
    short_description: string | null;
    content: string | null;
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
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Blog', href: '/admin/blog/posts' },
    { title: 'Posts', href: '/admin/blog/posts' },
    {
        title: isEdit.value ? 'Edit post' : 'Create a new post',
        href: '#',
    },
]);

const initialTranslations: Record<string, Translation> = Object.fromEntries(
    props.languages.map((lang) => [
        lang.code,
        props.post?.translations[lang.code] ?? {
            name: '',
            permalink: '',
            short_description: '',
            content: '',
        },
    ]),
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

const permalinkTouched = ref<Record<string, boolean>>(
    Object.fromEntries(props.languages.map((lang) => [lang.code, false])),
);

watch(
    () => form.translations,
    (translations) => {
        for (const code of Object.keys(translations)) {
            const t = translations[code];
            if (!permalinkTouched.value[code] && t.name.length > 0) {
                t.permalink = slugify(t.name);
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

const previewUrl = computed<string | null>(() => {
    if (!props.post) return null;
    const defaultLocale =
        props.languages.find((l) => l.is_default)?.code ?? 'de';
    const slug =
        props.post.translations[defaultLocale]?.permalink ??
        Object.values(props.post.translations)[0]?.permalink ??
        null;
    return slug ? localizedUrl(defaultLocale, `/blog/${slug}`) : null;
});

const errorFor = (code: string, field: keyof Translation) =>
    form.errors[`translations.${code}.${field}` as keyof typeof form.errors] as
        | string
        | undefined;
</script>

<template>
    <Head :title="isEdit ? 'Edit post' : 'New post'" />

    <div class="flex flex-col gap-6 p-4">
        <div class="flex items-start justify-between gap-4">
            <div class="flex items-center gap-3">
                <Button as-child variant="ghost" size="sm">
                    <Link href="/admin/blog/posts">
                        <ArrowLeft class="size-4" />
                    </Link>
                </Button>
                <Heading
                    :title="isEdit ? 'Edit post' : 'Create a new post'"
                    :description="
                        isEdit
                            ? 'Update content, image, and visibility.'
                            : 'German is required. English is optional — fill it now or later.'
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
                        <CardTitle>Translations</CardTitle>
                        <CardDescription>
                            Switch between German and English. Only German is
                            required.
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
                                            Title
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
                                            placeholder="Post title"
                                        />
                                        <InputError
                                            :message="errorFor(code, 'name')"
                                        />
                                    </div>

                                    <div class="grid gap-2">
                                        <Label :for="`permalink-${code}`">
                                            Permalink
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
                                                placeholder="your-permalink"
                                                class="h-9 w-full min-w-0 rounded-r-md border border-input bg-transparent px-3 py-1 text-base shadow-xs transition-[color,box-shadow] outline-none placeholder:text-muted-foreground focus-visible:border-ring focus-visible:ring-[3px] focus-visible:ring-ring/50 md:text-sm dark:bg-input/30"
                                                @input="onPermalinkInput(code)"
                                            />
                                        </div>
                                        <p
                                            class="text-xs text-muted-foreground"
                                        >
                                            Preview:
                                            <span class="text-primary">
                                                {{ urlPrefixes[code]
                                                }}{{
                                                    form.translations[code]
                                                        .permalink ||
                                                    'your-permalink'
                                                }}
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
                                            >Blog Short description</Label
                                        >
                                        <Textarea
                                            :id="`short-description-${code}`"
                                            v-model="
                                                form.translations[code]
                                                    .short_description as string
                                            "
                                            :rows="2"
                                            placeholder="Short summary shown in lists for this blog"
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
                                            Blog Content
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
                                            :placeholder="`Blog Content (${code.toUpperCase()})`"
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
                                            {{ wordCounts[code] ?? 0 }}
                                            words
                                            <span v-if="!readingTimeTouched">
                                                · est.
                                                {{ form.reading_time }} min read
                                                (auto)
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
            </div>

            <!-- RIGHT: publish, blog status, blog image, settings, stats -->
            <div class="space-y-6">
                <Card>
                    <CardHeader>
                        <CardTitle>Publish</CardTitle>
                    </CardHeader>
                    <CardContent>
                        <div class="flex items-center gap-2">
                            <Button
                                type="submit"
                                :disabled="form.processing"
                                class="flex-1"
                            >
                                <Save class="size-4" />
                                Save
                            </Button>
                            <Button
                                as-child
                                type="button"
                                variant="outline"
                                class="flex-1"
                            >
                                <Link href="/admin/blog/posts">
                                    <X class="size-4" />
                                    Cancel
                                </Link>
                            </Button>
                        </div>
                        <a
                            v-if="previewUrl"
                            :href="previewUrl"
                            target="_blank"
                            rel="noopener"
                            class="mt-3 inline-flex items-center gap-1.5 text-xs font-medium text-primary hover:underline"
                        >
                            <ExternalLink class="size-3.5" />
                            Preview on site
                        </a>
                        <p
                            v-if="form.progress"
                            class="mt-3 text-xs text-muted-foreground"
                        >
                            Uploading: {{ form.progress.percentage }}%
                        </p>
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader>
                        <CardTitle>
                            Blog status
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
                                <SelectItem value="published"
                                    >Published</SelectItem
                                >
                                <SelectItem value="draft">Draft</SelectItem>
                                <SelectItem value="inactive"
                                    >Inactive</SelectItem
                                >
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
                        <CardTitle>Blog image</CardTitle>
                        <CardDescription>
                            JPEG / PNG / WebP, up to 4 MB.
                        </CardDescription>
                    </CardHeader>
                    <CardContent class="space-y-3">
                        <div
                            class="flex aspect-video w-full items-center justify-center overflow-hidden rounded-md border border-dashed bg-muted"
                        >
                            <img
                                v-if="imagePreview"
                                :src="imagePreview"
                                alt="Preview"
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
                                {{ imagePreview ? 'Replace' : 'Upload' }}
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
                                Remove
                            </Button>
                        </div>
                        <p class="text-xs text-muted-foreground">
                            Opens the media library — pick an existing image or
                            upload a new one there.
                        </p>
                        <InputError :message="form.errors.image" />
                        <InputError :message="form.errors.image_path" />
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader>
                        <CardTitle>Settings</CardTitle>
                    </CardHeader>
                    <CardContent class="space-y-4">
                        <div class="grid gap-2">
                            <Label>Blog categories</Label>
                            <MultiSelect
                                :model-value="form.category_ids"
                                :options="categoryOptions"
                                placeholder="Select categories…"
                                search-placeholder="Search categories"
                                empty-text="No categories"
                                @update:model-value="
                                    (v) => (form.category_ids = v)
                                "
                            />
                            <InputError :message="form.errors.category_ids" />
                        </div>

                        <div class="grid gap-2">
                            <Label>Blog tags</Label>
                            <MultiSelect
                                :model-value="form.tag_ids"
                                :options="tagOptions"
                                placeholder="Select tags…"
                                search-placeholder="Search tags"
                                empty-text="No tags"
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
                                >Sticky?</Label
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
                                >Featured?</Label
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
                                Reading time (min)
                                <span
                                    v-if="!readingTimeTouched"
                                    class="ml-1 text-[10px] font-normal tracking-wide text-muted-foreground uppercase"
                                    >auto</span
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
                                Auto-calculated from content ({{ totalWords }}
                                words ÷ 200 wpm). Edit to override.
                            </p>
                            <InputError :message="form.errors.reading_time" />
                        </div>

                        <div class="grid gap-2">
                            <Label for="sort_order">Sort order</Label>
                            <Input
                                id="sort_order"
                                v-model.number="form.sort_order"
                                type="number"
                                min="1"
                            />
                            <p class="text-xs text-muted-foreground">
                                Set to 1 to put on top.
                            </p>
                            <InputError :message="form.errors.sort_order" />
                        </div>
                    </CardContent>
                </Card>

                <Card v-if="isEdit">
                    <CardHeader>
                        <CardTitle>Stats</CardTitle>
                    </CardHeader>
                    <CardContent class="space-y-2 text-sm">
                        <div class="flex justify-between">
                            <span class="text-muted-foreground">Views</span>
                            <span class="font-medium">{{
                                post?.view_count?.toLocaleString() ?? 0
                            }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-muted-foreground">Author</span>
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
