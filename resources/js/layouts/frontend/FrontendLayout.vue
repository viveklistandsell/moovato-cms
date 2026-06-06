<script setup lang="ts">
import { usePage } from '@inertiajs/vue3';
import { ArrowUp } from 'lucide-vue-next';
import { computed, onMounted, onUnmounted, ref } from 'vue';
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

type SiteLayout = { show_back_to_top?: boolean };
const layout = computed<SiteLayout>(
    () => ((page.props as Record<string, unknown>).siteLayout ?? {}) as SiteLayout,
);
const showBackToTop = computed<boolean>(
    () => layout.value.show_back_to_top !== false,
);

// Back-to-top: only render the button once the viewport is scrolled
// 400px+ down so it doesn't clutter the first-screen view.
const scrolledFar = ref(false);
function onScroll(): void {
    scrolledFar.value = window.scrollY > 400;
}
function scrollToTop(): void {
    window.scrollTo({ top: 0, behavior: 'smooth' });
}

// Public frontend is always light — admin owns the theme toggle.
onMounted(() => {
    document.documentElement.classList.remove('dark');
    window.addEventListener('scroll', onScroll, { passive: true });
    onScroll();
});
onUnmounted(() => {
    window.removeEventListener('scroll', onScroll);
    const saved = localStorage.getItem('appearance');
    const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
    const shouldBeDark = saved === 'dark' || (saved !== 'light' && prefersDark);
    document.documentElement.classList.toggle('dark', shouldBeDark);
});
</script>

<template>
    <div class="flex min-h-screen flex-col bg-background text-foreground">
        <SiteHeader :locale="locale" />
        <main class="flex-1">
            <slot />
        </main>
        <SiteFooter :locale="locale" />

        <Transition
            enter-active-class="transition-opacity duration-200"
            enter-from-class="opacity-0"
            enter-to-class="opacity-100"
            leave-active-class="transition-opacity duration-200"
            leave-from-class="opacity-100"
            leave-to-class="opacity-0"
        >
            <button
                v-if="showBackToTop && scrolledFar"
                type="button"
                aria-label="Back to top"
                class="fixed bottom-6 right-6 z-40 inline-flex size-11 items-center justify-center rounded-full border border-border bg-background/90 text-muted-foreground shadow-lg backdrop-blur transition hover:border-foreground hover:text-foreground"
                @click="scrollToTop"
            >
                <ArrowUp class="size-5" />
            </button>
        </Transition>
    </div>
</template>
