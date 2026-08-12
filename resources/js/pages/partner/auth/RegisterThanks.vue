<script setup lang="ts">
/**
 * Thanks page shown after /partner/register submission. Placeholder
 * styling — proper design lands in Phase 4 alongside the register
 * form redesign.
 */
import { Head, Link } from '@inertiajs/vue3';
import { CheckCircle2 } from 'lucide-vue-next';
import { computed } from 'vue';

const props = defineProps<{
    locale: string;
}>();

const de = {
    title: 'Vielen Dank für Ihre Registrierung!',
    lead: 'Wir haben Ihre Anmeldung erhalten und werden sie in Kürze prüfen. Sobald Ihre Anfrage genehmigt ist, senden wir Ihnen eine E-Mail mit einem Link zum Setzen Ihres Passworts.',
    next: 'So geht es weiter',
    step_1: 'Ein Admin prüft Ihre Anmeldung (in der Regel innerhalb von 24 Stunden).',
    step_2: 'Bei Genehmigung erhalten Sie eine E-Mail mit einem Link zum Passwort-Setup.',
    step_3: 'Nach dem Setzen des Passworts können Sie sich anmelden und Ihr Firmenprofil pflegen.',
    home: 'Zur Startseite',
} as const;
const en = {
    title: 'Thanks for registering!',
    lead: 'We have received your application and will review it shortly. Once approved we will email you a link to set your password.',
    next: 'What happens next',
    step_1: 'An admin reviews your registration (usually within 24 hours).',
    step_2: 'On approval we email you a link to set your password.',
    step_3: 'Once your password is set you can log in and manage your company profile.',
    home: 'Back to home',
} as const;
const t = computed(() => (props.locale === 'de' ? de : en));
</script>

<template>
    <Head :title="t.title" />

    <div class="min-h-screen bg-[var(--paper)] px-4 py-16">
        <div class="mx-auto max-w-xl rounded-lg border border-[var(--linen)] bg-white p-8 text-center shadow-sm">
            <div class="mx-auto mb-4 flex size-16 items-center justify-center rounded-full bg-emerald-50 text-emerald-600">
                <CheckCircle2 class="size-10" />
            </div>
            <h1 class="mb-3 text-xl font-bold text-[var(--midnight)]">
                {{ t.title }}
            </h1>
            <p class="mx-auto max-w-md text-sm text-[var(--slate)]">
                {{ t.lead }}
            </p>

            <div class="mt-6 rounded-md bg-[var(--paper)] p-4 text-left">
                <p class="mb-2 text-sm font-semibold text-[var(--midnight)]">
                    {{ t.next }}
                </p>
                <ul class="space-y-1.5 text-sm text-[var(--slate)]">
                    <li class="flex gap-2">
                        <span class="mt-1.5 size-1.5 shrink-0 rounded-full bg-[var(--orange)]"></span>
                        <span>{{ t.step_1 }}</span>
                    </li>
                    <li class="flex gap-2">
                        <span class="mt-1.5 size-1.5 shrink-0 rounded-full bg-[var(--orange)]"></span>
                        <span>{{ t.step_2 }}</span>
                    </li>
                    <li class="flex gap-2">
                        <span class="mt-1.5 size-1.5 shrink-0 rounded-full bg-[var(--orange)]"></span>
                        <span>{{ t.step_3 }}</span>
                    </li>
                </ul>
            </div>

            <Link
                href="/"
                class="mt-6 inline-flex items-center justify-center rounded-md border border-[var(--linen)] px-4 py-2 text-sm font-medium text-[var(--slate)] hover:border-[var(--orange)] hover:text-[var(--orange)]"
            >
                {{ t.home }}
            </Link>
        </div>
    </div>
</template>
