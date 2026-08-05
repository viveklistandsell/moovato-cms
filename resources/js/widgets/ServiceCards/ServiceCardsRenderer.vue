<script setup lang="ts">
import { computed } from 'vue';
import WidgetIcon from '@/widgets/shared/WidgetIcon.vue';

type Item = { icon?: string; title?: string; description?: string };

type Settings = {
    columns: 2 | 3 | 4;
};

type Data = {
    eyebrow?: string;
    heading?: string;
    items?: Item[];
};

const props = defineProps<{ settings: Settings; data: Data }>();

const colsClass = computed(() => {
    switch (props.settings.columns) {
        case 2:
            return 'md:grid-cols-2';
        case 4:
            return 'md:grid-cols-2 xl:grid-cols-4';
        default:
            return 'md:grid-cols-2 xl:grid-cols-3';
    }
});
</script>

<template>
    <section v-reveal class="mv-services section-py">
        <div class="mv-services__wrap">
            <div class="mv-services__lines" aria-hidden="true">
                <svg
                    class="mv-services__lines-svg"
                    viewBox="0 0 1360 400"
                    preserveAspectRatio="none"
                >
                    <path
                        class="mv-services__line"
                        d="M0,90 C400,90 960,50 1360,50"
                    />
                    <path
                        class="mv-services__line"
                        d="M0,170 C400,170 960,140 1360,140"
                    />
                    <path
                        class="mv-services__line"
                        d="M0,250 C400,250 960,290 1360,290"
                    />
                    <path
                        class="mv-services__line"
                        d="M0,330 C400,330 960,360 1360,360"
                    />
                    <path
                        class="mv-services__line-pulse"
                        pathLength="100"
                        style="--d: 0s"
                        d="M0,90 C400,90 960,50 1360,50"
                    />
                    <path
                        class="mv-services__line-pulse"
                        pathLength="100"
                        style="--d: 1.6s"
                        d="M0,170 C400,170 960,140 1360,140"
                    />
                    <path
                        class="mv-services__line-pulse"
                        pathLength="100"
                        style="--d: 3.2s"
                        d="M0,250 C400,250 960,290 1360,290"
                    />
                    <path
                        class="mv-services__line-pulse"
                        pathLength="100"
                        style="--d: 4.8s"
                        d="M0,330 C400,330 960,360 1360,360"
                    />
                </svg>
            </div>

            <div class="relative z-10 container-xxl">
                <div class="mv-services__box "> 
                    <div class="flex flex-col items-center">
                        <span v-if="data.eyebrow" class="mv-services__eyebrow">
                            {{ data.eyebrow }}
                        </span>
                        <h2 v-if="data.heading" class="mv-services__heading mv-section-heading">
                            {{ data.heading }}
                        </h2>
                    </div>

                    <div
                        class="mv-services__grid grid grid-cols-1 gap-4 p-5"
                        :class="colsClass"
                    >
                        <article
                            v-for="(item, i) in data.items ?? []"
                            :key="i"
                            class="mv-services__card"
                        >
                            <div class="mv-services__hover" aria-hidden="true">
                                <span class="mv-services__hover-bg" />
                                <span class="mv-services__hover-bg" />
                                <span class="mv-services__hover-bg" />
                                <span class="mv-services__hover-bg" />
                            </div>

                            <div class="mv-services__content">
                                <div class="mv-services__iconbox">
                                    <span class="mv-services__icon">
                                        <WidgetIcon
                                            :name="item.icon"
                                            fallback="Box"
                                            class="size-8"
                                        />
                                    </span>
                                </div>

                                <h3
                                    v-if="item.title"
                                    class="mv-services__title"
                                >
                                    {{ item.title }}
                                </h3>
                                <div
                                    v-if="item.description"
                                    class="mv-services__desc"
                                    v-html="item.description"
                                />
                            </div>

                            <span class="mv-services__btn" aria-hidden="true">
                                <svg
                                    viewBox="0 0 24 24"
                                    fill="none"
                                    stroke="currentColor"
                                    stroke-width="2.2"
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                >
                                    <path d="M5 12h14M13 6l6 6-6 6" />
                                </svg>
                            </span>
                        </article>
                    </div>
                </div>
            </div>
        </div>
    </section>
</template>
