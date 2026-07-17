<script setup lang="ts">
import { onMounted, onUnmounted, ref } from 'vue';

/**
 * Floating "Back to Top" button. Hidden until the page is scrolled past a
 * threshold, then fades in (bottom-right). Clicking smooth-scrolls to the top.
 * Styling lives in resources/css/gs.css (.back-to-top) and uses theme tokens.
 */
const visible = ref(false);
const launching = ref(false);
let launchTimer: ReturnType<typeof setTimeout> | undefined;

function onScroll(): void {
    visible.value = window.scrollY > 300;
}

function scrollToTop(): void {
    const reduceMotion = window.matchMedia(
        '(prefers-reduced-motion: reduce)',
    ).matches;

    if (!reduceMotion) {
        clearTimeout(launchTimer);
        launching.value = false;
        requestAnimationFrame(() => {
            launching.value = true;
        });
        launchTimer = setTimeout(() => {
            launching.value = false;
        }, 950);
    }

    window.scrollTo({ top: 0, behavior: reduceMotion ? 'auto' : 'smooth' });
}

onMounted(() => {
    window.addEventListener('scroll', onScroll, { passive: true });
    onScroll();
});

onUnmounted(() => {
    window.removeEventListener('scroll', onScroll);
    clearTimeout(launchTimer);
});
</script>

<template>
    <button
        type="button"
        class="back-to-top"
        :class="{ 'is-visible': visible, 'is-launching': launching }"
        aria-label="Back to top"
        @click="scrollToTop"
    >
        <span class="back-to-top__lines" aria-hidden="true">
            <i></i><i></i><i></i>
        </span>
        <svg
            class="back-to-top__icon"
            xmlns="http://www.w3.org/2000/svg"
            width="22"
            height="22"
            viewBox="0 0 24 24"
            fill="none"
            stroke="currentColor"
            stroke-width="2.4"
            stroke-linecap="round"
            stroke-linejoin="round"
            aria-hidden="true"
        >
            <path d="M12 19V6" />
            <path d="M5 13l7-7 7 7" />
        </svg>
    </button>
</template>
