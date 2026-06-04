<script setup lang="ts">
import { ChevronRight, CircleCheck, Image as ImageIcon } from 'lucide-vue-next';
import NextButton from '@/widgets/shared/NextButton.vue';

type Settings = {
    image_path: string | null;
    image_url: string | null;
    image_side: 'left' | 'right';
    marker_style: 'check' | 'chevron';
    card: boolean;
    heading_style?: 'bold' | 'italic';
    bg?: 'none' | 'tinted' | 'dark';
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
        class="mv-mediachecklist section-py"
        :class="{
            'is-dark': settings.bg === 'dark',
            'is-tinted': settings.bg === 'tinted',
        }"
    >
        <div class="container-xl">
            <div
                class="grid items-center gap-10 lg:grid-cols-2 lg:gap-12"
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
                            <span v-else class="mv-mc-chevron">
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
        </div>
    </section>
</template>
