<script setup lang="ts">
import { ChevronRight, CircleCheck, Image as ImageIcon } from 'lucide-vue-next';
import NextButton from '@/widgets/shared/NextButton.vue';

type Settings = {
    image_path: string | null;
    image_url: string | null;
    image_side: 'left' | 'right';
    marker_style: 'check' | 'chevron';
    heading_style?: 'bold' | 'italic';
};

type Data = {
    eyebrow?: string;
    heading?: string;
    body?: string;
    image_alt?: string;
    points?: string[];
    button_label?: string;
    button_url?: string;
};

defineProps<{ settings: Settings; data: Data }>();
</script>

<template>
    <section
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
                    class="text-3xl font-bold tracking-tight text-[var(--midnight)] sm:text-4xl"
                    :class="[
                        data.eyebrow ? 'mt-5' : '',
                        settings.heading_style === 'italic' ? 'italic' : '',
                    ]"
                >
                    {{ data.heading }}
                </h2>
                <p
                    v-if="data.body"
                    class="mt-5 max-w-xl text-base leading-relaxed whitespace-pre-line text-[var(--slate)]"
                >
                    {{ data.body }}
                </p>

                <ul v-if="data.points?.length" class="mt-7 space-y-4">
                    <li
                        v-for="(point, i) in data.points"
                        :key="i"
                        class="flex items-center gap-3 font-medium text-[var(--midnight)]"
                    >
                        <CircleCheck
                            v-if="settings.marker_style === 'check'"
                            class="size-5 shrink-0 text-[var(--orange)]"
                        />
                        <span v-else class="mv-splitmedia-chevron">
                            <ChevronRight class="size-3.5" />
                        </span>
                        <span>{{ point }}</span>
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
