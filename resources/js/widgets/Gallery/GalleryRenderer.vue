<script setup lang="ts">
import { computed } from 'vue';

type GalleryImage = { path?: string; url?: string };

type Settings = {
    layout: 'grid' | 'masonry' | 'carousel';
    columns: 2 | 3 | 4;
    gap: 'sm' | 'md' | 'lg';
    images?: GalleryImage[];
};

type Data = {
    heading?: string;
    description?: string;
    captions?: Record<string, string>;
};

const props = defineProps<{ settings: Settings; data: Data }>();

const images = computed<GalleryImage[]>(() => props.settings.images ?? []);

const gapClass = computed(() => {
    switch (props.settings.gap) {
        case 'sm':
            return 'gap-2';
        case 'lg':
            return 'gap-6';
        default:
            return 'gap-4';
    }
});

const colsClass = computed(() => {
    switch (props.settings.columns) {
        case 2:
            return 'sm:grid-cols-2';
        case 4:
            return 'sm:grid-cols-2 lg:grid-cols-4';
        default:
            return 'sm:grid-cols-2 lg:grid-cols-3';
    }
});

function caption(i: number): string | undefined {
    return props.data.captions?.[String(i)];
}
</script>

<template>
    <section v-if="images.length > 0" v-reveal class="w-full section-py">
        <div class="container-xl">
            <div v-if="data.heading || data.description" class="mb-10 text-center">
                <h2 v-if="data.heading" class="mv-section-heading">
                    {{ data.heading }}
                </h2>
                <div
                    v-if="data.description"
                    class="mv-rte mx-auto mt-4 max-w-2xl text-base leading-relaxed text-[var(--slate)]"
                    v-html="data.description"
                />
            </div>

            <div
                v-if="settings.layout === 'carousel'"
                class="flex snap-x snap-mandatory overflow-x-auto pb-4"
                :class="gapClass"
            >
                <figure
                    v-for="(img, i) in images"
                    :key="i"
                    class="min-w-[280px] snap-start"
                >
                    <img
                        :src="img.url"
                        :alt="caption(i) ?? ''"
                        class="aspect-square w-full rounded-lg object-cover"
                    />
                    <figcaption
                        v-if="caption(i)"
                        class="mt-2 text-center text-sm text-muted-foreground"
                    >
                        {{ caption(i) }}
                    </figcaption>
                </figure>
            </div>

            <div
                v-else-if="settings.layout === 'masonry'"
                class="columns-2 lg:columns-3"
                :class="gapClass"
            >
                <figure
                    v-for="(img, i) in images"
                    :key="i"
                    class="mb-4 break-inside-avoid"
                >
                    <img
                        :src="img.url"
                        :alt="caption(i) ?? ''"
                        class="w-full rounded-lg object-cover"
                    />
                    <figcaption
                        v-if="caption(i)"
                        class="mt-2 text-sm text-muted-foreground"
                    >
                        {{ caption(i) }}
                    </figcaption>
                </figure>
            </div>

            <div v-else class="grid grid-cols-1" :class="[gapClass, colsClass]">
                <figure v-for="(img, i) in images" :key="i">
                    <img
                        :src="img.url"
                        :alt="caption(i) ?? ''"
                        class="aspect-square w-full rounded-lg object-cover"
                    />
                    <figcaption
                        v-if="caption(i)"
                        class="mt-2 text-center text-sm text-muted-foreground"
                    >
                        {{ caption(i) }}
                    </figcaption>
                </figure>
            </div>
        </div>
    </section>
</template>
