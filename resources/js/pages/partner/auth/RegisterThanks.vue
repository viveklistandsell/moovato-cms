<script setup lang="ts">

import { Head, Link } from '@inertiajs/vue3';
import { ArrowLeft, CheckCircle2, Clock, Mail, ShieldCheck } from 'lucide-vue-next';
import { computed } from 'vue';
import PartnerAuthShell from '@/components/partner/PartnerAuthShell.vue';

const props = defineProps<{ locale: string }>();

const de = {
    brand_title: 'Anmeldung erfolgreich!',
    brand_subtitle: 'Wir haben Ihre Registrierung erhalten und melden uns nach der Prüfung bei Ihnen.',
    highlights: [
        'Prüfung meist innerhalb von 24 Stunden',
        'E-Mail zum Passwort-Setup nach Freigabe',
        'Danach volle Kontrolle über Ihr Firmenprofil',
    ],
    card_title: 'Vielen Dank für Ihre Registrierung',
    lead: 'Wir haben Ihre Anmeldung erhalten und werden sie in Kürze prüfen. Sobald Ihre Anfrage genehmigt ist, senden wir Ihnen eine E-Mail mit einem Link zum Setzen Ihres Passworts.',
    next: 'So geht es weiter',
    step_1_title: 'Prüfung',
    step_1_body: 'Unser Team prüft Ihre Anmeldung (in der Regel innerhalb von 24 Stunden).',
    step_2_title: 'E-Mail zum Passwort-Setup',
    step_2_body: 'Bei Genehmigung erhalten Sie einen sicheren Link an Ihre E-Mail-Adresse.',
    step_3_title: 'Loslegen',
    step_3_body: 'Nach dem Setzen des Passworts können Sie sich einloggen und Ihr Firmenprofil pflegen.',
    home: 'Zur Startseite',
    login: 'Bereits Partner? Einloggen',
} as const;

const en = {
    brand_title: 'Application received!',
    brand_subtitle: 'We\'ve received your registration and will get back to you after review.',
    highlights: [
        'Reviewed within 24 hours in most cases',
        'Password setup email after approval',
        'Full control of your company profile afterwards',
    ],
    card_title: 'Thanks for registering',
    lead: 'We have received your application and will review it shortly. Once approved we will email you a link to set your password.',
    next: 'What happens next',
    step_1_title: 'Review',
    step_1_body: 'Our team reviews your registration (usually within 24 hours).',
    step_2_title: 'Password setup email',
    step_2_body: 'On approval we email you a secure link to your address.',
    step_3_title: 'Get started',
    step_3_body: 'Once your password is set you can sign in and manage your company profile.',
    home: 'Back to home',
    login: 'Already a partner? Sign in',
} as const;

const t = computed(() => (props.locale === 'de' ? de : en));
</script>

<template>
    <Head :title="t.card_title" />

    <PartnerAuthShell
        :locale="props.locale"
        :title="t.brand_title"
        :subtitle="t.brand_subtitle"
        :highlights="[...t.highlights]"
    >
        <div class="rounded-xl border border-[var(--linen)] bg-[var(--white)] p-6 shadow-sm sm:p-8">
            <div class="mb-6 flex items-start gap-3">
                <div class="flex size-11 shrink-0 items-center justify-center rounded-full bg-[var(--orange-soft)] text-[var(--orange)]">
                    <CheckCircle2 class="size-6" />
                </div>
                <div>
                    <h1 class="text-2xl font-bold text-[var(--midnight)]">{{ t.card_title }}</h1>
                    <p class="mt-1 text-sm text-[var(--slate)]">{{ t.lead }}</p>
                </div>
            </div>

            <div class="mt-6 rounded-lg border border-[var(--linen)] bg-[var(--paper)] p-4">
                <p class="mb-3 text-sm font-semibold text-[var(--midnight)]">
                    {{ t.next }}
                </p>
                <ol class="space-y-3">
                    <li class="flex gap-3">
                        <div class="flex size-8 shrink-0 items-center justify-center rounded-full bg-[var(--orange-soft)] text-[var(--orange)]">
                            <Clock class="size-4" />
                        </div>
                        <div>
                            <p class="text-sm font-medium text-[var(--midnight)]">{{ t.step_1_title }}</p>
                            <p class="mt-0.5 text-xs text-[var(--slate)]">{{ t.step_1_body }}</p>
                        </div>
                    </li>
                    <li class="flex gap-3">
                        <div class="flex size-8 shrink-0 items-center justify-center rounded-full bg-[var(--orange-soft)] text-[var(--orange)]">
                            <Mail class="size-4" />
                        </div>
                        <div>
                            <p class="text-sm font-medium text-[var(--midnight)]">{{ t.step_2_title }}</p>
                            <p class="mt-0.5 text-xs text-[var(--slate)]">{{ t.step_2_body }}</p>
                        </div>
                    </li>
                    <li class="flex gap-3">
                        <div class="flex size-8 shrink-0 items-center justify-center rounded-full bg-[var(--orange-soft)] text-[var(--orange)]">
                            <ShieldCheck class="size-4" />
                        </div>
                        <div>
                            <p class="text-sm font-medium text-[var(--midnight)]">{{ t.step_3_title }}</p>
                            <p class="mt-0.5 text-xs text-[var(--slate)]">{{ t.step_3_body }}</p>
                        </div>
                    </li>
                </ol>
            </div>

            <div class="mt-6 flex flex-wrap items-center justify-between gap-3 border-t border-[var(--linen)] pt-4">
                <Link
                    href="/"
                    class="inline-flex items-center gap-1.5 text-sm font-medium text-[var(--slate)] hover:text-[var(--orange)]"
                >
                    <ArrowLeft class="size-4" />
                    {{ t.home }}
                </Link>
                <Link
                    href="/partner/login"
                    class="text-sm font-semibold text-[var(--orange)] hover:underline"
                >
                    {{ t.login }}
                </Link>
            </div>
        </div>
    </PartnerAuthShell>
</template>
