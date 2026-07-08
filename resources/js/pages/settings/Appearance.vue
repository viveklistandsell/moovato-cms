<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import AppearanceTabs from '@/components/AppearanceTabs.vue';
import Heading from '@/components/Heading.vue';
import { setBreadcrumbs } from '@/composables/common/useBreadcrumbs';
import { useT } from '@/composables/useT';
import { edit } from '@/routes/appearance';

const t = useT();

setBreadcrumbs(() => [
    { title: t('account_settings.appearance_head'), href: edit() },
]);
</script>

<template>
    <Head :title="t('account_settings.appearance_head')" />

    <h1 class="sr-only">{{ t('account_settings.appearance_head') }}</h1>

    <div class="space-y-6">
        <Heading
            variant="small"
            :title="t('account_settings.appearance_title')"
            :description="t('account_settings.appearance_description')"
        />
        <AppearanceTabs />
    </div>
</template>
