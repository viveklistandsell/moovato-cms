<script setup lang="ts">
import { CircleCheck } from 'lucide-vue-next';
import { computed, ref } from 'vue';

type Settings = {
    read_more_enabled?: boolean;
};

type Data = {
    heading?: string;
    subheading?: string;
    body?: string;
    intro?: string;
    points?: string[];
    outro?: string;
};

const props = defineProps<{ settings: Settings; data: Data }>();

const isExpanded = ref(false);
const isCollapsible = computed(() => Boolean(props.settings.read_more_enabled));
</script>

<template>
    <section v-reveal class="mv-textcols section-py">
        <div
            class="mv-textcols__collapse"
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
    </section>
</template>
