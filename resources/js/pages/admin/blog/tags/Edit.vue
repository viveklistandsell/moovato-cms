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
import { Textarea } from '@/components/ui/textarea';
import { setBreadcrumbs } from '@/composables/common/useBreadcrumbs';
import { slugify } from '@/lib/slug';

type Translation = {
    name: string;
    permalink: string;
    short_description: string | null;
};

type Tag = {
    id: number;
    status: string;
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

const props = defineProps<{
    tag: Tag | null;
    languages: Language[];
    urlPrefixes: Record<string, string>;
    nextSortOrder: number;
}>();

const isEdit = computed(() => props.tag !== null);

setBreadcrumbs(() => [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Blog', href: '/admin/blog/tags' },
    { title: 'Tags', href: '/admin/blog/tags' },
    {
        title: isEdit.value ? 'Edit tag' : 'Create a new tag',
        href: '#',
    },
]);

const initialTranslations: Record<string, Translation> = Object.fromEntries(
    props.languages.map((lang) => [
        lang.code,
        props.tag?.translations[lang.code] ?? {
            name: '',
            permalink: '',
            short_description: '',
        },
    ]),
);

const form = useForm({
    status: props.tag?.status ?? 'published',
    sort_order: props.tag?.sort_order ?? props.nextSortOrder,
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

function submit(): void {
    if (isEdit.value) {
        form.put(`/admin/blog/tags/${props.tag!.id}`);
    } else {
        form.post('/admin/blog/tags');
    }
}

const errorFor = (code: string, field: keyof Translation) =>
    form.errors[`translations.${code}.${field}` as keyof typeof form.errors] as
        | string
        | undefined;
</script>

<template>
    <Head :title="isEdit ? 'Edit tag' : 'New tag'" />

    <div class="flex flex-col gap-6 p-4">
        <div class="flex items-start justify-between gap-4">
            <div class="flex items-center gap-3">
                <Button as-child variant="ghost" size="sm">
                    <Link href="/admin/blog/tags">
                        <ArrowLeft class="size-4" />
                    </Link>
                </Button>
                <Heading
                    :title="isEdit ? 'Edit tag' : 'Create a new tag'"
                    :description="
                        isEdit
                            ? 'Update translations and visibility.'
                            : 'German is required. English is optional — fill it now or later.'
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
                                            Name
                                            <span
                                                v-if="
                                                    languages.find(
                                                        (l) =>
                                                            l.code === code,
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
                                            placeholder="Name"
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
                                                        (l) =>
                                                            l.code === code,
                                                    )?.is_default
                                                "
                                                class="text-destructive"
                                                >*</span
                                            >
                                        </Label>
                                        <div
                                            class="flex w-full items-stretch"
                                        >
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
                                                class="placeholder:text-muted-foreground focus-visible:border-ring focus-visible:ring-ring/50 dark:bg-input/30 h-9 w-full min-w-0 rounded-r-md border border-input bg-transparent px-3 py-1 text-base shadow-xs transition-[color,box-shadow] outline-none focus-visible:ring-[3px] md:text-sm"
                                                @input="
                                                    onPermalinkInput(code)
                                                "
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
                                            >Description</Label
                                        >
                                        <Textarea
                                            :id="`short-description-${code}`"
                                            v-model="
                                                form.translations[code]
                                                    .short_description as string
                                            "
                                            :rows="4"
                                            placeholder="Short description"
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
                        <CardTitle>Settings</CardTitle>
                        <CardDescription>
                            Applies to the tag itself, regardless of language.
                        </CardDescription>
                    </CardHeader>
                    <CardContent class="space-y-5">
                        <div class="grid gap-2">
                            <Label for="sort_order">Sort order</Label>
                            <Input
                                id="sort_order"
                                v-model.number="form.sort_order"
                                type="number"
                                min="1"
                            />
                            <p class="text-xs text-muted-foreground">
                                Auto-filled with next available position
                                ({{ nextSortOrder }}). Set to
                                <span class="font-medium">1</span> to make
                                this top priority — others shift down.
                            </p>
                            <InputError :message="form.errors.sort_order" />
                        </div>
                    </CardContent>
                </Card>
            </div>

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
                                <Link href="/admin/blog/tags">
                                    <X class="size-4" />
                                    Cancel
                                </Link>
                            </Button>
                        </div>
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader>
                        <CardTitle>
                            Status
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
            </div>
        </form>
    </div>
</template>
