<script setup lang="ts">
import { ChevronRight, CircleCheck, Image as ImageIcon } from 'lucide-vue-next';
import NextButton from '@/widgets/shared/NextButton.vue';

type Settings = {
    images: { path: string; url: string }[];
    image_side: 'left' | 'right';
    marker_style: 'check' | 'chevron';
};

type Data = {
    eyebrow?: string;
    heading?: string;
    body?: string;
    points?: string[];
    alts?: string[];
    button_label?: string;
    button_url?: string;
};

const props = defineProps<{ settings: Settings; data: Data }>();

function img(i: number): string | null {
    return props.settings.images?.[i]?.url ?? null;
}
</script>

<template>
    <section class="mv-collage section-py">
        <div
            class="container-xl grid items-center gap-10 lg:grid-cols-2 lg:gap-12"
        >
            <div
                class="grid aspect-square grid-cols-2 grid-rows-2 gap-4"
                :class="settings.image_side === 'right' ? 'lg:order-2' : ''"
            >
                <div class="mv-collage-slot">
                    <img
                        v-if="img(0)"
                        :src="img(0) as string"
                        :alt="data.alts?.[0] || ''"
                        loading="lazy"
                        decoding="async"
                    />
                    <div v-else class="mv-collage-ph"><ImageIcon class="size-8" /></div>
                </div>
                <div class="mv-collage-slot row-span-2">
                    <img
                        v-if="img(1)"
                        :src="img(1) as string"
                        :alt="data.alts?.[1] || ''"
                        loading="lazy"
                        decoding="async"
                    />
                    <div v-else class="mv-collage-ph"><ImageIcon class="size-8" /></div>
                </div>
                <div class="mv-collage-slot">
                    <img
                        v-if="img(2)"
                        :src="img(2) as string"
                        :alt="data.alts?.[2] || ''"
                        loading="lazy"
                        decoding="async"
                    />
                    <div v-else class="mv-collage-ph"><ImageIcon class="size-8" /></div>
                </div>
            </div>

            <div :class="settings.image_side === 'right' ? 'lg:order-1' : ''">
                <span v-if="data.eyebrow" class="mv-collage-eyebrow">
                    {{ data.eyebrow }}
                </span>
                <h2
                    v-if="data.heading"
                    class="text-3xl font-bold tracking-tight text-[var(--midnight)] sm:text-4xl"
                    :class="data.eyebrow ? 'mt-5' : ''"
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
                        <span v-else class="mv-collage-chevron">
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
    </section>
</template>
