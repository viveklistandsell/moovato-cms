<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { CheckCircle2, Home } from 'lucide-vue-next';
import { computed } from 'vue';
import { localizedUrl } from '@/lib/localizedUrl';

type CompanySummary = {
    id: number;
    name: string;
    permalink: string | null;
};

const props = defineProps<{
    locale: string;
    company: CompanySummary;
}>();

const t = computed(() => ({
    title: props.locale === 'de' ? 'Vielen Dank für Ihre Bewertung!' : 'Thank you for your review!',
    lead: props.locale === 'de'
        ? 'Ihre Bewertung wurde veröffentlicht und ist ab sofort auf dem Firmenprofil sichtbar. Sie hilft anderen Nutzern, das passende Umzugsunternehmen zu finden.'
        : 'Your review has been published and is now visible on the company profile. It helps other users find the right moving company.',
    next: props.locale === 'de' ? 'So geht es weiter' : 'What happens next',
    step_2: props.locale === 'de'
        ? 'Das Unternehmen kann öffentlich auf Ihre Bewertung antworten.'
        : 'The moving company can publicly reply to your review.',
    step_3: props.locale === 'de'
        ? 'Wir informieren Sie, sobald eine Antwort vorliegt.'
        : 'We will notify you as soon as a reply is posted.',
    view_profile: props.locale === 'de' ? 'Zur Firmenseite' : 'Back to company page',
    view_home: props.locale === 'de' ? 'Zur Startseite' : 'Back to home',
}));

const detailUrl = computed(() =>
    localizedUrl(props.locale, `/company/${props.company.permalink}`),
);
const homeUrl = computed(() => localizedUrl(props.locale, '/'));
</script>

<template>
    <Head :title="t.title" />

    <div class="mv-review-thanks bg-[var(--paper)] py-16 md:py-24">
        <div class="container-xl">
            <div class="mx-auto max-w-2xl rounded-xl border border-[var(--linen)] bg-white p-8 text-center shadow-sm md:p-12">
                <div class="mx-auto mb-5 flex size-16 items-center justify-center rounded-full bg-emerald-50 text-emerald-600">
                    <CheckCircle2 class="size-10" />
                </div>
                <h1 class="mb-3 text-2xl font-bold text-[var(--midnight)] md:text-3xl">
                    {{ t.title }}
                </h1>
                <p class="mx-auto max-w-lg text-sm text-[var(--slate)] md:text-base">
                    {{ t.lead }}
                </p>

                <div class="mt-8 rounded-lg bg-[var(--paper)] p-5 text-left">
                    <p class="mb-3 text-sm font-semibold text-[var(--midnight)]">
                        {{ t.next }}
                    </p>
                    <ul class="space-y-2 text-sm text-[var(--slate)]">
                        <li class="flex gap-2">
                            <span class="mt-1 size-1.5 shrink-0 rounded-full bg-[var(--orange)]"></span>
                            <span>{{ t.step_2 }}</span>
                        </li>
                        <li class="flex gap-2">
                            <span class="mt-1 size-1.5 shrink-0 rounded-full bg-[var(--orange)]"></span>
                            <span>{{ t.step_3 }}</span>
                        </li>
                    </ul>
                </div>

                <div class="mt-8 flex flex-col items-center justify-center gap-3 sm:flex-row">
                    <Link
                        :href="detailUrl"
                        class="inline-flex items-center justify-center gap-2 rounded-md bg-[var(--orange)] px-5 py-2.5 text-sm font-semibold text-white transition-colors hover:bg-[color-mix(in_srgb,var(--orange)_85%,black)]"
                    >
                        {{ t.view_profile }}
                    </Link>
                    <Link
                        :href="homeUrl"
                        class="inline-flex items-center justify-center gap-2 rounded-md border border-[var(--linen)] bg-white px-5 py-2.5 text-sm font-medium text-[var(--slate)] hover:border-[var(--orange)] hover:text-[var(--orange)]"
                    >
                        <Home class="size-4" />
                        {{ t.view_home }}
                    </Link>
                </div>
            </div>
        </div>
    </div>
</template>
