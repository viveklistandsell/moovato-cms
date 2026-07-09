<script setup lang="ts">
import type { HTMLAttributes } from 'vue';

defineOptions({
    inheritAttrs: false,
});

type Props = {
    className?: HTMLAttributes['class'];
};

defineProps<Props>();

const logoSrc = '/storage/media/2026/07/01KWGP5M385VMJMZRHR5Z8C9JB.svg';
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
