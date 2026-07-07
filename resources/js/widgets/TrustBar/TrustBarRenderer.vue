<script setup lang="ts">
import { computed } from 'vue';

type Stat = { value?: string; line1?: string; line2?: string };

type Settings = {
    theme: 'dark' | 'light';
    bg_image_path?: string | null;
    bg_image_url?: string | null;
};

type Data = {
    stats?: Stat[];
};

const props = defineProps<{ settings: Settings; data: Data }>();

const stats = computed(() => props.data.stats ?? []);

const bgStyle = computed(() =>
    props.settings.bg_image_url
        ? { backgroundImage: `url('${props.settings.bg_image_url}')` }
        : undefined,
);

function ordinal(i: number): string {
    return String(i + 1).padStart(2, '0');
}
</script>

<template>
    <section
        class="trust-bar"
        :class="`trust-bar--${settings.theme}`"
        :style="bgStyle"
    >
        <div class="trust-bar__bg" aria-hidden="true"></div>
        <img
            src="/images/shapes/funfact-shape-2-1.png"
            alt=""
            aria-hidden="true"
            class="trust-bar__shape trust-bar__shape--1"
        />
        <img
            src="/images/shapes/funfact-shape-2-2.png"
            alt=""
            aria-hidden="true"
            class="trust-bar__shape trust-bar__shape--2"
        />

        <div class="relative z-[3] container-xl">
            <div class="trust-bar__row">
                <div
                    v-for="(stat, i) in stats"
                    :key="i"
                    class="trust-bar__item"
                    :class="{ 'trust-bar__item--up': i % 2 === 1 }"
                >
                    <span
                        v-if="i % 2 === 0"
                        class="trust-bar__number"
                        aria-hidden="true"
                        >{{ ordinal(i) }}</span
                    >
                    <div v-if="i % 2 === 0" class="trust-bar__border"></div>

                    <div class="trust-bar__box">
                        <span class="trust-bar__blob" aria-hidden="true"></span>
                        <div class="trust-bar__count">{{ stat.value }}</div>
                        <p class="trust-bar__text">
                            <span v-if="stat.line1">{{ stat.line1 }}</span>
                            <span v-if="stat.line2">{{ stat.line2 }}</span>
                        </p>
                    </div>

                    <div v-if="i % 2 === 1" class="trust-bar__border"></div>
                    <span
                        v-if="i % 2 === 1"
                        class="trust-bar__number"
                        aria-hidden="true"
                        >{{ ordinal(i) }}</span
                    >
                </div>
            </div>
        </div>
    </section>
</template>
