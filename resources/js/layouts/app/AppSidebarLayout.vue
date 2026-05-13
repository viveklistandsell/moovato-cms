<script setup lang="ts">
import { watch } from 'vue';
import AppContent from '@/components/AppContent.vue';
import AppShell from '@/components/AppShell.vue';
import AppSidebar from '@/components/AppSidebar.vue';
import AppSidebarHeader from '@/components/AppSidebarHeader.vue';
import { Toaster } from '@/components/ui/sonner';
import { provideBreadcrumbs } from '@/composables/common/useBreadcrumbs';
import type { BreadcrumbItem } from '@/types';

type Props = {
    breadcrumbs?: BreadcrumbItem[];
};

const props = withDefaults(defineProps<Props>(), {
    breadcrumbs: () => [],
});

const breadcrumbsRef = provideBreadcrumbs(props.breadcrumbs);

// If the page uses defineOptions to pass static breadcrumbs (non-empty), they
// take precedence over a previous page's setBreadcrumbs() value. If the prop
// is empty, leave the ref alone — the page may set it via setBreadcrumbs().
watch(
    () => props.breadcrumbs,
    (next) => {
        if (next && next.length > 0) {
            breadcrumbsRef.value = next;
        }
    },
    { deep: true },
);
</script>

<template>
    <AppShell variant="sidebar">
        <AppSidebar />
        <AppContent variant="sidebar" class="overflow-x-hidden">
            <AppSidebarHeader />
            <slot />
        </AppContent>
        <Toaster />
    </AppShell>
</template>
