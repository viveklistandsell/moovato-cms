<script setup lang="ts">
import { ArrowRight } from 'lucide-vue-next';
import WidgetIcon from '@/widgets/shared/WidgetIcon.vue';

type Service = { icon?: string; label?: string };

type Settings = {
    dashboard_image_path?: string | null;
    dashboard_image_url?: string | null;
    reviews_image_path?: string | null;
    reviews_image_url?: string | null;
};

type Data = {
    promo_title?: string;
    promo_text?: string;
    promo_cta_label?: string;
    promo_cta_url?: string;
    dashboard_image_alt?: string;
    reviews_title?: string;
    reviews_text?: string;
    reviews_image_alt?: string;
    services_title?: string;
    services_text?: string;
    services_link_label?: string;
    services_link_url?: string;
    services?: Service[];
};

defineProps<{ settings: Settings; data: Data }>();
</script>

<template>
    <section v-reveal class="mv-dashpromo section-py bg-white">
        <div class="container-xl">
            <div class="mv-dashpromo-hero">
                <div class="mv-dashpromo-hero-inner">
                    <div class="mv-dashpromo-hero-glow" aria-hidden="true"></div>
                    <div class="mv-dashpromo-hero-content">
                        <span class="mv-dashpromo-eyebrow">
                            <span class="mv-dashpromo-eyebrow-dot" aria-hidden="true"></span>
                            Kundenportal
                        </span>
                        <h2 v-if="data.promo_title" class="mv-dashpromo-title">
                            {{ data.promo_title }}
                        </h2>
                        <p v-if="data.promo_text" class="mv-dashpromo-text">
                            {{ data.promo_text }}
                        </p>
                        <a v-if="data.promo_cta_label" :href="data.promo_cta_url || '#'" class="mv-dashpromo-cta">
                            {{ data.promo_cta_label }}
                            <span class="mv-dashpromo-btn-ico">
                                <ArrowRight :size="14" />
                            </span>
                        </a>
                    </div>
                    <div v-if="settings.dashboard_image_url" class="mv-dashpromo-hero-media">
                        <img :src="settings.dashboard_image_url" :alt="data.dashboard_image_alt || ''" loading="lazy"
                            decoding="async" />
                    </div>
                </div>
            </div>

            <div class="mt-6 grid grid-cols-1 gap-6 lg:grid-cols-2">
                <div class="mv-dashpromo-card">
                    <div class="mv-dashpromo-card-inner">
                        <div class="mv-dashpromo-card-body">
                            <h3 v-if="data.reviews_title" class="mv-dashpromo-card-title">
                                {{ data.reviews_title }}
                            </h3>
                            <p v-if="data.reviews_text" class="mv-dashpromo-text">
                                {{ data.reviews_text }}
                            </p>
                        </div>
                        <div v-if="settings.reviews_image_url" class="mv-dashpromo-card-media">
                            <img :src="settings.reviews_image_url" :alt="data.reviews_image_alt || ''" loading="lazy"
                                decoding="async" />
                        </div>
                    </div>
                </div>

                <div class="mv-dashpromo-card">
                    <div class="mv-dashpromo-card-inner">
                        <div class="mv-dashpromo-card-body">
                            <h3 v-if="data.services_title" class="mv-dashpromo-card-title">
                                {{ data.services_title }}
                            </h3>
                            <p v-if="data.services_text" class="mv-dashpromo-text">
                                {{ data.services_text }}
                            </p>
                            <a v-if="data.services_link_label" :href="data.services_link_url || '#'"
                                class="mv-dashpromo-link">
                                {{ data.services_link_label }}
                                <span class="mv-dashpromo-btn-ico">
                                    <ArrowRight :size="14" />
                                </span>
                            </a>
                        </div>
                        <ul v-if="(data.services ?? []).length" class="mv-dashpromo-services">
                            <li v-for="(service, i) in data.services" :key="i">
                                <span class="mv-dashpromo-service-ico">
                                    <WidgetIcon :name="service.icon" fallback="Box" class="size-4" />
                                </span>
                                {{ service.label }}
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>
</template>
 