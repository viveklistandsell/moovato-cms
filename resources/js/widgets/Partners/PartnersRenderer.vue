<script setup lang="ts">
import WidgetIcon from '@/widgets/shared/WidgetIcon.vue';

type Partner = {
    path?: string | null;
    url?: string | null;
    name?: string;
    link?: string;
};

type Certificate = { icon?: string; title?: string };

type Settings = {
    partners?: Partner[];
    certificates?: Certificate[];
};

type Data = {
    eyebrow?: string;
    heading?: string;
    subheading?: string;
    description?: string;
    partners_title?: string;
    certificates_title?: string;
};

defineProps<{ settings: Settings; data: Data }>();
</script>

<template>
    <section v-reveal class="mv-partners section-py">
        <div class="container-xl">
            <div
                v-if="data.eyebrow || data.heading || data.subheading || data.description"
                class="mx-auto max-w-2xl text-center"
            >
                <span v-if="data.eyebrow" class="mv-partners-eyebrow">{{ data.eyebrow }}</span>
                <h2
                    v-if="data.heading"
                    class="mt-5 mv-section-heading"
                >
                    {{ data.heading }}
                </h2>
                <div
                    v-if="data.description"
                    class="mv-rte mt-4 text-base leading-relaxed text-[var(--slate)]"
                    v-html="data.description"
                />
                <p
                    v-if="data.subheading"
                    class="mt-4 text-base text-[var(--slate)]"
                >
                    {{ data.subheading }}
                </p>
            </div>

            <div v-if="(settings.partners ?? []).length" class="mt-4 lg:mt-6">
                <p
                    v-if="data.partners_title"
                    class="mv-partners-caption"
                >
                    {{ data.partners_title }}
                </p>
                <ul
                    class="mt-8 grid grid-cols-2 gap-5 sm:grid-cols-3 lg:grid-cols-5"
                >
                    <li
                        v-for="(partner, i) in settings.partners"
                        :key="i"
                        class="mv-partner-card"
                    >
                        <component
                            :is="partner.link ? 'a' : 'div'"
                            :href="partner.link || undefined"
                            :target="partner.link ? '_blank' : undefined"
                            :rel="
                                partner.link ? 'noopener noreferrer' : undefined
                            "
                            class="mv-partner-inner"
                        >
                            <span class="mv-partner-core">
                                <img
                                    v-if="partner.url"
                                    :src="partner.url"
                                    :alt="partner.name || ''"
                                    loading="lazy"
                                    decoding="async"
                                />
                                <span
                                    v-if="partner.name"
                                    class="mv-partner-name"
                                    >{{ partner.name }}</span
                                >
                            </span>
                        </component>
                    </li>
                </ul>
            </div>

            <div v-if="(settings.certificates ?? []).length" class="mt-20 lg:mt-24">
                <p
                    v-if="data.certificates_title"
                    class="mv-partners-caption"
                >
                    {{ data.certificates_title }}
                </p>
                <ul
                    class="mt-8 flex flex-wrap items-center justify-center gap-6 sm:gap-8"
                >
                    <li
                        v-for="(cert, i) in settings.certificates"
                        :key="i"
                        class="mv-cert"
                    >
                        <span class="mv-cert-seal">
                            <WidgetIcon
                                :name="cert.icon"
                                fallback="Award"
                                class="size-7"
                            />
                        </span>
                        <span v-if="cert.title" class="mv-cert-title">{{
                            cert.title
                        }}</span>
                    </li>
                </ul>
            </div>
        </div>
    </section>
</template>
