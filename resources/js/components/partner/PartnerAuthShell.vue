<script setup lang="ts">
/**
 * Shared shell for every partner-auth page (Register, Login,
 * ForgotPassword, ResetPassword, RegisterThanks).
 *
 * Layout:
 *   lg+ → 2-column split, left dark brand panel + right form panel.
 *   md and below → single column, brand collapses to a compact
 *   header strip so mobile users still see the logo before the form.
 */
import { Link, usePage } from '@inertiajs/vue3';
import { CheckCircle2 } from 'lucide-vue-next';
import { computed } from 'vue';

const props = defineProps<{
    locale: string;
    title: string;
    subtitle?: string;
    highlights?: string[];
}>();

type SiteSettings = {
    site_name?: string | null;
    logo_light_url?: string | null;
};

const site = computed<SiteSettings>(
    () => (usePage().props as { siteSettings?: SiteSettings }).siteSettings ?? {},
);

const siteName = computed(() => site.value.site_name || 'Moovato');
const logoUrl = computed(() => site.value.logo_light_url || null);

const defaultHighlights = computed<string[]>(() => (props.locale === 'de'
    ? [
        'Profil, Fotos & Bewertungen an einem Ort verwalten',
        'Kostenlose Sichtbarkeit in Berlin und Umgebung',
        'Anfragen direkt von Kunden erhalten',
    ]
    : [
        'Manage profile, photos and reviews in one place',
        'Free visibility across Berlin and surrounding areas',
        'Receive requests directly from customers',
    ]));

const highlights = computed(() => props.highlights ?? defaultHighlights.value);

const tagline = computed(() => (props.locale === 'de'
    ? 'Ihr Moovato Partner-Portal'
    : 'Your Moovato partner portal'));
</script>

<template>
    <div class="mv-partnerauth min-h-svh bg-[var(--paper)]">
        <div class="grid min-h-svh lg:grid-cols-[minmax(0,5fr)_minmax(0,7fr)]">
            <!-- LEFT: brand panel (hidden on mobile, header strip fallback below) -->
            <aside
                class="mv-partnerauth__brand relative hidden overflow-hidden bg-[var(--midnight)] text-[var(--paper)] lg:flex lg:flex-col lg:justify-between lg:p-10 xl:p-12"
            >
                <!-- Decorative accent — pure CSS, uses --orange tokens only. -->
                <div class="mv-partnerauth__accent pointer-events-none absolute inset-0" aria-hidden="true"></div>

                <div class="relative z-10">
                    <Link href="/" class="inline-flex items-center gap-2 text-white">
                        <img
                            v-if="logoUrl"
                            :src="logoUrl"
                            :alt="siteName"
                            class="h-9 w-auto"
                        />
                        <span v-else class="text-lg font-bold tracking-tight">
                            {{ siteName }}
                        </span>
                    </Link>
                    <p class="mt-6 text-[11px] font-medium uppercase tracking-[0.16em] text-[var(--orange-soft)] opacity-90">
                        {{ tagline }}
                    </p>
                    <h2 class="mt-2 text-3xl font-bold leading-tight text-white xl:text-4xl">
                        {{ title }}
                    </h2>
                    <p v-if="subtitle" class="mt-3 max-w-md text-sm leading-relaxed text-[var(--linen)] opacity-90">
                        {{ subtitle }}
                    </p>
                </div>

                <div class="relative z-10 mt-8 space-y-3">
                    <p class="text-xs font-medium uppercase tracking-wider text-[var(--orange-soft)] opacity-80">
                        {{ locale === 'de' ? 'Warum Moovato Partner' : 'Why Moovato partners' }}
                    </p>
                    <ul class="space-y-2.5">
                        <li
                            v-for="(item, i) in highlights"
                            :key="i"
                            class="flex items-start gap-2.5 text-sm text-[var(--linen)]"
                        >
                            <CheckCircle2 class="mt-0.5 size-4 shrink-0 text-[var(--orange)]" />
                            <span>{{ item }}</span>
                        </li>
                    </ul>
                </div>

                <p class="relative z-10 mt-8 text-[11px] text-[var(--slate-light)] opacity-70">
                    &copy; {{ new Date().getFullYear() }} {{ siteName }} — Berlin
                </p>
            </aside>

            <!-- RIGHT: form panel -->
            <main class="flex flex-col">
                <!-- Mobile brand strip -->
                <header class="flex items-center justify-between border-b border-[var(--linen)] bg-[var(--midnight)] px-4 py-3 text-white lg:hidden">
                    <Link href="/" class="inline-flex items-center gap-2">
                        <img
                            v-if="logoUrl"
                            :src="logoUrl"
                            :alt="siteName"
                            class="h-6 w-auto"
                        />
                        <span v-else class="text-sm font-bold tracking-tight">{{ siteName }}</span>
                    </Link>
                    <span class="text-[10px] font-medium uppercase tracking-[0.14em] text-[var(--orange-soft)] opacity-90">
                        {{ tagline }}
                    </span>
                </header>

                <div class="flex flex-1 items-center justify-center px-4 py-8 sm:px-6 sm:py-12 lg:px-10">
                    <div class="w-full max-w-lg">
                        <slot />
                    </div>
                </div>
            </main>
        </div>
    </div>
</template>

<style>
.mv-partnerauth__accent {
    background:
        radial-gradient(circle at 15% 15%, color-mix(in srgb, var(--orange) 40%, transparent) 0%, transparent 45%),
        radial-gradient(circle at 90% 90%, color-mix(in srgb, var(--orange) 22%, transparent) 0%, transparent 45%),
        linear-gradient(140deg, transparent 40%, color-mix(in srgb, var(--midnight-soft) 60%, transparent) 100%);
    opacity: 0.7;
}
</style>
