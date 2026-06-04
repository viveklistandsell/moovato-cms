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
    <section class="mv-comparison section-py">
        <div class="container-xl">
            <div
                v-if="data.eyebrow || data.heading"
                class="mb-9 flex flex-col items-center text-center"
            >
                <span
                    v-if="data.eyebrow"
                    class="inline-block rounded-full border border-[var(--orange-soft)] bg-[var(--white)] px-5 py-3 text-sm font-semibold text-[color:var(--orange)]"
                >
                    {{ data.eyebrow }}
                </span>
                <h2
                    v-if="data.heading"
                    class="mt-4 text-3xl font-bold tracking-tight text-[color:var(--midnight)] sm:text-4xl"
                >
                    {{ data.heading }}
                </h2>
            </div>

            <div
                v-if="(data.tabs ?? []).length > 1"
                class="mb-5 flex justify-center"
            >
                <div
                    class="inline-flex gap-1.5 rounded-2xl bg-[var(--linen)] p-1.5"
                >
                    <button
                        v-for="(tab, ti) in data.tabs ?? []"
                        :key="ti"
                        type="button"
                        class="rounded-xl px-5 py-3 text-sm font-bold transition-all sm:px-6"
                        :class="
                            active === ti
                                ? 'bg-white text-[color:var(--midnight)] shadow-[0_4px_14px_rgba(15,23,42,0.08)]'
                                : 'text-[color:var(--slate)] hover:text-[color:var(--midnight)]'
                        "
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
                class="mx-auto max-w-3xl pt-3 rounded-3xl border border-[rgba(15,23,42,0.06)] shadow-[0_18px_50px_-24px_rgba(15,23,42,0.25)] overflow-hidden
                "
            >
                <div
                    class="grid grid-cols-[1fr_6rem_7rem] items-end gap-x-4 px-6 pb-4 sm:px-7"
                >
                    <span></span>
                    <span
                        class="text-center text-xs font-bold tracking-wider text-[color:var(--slate)] uppercase"
                    >
                        {{ tab.col_left }}
                    </span>
                    <span class="flex justify-center">
                        <img
                            v-if="tab.use_logo"
                            src="/logo.svg"
                            alt="Moovato"
                            class="h-5 w-auto"
                        />
                        <span
                            v-else
                            class="text-center text-sm font-extrabold text-[color:var(--midnight)]"
                        >
                            {{ tab.col_right }}
                        </span>
                    </span>
                </div>

                <div
                    class="overflow-hidden bg-white border border-[rgba(15,23,42,0.06)]"
                >
                    <div
                        v-for="(row, ri) in tab.rows ?? []"
                        :key="ri"
                        class="grid grid-cols-[1fr_6rem_7rem] items-center gap-x-4 px-6 py-5 odd:bg-[var(--paper)] sm:px-7"
                    >
                        <span
                            class="text-[15px] font-semibold text-[color:var(--midnight)]"
                        >
                            {{ row.label }}
                        </span>

                        <span class="flex justify-center">
                            <template v-if="tab.mode === 'check'">
                                <span
                                    v-if="row.left"
                                    class="inline-flex size-8 items-center justify-center rounded-full bg-[var(--orange)] text-white"
                                >
                                    <Check :size="16" />
                                </span>
                                <span
                                    v-else
                                    class="inline-flex size-8 items-center justify-center rounded-full bg-[rgba(15,23,42,0.05)] text-[color:var(--slate-light)]"
                                >
                                    <X :size="16" />
                                </span>
                            </template>
                            <span
                                v-else
                                class="text-center text-sm font-medium text-[color:var(--slate)]"
                            >
                                {{ row.left }}
                            </span>
                        </span>

                        <span class="flex justify-center">
                            <template v-if="tab.mode === 'check'">
                                <span
                                    v-if="row.right"
                                    class="inline-flex size-8 items-center justify-center rounded-full bg-[var(--orange)] text-white"
                                >
                                    <Check :size="16" />
                                </span>
                                <span
                                    v-else
                                    class="inline-flex size-8 items-center justify-center rounded-full bg-[rgba(15,23,42,0.05)] text-[color:var(--slate-light)]"
                                >
                                    <X :size="16" />
                                </span>
                            </template>
                            <span
                                v-else
                                class="text-center text-sm font-extrabold text-[color:var(--orange)]"
                            >
                                {{ row.right }}
                            </span>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </section>
</template>
