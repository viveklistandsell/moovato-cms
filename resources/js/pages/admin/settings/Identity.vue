<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import { Save } from 'lucide-vue-next';
import { computed, ref } from 'vue';
import Heading from '@/components/Heading.vue';
import InputError from '@/components/InputError.vue';
import LocaleTabs, { type LocaleOption } from '@/components/common/LocaleTabs.vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';

type TranslationRow = { site_tagline: string | null };

type Settings = {
    site_name: string | null;
    timezone: string | null;
    date_format: string | null;
    translations: Record<string, TranslationRow>;
};

type Timezones = { common: string[]; all: string[] };

const props = defineProps<{
    settings: Settings;
    languages: LocaleOption[];
    timezones: Timezones;
}>();

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Dashboard', href: '/dashboard' },
            { title: 'Settings', href: '/admin/settings/identity' },
            { title: 'Site Identity', href: '/admin/settings/identity' },
        ],
    },
});

const defaultLang = computed<string>(
    () =>
        props.languages.find((l) => l.is_default)?.code ??
        props.languages[0]?.code ??
        'de',
);
const activeLang = ref<string>(defaultLang.value);

const form = useForm({
    site_name: props.settings.site_name ?? '',
    timezone: props.settings.timezone ?? 'Europe/Berlin',
    date_format: props.settings.date_format ?? 'd.m.Y',
    translations: props.languages.map((lang) => ({
        lang: lang.code,
        site_tagline: props.settings.translations[lang.code]?.site_tagline ?? '',
    })),
});

function submit(): void {
    form.put('/admin/settings/identity', { preserveScroll: true });
}

function taglineErrorFor(lang: string): string | undefined {
    const idx = form.translations.findIndex((t) => t.lang === lang);
    if (idx < 0) return undefined;
    const errs = form.errors as Record<string, string | undefined>;
    return errs[`translations.${idx}.site_tagline`];
}

// Fold the common-timezone list to the top of the dropdown without
// duplicating its entries in the long IANA list.
const timezoneOptions = computed<string[]>(() => {
    const common = props.timezones?.common ?? [];
    const all = props.timezones?.all ?? [];
    const set = new Set(common);
    return [...common, ...all.filter((tz) => !set.has(tz))];
});

const dateFormatPresets = [
    { value: 'd.m.Y', label: 'd.m.Y · 03.06.2026' },
    { value: 'Y-m-d', label: 'Y-m-d · 2026-06-03' },
    { value: 'M j, Y', label: 'M j, Y · Jun 3, 2026' },
    { value: 'j M Y', label: 'j M Y · 3 Jun 2026' },
];

const dateFormatPreview = computed<string>(() => {
    const fmt = form.date_format || 'd.m.Y';
    const now = new Date();
    const dd = String(now.getDate()).padStart(2, '0');
    const mm = String(now.getMonth() + 1).padStart(2, '0');
    const yyyy = String(now.getFullYear());
    const monthShort = now.toLocaleDateString('en-US', { month: 'short' });
    return fmt
        .replace(/Y/g, yyyy)
        .replace(/d/g, dd)
        .replace(/m/g, mm)
        .replace(/M/g, monthShort)
        .replace(/j/g, String(now.getDate()));
});
</script>

<template>
    <Head title="Site Identity" />

    <div class="flex flex-col gap-6 p-4">
        <Heading
            title="Site Identity"
            description="Site name, tagline, timezone and date format — the bits that brand the site."
        />

        <form class="flex flex-col gap-4" @submit.prevent="submit">
            <Card>
                <CardHeader>
                    <CardTitle>Identity</CardTitle>
                </CardHeader>
                <CardContent class="grid gap-3 sm:grid-cols-2">
                    <div class="space-y-1">
                        <Label for="site_name">Site name</Label>
                        <Input id="site_name" v-model="form.site_name" />
                        <p class="text-xs text-muted-foreground">
                            Shown in &lt;title&gt;, emails and OG tags.
                        </p>
                        <InputError :message="form.errors.site_name" />
                    </div>
                    <div class="space-y-1">
                        <Label>Default locale</Label>
                        <Input
                            :model-value="defaultLang"
                            disabled
                            class="cursor-not-allowed"
                        />
                        <p class="text-xs text-muted-foreground">
                            Change via <strong>Languages</strong>.
                        </p>
                    </div>
                    <div class="space-y-1">
                        <Label for="timezone">Timezone</Label>
                        <Select
                            :model-value="form.timezone"
                            @update:model-value="(v) => (form.timezone = v as string)"
                        >
                            <SelectTrigger id="timezone">
                                <SelectValue placeholder="Pick a timezone" />
                            </SelectTrigger>
                            <SelectContent class="max-h-72">
                                <SelectItem
                                    v-for="tz in timezoneOptions"
                                    :key="tz"
                                    :value="tz"
                                >
                                    {{ tz }}
                                </SelectItem>
                            </SelectContent>
                        </Select>
                        <InputError :message="form.errors.timezone" />
                    </div>
                    <div class="space-y-1">
                        <Label for="date_format">Date format</Label>
                        <Select
                            :model-value="form.date_format"
                            @update:model-value="(v) => (form.date_format = v as string)"
                        >
                            <SelectTrigger id="date_format">
                                <SelectValue placeholder="Pick a format" />
                            </SelectTrigger>
                            <SelectContent>
                                <SelectItem
                                    v-for="opt in dateFormatPresets"
                                    :key="opt.value"
                                    :value="opt.value"
                                >
                                    {{ opt.label }}
                                </SelectItem>
                            </SelectContent>
                        </Select>
                        <p class="text-xs text-muted-foreground">
                            Preview today: <strong>{{ dateFormatPreview }}</strong>
                        </p>
                        <InputError :message="form.errors.date_format" />
                    </div>
                </CardContent>
            </Card>

            <Card>
                <CardHeader>
                    <CardTitle>Tagline (per language)</CardTitle>
                </CardHeader>
                <CardContent>
                    <LocaleTabs
                        :model-value="activeLang"
                        :languages="languages"
                        @update:model-value="(v) => (activeLang = v)"
                    >
                        <template #default="{ code }">
                            <div class="space-y-1 pt-3">
                                <Label :for="`tagline_${code}`">
                                    Tagline ({{ code }})
                                </Label>
                                <Input
                                    :id="`tagline_${code}`"
                                    v-model="form.translations[languages.findIndex((l) => l.code === code)].site_tagline"
                                    placeholder="Short marketing line"
                                />
                                <InputError :message="taglineErrorFor(code)" />
                            </div>
                        </template>
                    </LocaleTabs>
                </CardContent>
            </Card>

            <div class="flex justify-end">
                <Button type="submit" :disabled="form.processing">
                    <Save class="size-4" />
                    Save identity
                </Button>
            </div>
        </form>
    </div>
</template>
