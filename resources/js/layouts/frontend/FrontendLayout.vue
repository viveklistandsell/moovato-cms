<script setup lang="ts">
import { usePage } from '@inertiajs/vue3';
import { computed, onMounted, onUnmounted } from 'vue';
import BackToTop from '@/components/frontend/BackToTop.vue';
import CookieBanner from '@/components/frontend/CookieBanner.vue';
import SiteCursor from '@/components/frontend/SiteCursor.vue';
import SiteFooter from '@/components/frontend/SiteFooter.vue';
import SiteHeader from '@/components/frontend/SiteHeader.vue';

const page = usePage();

const locale = computed<string>(() => {
    const pageLocale = (page.props as Record<string, unknown>).locale;
    if (typeof pageLocale === 'string') {
        return pageLocale;
    }
    const match = (page.url ?? '/de').match(/^\/(de|en)(\/|$)/);
    return match ? match[1] : 'de';
});

type SiteLayout = {
    show_back_to_top?: boolean;
    cookie_consent_required?: boolean;
};
const layout = computed<SiteLayout>(
    () => ((page.props as Record<string, unknown>).siteLayout ?? {}) as SiteLayout,
);
const showBackToTop = computed<boolean>(
    () => layout.value.show_back_to_top !== false,
);
const showCookieBanner = computed<boolean>(
    () => layout.value.cookie_consent_required === true,
);

// Public frontend is always light — admin owns the theme toggle.
onMounted(() => {
    document.documentElement.classList.remove('dark');
});
onUnmounted(() => {
    const saved = localStorage.getItem('appearance');
    const prefersDark = window.matchMedia(
        '(prefers-color-scheme: dark)',
    ).matches;
    const shouldBeDark = saved === 'dark' || (saved !== 'light' && prefersDark);
    document.documentElement.classList.toggle('dark', shouldBeDark);
});
</script>

<template>
    <div
        class="mv-public-frame flex min-h-screen flex-col bg-background pb-[72px] text-foreground md:pb-0"
    >
        <SiteHeader :locale="locale" />
        <main class="flex-1">
            <slot />
        </main>
        <SiteFooter :locale="locale" />
        <BackToTop v-if="showBackToTop" />
        <SiteCursor />
        <CookieBanner v-if="showCookieBanner" />
    </div>
</template>
