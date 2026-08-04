<script setup lang="ts">
import { computed } from 'vue';

type Settings = {
    image_url: string | null;
    alignment: 'left' | 'center' | 'right';
    overlay: boolean;
    height: 'sm' | 'md' | 'lg' | 'xl';
};

type Data = {
    eyebrow?: string;
    title?: string;
    subtitle?: string;
    image_alt?: string;
    primary_label?: string;
    primary_url?: string;
    secondary_label?: string;
    secondary_url?: string;
};

const props = defineProps<{ settings: Settings; data: Data }>();

const heightClass = computed(() => {
    switch (props.settings.height) {
        case 'sm':
            return 'min-h-[35vh]';
        case 'md':
            return 'min-h-[50vh]';
        case 'xl':
            return 'min-h-[85vh]';
        default:
            return 'min-h-[65vh]';
    }
});

const alignClass = computed(() => {
    switch (props.settings.alignment) {
        case 'left':
            return 'items-start text-left';
        case 'right':
            return 'items-end text-right';
        default:
            return 'items-center text-center';
    }
});
</script>

<template>
    <section
        v-reveal
        class="relative w-full overflow-hidden bg-muted text-foreground"
        :class="heightClass"
    >
        <img
            v-if="settings.image_url"
            :src="settings.image_url"
            :alt="data.image_alt || ''"
            class="absolute inset-0 size-full object-cover"
        />
        <div
            v-if="settings.image_url && settings.overlay"
            class="absolute inset-0 bg-gradient-to-t from-black/70 via-black/40 to-black/20"
        />
        <div
            class="relative container-xl flex flex-col justify-center gap-5 py-20"
            :class="[alignClass, heightClass]"
        >
            <p
                v-if="data.eyebrow"
                class="text-xs font-semibold tracking-widest uppercase"
                :class="settings.image_url ? 'text-white/80' : 'text-primary'"
            >
                {{ data.eyebrow }}
            </p>
            <h1
                v-if="data.title"
                class="max-w-3xl text-4xl leading-tight font-extrabold sm:text-5xl md:text-6xl"
                :class="settings.image_url ? 'text-white drop-shadow' : ''"
            >
                {{ data.title }}
            </h1>
            <p
                v-if="data.subtitle"
                class="max-w-2xl text-base sm:text-lg"
                :class="
                    settings.image_url
                        ? 'text-white/90'
                        : 'text-muted-foreground'
                "
            >
                {{ data.subtitle }}
            </p>
            <div
                v-if="data.primary_label || data.secondary_label"
                class="flex flex-wrap gap-3"
                :class="{
                    'justify-start': settings.alignment === 'left',
                    'justify-center': settings.alignment === 'center',
                    'justify-end': settings.alignment === 'right',
                }"
            >
                <a
                    v-if="data.primary_label"
                    :href="data.primary_url || '#'"
                    class="inline-flex h-11 items-center rounded-md bg-primary px-6 text-sm font-semibold text-primary-foreground shadow-md transition hover:opacity-90"
                >
                    {{ data.primary_label }}
                </a>
                <a
                    v-if="data.secondary_label"
                    :href="data.secondary_url || '#'"
                    class="inline-flex h-11 items-center rounded-md border border-white/30 bg-white/10 px-6 text-sm font-semibold backdrop-blur transition hover:bg-white/20"
                    :class="
                        settings.image_url
                            ? 'text-white'
                            : 'border-input text-foreground'
                    "
                >
                    {{ data.secondary_label }}
                </a>
            </div>
        </div>
    </section>
</template>
