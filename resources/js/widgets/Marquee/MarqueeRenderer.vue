<script setup lang="ts">
import { Asterisk } from 'lucide-vue-next';
import { computed } from 'vue';

type Settings = {
    speed: 'slow' | 'normal' | 'fast';
    reverse: boolean;
};

type Data = {
    items?: string[];
};

const props = defineProps<{ settings: Settings; data: Data }>();

const items = computed(() => (props.data.items ?? []).filter(Boolean));

const duration = computed(
    () =>
        ({ slow: '46s', normal: '30s', fast: '18s' })[props.settings.speed] ??
        '30s',
);
</script>

<template>
    <section v-if="items.length" class="mv-ticker">
        <div class="mv-ticker__bar">
            <div
                class="mv-ticker__track"
                :class="{ 'is-reverse': settings.reverse }"
                :style="{ animationDuration: duration }"
            >
                <!-- Two identical groups make the loop seamless (-50% shift). -->
                <div
                    v-for="copy in 2"
                    :key="copy"
                    class="mv-ticker__group"
                    :aria-hidden="copy === 2 ? 'true' : undefined"
                >
                    <template v-for="(item, i) in items" :key="`${copy}-${i}`">
                        <span class="mv-ticker__word">{{ item }}</span>
                        <Asterisk
                            class="mv-ticker__star"
                            :size="22"
                            :stroke-width="2.5"
                        />
                    </template>
                </div>
            </div>
        </div>
    </section>
</template>
