<script setup lang="ts">
import { onBeforeUnmount, onMounted, ref } from 'vue';

/**
 * Custom site cursor: a center dot that tracks the pointer exactly and a ring
 * that trails with easing (grows over interactive elements). Theme-colored
 * (--orange) so it reads on both light pages and the dark header/footer.
 * Only enabled on fine-pointer (non-touch) devices; the native cursor is
 * hidden via `html.has-mv-cursor` while active.
 */
const active = ref(false);
const visible = ref(false);
const hovering = ref(false);
const down = ref(false);

const dotEl = ref<HTMLElement | null>(null);
const ringEl = ref<HTMLElement | null>(null);

let raf = 0;
let mouseX = 0;
let mouseY = 0;
let ringX = 0;
let ringY = 0;

const INTERACTIVE =
    'a, button, [role="button"], input, label, select, textarea, summary, .next-cta, .btn-orange, .btn-orange-gradient, .btn-black';

function onMove(e: MouseEvent): void {
    mouseX = e.clientX;
    mouseY = e.clientY;
    visible.value = true;
    if (dotEl.value) {
        dotEl.value.style.transform = `translate3d(${mouseX}px, ${mouseY}px, 0) translate(-50%, -50%)`;
    }
}

function onOver(e: MouseEvent): void {
    const target = e.target as Element | null;
    hovering.value = !!target?.closest?.(INTERACTIVE);
}

function onDown(): void {
    down.value = true;
}
function onUp(): void {
    down.value = false;
}
function onLeave(): void {
    visible.value = false;
}
function onEnter(): void {
    visible.value = true;
}

function loop(): void {
    ringX += (mouseX - ringX) * 0.18;
    ringY += (mouseY - ringY) * 0.18;
    if (ringEl.value) {
        ringEl.value.style.transform = `translate3d(${ringX}px, ${ringY}px, 0) translate(-50%, -50%)`;
    }
    raf = requestAnimationFrame(loop);
}

onMounted(() => {
    if (!window.matchMedia('(hover: hover) and (pointer: fine)').matches) {
        return;
    }
    active.value = true;
    document.documentElement.classList.add('has-mv-cursor');
    window.addEventListener('mousemove', onMove, { passive: true });
    document.addEventListener('mouseover', onOver, { passive: true });
    window.addEventListener('mousedown', onDown);
    window.addEventListener('mouseup', onUp);
    document.addEventListener('mouseleave', onLeave);
    document.addEventListener('mouseenter', onEnter);
    raf = requestAnimationFrame(loop);
});

onBeforeUnmount(() => {
    cancelAnimationFrame(raf);
    document.documentElement.classList.remove('has-mv-cursor');
    window.removeEventListener('mousemove', onMove);
    document.removeEventListener('mouseover', onOver);
    window.removeEventListener('mousedown', onDown);
    window.removeEventListener('mouseup', onUp);
    document.removeEventListener('mouseleave', onLeave);
    document.removeEventListener('mouseenter', onEnter);
});
</script>

<template>
    <div
        v-show="active"
        class="mv-cursor"
        :class="{
            'is-visible': visible,
            'is-hover': hovering,
            'is-down': down,
        }"
        aria-hidden="true"
    >
        <div ref="ringEl" class="mv-cursor__ring"></div>
        <div ref="dotEl" class="mv-cursor__dot"></div>
    </div>
</template>
