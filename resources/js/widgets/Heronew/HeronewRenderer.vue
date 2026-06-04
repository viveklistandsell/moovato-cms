<script setup lang="ts">
import { ArrowRight, Check, Star } from 'lucide-vue-next';
import { computed } from 'vue';
import WidgetIcon from '@/widgets/shared/WidgetIcon.vue';
import NextButton from '@/widgets/shared/NextButton.vue';
import HeroBackground from './HeroBackground.vue';

type Pill = { icon?: string; label?: string };

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
};

const props = defineProps<{ settings: Settings; data: Data }>();

const pillPositions = ['pill-3', 'pill-4'] as const;

const visiblePills = computed(() =>
    (props.data.pills ?? []).slice(2, 2 + pillPositions.length),
);
</script>

<template>
    <section class="mv-heronew">
        <!-- Animated, dependency-free background (CSS look-alike of the shader) -->
        <HeroBackground />

        <div class="hero">
            <div
                class="hero-inner container-xl grid grid-cols-1 items-center gap-y-10 lg:grid-cols-[60fr_40fr] lg:gap-x-10"
            >
                <!-- LEFT -->
                <div class="hero-content">
                    <div v-if="data.badge" class="hero-badge">
                        <Star class="ico" :size="13" /> {{ data.badge }}
                    </div>

                    <h1 class="hero-title">
                        {{ data.title_lead }}
                        <span v-if="data.title_highlight" class="hero-highlight"
                            ><span>{{ data.title_highlight }}</span></span
                        >
                        <br />
                        <em>{{ data.title_tail }}</em>
                    </h1>

                    <ul
                        v-if="data.features?.length"
                        class="hero-list grid grid-cols-1 gap-x-6 gap-y-3 sm:grid-cols-2"
                    >
                        <li v-for="(feature, i) in data.features" :key="i">
                            <span class="ico"><Check :size="12" /></span>
                            {{ feature }}
                        </li>
                    </ul>

                    <div class="hero-ctas">
                        <NextButton
                            v-if="data.primary_label"
                            :label="data.primary_label"
                            :href="data.primary_url || '#'"
                        />
                        <a
                            v-if="data.secondary_label"
                            :href="data.secondary_url || '#'"
                            class="link-cta"
                        >
                            {{ data.secondary_label }} <ArrowRight :size="12" />
                        </a>
                    </div>
                </div>

                <!-- RIGHT -->
                <div class="hero-visual">
                    <div class="hero-circle">
                        <img
                            v-if="settings.image_url"
                            :src="settings.image_url"
                            :alt="data.image_alt || ''"
                            class="mask1"
                            loading="lazy"
                            decoding="async"
                        />
                    </div>

                    <!-- Floating service pills -->
                    <div
                        v-for="(pill, i) in visiblePills"
                        :key="i"
                        class="pill"
                        :class="pillPositions[i]"
                    >
                        <WidgetIcon
                            :name="pill.icon"
                            fallback="Box"
                            class="ico size-[13px]"
                        />
                        {{ pill.label }}
                    </div>

                    <!-- Trust strip -->
                    <div
                        v-if="data.trust_title || data.avatars?.length"
                        class="trust-strip"
                    >
                        <div v-if="data.avatars?.length" class="avatars">
                            <div
                                v-for="(avatar, i) in data.avatars"
                                :key="i"
                                class="avatar"
                            >
                                {{ avatar }}
                            </div>
                        </div>
                        <div class="trust-strip-text">
                            <strong>{{ data.trust_title }}</strong>
                            <span>{{ data.trust_subtitle }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</template>
