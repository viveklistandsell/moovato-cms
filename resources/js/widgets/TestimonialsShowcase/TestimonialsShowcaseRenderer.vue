<script setup lang="ts">
import { ArrowLeft, ArrowRight, Quote, Star } from 'lucide-vue-next';
import { computed, ref } from 'vue';
import NextButton from '@/widgets/shared/NextButton.vue';

type Item = {
    rating?: number;
    rating_text?: string;
    quote?: string;
    name?: string;
    role?: string;
    image_path?: string | null;
    image_url?: string | null;
};

type Settings = {
    bg_image_path?: string | null;
    bg_image_url?: string | null;
};

type Data = {
    eyebrow?: string;
    heading?: string;
    button_label?: string;
    button_url?: string;
    items?: Item[];
};

const props = defineProps<{ settings: Settings; data: Data }>();

const items = computed<Item[]>(() => props.data.items ?? []);

const bgStyle = computed(() =>
    props.settings.bg_image_url
        ? { backgroundImage: `url('${props.settings.bg_image_url}')` }
        : undefined,
);
const active = ref(0);
const current = computed<Item | undefined>(() => items.value[active.value]);

function go(delta: number): void {
    const n = items.value.length;
    if (!n) {
        return;
    }
    active.value = (active.value + delta + n) % n;
}

function initials(name?: string): string {
    return (name ?? '')
        .split(/\s+/)
        .filter(Boolean)
        .slice(0, 2)
        .map((w) => w[0]?.toUpperCase() ?? '')
        .join('');
}

// Distribute thumbnails evenly around the ring, starting at the top.
function thumbStyle(i: number): Record<string, string> {
    const n = items.value.length || 1;
    const angle = (i / n) * 2 * Math.PI - Math.PI / 2;
    const radius = 50;
    return {
        left: `${50 + radius * Math.cos(angle)}%`,
        top: `${50 + radius * Math.sin(angle)}%`,
    };
}
</script>

<template>
    <section v-reveal class="mv-testimonials section-py" :style="bgStyle">
        <div class="mv-testimonials__bg" aria-hidden="true"></div>

        <div class="relative z-[2] container-xl">
            <div
                class="mb-12 flex flex-col gap-6 sm:flex-row sm:items-end sm:justify-between"
            >
                <div class="max-w-2xl">
                    <span v-if="data.eyebrow" class="mv-howitworks__eyebrow">{{
                        data.eyebrow
                    }}</span>
                    <h2 v-if="data.heading" class="mv-testimonials__heading mv-section-heading">
                        {{ data.heading }}
                    </h2>
                </div>
                <NextButton
                    v-if="data.button_label"
                    :label="data.button_label"
                    :href="data.button_url || '#'"
                />
            </div>

            <div
                v-if="items.length"
                class="grid items-center gap-14 lg:grid-cols-[minmax(0,369px)_1fr]"
            >
                <div class="mv-testimonials__thumb">
                    <span
                        class="mv-testimonials__thumb-border"
                        aria-hidden="true"
                    ></span>
                    <button
                        v-for="(item, i) in items"
                        :key="i"
                        type="button"
                        class="mv-testimonials__thumb-item"
                        :class="{ 'is-active': i === active }"
                        :style="thumbStyle(i)"
                        :aria-label="`Bewertung von ${item.name}`"
                        @click="active = i"
                    >
                        <img
                            v-if="item.image_url"
                            :src="item.image_url"
                            :alt="item.name || ''"
                            loading="lazy"
                            decoding="async"
                        />
                        <span v-else class="mv-testimonials__thumb-fallback">{{
                            initials(item.name)
                        }}</span>
                        <span class="mv-testimonials__thumb-overlay">
                            <Quote class="size-5" />
                        </span>
                    </button>
                    <!-- <span class="mv-testimonials__thumb-quote">
                        <Quote class="size-8" />
                    </span> -->
                </div>

                <div v-if="current" class="mv-testimonials__item">
                    <div class="mv-testimonials__item-top">
                        <div class="mv-testimonials__ratings">
                            <div class="mv-testimonials__ratings-inner">
                                <span
                                    v-for="n in 5"
                                    :key="n"
                                    class="mv-testimonials__star"
                                    :class="{
                                        'is-on': n <= (current.rating ?? 5),
                                    }"
                                >
                                    <Star class="size-3.5" />
                                </span>
                            </div>
                            <h3
                                v-if="current.rating_text"
                                class="mv-testimonials__ratings-text"
                            >
                                {{ current.rating_text }}
                            </h3>
                        </div>
                        <span class="mv-testimonials__quoteicon">
                            <Quote class="size-9" />
                        </span>
                    </div>

                    <p class="mv-testimonials__quote">{{ current.quote }}</p>

                    <div
                        class="flex flex-wrap items-center justify-between gap-6"
                    >
                        <div class="mv-testimonials__identity">
                            <span class="mv-testimonials__image">
                                <img
                                    v-if="current.image_url"
                                    :src="current.image_url"
                                    :alt="current.name || ''"
                                    loading="lazy"
                                    decoding="async"
                                />
                                <span
                                    v-else
                                    class="mv-testimonials__image-fallback"
                                    >{{ initials(current.name) }}</span
                                >
                            </span>
                            <div>
                                <h3
                                    v-if="current.name"
                                    class="mv-testimonials__name"
                                >
                                    {{ current.name }}
                                </h3>
                                <p
                                    v-if="current.role"
                                    class="mv-testimonials__designation"
                                >
                                    {{ current.role }}
                                </p>
                            </div>
                        </div>

                        <div
                            v-if="items.length > 1"
                            class="mv-testimonials__nav"
                        >
                            <button
                                type="button"
                                aria-label="Vorherige Bewertung"
                                @click="go(-1)"
                            >
                                <ArrowLeft class="size-5" />
                            </button>
                            <button
                                type="button"
                                class="mv-testimonials__nav-next"
                                aria-label="Nächste Bewertung"
                                @click="go(1)"
                            >
                                <ArrowRight class="size-5" />
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</template>
