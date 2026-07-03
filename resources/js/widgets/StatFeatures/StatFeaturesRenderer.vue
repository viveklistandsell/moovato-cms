<script setup lang="ts">
import { Image as ImageIcon } from 'lucide-vue-next';

type Feature = { title?: string; description?: string };

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
    features?: Feature[];
};

defineProps<{ settings: Settings; data: Data }>();
</script>

<template>
    <section class="mv-statfeatures section-py">
        <div
            class="container-xl grid items-center gap-10 lg:grid-cols-2 lg:gap-12"
        >
            <div>
                <span v-if="data.eyebrow" class="mv-statfeatures-eyebrow">
                    {{ data.eyebrow }}
                </span>
                <h2
                    v-if="data.heading"
                    class="text-3xl font-bold tracking-tight text-[var(--midnight)] sm:text-4xl"
                    :class="data.eyebrow ? 'mt-4' : ''"
                >
                    {{ data.heading }}
                </h2>
                <p
                    v-if="data.body"
                    class="mt-5 max-w-xl text-base leading-relaxed text-[var(--slate)]"
                >
                    {{ data.body }}
                </p>

                <ol v-if="data.features?.length" class="mt-9 space-y-6">
                    <li
                        v-for="(feature, i) in data.features"
                        :key="i"
                        class="flex gap-4"
                    >
                        <span class="mv-sf-num">
                            {{ String(i + 1).padStart(2, '0') }}
                        </span>
                        <span>
                            <span
                                v-if="feature.title"
                                class="block text-lg font-semibold text-[var(--midnight)]"
                            >
                                {{ feature.title }}
                            </span>
                            <div
                                v-if="feature.description"
                                class="mv-rte mt-1 block text-[15px] leading-relaxed text-[var(--slate)]"
                                v-html="feature.description"
                            />
                        </span>
                    </li>
                </ol>
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
