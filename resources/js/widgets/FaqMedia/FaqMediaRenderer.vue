<script setup lang="ts">
import { ChevronDown, Image as ImageIcon } from 'lucide-vue-next';
import { computed, ref } from 'vue';

type Item = { question?: string; answer?: string };

type Settings = {
    image_path: string | null;
    image_url: string | null;
    first_open: boolean;
};

type Data = {
    vertical_label?: string;
    image_alt?: string;
    eyebrow?: string;
    heading_lead?: string;
    heading_highlight?: string;
    heading_tail?: string;
    items?: Item[];
};

const props = defineProps<{ settings: Settings; data: Data }>();

const items = computed<Item[]>(() => props.data.items ?? []);

const openIndex = ref<number | null>(props.settings.first_open ? 0 : null);
function toggle(i: number): void {
    openIndex.value = openIndex.value === i ? null : i;
}
</script>

<template>
    <section class="mv-faqmedia section-py">
        <div
            class="container-xl grid grid-cols-1 items-start gap-10 lg:grid-cols-2 lg:gap-16"
        >
            <div class="mv-faqmedia-media">
                <span v-if="data.vertical_label" class="mv-faqmedia-vlabel">
                    {{ data.vertical_label }}
                </span>
                <div class="mv-faqmedia-image">
                    <img
                        v-if="settings.image_url"
                        :src="settings.image_url"
                        :alt="data.image_alt || ''"
                        loading="lazy"
                        decoding="async"
                    />
                    <div v-else class="mv-faqmedia-placeholder">
                        <ImageIcon class="size-10" />
                    </div>
                </div>
            </div>

            <div>
                <span v-if="data.eyebrow" class="mv-faqmedia-eyebrow">
                    {{ data.eyebrow }}
                </span>
                <h2 class="mv-faqmedia-heading">
                    {{ data.heading_lead }}
                    <span v-if="data.heading_highlight" class="hl">{{
                        data.heading_highlight
                    }}</span>
                    {{ data.heading_tail }}
                </h2>

                <ul class="mt-8 space-y-4">
                    <li
                        v-for="(item, i) in items"
                        :key="i"
                        class="mv-faqmedia-item"
                        :class="{ 'is-open': openIndex === i }"
                    >
                        <button
                            type="button"
                            class="mv-faqmedia-q"
                            :aria-expanded="openIndex === i"
                            @click="toggle(i)"
                        >
                            <span>{{ item.question }}</span>
                            <span class="mv-faqmedia-chev">
                                <ChevronDown class="size-4" />
                            </span>
                        </button>
                        <div v-if="openIndex === i" class="mv-faqmedia-a">
                            {{ item.answer }}
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </section>
</template>
