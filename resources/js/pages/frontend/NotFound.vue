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
        body: 'Sieht so aus, als hätten wir den Karton verlegt. Die Seite, die du suchst, existiert nicht (mehr) oder wurde verschoben.',
        home: 'Zur Startseite',
        back: 'Zurück',
    },
    en: {
        head: 'Page not found — Moovato',
        eyebrow: 'Error 404',
        title: 'This page has moved out.',
        body: "Looks like we misplaced the box. The page you're looking for doesn't exist (anymore) or has been moved.",
        home: 'Go home',
        back: 'Go back',
    },
};

const t = computed(() => TXT[lang.value]);
const homeHref = computed<string>(() => (lang.value === 'en' ? '/en' : '/'));
const goBack = () => window.history.back();
</script>

<template>
    <Head :title="t.head" />

    <section class="container-xl section-py">
        <div class="relative mx-auto max-w-2xl text-center">
            <div
                class="pointer-events-none absolute inset-0 -z-10 overflow-hidden"
                aria-hidden="true"
            >
                <div
                    class="absolute top-0 left-1/2 size-80 -translate-x-1/2 rounded-full bg-[var(--orange)]/20 blur-3xl"
                />
            </div>
            <div
                class="mx-auto mb-8 w-72 text-[var(--midnight)] dark:text-[var(--linen)]"
            >
                <svg
                    viewBox="0 0 200 120"
                    class="size-full"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="2.5"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                >
                    <rect
                        x="10"
                        y="30"
                        width="100"
                        height="60"
                        rx="4"
                        class="fill-[var(--orange)]/15"
                    />
                    <text
                        x="60"
                        y="68"
                        text-anchor="middle"
                        class="fill-[var(--orange)] font-black"
                        style="font-size: 24px; font-family: system-ui, sans-serif;"
                        stroke="none"
                    >
                        404
                    </text>
                    <path
                        d="M110 50 L138 50 L156 70 L156 90 L110 90 Z"
                        class="fill-[var(--orange)]/25"
                    />
                    <path
                        d="M116 56 L138 56 L150 70 L116 70 Z"
                        class="fill-[var(--linen)] dark:fill-[var(--midnight-soft)]"
                    />
                    <line x1="110" y1="50" x2="110" y2="90" />
                    <circle
                        cx="40"
                        cy="92"
                        r="10"
                        class="fill-[var(--midnight)] dark:fill-[var(--linen)]"
                        stroke="none"
                    />
                    <circle
                        cx="135"
                        cy="92"
                        r="10"
                        class="fill-[var(--midnight)] dark:fill-[var(--linen)]"
                        stroke="none"
                    />
                    <circle
                        cx="40"
                        cy="92"
                        r="3.5"
                        class="fill-[var(--linen)] dark:fill-[var(--midnight)]"
                        stroke="none"
                    />
                    <circle
                        cx="135"
                        cy="92"
                        r="3.5"
                        class="fill-[var(--linen)] dark:fill-[var(--midnight)]"
                        stroke="none"
                    />
                    <line x1="5" y1="105" x2="195" y2="105" class="opacity-30" />
                    <line x1="160" y1="40" x2="180" y2="40" class="opacity-40" />
                    <line x1="165" y1="50" x2="185" y2="50" class="opacity-30" />
                    <line x1="160" y1="60" x2="180" y2="60" class="opacity-40" />
                </svg>
            </div>

            <p
                class="text-xs font-bold uppercase tracking-widest text-[var(--orange)]"
            >
                {{ t.eyebrow }}
            </p>
            <h1
                class="mt-2 text-4xl font-extrabold tracking-tight text-[var(--midnight)] sm:text-5xl dark:text-[var(--linen)]"
            >
                {{ t.title }}
            </h1>
            <p
                class="mx-auto mt-3 max-w-md text-sm text-muted-foreground sm:text-base"
            >
                {{ t.body }}
            </p>

            <div class="mt-6 flex flex-wrap items-center justify-center gap-2">
                <Link
                    :href="homeHref"
                    class="inline-flex items-center gap-1.5 rounded-full bg-gradient-to-r from-[var(--orange)] to-[var(--yellow-dark)] px-4 py-2 text-sm font-bold text-white shadow-lg shadow-[var(--orange)]/40 transition-transform hover:scale-105"
                >
                    <Home class="size-4" />
                    {{ t.home }}
                </Link>
                <button
                    type="button"
                    class="inline-flex items-center gap-1.5 rounded-full border border-[var(--midnight)]/20 bg-white/70 px-4 py-2 text-sm font-bold text-[var(--midnight)] backdrop-blur-xl transition-colors hover:bg-white dark:border-white/10 dark:bg-[var(--midnight)]/40 dark:text-[var(--linen)] dark:hover:bg-[var(--midnight)]/60"
                    @click="goBack()"
                >
                    <ArrowLeft class="size-4" />
                    {{ t.back }}
                </button>
            </div>
        </div>
    </section>
</template>
