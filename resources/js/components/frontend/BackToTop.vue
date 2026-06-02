<script setup lang="ts">
import { onMounted, onUnmounted, ref } from 'vue';

/**
 * Floating "Back to Top" button. Hidden until the page is scrolled past a
 * threshold, then fades in (bottom-right). Clicking smooth-scrolls to the top.
 * Styling lives in resources/css/gs.css (.back-to-top) and uses theme tokens.
 */
const visible = ref(false);

function onScroll(): void {
    visible.value = window.scrollY > 300;
}

function scrollToTop(): void {
    const reduceMotion = window.matchMedia(
        '(prefers-reduced-motion: reduce)',
    ).matches;
    window.scrollTo({ top: 0, behavior: reduceMotion ? 'auto' : 'smooth' });
}

onMounted(() => {
    window.addEventListener('scroll', onScroll, { passive: true });
    onScroll();
});

onUnmounted(() => {
    window.removeEventListener('scroll', onScroll);
});
</script>

<template>
    <button
        type="button"
        class="back-to-top"
        :class="{ 'is-visible': visible }"
        aria-label="Back to top"
        @click="scrollToTop"
    >
        <svg class="svgIcon" viewBox="0 0 384 512" aria-hidden="true">
            <path
                d="M214.6 41.4c-12.5-12.5-32.8-12.5-45.3 0l-160 160c-12.5 12.5-12.5 32.8 0 45.3s32.8 12.5 45.3 0L160 141.2V448c0 17.7 14.3 32 32 32s32-14.3 32-32V141.2L329.4 246.6c12.5 12.5 32.8 12.5 45.3 0s12.5-32.8 0-45.3l-160-160z"
            ></path>
        </svg>
    </button>
</template>
