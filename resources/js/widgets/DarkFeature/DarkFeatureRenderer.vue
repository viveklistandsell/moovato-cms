<script setup lang="ts">
import { Check, Image as ImageIcon } from 'lucide-vue-next';
import NextButton from '@/widgets/shared/NextButton.vue';

type Settings = {
    image_path: string | null;
    image_url: string | null;
    image_side: 'left' | 'right';
};

type Data = {
    eyebrow?: string;
    heading?: string;
    body?: string;
    list_title?: string;
    points?: string[];
    button_label?: string;
    button_url?: string;
};

defineProps<{ settings: Settings; data: Data }>();
</script>

<template>
    <section class="mv-darkfeature">
        <div
            class="container-xl grid items-center gap-10 py-16 sm:py-20 lg:grid-cols-2 lg:gap-12"
        >
            <div
                class="mv-df-media"
                :class="settings.image_side === 'right' ? 'lg:order-2' : ''"
            >
                <img
                    v-if="settings.image_url"
                    :src="settings.image_url"
                    :alt="data.heading || ''"
                    loading="lazy"
                    decoding="async"
                />
                <div v-else class="mv-df-ph">
                    <ImageIcon class="size-10" />
                </div>
            </div>

            <div :class="settings.image_side === 'right' ? 'lg:order-1' : ''">
                <span v-if="data.eyebrow" class="mv-df-eyebrow">
                    {{ data.eyebrow }}
                </span>
                <h2
                    v-if="data.heading"
                    class="text-3xl font-bold tracking-tight text-white sm:text-4xl"
                    :class="data.eyebrow ? 'mt-4' : ''"
                >
                    {{ data.heading }}
                </h2>
                <p
                    v-if="data.body"
                    class="mt-5 max-w-xl text-base leading-relaxed whitespace-pre-line text-white/70"
                >
                    {{ data.body }}
                </p>

                <p
                    v-if="data.list_title"
                    class="mt-8 text-lg font-semibold text-white"
                >
                    {{ data.list_title }}
                </p>
                <ul
                    v-if="data.points?.length"
                    class="mt-5 grid gap-x-8 gap-y-3 sm:grid-cols-2"
                >
                    <li
                        v-for="(point, i) in data.points"
                        :key="i"
                        class="flex items-center gap-2.5 text-sm font-medium text-white/90"
                    >
                        <Check class="size-4 shrink-0 text-[var(--orange)]" />
                        <span>{{ point }}</span>
                    </li>
                </ul>

                <NextButton
                    v-if="data.button_label"
                    class="mt-9"
                    :label="data.button_label"
                    :href="data.button_url || '#'"
                />
            </div>
        </div>
    </section>
</template>
