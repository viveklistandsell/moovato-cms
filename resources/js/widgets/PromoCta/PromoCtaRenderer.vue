<script setup lang="ts">
import { Check } from 'lucide-vue-next';
import { computed } from 'vue';
import NextButton from '@/widgets/shared/NextButton.vue';

type Data = {
    heading?: string;
    features?: string[];
    button_label?: string;
    button_url?: string;
    badge_number?: string;
    badge_unit?: string;
    badge_label?: string;
    call_label?: string;
    phone?: string;
};

const props = defineProps<{ settings: Record<string, unknown>; data: Data }>();

const buildings = [
    60, 38, 84, 50, 110, 44, 72, 30, 96, 56, 40, 120, 64, 48, 88, 34, 76, 52,
    104, 42, 68, 58, 92, 36, 80, 46, 116, 62, 50, 100, 40, 74, 54, 86,
];
const step = 1200 / buildings.length;

const telHref = computed(
    () => 'tel:' + (props.data.phone ?? '').replace(/[^0-9+]/g, ''),
);
</script>

<template>
    <section v-reveal class="mv-promocta section-py">
        <div class="container-xl">
            <div class="mv-promocta-card">
                <div
                    class="mv-promocta-inner grid grid-cols-1 items-center gap-10 lg:grid-cols-[1.4fr_1fr] lg:gap-x-10"
                >
                    <div>
                        <h2 v-if="data.heading" class="mv-promocta-heading">
                            {{ data.heading }}
                        </h2>

                        <ul
                            v-if="data.features?.length"
                            class="mt-7 grid grid-cols-1 gap-x-8 gap-y-4 sm:grid-cols-2"
                        >
                            <li
                                v-for="(feature, i) in data.features"
                                :key="i"
                                class="mv-promocta-feature"
                            >
                                <span class="mv-promocta-check">
                                    <Check class="size-4" />
                                </span>
                                {{ feature }}
                            </li>
                        </ul>

                        <NextButton
                            v-if="data.button_label"
                            class="mt-8"
                            :label="data.button_label"
                            :href="data.button_url || '#'"
                        />
                    </div>

                    <div class="mv-promocta-call">
                        <div class="mv-promocta-badge">
                            <span class="mv-promocta-badge-num">{{
                                data.badge_number || '24'
                            }}</span>
                            <span class="mv-promocta-badge-stack">
                                <span class="mv-promocta-badge-unit">{{
                                    data.badge_unit
                                }}</span>
                                <span class="mv-promocta-badge-label">{{
                                    data.badge_label
                                }}</span>
                            </span>
                        </div>
                        <p
                            v-if="data.call_label"
                            class="mv-promocta-call-label"
                        >
                            {{ data.call_label }}
                        </p>
                        <a
                            v-if="data.phone"
                            :href="telHref"
                            class="mv-promocta-phone"
                        >
                            {{ data.phone }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>
</template>
