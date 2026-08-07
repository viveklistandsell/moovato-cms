<script setup lang="ts">
import { ArrowUpRight, Check, X } from 'lucide-vue-next';
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
                <h2 v-if="data.heading" class="mv-pricing__title mv-section-heading">
                    {{ data.heading }}
                </h2>
            </div>

            <div class="grid grid-cols-1 gap-8 md:grid-cols-2 lg:grid-cols-3">
                <article
                    v-for="(plan, i) in data.plans ?? []"
                    :key="i"
                    class="mv-pricing__card"
                >
                    <div class="mv-pricing__card-shape" aria-hidden="true" />
                    <div class="mv-pricing__card-content">
                        <div class="mv-pricing__card-inner">
                            <span class="mv-pricing__number">{{
                                String(i + 1).padStart(2, '0')
                            }}</span>
                            <div class="mv-pricing__icon">
                                <WidgetIcon
                                    :name="plan.icon"
                                    fallback="Package"
                                    class="size-7"
                                />
                            </div>

                            <h3 class="mv-pricing__name">{{ plan.name }}</h3>
                            <div class="mv-pricing__price">
                                {{ plan.price }}
                            </div>

                            <ul class="mv-pricing__features">
                                <li
                                    v-for="(feature, fi) in plan.features ??
                                    []"
                                    :key="fi"
                                    :class="{ 'is-muted': !feature.included }"
                                >
                                    <Check v-if="feature.included" :size="18" />
                                    <X v-else :size="18" />
                                    <span>{{ feature.label }}</span>
                                </li>
                            </ul>

                            <a
                                :href="plan.cta_url || '#'"
                                class="mv-pricing__btn"
                            >
                                <span>{{ plan.cta_label }}</span>
                                <ArrowUpRight :size="16" />
                            </a>
                        </div>
                    </div>
                </article>
            </div>
        </div>
    </section>
</template>
