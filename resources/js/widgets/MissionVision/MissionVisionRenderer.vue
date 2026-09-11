<script setup lang="ts">
import { Image as ImageIcon } from 'lucide-vue-next';
import NextButton from '@/widgets/shared/NextButton.vue';

type Settings = {
    image_path: string | null;
    image_url: string | null;
};

type Data = {
    image_alt?: string;
    heading?: string;
    body?: string;
    button_label?: string;
    button_url?: string;
};

defineProps<{ settings: Settings; data: Data }>();
</script>

<template>
    <section v-reveal class="mv-missionvision section-py">
        <div class="container-xl">
            <div class="grid grid-cols-1 gap-8 lg:grid-cols-2 lg:items-stretch">
                <div class="flex flex-col">
                    <h2
                        v-if="data.heading"
                        class="text-[var(--midnight)] mv-section-heading"
                    >
                        {{ data.heading }}
                    </h2>
                    <div class="mv-mv-media" :class="data.heading ? 'mt-6' : ''">
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
                    </div>

                    <div class="mv-mv-footer">
                        <NextButton
                            v-if="data.button_label"
                            class="mt-6 self-center"
                            :label="data.button_label"
                            :href="data.button_url || '#'"
                        />
                    </div>
                </div>

                <div class="mv-mv-panel">
                    <div
                        v-if="data.body"
                        class="mv-rte text-base leading-relaxed text-[var(--slate)]"
                        v-html="data.body"
                    />
                </div>
            </div>
        </div>
    </section>
</template>
