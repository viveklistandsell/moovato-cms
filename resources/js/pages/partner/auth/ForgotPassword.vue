<script setup lang="ts">
/**
 * "Forgot password?" form. The backend always shows the same
 * success flash on submit — for unknown / non-approved accounts it
 * silently succeeds so a stranger can't probe the DB.
 */
import { Head, Link, useForm, usePage } from '@inertiajs/vue3';
import { ArrowLeft, CheckCircle2, Loader2, Mail } from 'lucide-vue-next';
import { computed } from 'vue';
import PartnerAuthShell from '@/components/partner/PartnerAuthShell.vue';

const props = defineProps<{ locale: string }>();

type FlashBag = { success?: string };
const flash = computed<FlashBag>(
    () => (usePage().props as { flash?: FlashBag }).flash ?? {},
);

const de = {
    brand_title: 'Passwort vergessen?',
    brand_subtitle: 'Kein Problem — wir senden Ihnen einen sicheren Link zum Zurücksetzen an Ihre E-Mail-Adresse.',
    card_title: 'Passwort zurücksetzen',
    card_subtitle: 'Geben Sie die E-Mail-Adresse Ihres Partner-Kontos ein. Wir senden Ihnen einen Link, um ein neues Passwort zu vergeben.',
    label_email: 'E-Mail-Adresse',
    email_placeholder: 'name@firma.de',
    submit: 'Reset-Link senden',
    submitting: 'Wird gesendet…',
    back: 'Zurück zur Anmeldung',
} as const;

const en = {
    brand_title: 'Forgot password?',
    brand_subtitle: 'No problem — we\'ll email you a secure link to set a new password.',
    card_title: 'Reset your password',
    card_subtitle: 'Enter the email address for your partner account and we\'ll send you a link to choose a new password.',
    label_email: 'Email address',
    email_placeholder: 'name@company.com',
    submit: 'Send reset link',
    submitting: 'Sending…',
    back: 'Back to sign in',
} as const;

const t = computed(() => (props.locale === 'de' ? de : en));

const form = useForm<{ email: string }>({ email: '' });

function submit(): void {
    form.post('/partner/forgot-password', {
        preserveScroll: true,
        onSuccess: () => form.reset('email'),
    });
}
</script>

<template>
    <Head :title="t.card_title" />

    <PartnerAuthShell
        :locale="props.locale"
        :title="t.brand_title"
        :subtitle="t.brand_subtitle"
    >
        <div class="rounded-xl border border-[var(--linen)] bg-[var(--white)] p-6 shadow-sm sm:p-8">
            <div class="mb-6">
                <h1 class="text-2xl font-bold text-[var(--midnight)]">{{ t.card_title }}</h1>
                <p class="mt-1.5 text-sm text-[var(--slate)]">{{ t.card_subtitle }}</p>
            </div>

            <!-- Success flash -->
            <div
                v-if="flash.success"
                class="mb-5 flex items-start gap-2 rounded-lg border border-[var(--orange)]/25 bg-[var(--orange-soft)] px-3 py-2.5 text-xs text-[var(--midnight)]"
            >
                <CheckCircle2 class="mt-0.5 size-4 shrink-0 text-[var(--orange)]" />
                <span>{{ flash.success }}</span>
            </div>

            <form class="space-y-4" @submit.prevent="submit">
                <div>
                    <label for="partner-forgot-email" class="mb-1.5 block text-xs font-medium text-[var(--slate)]">
                        {{ t.label_email }}
                    </label>
                    <div class="relative">
                        <Mail class="pointer-events-none absolute left-3 top-1/2 size-4 -translate-y-1/2 text-[var(--slate-light)]" />
                        <input
                            id="partner-forgot-email"
                            v-model="form.email"
                            type="email"
                            required
                            autofocus
                            autocomplete="email"
                            :placeholder="t.email_placeholder"
                            class="w-full rounded-lg border border-[var(--linen)] bg-[var(--white)] py-2.5 pl-10 pr-3 text-sm text-[var(--midnight)] placeholder:text-[var(--slate-light)] transition-colors focus:border-[var(--orange)] focus:outline-none focus:ring-2 focus:ring-[var(--orange)]/15"
                            :class="{ 'border-[var(--orange)]': !!form.errors.email }"
                        />
                    </div>
                    <p v-if="form.errors.email" class="mt-1.5 text-xs text-[var(--orange)]">
                        {{ form.errors.email }}
                    </p>
                </div>

                <button
                    type="submit"
                    :disabled="form.processing"
                    class="flex w-full items-center justify-center gap-2 rounded-lg bg-[var(--orange)] px-4 py-2.5 text-sm font-semibold text-[var(--white)] transition-colors hover:bg-[color-mix(in_srgb,var(--orange)_85%,black)] disabled:cursor-not-allowed disabled:opacity-60"
                >
                    <Loader2 v-if="form.processing" class="size-4 animate-spin" />
                    <span>{{ form.processing ? t.submitting : t.submit }}</span>
                </button>
            </form>

            <div class="mt-6 border-t border-[var(--linen)] pt-4 text-center">
                <Link
                    href="/partner/login"
                    class="inline-flex items-center gap-1.5 text-sm font-medium text-[var(--orange)] hover:underline"
                >
                    <ArrowLeft class="size-4" />
                    {{ t.back }}
                </Link>
            </div>
        </div>
    </PartnerAuthShell>
</template>
