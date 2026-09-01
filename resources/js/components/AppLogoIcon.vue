<script setup lang="ts">
import type { HTMLAttributes } from 'vue';

defineOptions({
    inheritAttrs: false,
});

type Props = {
    className?: HTMLAttributes['class'];
};

defineProps<Props>();

const logoSrc = '/storage/media/2026/08/01M11N36E3M82TSPV5YHV25T3R.svg';
</script>

<template>
    <img
        :src="logoSrc"
        alt="Moovato"
        :class="className"
        class="object-contain"
        v-bind="$attrs"
    />
</template>
