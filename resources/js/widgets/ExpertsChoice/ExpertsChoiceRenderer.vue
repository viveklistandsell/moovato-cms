<script setup lang="ts">
import { ArrowRight, ChevronRight } from 'lucide-vue-next';
import WidgetIcon from '@/widgets/shared/WidgetIcon.vue';
import NextButton from '@/widgets/shared/NextButton.vue';

type Card = { icon?: string; title?: string; description?: string };

type Data = {
    eyebrow?: string;
    heading?: string;
    body?: string;
    points?: string[];
    button_label?: string;
    button_url?: string;
    since_label?: string;
    cards?: Card[];
};

defineProps<{ data: Data }>();
</script>

<template>
    <section class="mv-experts section-py">
        <div
            class="container-xl grid gap-10 lg:grid-cols-2 lg:items-center lg:gap-12"
        >
            <div class="ec-card">
                <span
                    v-if="data.since_label"
                    class="ec-since"
                    aria-hidden="true"
                >
                    {{ data.since_label }}
                </span>

                <div class="ec-card-body">
                    <span v-if="data.eyebrow" class="ec-eyebrow">
                        {{ data.eyebrow }}
                    </span>
                    <h2 v-if="data.heading" class="ec-title">
                        {{ data.heading }}
                    </h2>
                    <p v-if="data.body" class="ec-body">{{ data.body }}</p>

                    <ul v-if="data.points?.length" class="ec-points">
                        <li v-for="(point, i) in data.points" :key="i">
                            <ChevronRight class="ec-point-ico" />
                            <span>{{ point }}</span>
                        </li>
                    </ul>

                    <NextButton
                        v-if="data.button_label"
                        class="mt-7"
                        :label="data.button_label"
                        :href="data.button_url || '#'"
                    />
                </div>
            </div>

            <ul class="grid grid-cols-1 gap-x-10 gap-y-10 sm:grid-cols-2">
                <li
                    v-for="(card, i) in data.cards ?? []"
                    :key="i"
                    class="ec-feature"
                >
                    <span class="ec-feature-ico">
                        <WidgetIcon
                            :name="card.icon"
                            fallback="BadgeCheck"
                            class="size-7"
                        />
                    </span>
                    <h3 v-if="card.title" class="ec-feature-title">
                        {{ card.title }}
                    </h3>
                    <div
                        v-if="card.description"
                        class="mv-rte ec-feature-desc"
                        v-html="card.description"
                    />
                    <span class="ec-feature-arrow" aria-hidden="true">
                        <ArrowRight class="size-4" />
                    </span>
                </li>
            </ul>
        </div>
    </section>
</template>
