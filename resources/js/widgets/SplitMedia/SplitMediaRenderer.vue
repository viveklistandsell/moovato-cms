<script setup lang="ts">
import { Image as ImageIcon } from 'lucide-vue-next';
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
    image_alt?: string;
    button_label?: string;
    button_url?: string;
};

defineProps<{ settings: Settings; data: Data }>();
</script>

<template>
    <section
        v-reveal
        class="mv-splitmedia"
        :class="settings.image_side === 'left' ? 'image-left' : 'image-right'"
    >
        <div class="mv-splitmedia-content">
            <div class="mv-splitmedia-inner">
                <span v-if="data.eyebrow" class="mv-splitmedia-eyebrow">
                    {{ data.eyebrow }}
                </span>
                <h2
                    v-if="data.heading"
                    class="text-[var(--midnight)] mv-section-heading"
                    :class="data.eyebrow ? 'mt-5' : ''"
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
                    class="mt-9"
                    :label="data.button_label"
                    :href="data.button_url || '#'"
                />
            </div>
        </div>

        <div class="mv-splitmedia-media">
            <img
                v-if="settings.image_url"
                :src="settings.image_url"
                :alt="data.image_alt || ''"
                loading="lazy"
                decoding="async"
            />
            <div v-else class="mv-splitmedia-ph">
                <ImageIcon class="size-10" />
            </div>
        </div>
    </section>
</template>
