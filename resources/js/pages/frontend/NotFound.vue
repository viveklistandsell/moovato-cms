<script setup lang="ts">
import { Head, Link, usePage } from '@inertiajs/vue3';
import { ArrowLeft, Home } from 'lucide-vue-next';
import { computed } from 'vue';

defineProps<{
    locale?: string;
}>();

// The shared layout prop carries the active locale; fall back to the
// page-level prop if needed, then 'de' (primary locale for Moovato).
const page = usePage();
const lang = computed<'de' | 'en'>(() => {
    const fromShared =
        ((page.props as { locale?: string }).locale ?? '').toString();
    if (fromShared.startsWith('en')) return 'en';
    if (fromShared.startsWith('de')) return 'de';
    return 'de';
});

const TXT = {
    de: {
        head: 'Seite nicht gefunden — Moovato',
        eyebrow: 'Fehler 404',
        title: 'Diese Seite ist umgezogen.',
        body: 'Sieht so aus, als hätten wir den Karton verlegt. Die Seite, die du suchst, existiert nicht (mehr) oder wurde verschoben. Lass uns dich zurück nach Hause bringen.',
        home: 'Zur Startseite',
        back: 'Zurück',
        alt: 'Fehler 404 — Seite nicht gefunden',
    },
    en: {
        head: 'Page not found — Moovato',
        eyebrow: 'Error 404',
        title: 'This page has moved out.',
        body: "Looks like we misplaced the box. The page you're looking for doesn't exist (anymore) or has been moved. Let us move you back home.",
        home: 'Go home',
        back: 'Go back',
        alt: 'Error 404 — page not found',
    },
};

const t = computed(() => TXT[lang.value]);
const homeHref = computed<string>(() => (lang.value === 'en' ? '/en' : '/'));
const goBack = () => window.history.back();
</script>

<template>
    <Head :title="t.head" />

    <section class="mv-error404 container-xl section-py">
        <div class="mv-error404__inner">
            <img
                :src="'/images/error-404.png'"
                :alt="t.alt"
                class="mv-error404__image"
            />

            <p class="mv-error404__eyebrow">{{ t.eyebrow }}</p>
            <h1 class="mv-error404__title">{{ t.title }}</h1>
            <p class="mv-error404__text">{{ t.body }}</p>

            <div class="mv-error404__btns">
                <Link :href="homeHref" class="mv-error404__btn">
                    <Home class="size-4" />
                    {{ t.home }}
                </Link>
                <button
                    type="button"
                    class="mv-error404__btn mv-error404__btn--ghost"
                    @click="goBack()"
                >
                    <ArrowLeft class="size-4" />
                    {{ t.back }}
                </button>
            </div>
        </div>
    </section>
</template>
