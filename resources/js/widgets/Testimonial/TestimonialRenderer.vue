<script setup lang="ts">
import { Quote } from 'lucide-vue-next';
import { computed } from 'vue';

type Item = {
    quote?: string;
    author?: string;
    role?: string;
    avatar_url?: string | null;
};

type Settings = {
    layout: 'grid' | 'carousel' | 'single';
    columns: 1 | 2 | 3;
};

type Data = {
    heading?: string;
    items?: Item[];
};

const props = defineProps<{ settings: Settings; data: Data }>();

const items = computed<Item[]>(() => props.data.items ?? []);

const colsClass = computed(() => {
    if (props.settings.layout !== 'grid') return '';
    switch (props.settings.columns) {
        case 1:
            return 'grid-cols-1 max-w-2xl mx-auto';
        case 3:
            return 'sm:grid-cols-2 lg:grid-cols-3';
        default:
            return 'sm:grid-cols-2';
    }
});

const containerClass = computed(() => {
    if (props.settings.layout === 'single') {
        return 'flex items-center justify-center';
    }
    if (props.settings.layout === 'carousel') {
        return 'flex gap-6 overflow-x-auto snap-x snap-mandatory pb-4';
    }
    return 'grid grid-cols-1 gap-6';
});
</script>

<template>
    <section class="w-full bg-muted/30 section-py">
        <div class="container-xl">
            <h2
                v-if="data.heading"
                class="mb-10 text-center text-3xl font-bold tracking-tight sm:text-4xl"
            >
                {{ data.heading }}
            </h2>

            <div :class="[containerClass, colsClass]">
                <figure
                    v-for="(item, i) in items"
                    :key="i"
                    class="rounded-2xl border bg-card p-6 shadow-sm"
                    :class="
                        settings.layout === 'carousel'
                            ? 'min-w-[320px] snap-start'
                            : ''
                    "
                >
                    <Quote class="size-5 text-primary" />
                    <blockquote
                        class="mt-3 text-base leading-relaxed text-foreground"
                    >
                        {{ item.quote }}
                    </blockquote>
                    <figcaption class="mt-5 flex items-center gap-3">
                        <img
                            v-if="item.avatar_url"
                            :src="item.avatar_url"
                            :alt="item.author ?? ''"
                            class="size-10 rounded-full object-cover"
                        />
                        <div>
                            <div class="text-sm font-semibold">
                                {{ item.author }}
                            </div>
                            <div class="text-xs text-muted-foreground">
                                {{ item.role }}
                            </div>
                        </div>
                    </figcaption>
                </figure>
            </div>
        </div>
    </section>
</template>
