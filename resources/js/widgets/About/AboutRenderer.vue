<script setup lang="ts">
import { Check, Image as ImageIcon } from 'lucide-vue-next';
import NextButton from '@/widgets/shared/NextButton.vue';

type Settings = {
    image_path: string | null;
    image_url: string | null;
};

type Data = {
    eyebrow?: string;
    heading?: string;
    image_alt?: string;
    body?: string;
    badge?: string;
    features?: string[];
    trusted_label?: string;
    avatars?: string[];
    button_label?: string;
    button_url?: string;
};

defineProps<{ settings: Settings; data: Data }>();
</script>

<template>
    <section class="mv-about section-py">
        <div
            class="container-xl grid grid-cols-1 gap-16 lg:grid-cols-2 lg:items-center lg:gap-24"
        >
            <div class="mv-about-media">
                <div class="mv-about-image">
                    <img
                        v-if="settings.image_url"
                        :src="settings.image_url"
                        :alt="data.image_alt || ''"
                        loading="lazy"
                        decoding="async"
                    />
                    <div v-else class="mv-about-placeholder">
                        <ImageIcon class="size-10" />
                    </div>
                </div>

                <span v-if="data.badge" class="mv-about-badge">
                    {{ data.badge }}
                </span>

                <div
                    v-if="data.trusted_label || data.avatars?.length"
                    class="mv-about-trusted"
                >
                    <span class="mv-about-trusted-label">{{
                        data.trusted_label
                    }}</span>
                    <div v-if="data.avatars?.length" class="mv-about-avatars">
                        <span
                            v-for="(avatar, i) in data.avatars"
                            :key="i"
                            class="mv-about-avatar"
                            >{{ avatar }}</span
                        >
                    </div>
                </div>
            </div>

            <div>
                <span v-if="data.eyebrow" class="mv-about-eyebrow">
                    {{ data.eyebrow }}
                </span>
                <h2
                    v-if="data.heading"
                    class="mt-5 text-3xl font-bold tracking-tight text-[var(--midnight)] sm:text-4xl lg:text-5xl"
                >
                    {{ data.heading }}
                </h2>
                <p
                    v-if="data.body"
                    class="mt-6 max-w-xl text-base leading-relaxed text-[var(--slate)]"
                >
                    {{ data.body }}
                </p>

                <ul
                    v-if="data.features?.length"
                    class="mt-8 flex flex-wrap gap-3"
                >
                    <li
                        v-for="(feature, i) in data.features"
                        :key="i"
                        class="mv-about-feature"
                    >
                        <Check class="size-4" />
                        {{ feature }}
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
