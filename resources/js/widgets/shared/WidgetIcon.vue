<script setup lang="ts">
import * as Icons from 'lucide-vue-next';
import { computed, type FunctionalComponent } from 'vue';

const props = defineProps<{
    name?: string | null;
    fallback?: string;
    class?: string;
}>();

// Resolve at render time so widget editors can store any lucide icon name as
// plain string. Falls back to a sensible default if the named icon is missing.
const Resolved = computed<FunctionalComponent | null>(() => {
    const name = props.name && props.name !== '' ? props.name : props.fallback;
    if (!name) return null;
    const icon = (Icons as unknown as Record<string, FunctionalComponent>)[
        name
    ];
    return icon ?? null;
});
</script>

<template>
    <component
        :is="Resolved"
        v-if="Resolved"
        :class="props.class ?? 'size-5'"
    />
</template>
