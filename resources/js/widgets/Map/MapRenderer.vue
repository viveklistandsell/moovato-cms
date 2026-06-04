<script setup lang="ts">
import { computed } from 'vue';

type Pin = {
    x?: number;
    y?: number;
    city?: string;
    label?: string;
    flag_path?: string | null;
    flag_url?: string | null;
};

type Settings = {
    image_path: string | null;
    image_url: string | null;
    pins?: Pin[];
};

type Data = {
    eyebrow?: string;
    heading?: string;
    subheading?: string;
};

const props = defineProps<{ settings: Settings; data: Data }>();

const pins = computed<Pin[]>(() => props.settings.pins ?? []);

function clamp(value: number | undefined): number {
    if (typeof value !== 'number' || Number.isNaN(value)) {
        return 50;
    }

    return Math.min(100, Math.max(0, value));
}

function edgeClass(value: number | undefined): string {
    const x = clamp(value);

    if (x <= 20) {
        return 'is-left';
    }

    if (x >= 80) {
        return 'is-right';
    }

    return '';
}
</script>

<template>
    <section class="mv-map section-py">
        <div class="container-xl">
            <div
                v-if="data.eyebrow || data.heading || data.subheading"
                class="mx-auto max-w-2xl text-center"
            >
                <span v-if="data.eyebrow" class="mv-map-eyebrow">
                    {{ data.eyebrow }}
                </span>
                <h2
                    v-if="data.heading"
                    class="mt-5 text-3xl font-bold tracking-tight text-[var(--white)] sm:text-4xl"
                >
                    {{ data.heading }}
                </h2>
                <p
                    v-if="data.subheading"
                    class="mt-4 text-base text-[var(--slate-light)]"
                >
                    {{ data.subheading }}
                </p>
            </div>

            <div
                class="mv-map-canvas mx-auto max-w-5xl"
                :class="{ 'mv-map-canvas--empty': !settings.image_url }"
            >
                <img
                    v-if="settings.image_url"
                    :src="settings.image_url"
                    alt=""
                    class="mv-map-img"
                    loading="lazy"
                    decoding="async"
                />

                <button
                    v-for="(pin, i) in pins"
                    :key="i"
                    type="button"
                    class="mv-map-pin"
                    :class="edgeClass(pin.x)"
                    :style="{
                        left: clamp(pin.x) + '%',
                        top: clamp(pin.y) + '%',
                    }"
                    :aria-label="pin.city"
                >
                    <span class="mv-map-dot" aria-hidden="true"></span>

                    <span class="mv-map-card">
                        <span v-if="pin.flag_url" class="mv-map-flag">
                            <img :src="pin.flag_url" :alt="pin.city || ''" />
                        </span>
                        <span v-if="pin.city" class="mv-map-city">{{
                            pin.city
                        }}</span>
                        <span v-if="pin.label" class="mv-map-label">{{
                            pin.label
                        }}</span>
                    </span>
                </button>
            </div>
        </div>
    </section>
</template>
