<script setup lang="ts">
import {
    ArrowDownNarrowWide,
    ArrowUpDown,
    ArrowUpNarrowWide,
} from 'lucide-vue-next';

defineProps<{
    column: string;
    label: string;
    activeColumn: string | null;
    direction: 'asc' | 'desc';
}>();

defineEmits<{
    (e: 'sort', column: string): void;
}>();
</script>

<template>
    <button
        type="button"
        class="inline-flex items-center gap-1 text-left font-medium uppercase tracking-wide text-muted-foreground transition-colors hover:text-foreground"
        @click="$emit('sort', column)"
    >
        <span>{{ label }}</span>
        <ArrowUpDown
            v-if="activeColumn !== column"
            class="size-3 opacity-50"
        />
        <ArrowUpNarrowWide
            v-else-if="direction === 'asc'"
            class="size-3 text-foreground"
        />
        <ArrowDownNarrowWide v-else class="size-3 text-foreground" />
    </button>
</template>
