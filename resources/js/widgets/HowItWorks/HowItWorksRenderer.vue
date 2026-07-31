<script setup lang="ts">
import { onBeforeUnmount, onMounted, ref } from 'vue';
import WidgetIcon from '@/widgets/shared/WidgetIcon.vue';

type Step = { icon?: string; title?: string; description?: string };

type Data = {
    eyebrow?: string;
    heading?: string;
    steps?: Step[];
};

defineProps<{ settings: Record<string, unknown>; data: Data }>();

const SNAKE =
    'M186 -15L50.3294 90.0353C24.7048 109.874 24.4322 148.473 49.7742 168.672L152.943 250.9C178.058 270.917 178.058 309.083 152.943 329.1L49.0571 411.9C23.9423 431.917 23.9423 470.083 49.0572 490.1L152.943 572.9C178.058 592.917 178.058 631.083 152.943 651.1L0 773';

const root = ref<HTMLElement | null>(null);
let frame = 0;
let handler: (() => void) | null = null;

onMounted(() => {
    const el = root.value;
    if (!el) return;
    const timeline = el.querySelector<HTMLElement>('.mv-howitworks__timeline');
    const steps = Array.from(
        el.querySelectorAll<HTMLElement>('.mv-howitworks__step'),
    );
    if (!timeline) return;

    if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
        timeline.style.setProperty('--p', '1');
        steps.forEach((s) => s.classList.add('is-active'));
        return;
    }

    const update = () => {
        frame = 0;
        const rect = el.getBoundingClientRect();
        const vh = window.innerHeight || document.documentElement.clientHeight;
        const p = Math.min(1, Math.max(0, (vh * 0.6 - rect.top) / rect.height));
        timeline.style.setProperty('--p', String(p));
        steps.forEach((step, i) => {
            step.classList.toggle('is-active', p >= (i + 0.5) / steps.length);
        });
    };

    handler = () => {
        if (!frame) frame = requestAnimationFrame(update);
    };
    window.addEventListener('scroll', handler, { passive: true });
    window.addEventListener('resize', handler, { passive: true });
    update();
});

onBeforeUnmount(() => {
    if (handler) {
        window.removeEventListener('scroll', handler);
        window.removeEventListener('resize', handler);
    }
    if (frame) cancelAnimationFrame(frame);
});
</script>

<template>
    <section ref="root" class="mv-howitworks section-py">
        <div class="container-xl">
            <div class="flex flex-col items-center">
                <span v-if="data.eyebrow" class="mv-howitworks__eyebrow">
                    {{ data.eyebrow }}
                </span>
                <h2 v-if="data.heading" class="mv-howitworks__heading">
                    {{ data.heading }}
                </h2>
            </div>

            <div class="mv-howitworks__timeline">
                <div class="mv-howitworks__path" aria-hidden="true">
                    <svg
                        class="mv-howitworks__path-svg"
                        viewBox="0 -15 202 788"
                        preserveAspectRatio="none"
                    >
                        <path class="mv-howitworks__path-base" :d="SNAKE" />
                        <path
                            class="mv-howitworks__path-fill"
                            pathLength="100"
                            :d="SNAKE"
                        />
                    </svg>
                </div>

                <div
                    v-for="(step, i) in data.steps ?? []"
                    :key="i"
                    class="mv-howitworks__step"
                    :class="i % 2 === 0 ? 'is-left' : 'is-right'"
                >
                    <div class="mv-howitworks__card">
                        <h3 class="mv-howitworks__step-title">
                            <WidgetIcon
                                :name="step.icon"
                                fallback="Circle"
                                class="size-5"
                            />
                            {{ step.title }}
                        </h3>
                        <div
                            v-if="step.description"
                            class=" mv-howitworks__step-desc"
                            v-html="step.description"
                        />
                    </div>
                </div>
            </div>
        </div>
    </section>
</template>
