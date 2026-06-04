<script setup lang="ts">
import { CircleCheck } from 'lucide-vue-next';
import NextButton from '@/widgets/shared/NextButton.vue';

type Crumb = { label?: string; url?: string };

type Settings = {
    image_path: string | null;
    image_url: string | null;
};

type Data = {
    crumbs?: Crumb[];
    heading?: string;
    primary_label?: string;
    primary_url?: string;
    points?: string[];
    image_alt?: string;
};

defineProps<{ settings: Settings; data: Data }>();
</script>

<template>
    <section class="mv-pagebanner">
        <img
            v-if="settings.image_url"
            :src="settings.image_url"
            :alt="data.image_alt || ''"
            class="mv-pagebanner-bg"
            loading="lazy"
            decoding="async"
        />
        <div class="mv-pagebanner-overlay" aria-hidden="true"></div>

        <div
            class="mv-pagebanner-inner container-xl grid gap-10 lg:grid-cols-2 lg:items-center"
        >
            <div>
                <nav
                    v-if="data.crumbs?.length"
                    class="mv-pagebanner-crumbs"
                    aria-label="Breadcrumb"
                >
                    <template v-for="(crumb, i) in data.crumbs" :key="i">
                        <a v-if="crumb.url" :href="crumb.url">{{
                            crumb.label
                        }}</a>
                        <span v-else class="is-current">{{ crumb.label }}</span>
                        <span
                            v-if="i < data.crumbs.length - 1"
                            class="sep"
                            aria-hidden="true"
                            >›</span
                        >
                    </template>
                </nav>

                <h1 v-if="data.heading" class="mv-pagebanner-title">
                    {{ data.heading }}
                </h1>

                <div v-if="data.primary_label" class="mv-pagebanner-ctas">
                    <NextButton
                        :label="data.primary_label"
                        :href="data.primary_url || '#'"
                    />
                </div>
            </div>

            <ul v-if="data.points?.length" class="mv-pagebanner-points">
                <li v-for="(point, i) in data.points" :key="i">
                    <CircleCheck class="size-5" />
                    <span>{{ point }}</span>
                </li>
            </ul>
        </div>
    </section>
</template>
