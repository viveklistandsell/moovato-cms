<script setup lang="ts">
import { Image as ImageIcon } from 'lucide-vue-next';
import NextButton from '@/widgets/shared/NextButton.vue';

type Settings = {
    image_path: string | null;
    image_url: string | null;
    image_side: 'left' | 'right';
    card: boolean;
    bg?: 'none' | 'tinted' | 'dark';
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
        class="mv-mediachecklist section-py"
        :class="{
            'is-dark': settings.bg === 'dark',
            'is-tinted': settings.bg === 'tinted',
        }"
    >
        <div class="container-xl">
            <div
                class="grid items-center gap-10 lg:grid-cols-2 lg:gap-x-10"
                :class="settings.card ? 'mv-mc-card' : ''"
            >
                <div
                    class="mv-mc-media"
                    :class="settings.image_side === 'right' ? 'lg:order-2' : ''"
                >
                    <img
                        v-if="settings.image_url"
                        :src="settings.image_url"
                        :alt="data.image_alt || ''"
                        loading="lazy"
                        decoding="async"
                    />
                    <div v-else class="mv-mc-ph">
                        <ImageIcon class="size-10" />
                    </div>
                </div>

                <div
                    :class="settings.image_side === 'right' ? 'lg:order-1' : ''"
                >
                    <span
                        v-if="data.eyebrow"
                        class="mv-mediachecklist-eyebrow"
                    >
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
        </div>
    </section>
</template>
