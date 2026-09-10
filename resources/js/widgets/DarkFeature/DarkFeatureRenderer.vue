<script setup lang="ts">
import { Check, Image as ImageIcon } from 'lucide-vue-next';
import NextButton from '@/widgets/shared/NextButton.vue';

type Settings = {
    image_path: string | null;
    image_url: string | null;
    image_side: 'left' | 'right';
};

type Point = { title?: string; description?: string };

type Data = {
    eyebrow?: string;
    heading?: string;
    body?: string;
    list_title?: string;
    points?: Point[];
    button_label?: string;
    button_url?: string;
};

defineProps<{ settings: Settings; data: Data }>();
</script>

<template>
    <section v-reveal class="mv-darkfeature">
        <div
            class="container-xl grid items-center gap-10 py-16 sm:py-20 lg:grid-cols-2 lg:gap-x-10"
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
                    class="text-white mv-section-heading"
                    :class="data.eyebrow ? 'mt-4' : ''"
                >
                    {{ data.heading }}
                </h2>
                <div
                    v-if="data.body"
                    class="mv-rte mt-5 max-w-xl text-base leading-relaxed text-white/70"
                    v-html="data.body"
                />

                <p
                    v-if="data.list_title"
                    class="mt-8 text-lg font-semibold text-white"
                >
                    {{ data.list_title }}
                </p>
                <ul
                    v-if="data.points?.length"
                    class="mt-5 grid gap-x-8 gap-y-5 sm:grid-cols-2"
                >
                    <li
                        v-for="(point, i) in data.points"
                        :key="i"
                        class="flex items-start gap-2.5"
                    >
                        <Check class="mt-1 size-4 shrink-0 text-[var(--orange)]" />
                        <span>
                            <span
                                v-if="point.title"
                                class="block text-lg font-semibold text-white"
                            >
                                {{ point.title }}
                            </span>
                            <div
                                v-if="point.description"
                                class="mv-rte block text-[15px] leading-relaxed text-white/70"
                                v-html="point.description"
                            />
                        </span>
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
