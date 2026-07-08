<script setup lang="ts">
import { computed } from 'vue';
import NextButton from '@/widgets/shared/NextButton.vue';

type Settings = {
    bg_image_path?: string | null;
    bg_image_url?: string | null;
    inline_image_path?: string | null;
    inline_image_url?: string | null;
};

type Data = {
    title_before?: string;
    title_after?: string;
    inline_image_alt?: string;
    button_label?: string;
    button_url?: string;
};

const props = defineProps<{ settings: Settings; data: Data }>();

const bgStyle = computed(() =>
    props.settings.bg_image_url
        ? { backgroundImage: `url('${props.settings.bg_image_url}')` }
        : undefined,
);
</script>

<template>
    <section class="mv-workcta">
        <div class="mv-workcta__bg" :style="bgStyle" aria-hidden="true"></div>
        <div class="mv-workcta__inner container-xl">
            <h2 class="mv-workcta__title">
                <span v-if="data.title_before">{{ data.title_before }}</span>
                <img
                    v-if="settings.inline_image_url"
                    :src="settings.inline_image_url"
                    :alt="data.inline_image_alt || ''"
                    loading="lazy"
                    decoding="async"
                />
                <span v-if="data.title_after">{{ data.title_after }}</span>
            </h2>

            <NextButton
                v-if="data.button_label"
                :label="data.button_label"
                :href="data.button_url || '#'"
            />
        </div>
    </section>
</template>
