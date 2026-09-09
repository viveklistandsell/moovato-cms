<script setup lang="ts">
import { ArrowLeft, ArrowRight, Star } from 'lucide-vue-next';
import { computed, onBeforeUnmount, onMounted, ref, watch } from 'vue';

type Testimonial = {
    quote?: string;
    author?: string;
    role?: string;
    avatar_url?: string | null;
    rating?: number;
};

type Data = {
    heading?: string;
    description?: string;
    badge?: string;
    rating_value?: string;
    rating_label?: string;
    rating_sub?: string;
    cta_label?: string;
    cta_url?: string;
    testimonials?: Testimonial[];
};

const props = defineProps<{ settings: Record<string, unknown>; data: Data }>();

const items = computed<Testimonial[]>(() => props.data.testimonials ?? []);
const current = ref(0);
const active = computed<Testimonial | null>(
    () => items.value[current.value] ?? null,
);

const isPulsing = ref(false);
let pulseTimeout: ReturnType<typeof setTimeout> | undefined;

watch(current, () => {
    if (pulseTimeout) clearTimeout(pulseTimeout);
    isPulsing.value = true;
    pulseTimeout = setTimeout(() => {
        isPulsing.value = false;
    }, 500);
});

function prev(): void {
    const n = items.value.length;
    if (n) current.value = (current.value - 1 + n) % n;
}
function next(): void {
    const n = items.value.length;
    if (n) current.value = (current.value + 1) % n;
}

const AUTOPLAY_DELAY = 6000;
let autoplayTimer: ReturnType<typeof setInterval> | undefined;
const prefersReducedMotion =
    typeof window !== 'undefined' &&
    window.matchMedia('(prefers-reduced-motion: reduce)').matches;

function stopAutoplay(): void {
    if (autoplayTimer) {
        clearInterval(autoplayTimer);
        autoplayTimer = undefined;
    }
}

function startAutoplay(): void {
    stopAutoplay();
    if (prefersReducedMotion || items.value.length < 2) return;
    autoplayTimer = setInterval(next, AUTOPLAY_DELAY);
}

function handlePrev(): void {
    prev();
    startAutoplay();
}
function handleNext(): void {
    next();
    startAutoplay();
}
function handleGoTo(index: number): void {
    current.value = index;
    startAutoplay();
}

onMounted(startAutoplay);
onBeforeUnmount(stopAutoplay);
function starCount(rating?: number): number {
    return Math.max(1, Math.min(5, rating ?? 5));
}
function initials(name?: string): string {
    return (name ?? '')
        .trim()
        .split(/\s+/)
        .map((w) => w[0])
        .slice(0, 2)
        .join('')
        .toUpperCase();
}
</script>

<template>
    <section v-reveal class="mv-reviews section-py">
        <div class="container-xl">
            <div class="mv-reviews__intro">
                <span v-if="data.badge" class="mv-reviews__badge mb-4">{{ data.badge }}</span>
                <h2 v-if="data.heading" class="mv-reviews__heading mv-section-heading">
                    {{ data.heading }}
                </h2>
                <div
                    v-if="data.description"
                    class="mv-rte mt-4 text-base leading-relaxed text-[var(--slate)]"
                    v-html="data.description"
                />
            </div>

            <div class="grid grid-cols-1 gap-6 lg:grid-cols-[1fr_1.7fr]">
                <div class="mv-reviews__rating">
                    <div class="mv-reviews__rating-value">
                        {{ data.rating_value }}
                    </div>
                    <div class="mv-reviews__rating-label">
                        <Star :size="16" />
                        {{ data.rating_label }}
                    </div>
                    <p v-if="data.rating_sub" class="mv-reviews__rating-sub">
                        {{ data.rating_sub }}
                    </p>
                    <a
                        v-if="data.cta_label"
                        :href="data.cta_url || '#'"
                        class="mv-reviews__rating-btn"
                    >
                        {{ data.cta_label }}
                        <span class="mv-reviews__rating-btn-ico">
                            <ArrowRight :size="16" />
                        </span>
                    </a>
                </div>

                <div
                    v-if="active"
                    class="mv-reviews__card"
                    :class="{ 'is-pulsing': isPulsing }"
                    @mouseenter="stopAutoplay"
                    @mouseleave="startAutoplay"
                    @focusin="stopAutoplay"
                    @focusout="startAutoplay"
                >
                    <Transition name="mv-reviews-fade" mode="out-in">
                        <div :key="current" class="mv-reviews__content">
                            <div class="mv-reviews__stars">
                                <Star
                                    v-for="s in starCount(active.rating)"
                                    :key="s"
                                    :size="18"
                                />
                            </div>

                            <blockquote class="mv-reviews__quote">
                                {{ active.quote }}
                            </blockquote>

                            <div class="mv-reviews__author">
                                <span class="mv-reviews__avatar">
                                    <img
                                        v-if="active.avatar_url"
                                        :src="active.avatar_url"
                                        :alt="active.author || ''"
                                    />
                                    <template v-else>
                                        {{ initials(active.author) }}
                                    </template>
                                </span>
                                <span class="mv-reviews__author-text">
                                    <span class="mv-reviews__name">
                                        {{ active.author }}
                                    </span>
                                    <span class="mv-reviews__role">
                                        {{ active.role }}
                                    </span>
                                </span>
                            </div>
                        </div>
                    </Transition>

                    <div v-if="items.length > 1" class="mv-reviews__nav">
                        <div class="mv-reviews__dots">
                            <button
                                v-for="(item, i) in items"
                                :key="i"
                                type="button"
                                class="mv-reviews__dot"
                                :class="{ 'is-active': i === current }"
                                :aria-label="`Bewertung ${i + 1}`"
                                @click="handleGoTo(i)"
                            ></button>
                        </div>
                        <div class="mv-reviews__arrows">
                            <button
                                type="button"
                                class="mv-reviews__arrow"
                                aria-label="Vorherige"
                                @click="handlePrev"
                            >
                                <ArrowLeft :size="18" />
                            </button>
                            <button
                                type="button"
                                class="mv-reviews__arrow"
                                aria-label="Nächste"
                                @click="handleNext"
                            >
                                <ArrowRight :size="18" />
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</template>
