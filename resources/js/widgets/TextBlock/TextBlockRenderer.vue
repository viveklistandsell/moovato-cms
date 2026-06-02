<script setup lang="ts">
import { computed } from 'vue';

type Settings = {
    width: 'narrow' | 'wide' | 'full';
    alignment: 'left' | 'center' | 'right';
    background: 'none' | 'muted' | 'brand';
};

type Data = { heading?: string; body?: string };

const props = defineProps<{ settings: Settings; data: Data }>();

const widthClass = computed(() => {
    switch (props.settings.width) {
        case 'wide':
            return 'max-w-5xl';
        case 'full':
            return 'max-w-none';
        default:
            return 'max-w-3xl';
    }
});

const alignClass = computed(() => {
    switch (props.settings.alignment) {
        case 'center':
            return 'text-center';
        case 'right':
            return 'text-right';
        default:
            return 'text-left';
    }
});

const bgClass = computed(() => {
    switch (props.settings.background) {
        case 'muted':
            return 'bg-muted';
        case 'brand':
            return 'bg-primary/5';
        default:
            return '';
    }
});
</script>

<template>
    <section class="w-full section-py" :class="bgClass">
        <div class="container-xl">
            <div class="mx-auto" :class="[widthClass, alignClass]">
                <h2
                    v-if="data.heading"
                    class="mb-6 text-3xl font-bold tracking-tight sm:text-4xl"
                >
                    {{ data.heading }}
                </h2>
                <div
                    v-if="data.body"
                    class="prose prose-neutral dark:prose-invert prose-headings:font-bold prose-a:text-primary prose-img:rounded-md max-w-none"
                    v-html="data.body"
                />
            </div>
        </div>
    </section>
</template>
