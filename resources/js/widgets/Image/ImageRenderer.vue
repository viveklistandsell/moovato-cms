<script setup lang="ts">
import { computed } from 'vue';

type Settings = {
    image_url: string | null;
    aspect: '16/9' | '4/3' | '1/1' | 'auto';
    width: 'narrow' | 'wide' | 'full';
    rounded: boolean;
    link_url: string | null;
};

type Data = { alt?: string; caption?: string };

const props = defineProps<{ settings: Settings; data: Data }>();

const widthClass = computed(() => {
    switch (props.settings.width) {
        case 'narrow':
            return 'max-w-3xl';
        case 'full':
            return 'max-w-none';
        default:
            return 'max-w-5xl';
    }
});

const aspectClass = computed(() => {
    switch (props.settings.aspect) {
        case '16/9':
            return 'aspect-video';
        case '4/3':
            return 'aspect-[4/3]';
        case '1/1':
            return 'aspect-square';
        default:
            return '';
    }
});
</script>

<template>
    <section v-if="settings.image_url" class="w-full py-10 sm:py-12">
        <figure class="mx-auto px-4 sm:px-6 lg:px-8" :class="widthClass">
            <component
                :is="settings.link_url ? 'a' : 'div'"
                :href="settings.link_url ?? undefined"
                class="block overflow-hidden bg-muted"
                :class="[aspectClass, settings.rounded ? 'rounded-xl' : '']"
            >
                <img
                    :src="settings.image_url"
                    :alt="data.alt ?? ''"
                    class="size-full object-cover"
                />
            </component>
            <figcaption
                v-if="data.caption"
                class="mt-3 text-center text-sm text-muted-foreground"
            >
                {{ data.caption }}
            </figcaption>
        </figure>
    </section>
</template>
