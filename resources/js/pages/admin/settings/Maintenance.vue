<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import { Check, Plus, Save } from 'lucide-vue-next';
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
import { setBreadcrumbs } from '@/composables/common/useBreadcrumbs';
import { useT } from '@/composables/useT';

const t = useT();
setBreadcrumbs(() => [
    { title: t('sidebar.dashboard'), href: '/dashboard' },
    { title: t('sidebar.settings'), href: '/admin/settings/identity' },
    { title: t('sidebar.maintenance_mode'), href: '/admin/settings/maintenance' },
]);

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
    currentIp: string | null;
}>();

// Breadcrumbs set dynamically above via setBreadcrumbs().
defineOptions({});

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

function parseBypassList(csv: string): string[] {
    return csv
        .split(',')
        .map((entry) => entry.trim())
        .filter((entry) => entry !== '');
}

const currentIpAlreadyListed = computed<boolean>(() => {
    if (!props.currentIp) return false;
    return parseBypassList(form.maintenance_bypass_ips).includes(props.currentIp);
});

function addCurrentIp(): void {
    if (!props.currentIp || currentIpAlreadyListed.value) return;

    const existing = parseBypassList(form.maintenance_bypass_ips);
    existing.push(props.currentIp);
    form.maintenance_bypass_ips = existing.join(', ');
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
    <Head :title="t('settings.maintenance_title')" />

    <div class="flex flex-col gap-6 p-4">
        <Heading
            :title="t('settings.maintenance_title')"
            :description="t('settings.maintenance_description')"
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
                            <span class="block font-medium">{{ t('settings.maintenance_enabled_label') }}</span>
                            <span class="block text-xs text-muted-foreground">
                                {{ t('settings.maintenance_enabled_help') }}
                            </span>
                        </span>
                    </label>
                </CardContent>
            </Card>

            <Card>
                <CardHeader>
                    <CardTitle>{{ t('settings.maintenance_holding_card') }}</CardTitle>
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
                                        {{ t('settings.maintenance_heading_label', { code }) }}
                                    </Label>
                                    <Input
                                        :id="`maint_heading_${code}`"
                                        v-model="form.translations[languages.findIndex((l) => l.code === code)].maintenance_heading"
                                    />
                                    <InputError :message="errorFor(code, 'maintenance_heading')" />
                                </div>
                                <div class="space-y-1">
                                    <Label :for="`maint_msg_${code}`">
                                        {{ t('settings.maintenance_message_label', { code }) }}
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
                    <CardTitle>{{ t('settings.maintenance_bypass_card') }}</CardTitle>
                </CardHeader>
                <CardContent class="space-y-2">
                    <div class="flex flex-col gap-2 sm:flex-row sm:items-center">
                        <Input
                            id="maintenance_bypass_ips"
                            v-model="form.maintenance_bypass_ips"
                            :placeholder="t('settings.maintenance_bypass_placeholder')"
                            class="flex-1"
                        />
                        <Button
                            v-if="currentIp"
                            type="button"
                            variant="outline"
                            size="sm"
                            :disabled="currentIpAlreadyListed"
                            class="sm:w-auto"
                            @click="addCurrentIp"
                        >
                            <Check
                                v-if="currentIpAlreadyListed"
                                class="size-4 text-emerald-600"
                            />
                            <Plus v-else class="size-4" />
                            <span class="font-mono text-xs">
                                {{
                                    currentIpAlreadyListed
                                        ? t('settings.maintenance_ip_already_added', { ip: currentIp })
                                        : t('settings.maintenance_add_my_ip_active', { ip: currentIp })
                                }}
                            </span>
                        </Button>
                    </div>
                    <p class="text-xs text-muted-foreground">
                        {{ t('settings.maintenance_bypass_help') }}
                    </p>
                    <ul
                        class="ml-4 list-disc space-y-0.5 text-xs text-muted-foreground"
                    >
                        <li v-html="t('settings.maintenance_bypass_ips_bullet')" />
                        <li v-html="t('settings.maintenance_bypass_urls_bullet')" />
                    </ul>
                    <InputError :message="form.errors.maintenance_bypass_ips" />
                </CardContent>
            </Card>

            <div class="flex justify-end">
                <Button type="submit" :disabled="form.processing">
                    <Save class="size-4" />
                    {{ t('settings.maintenance_save') }}
                </Button>
            </div>
        </form>
    </div>
</template>
