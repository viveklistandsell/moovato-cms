<script setup lang="ts">
import NextButton from '@/widgets/shared/NextButton.vue';

type Data = {
    heading?: string;
    subheading?: string;
    body?: string;
    intro?: string;
    outro?: string;
    button_label?: string;
    button_url?: string;
};

defineProps<{ settings: Record<string, unknown>; data: Data }>();
</script>

<template>
    <section v-reveal class="mv-textcols section-py">
        <div class="container-xl grid gap-10 lg:grid-cols-2 lg:gap-x-10">
            <div>
                <h2
                    v-if="data.heading"
                    class="text-[var(--midnight)] mv-section-heading"
                >
                    {{ data.heading }}
                </h2>
                <h3
                    v-if="data.subheading"
                    class="mt-8 text-lg font-semibold text-[var(--midnight)]"
                >
                    {{ data.subheading }}
                </h3>
                <div
                    v-if="data.body"
                    class="mv-rte mt-4 text-base leading-relaxed text-[var(--slate)]"
                    v-html="data.body"
                />
            </div>

            <div class="lg:pt-2">
                <div
                    v-if="data.intro"
                    class="mv-rte text-base leading-relaxed text-[var(--slate)]"
                    v-html="data.intro"
                />
                <p
                    v-if="data.outro"
                    class="mt-6 text-base leading-relaxed text-[var(--slate)]"
                >
                    {{ data.outro }}
                </p>
                <NextButton
                    v-if="data.button_label"
                    class="mt-6"
                    :label="data.button_label"
                    :href="data.button_url || '#'"
                />
            </div>
        </div>
    </section>
</template>
