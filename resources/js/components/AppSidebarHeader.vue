<script setup lang="ts">
import { computed } from 'vue';
import Breadcrumbs from '@/components/Breadcrumbs.vue';
import AppearanceToggle from '@/components/common/AppearanceToggle.vue';
import { SidebarTrigger } from '@/components/ui/sidebar';
import { useBreadcrumbs } from '@/composables/common/useBreadcrumbs';

const breadcrumbsRef = useBreadcrumbs();
const breadcrumbs = computed(() => breadcrumbsRef?.value ?? []);
</script>

<template>
    <header
        class="flex h-16 shrink-0 items-center justify-between gap-2 border-b border-sidebar-border/70 px-6 transition-[width,height] ease-linear group-has-data-[collapsible=icon]/sidebar-wrapper:h-12 md:px-4"
    >
        <div class="flex items-center gap-2">
            <SidebarTrigger class="-ml-1" />
            <template v-if="breadcrumbs.length > 0">
                <Breadcrumbs :breadcrumbs="breadcrumbs" />
            </template>
        </div>
        <div class="flex items-center gap-2">
            <AppearanceToggle />
        </div>
    </header>
</template>
