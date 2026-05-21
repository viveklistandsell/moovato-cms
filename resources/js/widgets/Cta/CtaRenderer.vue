<script setup lang="ts">
import { computed } from 'vue';

type Settings = {
    variant: 'brand' | 'muted' | 'dark';
    alignment: 'left' | 'center';
};

type Data = {
    eyebrow?: string;
    title?: string;
    description?: string;
    primary_label?: string;
    primary_url?: string;
    secondary_label?: string;
    secondary_url?: string;
};

const props = defineProps<{ settings: Settings; data: Data }>();

const variantClass = computed(() => {
    switch (props.settings.variant) {
        case 'muted':
            return 'bg-muted text-foreground';
        case 'dark':
            return 'bg-foreground text-background';
        default:
            return 'bg-primary text-primary-foreground';
    }
});

const alignClass = computed(() =>
    props.settings.alignment === 'left' ? 'text-left items-start' : 'text-center items-center',
);
</script>

<template>
    <section class="w-full px-4 py-16 sm:px-6 sm:py-20 lg:px-8">
        <div
            class="mx-auto flex max-w-5xl flex-col gap-5 rounded-2xl px-8 py-12 shadow-lg sm:px-12 sm:py-16"
            :class="[variantClass, alignClass]"
        >
            <p v-if="data.eyebrow" class="text-xs font-semibold uppercase tracking-widest opacity-80">
                {{ data.eyebrow }}
            </p>
            <h2 v-if="data.title" class="text-3xl font-bold tracking-tight sm:text-4xl">
                {{ data.title }}
            </h2>
            <p v-if="data.description" class="max-w-2xl text-base opacity-90 sm:text-lg">
                {{ data.description }}
            </p>
            <div
                v-if="data.primary_label || data.secondary_label"
                class="flex flex-wrap gap-3"
                :class="settings.alignment === 'center' ? 'justify-center' : 'justify-start'"
            >
                <a
                    v-if="data.primary_label"
                    :href="data.primary_url || '#'"
                    class="inline-flex h-11 items-center rounded-md bg-background px-6 text-sm font-semibold text-foreground shadow-md transition hover:opacity-90"
                >
                    {{ data.primary_label }}
                </a>
                <a
                    v-if="data.secondary_label"
                    :href="data.secondary_url || '#'"
                    class="inline-flex h-11 items-center rounded-md border border-current/30 px-6 text-sm font-semibold transition hover:bg-background/10"
                >
                    {{ data.secondary_label }}
                </a>
            </div>
        </div>
    </section>
</template>
