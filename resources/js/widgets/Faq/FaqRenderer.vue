<script setup lang="ts">
import { ChevronDown } from 'lucide-vue-next';
import { computed, ref } from 'vue';

type Item = { question?: string; answer?: string };

type Settings = {
    layout: 'accordion' | 'grid';
    first_open: boolean;
};

type Data = {
    heading?: string;
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
    <section v-reveal class="w-full section-py">
        <div class="container-xl">
            <div class="mx-auto max-w-4xl">
                <h2
                    v-if="data.heading"
                    class="mb-10 text-center mv-section-heading"
                >
                    {{ data.heading }}
                </h2>

                <div
                    v-if="settings.layout === 'grid'"
                    class="grid gap-6 sm:grid-cols-2"
                >
                    <div
                        v-for="(item, i) in items"
                        :key="i"
                        class="rounded-xl border bg-card p-5 shadow-sm"
                    >
                        <h3 class="font-semibold">{{ item.question }}</h3>
                        <div
                            class="mv-rte mt-2 text-sm text-muted-foreground"
                            v-html="item.answer"
                        />
                    </div>
                </div>

                <div v-else class="divide-y rounded-xl border bg-card">
                    <div v-for="(item, i) in items" :key="i">
                        <button
                            type="button"
                            class="flex w-full items-center justify-between gap-4 px-5 py-4 text-left font-medium hover:bg-muted/50"
                            @click="toggle(i)"
                        >
                            <span>{{ item.question }}</span>
                            <ChevronDown
                                class="size-4 transition"
                                :class="openIndex === i ? 'rotate-180' : ''"
                            />
                        </button>
                        <div
                            v-if="openIndex === i"
                            class="mv-rte px-5 pb-5 text-sm text-muted-foreground"
                            v-html="item.answer"
                        />
                    </div>
                </div>
            </div>
        </div>
    </section>
</template>
