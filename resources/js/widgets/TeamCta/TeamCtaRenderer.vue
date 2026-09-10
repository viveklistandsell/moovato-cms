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
    points?: Point[];
    image_alt?: string;
    cta_title?: string;
    cta_body?: string;
    cta_button_label?: string;
    cta_button_url?: string;
};

defineProps<{ settings: Settings; data: Data }>();
</script>

<template>
    <section v-reveal class="mv-teamcta section-py">
        <div
            class="container-xl grid items-center gap-10 lg:grid-cols-2 lg:gap-x-10"
        >
            <div :class="settings.image_side === 'right' ? 'lg:order-1' : ''">
                <span v-if="data.eyebrow" class="mv-teamcta-eyebrow">
                    {{ data.eyebrow }}
                </span>
                <h2
                    v-if="data.heading"
                    class="text-[var(--midnight)] mv-section-heading"
                    :class="data.eyebrow ? 'mt-4' : ''"
                >
                    {{ data.heading }}
                </h2>
                <div
                    v-if="data.body"
                    class="mv-rte mt-5 max-w-xl text-base leading-relaxed text-[var(--slate)]"
                    v-html="data.body"
                />

                <ul
                    v-if="data.points?.length"
                    class="mt-7 grid gap-x-8 gap-y-5 sm:grid-cols-2"
                >
                    <li
                        v-for="(point, i) in data.points"
                        :key="i"
                        class="flex items-start gap-2.5"
                    >
                        <span class="mv-teamcta-check mt-1">
                            <Check class="size-3.5" />
                        </span>
                        <span>
                            <span
                                v-if="point.title"
                                class="block font-semibold text-[var(--midnight)]"
                            >
                                {{ point.title }}
                            </span>
                            <div
                                v-if="point.description"
                                class="mv-rte block text-[15px] leading-relaxed text-[var(--slate)]"
                                v-html="point.description"
                            />
                        </span>
                    </li>
                </ul>

                <div v-if="data.cta_title" class="mv-teamcta-card mt-5">
                    <h3 class="text-lg font-semibold text-white">
                        {{ data.cta_title }}
                    </h3>
                    <div
                        v-if="data.cta_body"
                        class="mv-rte mt-2 text-sm text-white/70"
                        v-html="data.cta_body"
                    />
                    <NextButton
                        v-if="data.cta_button_label"
                        class="mt-5"
                        :label="data.cta_button_label"
                        :href="data.cta_button_url || '#'"
                    />
                </div>
            </div>

            <div
                class="mv-teamcta-media"
                :class="settings.image_side === 'right' ? 'lg:order-2' : ''"
            >
                <img
                    v-if="settings.image_url"
                    :src="settings.image_url"
                    :alt="data.image_alt || ''"
                    loading="lazy"
                    decoding="async"
                />
                <div v-else class="mv-teamcta-ph">
                    <ImageIcon class="size-10" />
                </div>
            </div>
        </div>
    </section>
</template>
