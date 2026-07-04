<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ArrowLeft, Save } from 'lucide-vue-next';
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
import { getIcon, iconOptions } from '@/lib/iconMap';
import { slugify } from '@/lib/slug';

const t = useT();

type Translation = {
    name: string;
    permalink: string;
    short_description: string | null;
};

/**
 * Note: parent-level rows never expose `parent_id` — the controller forces
 * it to null on save. If a category is later demoted to a child from the
 * main Categories admin, it stops appearing in this page.
 */
type Category = {
    id: number;
    icon: string | null;
    status: string;
    is_featured: boolean;
    is_popular: boolean;
    sort_order: number;
    children_count?: number;
    translations: Record<string, Translation>;
};

type Language = {
    code: string;
    name: string;
    native_name: string;
    flag: string | null;
    is_default: boolean;
};

const props = defineProps<{
    category: Category | null;
    languages: Language[];
    nextSortOrder: number;
}>();

const isEdit = computed(() => props.category !== null);

setBreadcrumbs(() => [
    { title: t('sidebar.dashboard'), href: '/dashboard' },
    { title: t('sidebar.service_parent_categories'), href: '/admin/services/parent-categories' },
    {
        title: isEdit.value
            ? t('service_parent_categories.edit_title')
            : t('service_parent_categories.create_title'),
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
    icon: props.category?.icon ?? '',
    status: props.category?.status ?? 'published',
    is_featured: props.category?.is_featured ?? false,
    is_popular: props.category?.is_popular ?? false,
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
        form.put(`/admin/services/parent-categories/${props.category.id}`);
    } else {
        form.post('/admin/services/parent-categories');
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
                ? t('service_parent_categories.edit_title')
                : t('service_parent_categories.create_title')
        "
    />

    <div class="flex flex-col gap-6 p-4">
        <div class="flex items-start justify-between gap-4">
            <div class="flex items-center gap-3">
                <Button as-child variant="ghost" size="sm">
                    <Link href="/admin/services/parent-categories">
                        <ArrowLeft class="size-4" />
                    </Link>
                </Button>
                <Heading
                    :title="
                        isEdit
                            ? t('service_parent_categories.edit_title')
                            : t('service_parent_categories.create_title')
                    "
                    :description="
                        isEdit
                            ? t('service_parent_categories.edit_description')
                            : t('service_parent_categories.create_description')
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
                            {{ t('service_parent_categories.settings_description') }}
                        </CardDescription>
                    </CardHeader>
                    <CardContent class="space-y-5">
                        <div
                            v-if="isEdit && (category?.children_count ?? 0) > 0"
                            class="rounded-md border border-sidebar-border bg-muted/40 p-3 text-xs text-muted-foreground"
                        >
                            {{ t('service_parent_categories.children_count_hint', { count: category?.children_count ?? 0 }) }}
                        </div>

                        <div class="grid gap-2">
                            <Label for="icon">{{ t('service_categories.field_icon') }}</Label>
                            <Select
                                :model-value="form.icon || 'none'"
                                @update:model-value="
                                    (v) =>
                                        (form.icon =
                                            typeof v === 'string' && v !== 'none'
                                                ? v
                                                : '')
                                "
                            >
                                <SelectTrigger id="icon">
                                    <div class="flex items-center gap-2">
                                        <component
                                            v-if="getIcon(form.icon)"
                                            :is="getIcon(form.icon)"
                                            class="size-4"
                                        />
                                        <SelectValue :placeholder="t('service_categories.field_icon_placeholder')" />
                                    </div>
                                </SelectTrigger>
                                <SelectContent>
                                    <SelectItem value="none">
                                        {{ t('service_categories.field_icon_none') }}
                                    </SelectItem>
                                    <SelectItem
                                        v-for="opt in iconOptions"
                                        :key="opt.value"
                                        :value="opt.value"
                                    >
                                        <div class="flex items-center gap-2">
                                            <component :is="getIcon(opt.value)" class="size-4" />
                                            <span>{{ opt.label }}</span>
                                        </div>
                                    </SelectItem>
                                </SelectContent>
                            </Select>
                            <InputError :message="form.errors.icon" />
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
                        <Link href="/admin/services/parent-categories">
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
    </div>
</template>
