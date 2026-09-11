<script setup lang="ts">
import { Image as ImageIcon } from 'lucide-vue-next';
import NextButton from '@/widgets/shared/NextButton.vue';

type Settings = {
    image_path: string | null;
    image_url: string | null;
};

type Data = {
    eyebrow?: string;
    heading?: string;
    body?: string;
    image_alt?: string;
    stat_value?: string;
    stat_label?: string;
    button_label?: string;
    button_url?: string;
};

defineProps<{ settings: Settings; data: Data }>();
</script>

<template>
    <section v-reveal class="mv-statfeatures section-py">
        <div
            class="container-xl grid items-center gap-10 lg:grid-cols-2 lg:gap-x-10"
        >
            <div>
                <span v-if="data.eyebrow" class="mv-statfeatures-eyebrow">
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

                <NextButton
                    v-if="data.button_label"
                    class="mt-8"
                    :label="data.button_label"
                    :href="data.button_url || '#'"
                />
            </div>

            <div class="mv-sf-media">
                <img
                    v-if="settings.image_url"
                    :src="settings.image_url"
                    :alt="data.image_alt || ''"
                    loading="lazy"
                    decoding="async"
                />
                <div v-else class="mv-sf-ph">
                    <ImageIcon class="size-10" />
                </div>
                <div
                    v-if="data.stat_value"
                    class="mv-sf-badge"
                    aria-hidden="true"
                >
                    <span class="mv-sf-badge-value">{{ data.stat_value }}</span>
                    <span class="mv-sf-badge-label">{{ data.stat_label }}</span>
                </div>
            </div>
        </div>
    </section>
</template>
