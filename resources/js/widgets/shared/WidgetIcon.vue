<script setup lang="ts">
import * as Icons from 'lucide-vue-next';
import { computed, type FunctionalComponent } from 'vue';
import { getIcon } from '@/lib/iconMap';

const props = defineProps<{
    name?: string | null;
    fallback?: string;
    class?: string;
}>();

/**
 * Resolve the icon at render time so widget editors can store any icon
 * reference as plain string in the DB. Two conventions are accepted:
 *
 *   1. The Tabler-style keys used by the admin's icon picker (e.g.
 *      `ti ti-car`). These are looked up through {@link getIcon} which
 *      maps them back to lucide-vue-next components.
 *   2. Raw lucide-vue-next PascalCase names (e.g. `Boxes`, `Building2`)
 *      — how seed data and older widgets store icons.
 *
 * Falls back to `props.fallback` (also passed through both resolutions)
 * if neither lookup finds a match. Returns null if all avenues fail so
 * the template can hide the wrapper instead of rendering a broken icon.
 */
const Resolved = computed<FunctionalComponent | null>(() => {
    const resolve = (raw: string | null | undefined): FunctionalComponent | null => {
        if (!raw) return null;
        // Try the admin picker convention first (ti ti-*).
        const mapped = getIcon(raw);
        if (mapped) return mapped as unknown as FunctionalComponent;
        // Fall through to a direct lucide lookup for PascalCase seed data.
        const direct = (Icons as unknown as Record<string, FunctionalComponent>)[raw];
        return direct ?? null;
    };
    return resolve(props.name) ?? resolve(props.fallback);
});
</script>

<template>
    <component
        :is="Resolved"
        v-if="Resolved"
        :class="props.class ?? 'size-5'"
    />
</template>
