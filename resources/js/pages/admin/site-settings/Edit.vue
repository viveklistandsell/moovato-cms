<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import { Save } from 'lucide-vue-next';
import { computed, ref } from 'vue';
import Heading from '@/components/Heading.vue';
import InputError from '@/components/InputError.vue';
import LocaleTabs, { type LocaleOption } from '@/components/common/LocaleTabs.vue';
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
import { Textarea } from '@/components/ui/textarea';

type TranslationRow = { about_text: string | null };

type Settings = {
    address: string | null;
    phone: string | null;
    email: string | null;
    whatsapp: string | null;
    facebook_url: string | null;
    twitter_url: string | null;
    linkedin_url: string | null;
    instagram_url: string | null;
    translations: Record<string, TranslationRow>;
};

const props = defineProps<{
    settings: Settings;
    languages: LocaleOption[];
}>();

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Dashboard', href: '/dashboard' },
            { title: 'Site Settings', href: '/admin/site-settings' },
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

function seedTranslations(): Array<{ lang: string; about_text: string }> {
    return props.languages.map((lang) => ({
        lang: lang.code,
        about_text:
            props.settings.translations[lang.code]?.about_text ?? '',
    }));
}

const form = useForm({
    address: props.settings.address ?? '',
    phone: props.settings.phone ?? '',
    email: props.settings.email ?? '',
    whatsapp: props.settings.whatsapp ?? '',
    facebook_url: props.settings.facebook_url ?? '',
    twitter_url: props.settings.twitter_url ?? '',
    linkedin_url: props.settings.linkedin_url ?? '',
    instagram_url: props.settings.instagram_url ?? '',
    translations: seedTranslations(),
});

function submit(): void {
    form.put('/admin/site-settings', { preserveScroll: true });
}

function errorForTranslation(lang: string): string | undefined {
    const idx = form.translations.findIndex((t) => t.lang === lang);
    if (idx < 0) return undefined;
    const errors = form.errors as Record<string, string | undefined>;
    return errors[`translations.${idx}.about_text`];
}
</script>

<template>
    <Head title="Site Settings" />

    <div class="flex flex-col gap-6 p-4">
        <Heading
            title="Site Settings"
            description="Powers the public footer — About text per language, contact details, WhatsApp number, and social links."
        />

        <form class="flex flex-col gap-4" @submit.prevent="submit">
            <Card>
                <CardHeader>
                    <CardTitle>About</CardTitle>
                    <CardDescription>
                        One paragraph per language. The public footer shows
                        the one matching the visitor's locale.
                    </CardDescription>
                </CardHeader>
                <CardContent>
                    <LocaleTabs
                        :model-value="activeLang"
                        :languages="languages"
                        @update:model-value="(v) => (activeLang = v)"
                    >
                        <template #default="{ code }">
                            <div class="space-y-1 pt-3">
                                <Label :for="`about_text_${code}`">
                                    About text ({{ code }})
                                </Label>
                                <Textarea
                                    :id="`about_text_${code}`"
                                    v-model="form.translations[props.languages.findIndex((l) => l.code === code)].about_text"
                                    :rows="4"
                                    placeholder="We deliver creative solutions to help your business grow…"
                                />
                                <InputError :message="errorForTranslation(code)" />
                            </div>
                        </template>
                    </LocaleTabs>
                </CardContent>
            </Card>

            <Card>
                <CardHeader>
                    <CardTitle>Contact</CardTitle>
                    <CardDescription>
                        WhatsApp is stored as digits only. The footer turns
                        it into a wa.me link so taps open WhatsApp directly.
                    </CardDescription>
                </CardHeader>
                <CardContent class="grid gap-3 sm:grid-cols-2">
                    <div class="space-y-1 sm:col-span-2">
                        <Label for="address">Address</Label>
                        <Input id="address" v-model="form.address" />
                        <InputError :message="form.errors.address" />
                    </div>
                    <div class="space-y-1">
                        <Label for="phone">Phone</Label>
                        <Input id="phone" v-model="form.phone" />
                        <InputError :message="form.errors.phone" />
                    </div>
                    <div class="space-y-1">
                        <Label for="email">Email</Label>
                        <Input id="email" v-model="form.email" type="email" />
                        <InputError :message="form.errors.email" />
                    </div>
                    <div class="space-y-1 sm:col-span-2">
                        <Label for="whatsapp">WhatsApp number</Label>
                        <Input
                            id="whatsapp"
                            v-model="form.whatsapp"
                            placeholder="+49 123 456 7890"
                        />
                        <p class="text-xs text-muted-foreground">
                            Country code + number. Spaces, dashes and "+" are
                            stripped automatically.
                        </p>
                        <InputError :message="form.errors.whatsapp" />
                    </div>
                </CardContent>
            </Card>

            <Card>
                <CardHeader>
                    <CardTitle>Social links</CardTitle>
                    <CardDescription>
                        Each filled URL renders a matching icon under the
                        About column.
                    </CardDescription>
                </CardHeader>
                <CardContent class="grid gap-3 sm:grid-cols-2">
                    <div class="space-y-1">
                        <Label for="facebook_url">Facebook URL</Label>
                        <Input
                            id="facebook_url"
                            v-model="form.facebook_url"
                            type="url"
                        />
                        <InputError :message="form.errors.facebook_url" />
                    </div>
                    <div class="space-y-1">
                        <Label for="twitter_url">Twitter / X URL</Label>
                        <Input
                            id="twitter_url"
                            v-model="form.twitter_url"
                            type="url"
                        />
                        <InputError :message="form.errors.twitter_url" />
                    </div>
                    <div class="space-y-1">
                        <Label for="linkedin_url">LinkedIn URL</Label>
                        <Input
                            id="linkedin_url"
                            v-model="form.linkedin_url"
                            type="url"
                        />
                        <InputError :message="form.errors.linkedin_url" />
                    </div>
                    <div class="space-y-1">
                        <Label for="instagram_url">Instagram URL</Label>
                        <Input
                            id="instagram_url"
                            v-model="form.instagram_url"
                            type="url"
                        />
                        <InputError :message="form.errors.instagram_url" />
                    </div>
                </CardContent>
            </Card>

            <div class="flex justify-end">
                <Button type="submit" :disabled="form.processing">
                    <Save class="size-4" />
                    Save settings
                </Button>
            </div>
        </form>
    </div>
</template>
