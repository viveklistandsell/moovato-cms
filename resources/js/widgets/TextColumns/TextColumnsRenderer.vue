<script setup lang="ts">
import { CircleCheck } from 'lucide-vue-next';

type Data = {
    heading?: string;
    subheading?: string;
    body?: string;
    intro?: string;
    points?: string[];
    outro?: string;
};

defineProps<{ data: Data }>();
</script>

<template>
    <section v-reveal class="mv-textcols section-py">
        <div class="container-xl grid gap-10 lg:grid-cols-2 lg:gap-x-10">
            <div>
                <h2
                    v-if="data.heading"
                    class="text-3xl font-bold tracking-tight text-[var(--midnight)] sm:text-4xl"
                >
                    {{ data.heading }}
                </h2>
                <h3
                    v-if="data.subheading"
                    class="mt-8 text-lg font-semibold text-[var(--midnight)]"
                >
                    {{ data.subheading }}
                </h3>
                <p
                    v-if="data.body"
                    class="mt-4 text-base leading-relaxed whitespace-pre-line text-[var(--slate)]"
                >
                    {{ data.body }}
                </p>
            </div>

            <div class="lg:pt-2">
                <p
                    v-if="data.intro"
                    class="text-lg font-semibold text-[var(--midnight)]"
                >
                    {{ data.intro }}
                </p>
                <ul v-if="data.points?.length" class="mt-6 space-y-4">
                    <li
                        v-for="(point, i) in data.points"
                        :key="i"
                        class="flex items-center gap-3 font-semibold text-[var(--midnight)]"
                    >
                        <CircleCheck
                            class="size-5 shrink-0 text-[var(--orange)]"
                        />
                        <span>{{ point }}</span>
                    </li>
                </ul>
                <p
                    v-if="data.outro"
                    class="mt-6 text-base leading-relaxed text-[var(--slate)]"
                >
                    {{ data.outro }}
                </p>
            </div>
        </div>
    </section>
</template>
