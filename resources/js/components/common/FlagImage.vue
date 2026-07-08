<script setup lang="ts">
import { computed } from 'vue';

const props = withDefaults(
    defineProps<{
        code: string | null | undefined;
        size?: 'xs' | 'sm' | 'md' | 'lg';
    }>(),
    { size: 'sm' },
);

/**
 * A raw 2-letter ASCII code (case-insensitive) is treated as an ISO
 * country code — anything else (emoji, longer strings) renders as text
 * so nothing regresses for languages saved before the switch.
 */
const isoCode = computed<string | null>(() => {
    const v = (props.code ?? '').trim();
    if (v.length !== 2) return null;
    if (!/^[a-zA-Z]{2}$/.test(v)) return null;
    return v.toLowerCase();
});

const sizeClass = computed<string>(() => {
    switch (props.size) {
        case 'xs':
            return 'h-3 w-4';
        case 'md':
            return 'h-5 w-7';
        case 'lg':
            return 'h-7 w-10';
        default:
            return 'h-4 w-6';
    }
});

const emojiSize = computed<string>(() => {
    switch (props.size) {
        case 'xs':
            return 'text-xs leading-none';
        case 'md':
            return 'text-lg leading-none';
        case 'lg':
            return 'text-2xl leading-none';
        default:
            return 'text-base leading-none';
    }
});
</script>

<template>
    <img
        v-if="isoCode"
        :src="`/images/flags/${isoCode}.svg`"
        :alt="`${isoCode.toUpperCase()} flag`"
        :class="[sizeClass, 'inline-block rounded-[2px] object-cover shadow-sm ring-1 ring-black/5']"
    />
    <span
        v-else-if="code"
        :class="[emojiSize, 'inline-block']"
    >
        {{ code }}
    </span>
    <span v-else :class="[emojiSize, 'inline-block text-muted-foreground']">
        🏳️
    </span>
</template>
