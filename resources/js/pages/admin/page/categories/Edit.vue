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
import { setBreadcrumbs } from '@/composables/common/useBreadcrumbs';
import { useT } from '@/composables/useT';
import { slugify } from '@/lib/slug';

const t = useT();

type Translation = { title: string; permalink: string };

type Category = {
    id: number;
    parent_id: number | null;
    status: string;
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

type ParentOption = { id: number; title: string };

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
    { title: t('sidebar.page_categories'), href: '/admin/pages/categories' },
    {
        title: isEdit.value ? t('pages.category_edit_title') : t('pages.category_create_title'),
        href: '#',
    },
]);

const initialTranslations: Record<string, Translation> = Object.fromEntries(
    props.languages.map((lang) => [
        lang.code,
        props.category?.translations[lang.code] ?? {
            title: '',
            permalink: '',
        },
    ]),
);

const form = useForm({
    parent_id: props.category?.parent_id ?? null,
    status: props.category?.status ?? 'published',
    is_default: props.category?.is_default ?? false,
    sort_order: props.category?.sort_order ?? props.nextSortOrder,
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

function submit(): void {
    if (isEdit.value) {
        form.transform((data) => ({ ...data, _method: 'put' })).post(
            `/admin/pages/categories/${props.category!.id}`,
        );
    } else {
        form.post('/admin/pages/categories');
    }
}

const errorFor = (code: string, field: keyof Translation) =>
    form.errors[`translations.${code}.${field}` as keyof typeof form.errors] as
        | string
        | undefined;
</script>

<template>
    <Head :title="isEdit ? t('pages.category_edit_title') : t('pages.category_new')" />

    <div class="flex flex-col gap-6 p-4">
        <div class="flex items-center gap-3">
            <Button as-child variant="ghost" size="sm">
                <Link href="/admin/pages/categories">
                    <ArrowLeft class="size-4" />
                </Link>
            </Button>
            <Heading
                :title="isEdit ? t('pages.category_edit_title') : t('pages.category_create_title')"
                :description="
                    isEdit
                        ? t('pages.category_edit_description')
                        : t('pages.category_create_description')
                "
            />
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
                                            {{ t('pages.category_title_label') }}
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
                                            :placeholder="t('pages.category_title_placeholder')"
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
            </div>

            <!-- RIGHT: settings -->
            <div class="space-y-6">
                <Card>
                    <CardHeader>
                        <CardTitle>{{ t('pages.category_publish') }}</CardTitle>
                    </CardHeader>
                    <CardContent>
                        <div class="flex items-center gap-2">
                            <Button
                                type="submit"
                                :disabled="form.processing"
                                class="flex-1"
                            >
                                <Save class="size-4" />
                                {{ t('pages.category_save') }}
                            </Button>
                            <Button
                                as-child
                                type="button"
                                variant="outline"
                                class="flex-1"
                            >
                                <Link href="/admin/pages/categories">
                                    <X class="size-4" />
                                    {{ t('pages.category_cancel') }}
                                </Link>
                            </Button>
                        </div>
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader>
                        <CardTitle>{{ t('pages.category_settings_title') }}</CardTitle>
                    </CardHeader>
                    <CardContent class="space-y-4">
                        <div class="grid gap-2">
                            <Label for="parent">{{ t('pages.category_parent_label') }}</Label>
                            <Select
                                :model-value="
                                    form.parent_id
                                        ? String(form.parent_id)
                                        : 'none'
                                "
                                @update:model-value="
                                    (v) =>
                                        (form.parent_id =
                                            v === 'none'
                                                ? null
                                                : Number(v as string))
                                "
                            >
                                <SelectTrigger id="parent" class="w-full">
                                    <SelectValue :placeholder="t('pages.category_parent_placeholder')" />
                                </SelectTrigger>
                                <SelectContent>
                                    <SelectItem value="none">{{ t('pages.category_parent_top') }}</SelectItem>
                                    <SelectItem
                                        v-for="opt in parentOptions"
                                        :key="opt.id"
                                        :value="String(opt.id)"
                                    >
                                        {{ opt.title }}
                                    </SelectItem>
                                </SelectContent>
                            </Select>
                            <InputError :message="form.errors.parent_id" />
                        </div>

                        <div class="flex items-center justify-between">
                            <Label for="is_default" class="cursor-pointer">
                                {{ t('pages.category_default_label') }}
                            </Label>
                            <Switch
                                id="is_default"
                                :model-value="form.is_default"
                                @update:model-value="
                                    (v) => (form.is_default = v)
                                "
                            />
                        </div>

                        <div class="grid gap-2">
                            <Label for="status">
                                {{ t('common.status') }}
                                <span class="text-destructive">*</span>
                            </Label>
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
                        </div>

                        <div class="grid gap-2">
                            <Label for="sort_order">{{ t('pages.category_sort_order') }}</Label>
                            <Input
                                id="sort_order"
                                v-model.number="form.sort_order"
                                type="number"
                                min="1"
                            />
                            <InputError :message="form.errors.sort_order" />
                        </div>
                    </CardContent>
                </Card>
            </div>
        </form>
    </div>
</template>
