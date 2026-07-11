<script setup lang="ts">
import type { HTMLAttributes } from 'vue';

defineOptions({
    inheritAttrs: false,
});

type Props = {
    className?: HTMLAttributes['class'];
};

defineProps<Props>();

const logoSrc = '/storage/media/2026/06/01KTP29F3DRY13P1KQG0F45YAX.svg';
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
