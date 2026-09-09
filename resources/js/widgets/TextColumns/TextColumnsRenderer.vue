<script setup lang="ts">
import {
    ChevronDown,
    CircleCheck,
    Image as ImageIcon,
} from 'lucide-vue-next';
import { computed, ref } from 'vue';

type Settings = {
    read_more_enabled?: boolean;
    image_card_enabled?: boolean;
    image_card_read_more_enabled?: boolean;
    image_path?: string | null;
    image_url?: string | null;
};

type Data = {
    heading?: string;
    subheading?: string;
    body?: string;
    intro?: string;
    points?: string[];
    outro?: string;
    image_alt?: string;
    image_card_heading?: string;
    image_card_body?: string;
};

const props = defineProps<{ settings: Settings; data: Data }>();

const isExpanded = ref(false);
const isCollapsible = computed(() => Boolean(props.settings.read_more_enabled));

const isCardExpanded = ref(false);
const isCardCollapsible = computed(() =>
    Boolean(props.settings.image_card_read_more_enabled),
);
</script>

<template>
    <section v-reveal class="mv-textcols ">
        <div
            class="mv-textcols__collapse section-py"
            :class="{ 'is-collapsed': isCollapsible && !isExpanded }"
        >
            <div class="container-xl grid gap-10 lg:grid-cols-2 lg:gap-x-10">
                <div>
                    <h2
                        v-if="data.heading"
                        class="text-[var(--midnight)] mv-section-heading"
                    >
                        {{ data.heading }}
                    </h2>
                    <h3
                        v-if="data.subheading"
                        class="mt-8 text-lg font-semibold text-[var(--midnight)]"
                    >
                        {{ data.subheading }}
                    </h3>
                    <div
                        v-if="data.body"
                        class="mv-rte mt-4 text-base leading-relaxed text-[var(--slate)]"
                        v-html="data.body"
                    />
                </div>

                <div class="lg:pt-2">
                    <p
                        v-if="data.intro"
                        class="text-lg font-semibold text-[var(--midnight)]"
                    >
                        {{ data.intro }}
                    </p>
                    <ul v-if="data.points?.length" class="mt-6 space-y-4">
                        <li
                            v-for="(point, i) in data.points"
                            :key="i"
                            class="flex items-center gap-3 font-semibold text-[var(--midnight)]"
                        >
                            <CircleCheck
                                class="size-5 shrink-0 text-[var(--orange)]"
                            />
                            <span>{{ point }}</span>
                        </li>
                    </ul>
                    <p
                        v-if="data.outro"
                        class="mt-6 text-base leading-relaxed text-[var(--slate)]"
                    >
                        {{ data.outro }}
                    </p>
                </div>
            </div>
        </div>

        <div v-if="isCollapsible" class="container-xl mt-6 text-center">
            <button
                type="button"
                class="mv-textcols__toggle"
                :aria-expanded="isExpanded"
                @click="isExpanded = !isExpanded"
            >
                {{ isExpanded ? 'Weniger anzeigen' : 'Mehr lesen' }}
            </button>
        </div>

        <div v-if="settings.image_card_enabled" class="new-collapsable-section">
            <div class="mv-textcols__card">
                <span
                    v-for="n in 4"
                    :key="n"
                    class="mv-textcols__card-ring"
                    aria-hidden="true"
                />

                <div
                    class="relative grid grid-cols-1 gap-10 lg:grid-cols-[1fr_1.3fr] lg:items-start lg:gap-12"
                >
                    <div class="mv-textcols__card-media">
                        <img
                            v-if="settings.image_url"
                            :src="settings.image_url"
                            :alt="data.image_alt || ''"
                            loading="lazy"
                            decoding="async"
                        />
                        <div v-else class="mv-textcols__card-placeholder">
                            <ImageIcon class="size-10" />
                        </div>
                    </div>

                    <div>
                        <h2
                            v-if="data.image_card_heading"
                            class="text-[var(--midnight)] mv-section-heading"
                        >
                            {{ data.image_card_heading }}
                        </h2>
                        <div
                            class="mv-textcols__card-collapse"
                            :class="{ 'is-collapsed': isCardCollapsible && !isCardExpanded }"
                        >
                            <div
                                v-if="data.image_card_body"
                                class="mv-rte mt-4 text-base leading-relaxed text-[var(--slate)]"
                                v-html="data.image_card_body"
                            />
                        </div>
                    </div>
                </div>

                <div
                    v-if="isCardCollapsible"
                    class="relative mt-8 text-center lg:mt-10"
                >
                    <button
                        type="button"
                        class="mv-textcols__card-toggle"
                        :aria-expanded="isCardExpanded"
                        @click="isCardExpanded = !isCardExpanded"
                    >
                        {{ isCardExpanded ? 'Weniger anzeigen' : 'Mehr anzeigen' }}
                        <ChevronDown
                            class="size-4 transition-transform duration-300"
                            :class="{ 'rotate-180': isCardExpanded }"
                        />
                    </button>
                </div>
            </div>
        </div>
    </section>
</template>
