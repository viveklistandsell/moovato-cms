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
import { Switch } from '@/components/ui/switch';
import { Textarea } from '@/components/ui/textarea';

type TranslationRow = {
    maintenance_heading: string | null;
    maintenance_message: string | null;
};

type Settings = {
    maintenance_enabled: boolean;
    maintenance_bypass_ips: string | null;
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
            { title: 'Settings', href: '/admin/settings/identity' },
            { title: 'Maintenance mode', href: '/admin/settings/maintenance' },
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
    maintenance_enabled: props.settings.maintenance_enabled,
    maintenance_bypass_ips: props.settings.maintenance_bypass_ips ?? '',
    translations: props.languages.map((lang) => ({
        lang: lang.code,
        maintenance_heading:
            props.settings.translations[lang.code]?.maintenance_heading ?? '',
        maintenance_message:
            props.settings.translations[lang.code]?.maintenance_message ?? '',
    })),
});

function submit(): void {
    form.put('/admin/settings/maintenance', { preserveScroll: true });
}

function errorFor(
    lang: string,
    field: 'maintenance_heading' | 'maintenance_message',
): string | undefined {
    const idx = form.translations.findIndex((t) => t.lang === lang);
    if (idx < 0) return undefined;
    const errs = form.errors as Record<string, string | undefined>;
    return errs[`translations.${idx}.${field}`];
}
</script>

<template>
    <Head title="Maintenance mode" />

    <div class="flex flex-col gap-6 p-4">
        <Heading
            title="Maintenance mode"
            description="Wrap the public site in a holding page. Admin routes stay reachable so you can flip the toggle from here."
        />

        <form class="flex flex-col gap-4" @submit.prevent="submit">
            <Card>
                <CardContent class="pt-6">
                    <label class="flex cursor-pointer items-center gap-3 rounded-md border p-3 text-sm">
                        <Switch
                            :model-value="form.maintenance_enabled"
                            @update:model-value="(v) => (form.maintenance_enabled = v as boolean)"
                        />
                        <span class="flex-1">
                            <span class="block font-medium">Maintenance mode enabled</span>
                            <span class="block text-xs text-muted-foreground">
                                Public site shows the holding page below.
                            </span>
                        </span>
                    </label>
                </CardContent>
            </Card>

            <Card>
                <CardHeader>
                    <CardTitle>Holding page (per language)</CardTitle>
                </CardHeader>
                <CardContent>
                    <LocaleTabs
                        :model-value="activeLang"
                        :languages="languages"
                        @update:model-value="(v) => (activeLang = v)"
                    >
                        <template #default="{ code }">
                            <div class="space-y-3 pt-3">
                                <div class="space-y-1">
                                    <Label :for="`maint_heading_${code}`">
                                        Heading ({{ code }})
                                    </Label>
                                    <Input
                                        :id="`maint_heading_${code}`"
                                        v-model="form.translations[languages.findIndex((l) => l.code === code)].maintenance_heading"
                                    />
                                    <InputError :message="errorFor(code, 'maintenance_heading')" />
                                </div>
                                <div class="space-y-1">
                                    <Label :for="`maint_msg_${code}`">
                                        Message ({{ code }})
                                    </Label>
                                    <Textarea
                                        :id="`maint_msg_${code}`"
                                        v-model="form.translations[languages.findIndex((l) => l.code === code)].maintenance_message"
                                        :rows="3"
                                    />
                                    <InputError :message="errorFor(code, 'maintenance_message')" />
                                </div>
                            </div>
                        </template>
                    </LocaleTabs>
                </CardContent>
            </Card>

            <Card>
                <CardHeader>
                    <CardTitle>Bypass IPs</CardTitle>
                </CardHeader>
                <CardContent class="space-y-1">
                    <Input
                        id="maintenance_bypass_ips"
                        v-model="form.maintenance_bypass_ips"
                        placeholder="1.2.3.4, 5.6.7.8"
                    />
                    <p class="text-xs text-muted-foreground">
                        Comma-separated. These IPs see the site even while
                        maintenance is on — useful for previewing your own
                        traffic.
                    </p>
                    <InputError :message="form.errors.maintenance_bypass_ips" />
                </CardContent>
            </Card>

            <div class="flex justify-end">
                <Button type="submit" :disabled="form.processing">
                    <Save class="size-4" />
                    Save maintenance
                </Button>
            </div>
        </form>
    </div>
</template>
