<script setup lang="ts">
import { Image as ImageIcon } from 'lucide-vue-next';
import NextButton from '@/widgets/shared/NextButton.vue';

type Stat = { description?: string };

type Settings = {
    image_path: string | null;
    image_url: string | null;
    image_2_path: string | null;
    image_2_url: string | null;
};

type Data = {
    eyebrow?: string;
    heading?: string;
    heading_accent?: string;
    body?: string;
    reviews_label?: string;
    avatars?: string[];
    image_alt?: string;
    image_2_alt?: string;
    stats?: Stat[];
    button_label?: string;
    button_url?: string;
};

defineProps<{ settings: Settings; data: Data }>();
</script>

<template>
    <section v-reveal class="mv-aboutstats section-py">
        <div class="container-xl">
            <div class="flex flex-col items-start gap-5">
                <span v-if="data.eyebrow" class="mv-aboutstats-eyebrow">
                    {{ data.eyebrow }}
                </span>
                <h2
                    v-if="data.heading || data.heading_accent"
                    class="text-[var(--midnight)] mv-section-heading"
                >
                    {{ data.heading }}
                    <span
                        v-if="data.heading_accent"
                        class="text-[var(--orange)]"
                    >
                        {{ data.heading_accent }}
                    </span>
                </h2>
            </div>

            <div
                class="mt-8"
            >
                <div
                    v-if="data.body"
                    class="mv-rte text-base leading-relaxed text-[var(--slate)]"
                    v-html="data.body"
                />
            </div>

            <div class="mt-5 grid gap-8 lg:grid-cols-[1.5fr_1fr] lg:gap-x-10">
                <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                    <div class="mv-aboutstats-photo">
                        <img
                            v-if="settings.image_url"
                            :src="settings.image_url"
                            :alt="data.image_alt || ''"
                            loading="lazy"
                            decoding="async"
                        />
                        <div v-else class="mv-aboutstats-ph">
                            <ImageIcon class="size-10" />
                        </div>
                    </div>
                    <div class="mv-aboutstats-photo">
                        <img
                            v-if="settings.image_2_url"
                            :src="settings.image_2_url"
                            :alt="data.image_2_alt || ''"
                            loading="lazy"
                            decoding="async"
                        />
                        <div v-else class="mv-aboutstats-ph">
                            <ImageIcon class="size-10" />
                        </div>
                    </div>
                </div>

                <div class="flex flex-col gap-8">
                   
                        <div
                            v-for="(stat, i) in data.stats ?? []"
                            :key="i"
                            v-show="stat.description"
                        >
                            <div v-html="stat.description" />
                        </div>
                    

                    <NextButton
                        v-if="data.button_label"
                        class="self-start"
                        :label="data.button_label"
                        :href="data.button_url || '#'"
                    />
                </div>
            </div>
        </div>
    </section>
</template>
