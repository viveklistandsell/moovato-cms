<script setup lang="ts">
import { ExternalLink, Search } from 'lucide-vue-next';
import { computed, onMounted, ref } from 'vue';
import Breadcrumbs from '@/components/Breadcrumbs.vue';
import AdminLocaleSwitcher from '@/components/admin/AdminLocaleSwitcher.vue';
import AppearanceToggle from '@/components/common/AppearanceToggle.vue';
import { SidebarTrigger } from '@/components/ui/sidebar';
import { useBreadcrumbs } from '@/composables/common/useBreadcrumbs';
import { useT } from '@/composables/useT';

const t = useT();

const breadcrumbsRef = useBreadcrumbs();
const breadcrumbs = computed(() => breadcrumbsRef?.value ?? []);
function openSearch(): void {
    window.dispatchEvent(new CustomEvent('admin:search:open'));
}

const isMac = ref(false);
onMounted(() => {
    isMac.value =
        typeof navigator !== 'undefined' &&
        /Mac|iPhone|iPad/.test(navigator.platform);
});
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
            <a
                href="/"
                target="_blank"
                rel="noopener"
                :title="t('dashboard.view_site')"
                class="group inline-flex h-8 items-center gap-1.5 rounded-md border-0 bg-gradient-to-br from-[var(--orange)] to-[var(--yellow-dark)] px-2.5 text-xs font-semibold text-white shadow-sm shadow-[var(--orange)]/30 transition-all hover:-translate-y-0.5 hover:shadow-md hover:shadow-[var(--orange)]/40"
            >
                <ExternalLink
                    class="size-3.5 transition-transform group-hover:translate-x-0.5 group-hover:-translate-y-0.5"
                />
                <span class="hidden md:inline">{{ t('dashboard.view_site') }}</span>
            </a>
            <button
                type="button"
                class="inline-flex h-8 items-center gap-2 rounded-md border bg-background px-2 text-xs text-muted-foreground transition-colors hover:bg-accent hover:text-foreground"
                @click="openSearch"
            >
                <Search class="size-3.5" />
                <span class="hidden md:inline">{{ t('common.search') }}</span>
                <kbd
                    class="hidden rounded border bg-muted px-1 py-0.5 font-mono text-[10px] md:inline"
                >
                    {{ isMac ? '⌘' : 'Ctrl' }}K
                </kbd>
            </button>
            <AdminLocaleSwitcher />
            <AppearanceToggle />
        </div>
    </header>
</template>
