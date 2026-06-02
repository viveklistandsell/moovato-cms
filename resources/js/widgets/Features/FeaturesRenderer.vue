<script setup lang="ts">
import { computed } from 'vue';
import WidgetIcon from '@/widgets/shared/WidgetIcon.vue';

type Item = { icon?: string; title?: string; description?: string };

type Settings = {
    columns: 2 | 3 | 4;
    icon_style: 'badge' | 'outline' | 'plain';
};

type Data = {
    heading?: string;
    subheading?: string;
    items?: Item[];
};

const props = defineProps<{ settings: Settings; data: Data }>();

const colsClass = computed(() => {
    switch (props.settings.columns) {
        case 2:
            return 'sm:grid-cols-2';
        case 4:
            return 'sm:grid-cols-2 lg:grid-cols-4';
        default:
            return 'sm:grid-cols-2 lg:grid-cols-3';
    }
});

const iconWrapperClass = computed(() => {
    switch (props.settings.icon_style) {
        case 'outline':
            return 'inline-flex size-12 items-center justify-center rounded-full border-2 border-primary text-primary';
        case 'plain':
            return 'inline-flex size-12 items-center justify-center text-primary';
        default:
            return 'inline-flex size-12 items-center justify-center rounded-full bg-primary/10 text-primary';
    }
});
</script>

<template>
    <section class="w-full section-py">
        <div class="container-xl">
            <div
                v-if="data.heading || data.subheading"
                class="mx-auto mb-12 max-w-2xl text-center"
            >
                <h2
                    v-if="data.heading"
                    class="text-3xl font-bold tracking-tight sm:text-4xl"
                >
                    {{ data.heading }}
                </h2>
                <p v-if="data.subheading" class="mt-3 text-muted-foreground">
                    {{ data.subheading }}
                </p>
            </div>
            <div class="grid grid-cols-1 gap-8" :class="colsClass">
                <div
                    v-for="(item, i) in data.items ?? []"
                    :key="i"
                    class="rounded-xl border bg-card p-6 shadow-sm transition hover:shadow-md"
                >
                    <div :class="iconWrapperClass">
                        <WidgetIcon
                            :name="item.icon"
                            fallback="Zap"
                            class="size-6"
                        />
                    </div>
                    <h3 v-if="item.title" class="mt-4 text-lg font-semibold">
                        {{ item.title }}
                    </h3>
                    <p
                        v-if="item.description"
                        class="mt-2 text-sm text-muted-foreground"
                    >
                        {{ item.description }}
                    </p>
                </div>
            </div>
        </div>
    </section>
</template>
