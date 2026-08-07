<script setup lang="ts">
import { Check, X } from 'lucide-vue-next';
import { ref } from 'vue';

type Row = { label?: string; left?: string | boolean; right?: string | boolean };
type Tab = {
    label?: string;
    mode?: 'value' | 'check';
    col_left?: string;
    col_right?: string;
    use_logo?: boolean;
    rows?: Row[];
};
type Data = {
    eyebrow?: string;
    heading?: string;
    tabs?: Tab[];
};

defineProps<{ settings: Record<string, unknown>; data: Data }>();

const active = ref(0);
</script>

<template>
    <section v-reveal class="mv-comparison section-py">
        <div class="container-xl">
            <div
                v-if="data.eyebrow || data.heading"
                class="mb-10 flex flex-col items-center text-center"
            >
                <span v-if="data.eyebrow" class="mv-comparison-eyebrow mb-4">{{ data.eyebrow }}</span>
                <h2 v-if="data.heading" class="mv-comparison-heading mv-section-heading">
                    {{ data.heading }}
                </h2>
            </div>

            <div
                v-if="(data.tabs ?? []).length > 1"
                class="mb-7 flex justify-center"
            >
                <div class="mv-comparison-tabs">
                    <button
                        v-for="(tab, ti) in data.tabs ?? []"
                        :key="ti"
                        type="button"
                        class="mv-comparison-tab active:scale-[0.97]"
                        :class="{ 'is-active': active === ti }"
                        @click="active = ti"
                    >
                        {{ tab.label }}
                    </button>
                </div>
            </div>

            <div
                v-for="(tab, ti) in data.tabs ?? []"
                v-show="active === ti"
                :key="ti"
                class="mx-auto max-w-3xl"
            >
                <div class="mv-comparison-shell">
                    <div class="mv-comparison-panel">
                        <div
                            class="mv-comparison-head grid grid-cols-[1fr_6rem_7rem] items-center gap-x-4 px-6 py-6 sm:px-8"
                        >
                            <span></span>
                            <span class="mv-comparison-col-label">{{ tab.col_left }}</span>
                            <span class="flex justify-center">
                                <img
                                    v-if="tab.use_logo"
                                    src="/logo.svg"
                                    alt="Moovato"
                                    class="h-5 w-auto"
                                />
                                <span
                                    v-else
                                    class="mv-comparison-col-label mv-comparison-col-label--brand"
                                >
                                    {{ tab.col_right }}
                                </span>
                            </span>
                        </div>

                        <div class="mv-comparison-rows">
                            <div
                                v-for="(row, ri) in tab.rows ?? []"
                                :key="ri"
                                class="mv-comparison-row grid grid-cols-[1fr_6rem_7rem] items-center gap-x-4 px-5 py-5 sm:px-5"
                            >
                                <span class="mv-comparison-row-label">
                                    {{ row.label }}
                                </span>

                                <span class="flex justify-center">
                                    <template v-if="tab.mode === 'check'">
                                        <span
                                            v-if="row.left"
                                            class="mv-comparison-icon mv-comparison-icon--yes"
                                        >
                                            <Check :size="15" />
                                        </span>
                                        <span
                                            v-else
                                            class="mv-comparison-icon mv-comparison-icon--no"
                                        >
                                            <X :size="15" />
                                        </span>
                                    </template>
                                    <span v-else class="mv-comparison-value">
                                        {{ row.left }}
                                    </span>
                                </span>

                                <span class="flex justify-center">
                                    <template v-if="tab.mode === 'check'">
                                        <span
                                            v-if="row.right"
                                            class="mv-comparison-icon mv-comparison-icon--yes"
                                        >
                                            <Check :size="15" />
                                        </span>
                                        <span
                                            v-else
                                            class="mv-comparison-icon mv-comparison-icon--no"
                                        >
                                            <X :size="15" />
                                        </span>
                                    </template>
                                    <span
                                        v-else
                                        class="mv-comparison-value mv-comparison-value--brand"
                                    >
                                        {{ row.right }}
                                    </span>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</template>
