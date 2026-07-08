<script setup lang="ts">
import { ArrowDown } from 'lucide-vue-next';
import { computed } from 'vue';
import NextButton from '@/widgets/shared/NextButton.vue';
import WidgetIcon from '@/widgets/shared/WidgetIcon.vue';

type Item = {
    title?: string;
    url?: string;
    icon?: string;
    image_alt?: string;
    image_path?: string | null;
    image_url?: string | null;
};

type Settings = {
    bg_image_path?: string | null;
    bg_image_url?: string | null;
};

type Data = {
    eyebrow?: string;
    heading?: string;
    description?: string;
    step_label?: string;
    button_label?: string;
    button_url?: string;
    items?: Item[];
};

const props = defineProps<{ settings: Settings; data: Data }>();

const items = computed<Item[]>(() => props.data.items ?? []);

const bgStyle = computed(() =>
    props.settings.bg_image_url
        ? { backgroundImage: `url('${props.settings.bg_image_url}')` }
        : undefined,
);

function ordinal(i: number): string {
    return String(i + 1).padStart(2, '0');
}

// Each connector grows taller than the last so the cards descend a staircase.
function connectorStyle(i: number): Record<string, string> {
    return { '--wp-connector': `${20 + i * 40}px` };
}
</script>

<template>
    <section class="mv-workproc section-py" :style="bgStyle">
        <div class="container-xl">
            <div class="grid items-center gap-8 lg:grid-cols-2 lg:gap-12">
                <div>
                    <span v-if="data.eyebrow" class="mv-howitworks__eyebrow">{{
                        data.eyebrow
                    }}</span>
                    <h2 v-if="data.heading" class="mv-workproc__heading">
                        {{ data.heading }}
                    </h2>
                </div>
                <p v-if="data.description" class="mv-workproc__text">
                    {{ data.description }}
                </p>
            </div>

            <div
                class="mt-12 grid gap-x-6 gap-y-10 sm:grid-cols-2 xl:grid-cols-4"
            >
                <div
                    v-for="(item, i) in items"
                    :key="i"
                    class="mv-workproc__item"
                >
                    <div class="mv-workproc__step">
                        <span class="mv-workproc__step-text">
                            {{ data.step_label || 'Schritt' }} {{ ordinal(i) }}
                        </span>
                    </div>
                    <div
                        class="mv-workproc__border"
                        :style="connectorStyle(i)"
                        aria-hidden="true"
                    >
                        <span class="mv-workproc__border-icon">
                            <ArrowDown class="size-4" />
                        </span>
                    </div>
                    <h3 class="mv-workproc__title">{{ item.title }}</h3>
                    <div
                        v-if="item.icon || item.image_url"
                        class="mv-workproc__image"
                        :class="{ 'mv-workproc__image--icon': item.icon }"
                    >
                        <WidgetIcon
                            v-if="item.icon"
                            :name="item.icon"
                            class="mv-workproc__icon"
                        />
                        <img
                            v-else
                            :src="item.image_url!"
                            :alt="item.image_alt || ''"
                            loading="lazy"
                            decoding="async"
                        />
                    </div>
                    <div
                        v-if="i < items.length - 1"
                        class="mv-workproc__shape"
                        aria-hidden="true"
                    >
                        <img
                            src="/images/shapes/work-process-shape-1-1.png"
                            alt=""
                            loading="lazy"
                            decoding="async"
                        />
                    </div>
                </div>
            </div>
        </div>
    </section>
</template>
