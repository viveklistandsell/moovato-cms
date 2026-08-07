<script setup lang="ts">
import { Image as ImageIcon } from 'lucide-vue-next';
import WidgetIcon from '@/widgets/shared/WidgetIcon.vue';

type Card = { icon?: string; title?: string; description?: string };

type Settings = {
    image_path: string | null;
    image_url: string | null;
};

type Data = {
    eyebrow?: string;
    heading?: string;
    subheading?: string;
    image_alt?: string;
    cards?: Card[];
};

defineProps<{ settings: Settings; data: Data }>();
</script>

<template>
    <section v-reveal class="mv-whychoose section-py">
        <div
            class="container-xl grid grid-cols-1 gap-10 lg:grid-cols-2 lg:gap-x-10"
        >
            <div class="wc-sticky lg:sticky lg:top-24 lg:self-start">
                <span v-if="data.eyebrow" class="wc-eyebrow">
                    {{ data.eyebrow }}
                </span>
                <h2
                    v-if="data.heading"
                    class="mt-5 mv-section-heading"
                >
                    {{ data.heading }}
                </h2>
                <p
                    v-if="data.subheading"
                    class="mt-4 max-w-md text-base text-[var(--slate)]"
                >
                    {{ data.subheading }}
                </p>

                <div class="wc-image mt-8">
                    <img
                        v-if="settings.image_url"
                        :src="settings.image_url"
                        :alt="data.image_alt || ''"
                        loading="lazy"
                        decoding="async"
                    />
                    <div v-else class="wc-image-placeholder">
                        <ImageIcon class="size-10" />
                    </div>
                </div>
            </div>

            <ul class="wc-stack">
                <li
                    v-for="(card, i) in data.cards ?? []"
                    :key="i"
                    class="wc-card rounded-3xl border border-black/5 bg-white p-7 sm:p-8"
                    :style="{ '--i': i }"
                >
                    <div class="wc-card-head">
                        <span class="wc-num">{{
                            String(i + 1).padStart(2, '0')
                        }}</span>
                        <span class="wc-icon">
                            <WidgetIcon
                                :name="card.icon"
                                fallback="BadgeCheck"
                                class="size-6"
                            />
                        </span>
                    </div>
                    <h3 v-if="card.title" class="mt-5 text-xl font-semibold">
                        {{ card.title }}
                    </h3>
                    <div
                        v-if="card.description"
                        class="mv-rte mt-3 text-[15px] leading-relaxed text-[var(--slate)]"
                        v-html="card.description"
                    />
                </li>
            </ul>
        </div>
    </section>
</template>
