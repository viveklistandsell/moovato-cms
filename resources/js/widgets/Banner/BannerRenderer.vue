<script setup lang="ts">
import { computed, ref } from 'vue';

type Settings = {
    variant: 'info' | 'success' | 'warning' | 'danger' | 'brand';
    dismissible: boolean;
    icon: string;
};

type Data = {
    message?: string;
    cta_label?: string;
    cta_url?: string;
};

const props = defineProps<{ settings: Settings; data: Data }>();

const dismissed = ref(false);

const variantClass = computed(() => {
    switch (props.settings.variant) {
        case 'success':
            return 'bg-emerald-500/10 text-emerald-900 dark:text-emerald-200 border-emerald-500/30';
        case 'warning':
            return 'bg-amber-500/10 text-amber-900 dark:text-amber-200 border-amber-500/30';
        case 'danger':
            return 'bg-red-500/10 text-red-900 dark:text-red-200 border-red-500/30';
        case 'brand':
            return 'bg-primary/10 text-foreground border-primary/30';
        default:
            return 'bg-sky-500/10 text-sky-900 dark:text-sky-200 border-sky-500/30';
    }
});
</script>

<template>
    <section v-if="!dismissed && data.message" class="w-full">
        <div
            class="mx-auto flex max-w-6xl items-center gap-3 border-y px-4 py-3 sm:px-6 lg:px-8"
            :class="variantClass"
        >
            <p class="flex-1 text-sm sm:text-base">{{ data.message }}</p>
            <a
                v-if="data.cta_label"
                :href="data.cta_url || '#'"
                class="inline-flex h-9 items-center rounded-md border bg-background px-3 text-sm font-medium text-foreground transition hover:bg-muted"
            >
                {{ data.cta_label }}
            </a>
            <button
                v-if="settings.dismissible"
                type="button"
                class="rounded-md p-1 text-sm font-medium hover:bg-background/50"
                aria-label="Dismiss"
                @click="dismissed = true"
            >
                ×
            </button>
        </div>
    </section>
</template>
