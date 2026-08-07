<script setup lang="ts">
import { CircleCheck, Image as ImageIcon } from 'lucide-vue-next';
import NextButton from '@/widgets/shared/NextButton.vue';

type Settings = {
    image_path: string | null;
    image_url: string | null;
    image_2_path: string | null;
    image_2_url: string | null;
    founder_path: string | null;
    founder_url: string | null;
};

type Data = {
    eyebrow?: string;
    heading?: string;
    heading_accent?: string;
    body?: string;
    experience_value?: string;
    experience_label?: string;
    image_alt?: string;
    image_2_alt?: string;
    points?: string[];
    button_label?: string;
    button_url?: string;
    founder_name?: string;
    founder_role?: string;
    founder_alt?: string;
};

defineProps<{ settings: Settings; data: Data }>();
</script>

<template>
    <section v-reveal class="mv-aboutexp section-py">
        <div
            class="container-xl grid gap-10 lg:grid-cols-2 lg:items-center lg:gap-x-10"
        >
            <div class="ax-media">
                <div class="ax-photo ax-photo--main">
                    <img
                        v-if="settings.image_url"
                        :src="settings.image_url"
                        :alt="data.image_alt || ''"
                        loading="lazy"
                        decoding="async"
                    />
                    <div v-else class="ax-ph">
                        <ImageIcon class="size-10" />
                    </div>
                </div>
                <div class="ax-photo ax-photo--sub">
                    <img
                        v-if="settings.image_2_url"
                        :src="settings.image_2_url"
                        :alt="data.image_2_alt || ''"
                        loading="lazy"
                        decoding="async"
                    />
                    <div v-else class="ax-ph">
                        <ImageIcon class="size-8" />
                    </div>
                </div>
                <div
                    v-if="data.experience_value"
                    class="ax-exp"
                    aria-hidden="true"
                >
                    <span class="ax-exp-value">{{ data.experience_value }}</span>
                    <span class="ax-exp-label">{{ data.experience_label }}</span>
                </div>
            </div>

            <div>
                <span v-if="data.eyebrow" class="mv-aboutexp-eyebrow">
                    {{ data.eyebrow }}
                </span>
                <h2
                    v-if="data.heading || data.heading_accent"
                    class="mt-5 text-[var(--midnight)] mv-section-heading"
                >
                    {{ data.heading }}
                    <span v-if="data.heading_accent" class="text-[var(--orange)]">
                        {{ data.heading_accent }}
                    </span>
                </h2>
                <p
                    v-if="data.body"
                    class="mt-6 max-w-xl text-base leading-relaxed text-[var(--slate)]"
                >
                    {{ data.body }}
                </p>

                <ul v-if="data.points?.length" class="mt-8 space-y-4">
                    <li
                        v-for="(point, i) in data.points"
                        :key="i"
                        class="flex items-center gap-3 text-[var(--midnight)]"
                    >
                        <CircleCheck
                            class="size-5 shrink-0 text-[var(--orange)]"
                        />
                        <span>{{ point }}</span>
                    </li>
                </ul>

                <div class="mt-10 flex flex-col sm:flex-row flex-wrap items-center gap-6">
                    <NextButton
                        v-if="data.button_label"
                        :label="data.button_label"
                        :href="data.button_url || '#'"
                    />
                    <div
                        v-if="data.founder_name"
                        class="flex items-center gap-3"
                    >
                        <span class="leading-tight">
                            <span
                                class="block font-semibold text-[var(--midnight)]"
                            >
                                {{ data.founder_name }}
                            </span>
                            <span
                                v-if="data.founder_role"
                                class="block text-sm text-[var(--slate)]"
                            >
                                {{ data.founder_role }}
                            </span>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </section>
</template>
