<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ArrowLeft, Save, X } from 'lucide-vue-next';
import { computed, ref, watch } from 'vue';
import Heading from '@/components/Heading.vue';
import InputError from '@/components/InputError.vue';
import LocaleTabs from '@/components/common/LocaleTabs.vue';
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
import { useT } from '@/composables/useT';
import { iconOptions } from '@/lib/iconMap';
import { slugify } from '@/lib/slug';

const t = useT();

type Translation = {
    name: string;
    permalink: string;
    short_description: string | null;
};

type Category = {
    id: number;
    parent_id: number | null;
    icon: string | null;
    status: string;
    is_featured: boolean;
    is_default: boolean;
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
    depth: number;
};

const props = defineProps<{
    category: Category | null;
    languages: Language[];
    parentOptions: ParentOption[];
    urlPrefixes: Record<string, string>;
    nextSortOrder: number;
}>();

const isEdit = computed(() => props.category !== null);

setBreadcrumbs(() => [
    { title: t('sidebar.dashboard'), href: '/dashboard' },
    { title: t('sidebar.blog_categories'), href: '/admin/blog/categories' },
    {
        title: isEdit.value ? t('blog.category_edit_title') : t('blog.category_create_title'),
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

const form = useForm({
    parent_id: props.category?.parent_id ?? null,
    icon: props.category?.icon ?? '',
    status: props.category?.status ?? 'published',
    is_featured: props.category?.is_featured ?? false,
    is_default: props.category?.is_default ?? false,
    sort_order: props.category?.sort_order ?? props.nextSortOrder,
    translations: initialTranslations,
});

const activeLocale = ref(
    props.languages.find((l) => l.is_default)?.code ??
        props.languages[0]?.code ??
        'de',
);

// Always start "not touched" so typing in the name field re-slugifies the
// permalink, even when editing an existing category. The flag flips to true
// the moment the user types directly in the permalink field.
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

function submit(): void {
    if (isEdit.value) {
        form.put(`/admin/blog/categories/${props.category!.id}`);
    } else {
        form.post('/admin/blog/categories');
    }
}

const errorFor = (code: string, field: keyof Translation) =>
    form.errors[`translations.${code}.${field}` as keyof typeof form.errors] as
        | string
        | undefined;
</script>

<template>
    <Head :title="isEdit ? t('blog.category_edit_title') : t('blog.category_create')" />

    <div class="flex flex-col gap-6 p-4">
        <div class="flex items-start justify-between gap-4">
            <div class="flex items-center gap-3">
                <Button as-child variant="ghost" size="sm">
                    <Link href="/admin/blog/categories">
                        <ArrowLeft class="size-4" />
                    </Link>
                </Button>
                <Heading
                    :title="isEdit ? t('blog.category_edit_title') : t('blog.category_create_title')"
                    :description="
                        isEdit
                            ? t('blog.category_edit_description')
                            : t('blog.category_create_description')
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
                                            {{ t('blog.category_name_label') }}
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
                                            :placeholder="t('blog.category_name_placeholder')"
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
                                            <span class="text-primary">
                                                {{ urlPrefixes[code]
                                                }}{{
                                                    form.translations[code]
                                                        .permalink ||
                                                    t('blog.permalink_placeholder')
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
                                            >{{ t('blog.category_description_label') }}</Label
                                        >
                                        <Textarea
                                            :id="`short-description-${code}`"
                                            v-model="
                                                form.translations[code]
                                                    .short_description as string
                                            "
                                            :rows="4"
                                            :placeholder="t('blog.category_description_placeholder')"
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
                                </div>
                            </template>
                        </LocaleTabs>
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader>
                        <CardTitle>{{ t('blog.category_settings_title') }}</CardTitle>
                        <CardDescription>
                            {{ t('blog.category_settings_description') }}
                        </CardDescription>
                    </CardHeader>
                    <CardContent class="space-y-5">
                        <div class="grid gap-2">
                            <Label for="parent">{{ t('blog.category_parent_label') }}</Label>
                            <Select
                                :model-value="
                                    form.parent_id !== null
                                        ? String(form.parent_id)
                                        : 'none'
                                "
                                @update:model-value="
                                    (v) =>
                                        (form.parent_id =
                                            v === 'none' || v === undefined
                                                ? null
                                                : Number(v))
                                "
                            >
                                <SelectTrigger id="parent" class="w-full">
                                    <SelectValue :placeholder="t('blog.category_parent_none')" />
                                </SelectTrigger>
                                <SelectContent>
                                    <SelectItem value="none">{{ t('blog.category_parent_none') }}</SelectItem>
                                    <SelectItem
                                        v-for="opt in parentOptions"
                                        :key="opt.id"
                                        :value="String(opt.id)"
                                    >
                                        {{ opt.label }}
                                    </SelectItem>
                                </SelectContent>
                            </Select>
                            <InputError :message="form.errors.parent_id" />
                        </div>

                        <div
                            class="flex items-center justify-between rounded-md border p-3"
                        >
                            <div>
                                <Label
                                    for="is_default"
                                    class="cursor-pointer font-medium"
                                    >{{ t('blog.category_default_label') }}</Label
                                >
                                <p class="mt-1 text-xs text-muted-foreground">
                                    {{ t('blog.category_default_hint') }}
                                </p>
                            </div>
                            <Switch
                                id="is_default"
                                :model-value="form.is_default"
                                @update:model-value="
                                    (v) => (form.is_default = !!v)
                                "
                            />
                        </div>

                        <div class="grid gap-2">
                            <Label for="icon">{{ t('blog.category_icon_label') }}</Label>
                            <Select
                                :model-value="form.icon || 'none'"
                                @update:model-value="
                                    (v) =>
                                        (form.icon =
                                            v === 'none' ? '' : (v as string))
                                "
                            >
                                <SelectTrigger id="icon" class="w-full">
                                    <SelectValue :placeholder="t('blog.category_icon_placeholder')" />
                                </SelectTrigger>
                                <SelectContent>
                                    <SelectItem value="none">{{ t('blog.category_icon_none') }}</SelectItem>
                                    <SelectItem
                                        v-for="opt in iconOptions"
                                        :key="opt.value"
                                        :value="opt.value"
                                    >
                                        <span class="flex items-center gap-2">
                                            <component
                                                :is="opt.icon"
                                                class="size-4"
                                            />
                                            <span class="font-mono text-xs">{{
                                                opt.label
                                            }}</span>
                                        </span>
                                    </SelectItem>
                                </SelectContent>
                            </Select>
                            <InputError :message="form.errors.icon" />
                        </div>

                        <div
                            class="flex items-center justify-between rounded-md border p-3"
                        >
                            <div>
                                <Label
                                    for="is_featured"
                                    class="cursor-pointer font-medium"
                                    >{{ t('blog.category_featured_label') }}</Label
                                >
                                <p class="mt-1 text-xs text-muted-foreground">
                                    {{ t('blog.category_featured_hint') }}
                                </p>
                            </div>
                            <Switch
                                id="is_featured"
                                :model-value="form.is_featured"
                                @update:model-value="
                                    (v) => (form.is_featured = !!v)
                                "
                            />
                        </div>

                        <div class="grid gap-2">
                            <Label for="sort_order">{{ t('blog.category_sort_order') }}</Label>
                            <Input
                                id="sort_order"
                                v-model.number="form.sort_order"
                                type="number"
                                min="1"
                            />
                            <p class="text-xs text-muted-foreground">
                                {{ t('blog.category_sort_order_hint', { next: nextSortOrder }) }}
                            </p>
                            <InputError :message="form.errors.sort_order" />
                        </div>
                    </CardContent>
                </Card>
            </div>

            <div class="space-y-6">
                <Card>
                    <CardHeader>
                        <CardTitle>{{ t('blog.category_publish_title') }}</CardTitle>
                    </CardHeader>
                    <CardContent>
                        <div class="flex items-center gap-2">
                            <Button
                                type="submit"
                                :disabled="form.processing"
                                class="flex-1"
                            >
                                <Save class="size-4" />
                                {{ t('blog.category_save') }}
                            </Button>
                            <Button
                                as-child
                                type="button"
                                variant="outline"
                                class="flex-1"
                            >
                                <Link href="/admin/blog/categories">
                                    <X class="size-4" />
                                    {{ t('blog.category_cancel') }}
                                </Link>
                            </Button>
                        </div>
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader>
                        <CardTitle>
                            {{ t('common.status') }}
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
            </div>
        </form>
    </div>
</template>
