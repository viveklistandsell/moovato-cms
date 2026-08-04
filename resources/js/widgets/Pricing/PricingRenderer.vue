<script setup lang="ts">
import { ArrowUpRight, Check } from 'lucide-vue-next';
import WidgetIcon from '@/widgets/shared/WidgetIcon.vue';

type Feature = { label?: string; included?: boolean };
type Plan = {
    name?: string;
    price?: string;
    icon?: string;
    cta_label?: string;
    cta_url?: string;
    features?: Feature[];
};

type Data = {
    eyebrow?: string;
    heading?: string;
    plans?: Plan[];
};

defineProps<{ settings: Record<string, unknown>; data: Data }>();
</script>

<template>
    <section v-reveal class="mv-pricing section-py">
        <div class="container-xl">
            <div class="mv-pricing__intro">
                <span v-if="data.eyebrow" class="mv-pricing__eyebrow">
                    {{ data.eyebrow }}
                </span>
                <h2 v-if="data.heading" class="mv-pricing__title">
                    {{ data.heading }}
                </h2>
            </div>

            <div class="grid grid-cols-1 gap-7 md:grid-cols-2 lg:grid-cols-3">
                <article
                    v-for="(plan, i) in data.plans ?? []"
                    :key="i"
                    class="mv-pricing__card"
                >
                    <div class="mv-pricing__head">
                        <div class="mv-pricing__head-text">
                            <h3 class="mv-pricing__name">{{ plan.name }}</h3>
                            <div class="mv-pricing__price">
                                {{ plan.price }}
                            </div>
                        </div>
                        <div class="mv-pricing__icon">
                            <WidgetIcon
                                :name="plan.icon"
                                fallback="Package"
                                class="size-8"
                            />
                        </div>
                    </div>

                    <ul class="mv-pricing__features">
                        <li
                            v-for="(feature, fi) in plan.features ?? []"
                            :key="fi"
                            :class="{ 'is-muted': !feature.included }"
                        >
                            <Check :size="18" />
                            <span>{{ feature.label }}</span>
                        </li>
                    </ul>

                    <div class="mv-pricing__foot">
                        <a :href="plan.cta_url || '#'" class="mv-pricing__btn">
                            {{ plan.cta_label }}
                            <ArrowUpRight :size="16" />
                        </a>
                    </div>
                </article>
            </div>
        </div>
    </section>
</template>
