<script setup lang="ts">
import { ChevronDown, Image as ImageIcon } from 'lucide-vue-next';
import { computed, ref } from 'vue';

type Settings = {
    image_path?: string | null;
    image_url?: string | null;
    read_more_enabled?: boolean;
};

type Data = {
    heading?: string;
    body?: string;
    image_alt?: string;
};

const props = defineProps<{ settings: Settings; data: Data }>();

const isExpanded = ref(false);
const isCollapsible = computed(() => Boolean(props.settings.read_more_enabled));
</script>

<template>
    <section v-reveal class="mv-imagecard section-py">
        <div class="container-xl">
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
                            v-if="data.heading"
                            class="text-[var(--midnight)] mv-section-heading"
                        >
                            {{ data.heading }}
                        </h2>
                        <div
                            class="mv-textcols__card-collapse"
                            :class="{ 'is-collapsed': isCollapsible && !isExpanded }"
                        >
                            <div
                                v-if="data.body"
                                class="mv-rte mt-4 text-base leading-relaxed text-[var(--slate)]"
                                v-html="data.body"
                            />
                        </div>
                    </div>
                </div>

                <div
                    v-if="isCollapsible"
                    class="relative mt-8 text-center lg:mt-10"
                >
                    <button
                        type="button"
                        class="mv-textcols__card-toggle"
                        :aria-expanded="isExpanded"
                        @click="isExpanded = !isExpanded"
                    >
                        {{ isExpanded ? 'Weniger anzeigen' : 'Mehr anzeigen' }}
                        <ChevronDown
                            class="size-4 transition-transform duration-300"
                            :class="{ 'rotate-180': isExpanded }"
                        />
                    </button>
                </div>
            </div>
        </div>
    </section>
</template>
