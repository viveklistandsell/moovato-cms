<script setup lang="ts">
import { ArrowDown } from 'lucide-vue-next';
import NextButton from '@/widgets/shared/NextButton.vue';

type Settings = {
    image_path: string | null;
    image_url: string | null;
    inline_image_path: string | null;
    inline_image_url: string | null;
};

type Crumb = { label?: string; url?: string };

type Data = {
    crumbs?: Crumb[];
    highlight?: string;
    heading?: string;
    description?: string;
    primary_label?: string;
    primary_url?: string;
    image_alt?: string;
    inline_image_alt?: string;
};

defineProps<{ settings: Settings; data: Data }>();
</script>

<template>
    <section class="mv-orbitbanner">
        <div
            class="pointer-events-none absolute inset-0 z-[3] overflow-hidden max-lg:hidden"
            aria-hidden="true"
        >
            <div class="absolute top-1/2 right-0 size-175 -translate-y-1/2">
                <div
                    class="rotate-center animate-rotate-center absolute z-2 -right-4/5"
                >
                    <span
                        class="relative block size-175 rounded-full border border-white/30 after:absolute after:top-1/4 after:right-8.75 after:z-10 after:size-3.5 after:rounded-full after:bg-white"
                    ></span>
                </div>
            </div>
            <div class="absolute top-1/2 right-0 size-225 -translate-y-1/2">
                <div
                    class="animate-rotate-center absolute right-[-70%] z-2"
                >
                    <span
                        class="relative block size-225 rounded-full border border-white/30 after:absolute after:right-18.75 after:bottom-1/5 after:z-10 after:size-3.5 after:rounded-full after:bg-[var(--orange)]"
                    ></span>
                </div>
            </div>
            <div class="absolute top-1/2 right-0 size-275 -translate-y-1/2">
                <div class="animate-rotate-center absolute z-2 -right-3/5">
                    <span
                        class="relative block size-275 rounded-full border border-white/30 after:absolute after:top-2/5 after:left-0.75 after:z-10 after:size-3.5 after:rounded-full after:bg-[var(--linen)]"
                    ></span>
                </div>
            </div>
        </div>

        <div
            class="container-xl relative z-[4] grid grid-cols-1 items-center gap-12 py-16 lg:grid-cols-[1.15fr_1fr] lg:py-24"
        >
            <div>
                <nav
                    v-if="data.crumbs?.length"
                    class="mv-pagebanner-crumbs mb-5"
                    aria-label="Breadcrumb"
                >
                    <template v-for="(crumb, i) in data.crumbs" :key="i">
                        <a v-if="crumb.url" :href="crumb.url">{{
                            crumb.label
                        }}</a>
                        <span v-else class="is-current">{{ crumb.label }}</span>
                        <span
                            v-if="i < data.crumbs.length - 1"
                            class="sep"
                            aria-hidden="true"
                            >›</span
                        >
                    </template>
                </nav>

                <h1 class="mv-orbitbanner-title">
                    <span
                        v-if="data.highlight || settings.inline_image_url"
                        class="flex flex-wrap items-center gap-3"
                    >
                        <img
                            v-if="settings.inline_image_url"
                            :src="settings.inline_image_url"
                            :alt="data.inline_image_alt || ''"
                            class="mv-orbitbanner-pill"
                            loading="lazy"
                            decoding="async"
                        />
                        <span class="mv-orbitbanner-highlight"
                            ><span>{{ data.highlight }}</span></span
                        >
                    </span>
                    <span class="block">{{ data.heading }}</span>
                </h1>

                <div
                    v-if="data.description"
                    class="mv-orbitbanner-desc mt-8 flex items-start gap-4"
                >
                    <ArrowDown class="mt-1 size-4 shrink-0" />
                    <p class="max-w-md">{{ data.description }}</p>
                </div>

                <div v-if="data.primary_label" class="mt-10">
                    <NextButton
                        :label="data.primary_label"
                        :href="data.primary_url || '#'"
                    />
                </div>
            </div>

            <div
                v-if="settings.image_url"
                class="mv-orbitbanner-media relative mx-auto w-full max-w-sm lg:justify-self-end"
            >
                <img
                    :src="settings.image_url"
                    :alt="data.image_alt || ''"
                    class="aspect-[10/13] w-full rounded-full object-cover"
                    loading="lazy"
                    decoding="async"
                />
            </div>
        </div>
    </section>
</template>
