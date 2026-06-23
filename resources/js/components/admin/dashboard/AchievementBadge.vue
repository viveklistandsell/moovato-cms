<script setup lang="ts">
/**
 * Small badge shown in the dashboard welcome strip. Looks like a
 * gentle gradient pill: emoji on the left, label on the right.
 * Tones picked to feel celebratory without being loud.
 */
type Tone = 'fire' | 'orange' | 'sky' | 'violet';

defineProps<{
    icon: string;
    label: string;
    tone: Tone;
}>();

const toneClasses: Record<Tone, string> = {
    fire: 'from-rose-500/20 to-orange-500/20 text-rose-700 ring-rose-500/30 dark:text-rose-300',
    orange:
        'from-[var(--orange)]/25 to-[var(--yellow-dark)]/25 text-[var(--orange)] ring-[var(--orange)]/30',
    sky: 'from-sky-500/20 to-cyan-500/20 text-sky-700 ring-sky-500/30 dark:text-sky-300',
    violet: 'from-violet-500/20 to-fuchsia-500/20 text-violet-700 ring-violet-500/30 dark:text-violet-300',
};
</script>

<template>
    <span
        class="inline-flex items-center gap-1.5 rounded-full bg-gradient-to-r px-2.5 py-0.5 text-xs font-semibold ring-1 ring-inset"
        :class="toneClasses[tone]"
    >
        <span class="text-sm leading-none">{{ icon }}</span>
        {{ label }}
    </span>
</template>
