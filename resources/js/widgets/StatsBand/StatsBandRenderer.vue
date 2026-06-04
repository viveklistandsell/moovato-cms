<script setup lang="ts">
type Stat = { value?: string; label?: string };

type Settings = {
    variant: 'light' | 'dark';
};

type Data = {
    eyebrow?: string;
    heading?: string;
    stats?: Stat[];
};

defineProps<{ settings: Settings; data: Data }>();
</script>

<template>
    <section
        class="mv-statsband section-py"
        :class="settings.variant === 'dark' ? 'is-dark' : 'is-light'"
    >
        <div class="container-xl">
            <div v-if="data.eyebrow || data.heading" class="text-center">
                <span v-if="data.eyebrow" class="mv-statsband-eyebrow">
                    {{ data.eyebrow }}
                </span>
                <h2
                    v-if="data.heading"
                    class="mt-4 text-3xl font-bold tracking-tight sm:text-4xl"
                >
                    {{ data.heading }}
                </h2>
            </div>

            <dl
                class="mt-10 grid grid-cols-2 gap-8 lg:grid-cols-4"
                :class="data.eyebrow || data.heading ? 'mt-10' : 'mt-0'"
            >
                <div
                    v-for="(stat, i) in data.stats ?? []"
                    :key="i"
                    class="mv-statsband-item"
                >
                    <dt class="mv-statsband-value">{{ stat.value }}</dt>
                    <dd class="mv-statsband-label">{{ stat.label }}</dd>
                </div>
            </dl>
        </div>
    </section>
</template>
