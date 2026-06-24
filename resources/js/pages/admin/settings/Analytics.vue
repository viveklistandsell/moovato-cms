<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import { Save } from 'lucide-vue-next';
import Heading from '@/components/Heading.vue';
import InputError from '@/components/InputError.vue';
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
    { title: t('sidebar.analytics_tracking'), href: '/admin/settings/analytics' },
]);

type Settings = {
    google_analytics_id: string | null;
    google_tag_manager_id: string | null;
    meta_pixel_id: string | null;
    custom_head_code: string | null;
    cookie_consent_required: boolean;
};

const props = defineProps<{ settings: Settings }>();

// Breadcrumbs set dynamically above via setBreadcrumbs().
defineOptions({});

const form = useForm({
    google_analytics_id: props.settings.google_analytics_id ?? '',
    google_tag_manager_id: props.settings.google_tag_manager_id ?? '',
    meta_pixel_id: props.settings.meta_pixel_id ?? '',
    custom_head_code: props.settings.custom_head_code ?? '',
    cookie_consent_required: props.settings.cookie_consent_required,
});

function submit(): void {
    form.put('/admin/settings/analytics', { preserveScroll: true });
}
</script>

<template>
    <Head :title="t('settings.analytics_title')" />

    <div class="flex flex-col gap-6 p-4">
        <Heading
            :title="t('settings.analytics_title')"
            :description="t('settings.analytics_description')"
        />

        <form class="flex flex-col gap-4" @submit.prevent="submit">
            <Card>
                <CardHeader>
                    <CardTitle>{{ t('settings.analytics_tracking_card') }}</CardTitle>
                </CardHeader>
                <CardContent class="grid gap-3 sm:grid-cols-3">
                    <div class="space-y-1">
                        <Label for="google_analytics_id">{{ t('settings.analytics_ga_id') }}</Label>
                        <Input
                            id="google_analytics_id"
                            v-model="form.google_analytics_id"
                            :placeholder="t('settings.analytics_ga_placeholder')"
                        />
                        <InputError :message="form.errors.google_analytics_id" />
                    </div>
                    <div class="space-y-1">
                        <Label for="google_tag_manager_id">{{ t('settings.analytics_gtm_label') }}</Label>
                        <Input
                            id="google_tag_manager_id"
                            v-model="form.google_tag_manager_id"
                            :placeholder="t('settings.analytics_gtm_placeholder')"
                        />
                        <InputError :message="form.errors.google_tag_manager_id" />
                    </div>
                    <div class="space-y-1">
                        <Label for="meta_pixel_id">{{ t('settings.analytics_meta_pixel') }}</Label>
                        <Input
                            id="meta_pixel_id"
                            v-model="form.meta_pixel_id"
                            :placeholder="t('settings.analytics_meta_placeholder')"
                        />
                        <InputError :message="form.errors.meta_pixel_id" />
                    </div>
                </CardContent>
            </Card>

            <Card>
                <CardHeader>
                    <CardTitle v-html="t('settings.analytics_custom_head_card')" />
                </CardHeader>
                <CardContent>
                    <Textarea
                        id="custom_head_code"
                        v-model="form.custom_head_code"
                        :rows="6"
                        class="font-mono text-xs"
                        :placeholder="t('settings.analytics_custom_head_placeholder')"
                    />
                    <p class="mt-1 text-xs text-muted-foreground" v-html="t('settings.analytics_custom_head_help')" />
                    <InputError :message="form.errors.custom_head_code" />
                </CardContent>
            </Card>

            <Card>
                <CardContent class="pt-6">
                    <label class="flex cursor-pointer items-center gap-3 rounded-md border p-3 text-sm">
                        <Switch
                            :model-value="form.cookie_consent_required"
                            @update:model-value="(v) => (form.cookie_consent_required = v as boolean)"
                        />
                        <span class="flex-1">
                            <span class="block font-medium">{{ t('settings.analytics_cookie_label') }}</span>
                            <span class="block text-xs text-muted-foreground">
                                {{ t('settings.analytics_cookie_help') }}
                            </span>
                        </span>
                    </label>
                </CardContent>
            </Card>

            <div class="flex justify-end">
                <Button type="submit" :disabled="form.processing">
                    <Save class="size-4" />
                    {{ t('settings.analytics_save') }}
                </Button>
            </div>
        </form>
    </div>
</template>
