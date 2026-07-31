<script setup lang="ts">
import { ArrowLeft, ArrowRight, Star } from 'lucide-vue-next';
import { computed, ref } from 'vue';

type Testimonial = {
    quote?: string;
    author?: string;
    role?: string;
    avatar_url?: string | null;
    rating?: number;
};

type Data = {
    heading?: string;
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

function prev(): void {
    const n = items.value.length;
    if (n) current.value = (current.value - 1 + n) % n;
}
function next(): void {
    const n = items.value.length;
    if (n) current.value = (current.value + 1) % n;
}
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
    <section class="mv-reviews section-py">
        <div class="container-xl">
            <div class="mv-reviews__intro">
                 <span v-if="data.badge" class="mv-reviews__badge">
                    {{ data.badge }}
                </span>
                <h2 v-if="data.heading" class="mv-reviews__heading">
                    {{ data.heading }}
                </h2>
               
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
                        <ArrowRight :size="16" />
                    </a>
                </div>

                <div v-if="active" class="mv-reviews__card">
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

                    <div class="mv-reviews__foot">
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

                        <div v-if="items.length > 1" class="mv-reviews__nav">
                            <div class="mv-reviews__dots">
                                <button
                                    v-for="(item, i) in items"
                                    :key="i"
                                    type="button"
                                    class="mv-reviews__dot"
                                    :class="{ 'is-active': i === current }"
                                    :aria-label="`Bewertung ${i + 1}`"
                                    @click="current = i"
                                ></button>
                            </div>
                            <button
                                type="button"
                                class="mv-reviews__arrow"
                                aria-label="Vorherige"
                                @click="prev"
                            >
                                <ArrowLeft :size="18" />
                            </button>
                            <button
                                type="button"
                                class="mv-reviews__arrow"
                                aria-label="Nächste"
                                @click="next"
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
