<script setup lang="ts">
import { ArrowRight, Check } from 'lucide-vue-next';
import { computed } from 'vue';
import BestQualityBadge from '@/widgets/shared/BestQualityBadge.vue';
import WidgetIcon from '@/widgets/shared/WidgetIcon.vue';
import NextButton from '@/widgets/shared/NextButton.vue';
import HeroBackground from './HeroBackground.vue';

type Pill = { icon?: string; label?: string };
type TrustItem = { icon?: string; label?: string };

type Settings = {
    image_path: string | null;
    image_url: string | null;
};

type Data = {
    badge?: string;
    title_lead?: string;
    title_highlight?: string;
    title_tail?: string;
    image_alt?: string;
    features?: string[];
    primary_label?: string;
    primary_url?: string;
    secondary_label?: string;
    secondary_url?: string;
    pills?: Pill[];
    avatars?: string[];
    trust_title?: string;
    trust_subtitle?: string;
    title?: string;
    subtitle?: string;
    from_label?: string;
    from_placeholder?: string;
    to_label?: string;
    to_placeholder?: string;
    button_label?: string;
    button_url?: string;
    trust_items?: TrustItem[];
    rating_value?: string;
    rating_reviews?: string;
    rating_url?: string;
};

const props = defineProps<{ settings: Settings; data: Data }>();

const pillPositions = ['pill-3', 'pill-4'] as const;

const visiblePills = computed(() =>
    (props.data.pills ?? []).slice(2, 2 + pillPositions.length),
);
</script>

<template>
    <section v-reveal class="mv-heronew">
        <HeroBackground />

        <div class="hero">
            <div
                class="hero-inner container-xl grid grid-cols-1 items-center gap-y-10 lg:grid-cols-[65fr_35fr] lg:gap-x-10">
                <!-- LEFT -->
                <div class="hero-content">
                    <div v-if="data.badge" class="hero-badge">
                        {{ data.badge }}
                    </div>

                    <h1 class="hero-title">
                        {{ data.title_lead }}
                        <span v-if="data.title_highlight" class="hero-highlight"><span>{{ data.title_highlight
                                }}</span></span>
                        <br />
                        <em>{{ data.title_tail }}</em>
                    </h1>

                    <ul v-if="data.features?.length" class="hero-list grid grid-cols-1 gap-x-6 gap-y-3 sm:grid-cols-2">
                        <li v-for="(feature, i) in data.features" :key="i">
                            <span class="ico">
                                <Check :size="12" />
                            </span>
                            {{ feature }}
                        </li>
                    </ul>

                    <div class="hero-ctas">
                        <NextButton v-if="data.primary_label" :label="data.primary_label"
                            :href="data.primary_url || '#'" />
                        <a v-if="data.secondary_label" :href="data.secondary_url || '#'" class="link-cta">
                            {{ data.secondary_label }}
                            <ArrowRight :size="12" />
                        </a>
                    </div>
                </div>

                <!-- RIGHT -->
                <div class="hero-visual">
                    <div class="hero-circle">
                        <img v-if="settings.image_url" :src="settings.image_url" :alt="data.image_alt || ''"
                            class="mask1" loading="lazy" decoding="async" />
                    </div>

                    <!-- Floating service pills -->
                    <div v-for="(pill, i) in visiblePills" :key="i" class="pill" :class="pillPositions[i]">
                        <WidgetIcon :name="pill.icon" fallback="Box" class="ico size-[13px]" />
                        {{ pill.label }}
                    </div>

                    <!-- Trust strip -->
                    <div v-if="data.trust_title" class="trust-strip">
                        <BestQualityBadge class="trust-strip-badge" />
                        <div class="trust-strip-text">
                            <strong>{{ data.trust_title }}</strong>
                            <span>{{ data.trust_subtitle }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mv-heroquote hero-quote-row pb-3">
                <div class="container-xl">
                    <div class="hero-content">
                        <form class="hero-form" @submit.prevent>
                            <div class="hero-field">
                                <label>Ich ziehe um von</label>
                                <input type="text" placeholder="Ihre aktuelle Stadt oder Adresse" />
                            </div>
                            <div class="hero-field">
                                <label>Ich ziehe um nach</label>
                                <input type="text" placeholder="Ihr Zielland, Ihre Stadt oder Adresse" />
                            </div>
                            <a class="hero-submit" href="#">
                                Angebote erhalten
                            </a>
                        </form>

                        <ul class="hero-trust">
                            <li>
                                <WidgetIcon name="Globe" fallback="Circle" class="ico size-[18px]" />
                                <span>Netzwerk von <strong>700+</strong> Umzugsunternehmen</span>
                            </li>
                            <li class="sep" aria-hidden="true">|</li>
                            <li>
                                <WidgetIcon name="Truck" fallback="Circle" class="ico size-[18px]" />
                                <span><strong>200.000</strong> Jährliche Umzüge</span>
                            </li>
                            <li class="sep" aria-hidden="true">|</li>
                            <li class="hero-rating">
                                <strong>4,3</strong>
                                <span class="stars" aria-hidden="true">★★★★★</span>
                                <a href="#">785 Google Bewertungen</a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>
</template>
