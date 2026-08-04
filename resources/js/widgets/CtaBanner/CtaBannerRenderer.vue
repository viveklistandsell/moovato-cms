<script setup lang="ts">
import { CircleCheck } from 'lucide-vue-next';
import NextButton from '@/widgets/shared/NextButton.vue';

type Settings = {
    image_path: string | null;
    image_url: string | null;
};

type Data = {
    heading?: string;
    primary_label?: string;
    primary_url?: string;
    points?: string[];
    image_alt?: string;
};

defineProps<{ settings: Settings; data: Data }>();
</script>

<template>
    <section v-reveal class="mv-ctabanner">
        <img
            v-if="settings.image_url"
            :src="settings.image_url"
            :alt="data.image_alt || ''"
            class="mv-ctabanner-bg"
            loading="lazy"
            decoding="async"
        />
        <div class="mv-ctabanner-overlay" aria-hidden="true"></div>

        <div
            class="mv-ctabanner-inner container-xl grid gap-10 lg:grid-cols-2 lg:items-center"
        >
            <div>
                <h1 v-if="data.heading" class="mv-ctabanner-title">
                    {{ data.heading }}
                </h1>

                <div v-if="data.primary_label" class="mv-ctabanner-ctas">
                    <NextButton
                        :label="data.primary_label"
                        :href="data.primary_url || '#'"
                    />
                </div>
            </div>

            <ul v-if="data.points?.length" class="mv-ctabanner-points">
                <li v-for="(point, i) in data.points" :key="i">
                    <CircleCheck class="size-5" />
                    <span>{{ point }}</span>
                </li>
            </ul>
        </div>
    </section>
</template>
