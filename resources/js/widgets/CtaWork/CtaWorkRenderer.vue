<script setup lang="ts">
import { computed } from 'vue';
import NextButton from '@/widgets/shared/NextButton.vue';

type Settings = {
    media_image_path?: string | null;
    media_image_url?: string | null;
};

type Data = {
    heading?: string;
    subtext?: string;
    media_image_alt?: string;
    button_label?: string;
    button_url?: string;
};

const props = defineProps<{ settings: Settings; data: Data }>();

const hasMedia = computed(() => Boolean(props.settings.media_image_url));
</script>

<template>
    <section v-reveal class="mv-workcta">
        <div class="mv-workcta__bg" aria-hidden="true" />
        <div class="mv-workcta__overlay" aria-hidden="true" />

        <div class="container-xl relative z-10">
            <div
                class="grid items-center gap-10 lg:gap-16"
                :class="
                    hasMedia
                        ? 'grid-cols-1 text-center lg:grid-cols-[1.45fr_0.55fr] lg:text-left'
                        : 'grid-cols-1 justify-items-center text-center'
                "
            >
                <div>
                    <h2
                        v-if="data.heading"
                        class="mv-workcta__heading mx-auto max-w-xl text-3xl leading-tight font-bold text-white sm:text-4xl"
                        :class="hasMedia ? 'lg:mx-0' : ''"
                    >
                        {{ data.heading }}
                    </h2>

                    <p
                        v-if="data.subtext"
                        class="mx-auto mt-4  text-[15px] leading-relaxed text-white/60"
                        :class="hasMedia ? 'lg:mx-0' : ''"
                    >
                        {{ data.subtext }}
                    </p>

                    <NextButton
                        v-if="data.button_label"
                        class="mt-9"
                        :label="data.button_label"
                        :href="data.button_url || '#'"
                    />
                </div>

                <div v-if="hasMedia" class="mv-workcta__media">
                    <img
                        class="mv-workcta__image"
                        :src="settings.media_image_url!"
                        :alt="data.media_image_alt || ''"
                        loading="lazy"
                        decoding="async"
                    />
                </div>
            </div>
        </div>
    </section>
</template>
