<script setup lang="ts">
import { Check, Image as ImageIcon } from 'lucide-vue-next';
import NextButton from '@/widgets/shared/NextButton.vue';

type Settings = {
    image_path: string | null;
    image_url: string | null;
};

type Data = {
    image_alt?: string;
    heading?: string;
    subheading?: string;
    body?: string;
    button_label?: string;
    button_url?: string;
    panel_one_heading?: string;
    panel_one_body?: string;
    panel_one_points?: string[];
    panel_two_heading?: string;
    panel_two_body?: string;
    panel_two_points?: string[];
};

defineProps<{ settings: Settings; data: Data }>();
</script>

<template>
    <section v-reveal class="mv-missionvision section-py">
        <div class="container-fluid">
            <div class="grid grid-cols-1 gap-8 lg:grid-cols-2 lg:items-stretch">
                <div class="flex flex-col">
                    <div class="mv-mv-media">
                        <img
                            v-if="settings.image_url"
                            :src="settings.image_url"
                            :alt="data.image_alt || ''"
                            loading="lazy"
                            decoding="async"
                        />
                        <div v-else class="mv-mv-placeholder">
                            <ImageIcon class="size-10" />
                        </div>
                        <div class="mv-mv-media-overlay" aria-hidden="true" />
                        <div class="mv-mv-media-text">
                            <h2
                                v-if="data.heading"
                                class="text-white mv-section-heading"
                            >
                                {{ data.heading }}
                            </h2>
                            <p
                                v-if="data.subheading"
                                class="mt-3 text-base text-white/85"
                            >
                                {{ data.subheading }}
                            </p>
                        </div>
                    </div>

                    <div class="mv-mv-footer">
                        <div
                            v-if="data.body"
                            class="mv-rte text-base leading-relaxed text-[var(--slate)]"
                            v-html="data.body"
                        />
                        <NextButton
                            v-if="data.button_label"
                            class="mt-6"
                            :label="data.button_label"
                            :href="data.button_url || '#'"
                        />
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                    <div class="mv-mv-panel">
                        <h3
                            v-if="data.panel_one_heading"
                            class="relative text-lg font-semibold text-[var(--midnight)]"
                        >
                            {{ data.panel_one_heading }}
                        </h3>
                        <p
                            v-if="data.panel_one_body"
                            class="relative mt-4 text-base leading-relaxed text-[var(--slate)]"
                        >
                            {{ data.panel_one_body }}
                        </p>
                        <ul
                            v-if="data.panel_one_points?.length"
                            class="relative mt-6 space-y-3"
                        >
                            <li
                                v-for="(point, i) in data.panel_one_points"
                                :key="i"
                                class="flex items-center gap-2.5 text-sm font-medium text-[var(--midnight)]"
                            >
                                <Check
                                    class="size-4 shrink-0 text-[var(--orange)]"
                                />
                                <span>{{ point }}</span>
                            </li>
                        </ul>
                    </div>

                    <div class="mv-mv-panel">
                        <span
                            v-for="n in 2"
                            :key="n"
                            class="mv-mv-panel-ring"
                            aria-hidden="true"
                        />
                        <h3
                            v-if="data.panel_two_heading"
                            class="relative text-lg font-semibold text-[var(--midnight)]"
                        >
                            {{ data.panel_two_heading }}
                        </h3>
                        <p
                            v-if="data.panel_two_body"
                            class="relative mt-4 text-base leading-relaxed text-[var(--slate)]"
                        >
                            {{ data.panel_two_body }}
                        </p>
                        <ul
                            v-if="data.panel_two_points?.length"
                            class="relative mt-6 space-y-3"
                        >
                            <li
                                v-for="(point, i) in data.panel_two_points"
                                :key="i"
                                class="flex items-center gap-2.5 text-sm font-medium text-[var(--midnight)]"
                            >
                                <Check
                                    class="size-4 shrink-0 text-[var(--orange)]"
                                />
                                <span>{{ point }}</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>
</template>
