<script setup lang="ts">
/**
 * Partner login form — matches the split-panel design used across
 * the partner-auth flow. Colors come exclusively from the project
 * theme tokens defined in gs.css (--midnight / --orange / --paper
 * / --linen / --slate).
 */
import { Head, Link, useForm, usePage } from '@inertiajs/vue3';
import { AlertCircle, Eye, EyeOff, Loader2, Lock, Mail } from 'lucide-vue-next';
import { computed, ref } from 'vue';
import PartnerAuthShell from '@/components/partner/PartnerAuthShell.vue';

const props = defineProps<{ locale: string }>();

type FlashBag = { success?: string; warning?: string; error?: string };
const flash = computed<FlashBag>(
    () => (usePage().props as { flash?: FlashBag }).flash ?? {},
);

const de = {
    brand_title: 'Willkommen zurück',
    brand_subtitle: 'Melden Sie sich an, um Ihr Firmenprofil, Ihre Bewertungen und Anfragen zu verwalten.',
    card_title: 'Anmelden',
    card_subtitle: 'Geben Sie Ihre Zugangsdaten ein, um auf Ihr Partner-Konto zuzugreifen.',
    label_email: 'E-Mail-Adresse',
    label_password: 'Passwort',
    email_placeholder: 'name@firma.de',
    password_placeholder: 'Ihr Passwort',
    label_remember: 'Angemeldet bleiben',
    submit: 'Anmelden',
    submitting: 'Wird angemeldet…',
    forgot: 'Passwort vergessen?',
    no_account: 'Noch kein Partner?',
    register: 'Konto erstellen',
    show_password: 'Passwort anzeigen',
    hide_password: 'Passwort verbergen',
} as const;
const en = {
    brand_title: 'Welcome back',
    brand_subtitle: 'Sign in to manage your company profile, reviews and inbound requests.',
    card_title: 'Sign in',
    card_subtitle: 'Enter your credentials to access your partner account.',
    label_email: 'Email address',
    label_password: 'Password',
    email_placeholder: 'name@company.com',
    password_placeholder: 'Your password',
    label_remember: 'Keep me signed in',
    submit: 'Sign in',
    submitting: 'Signing in…',
    forgot: 'Forgot password?',
    no_account: 'Not a partner yet?',
    register: 'Create an account',
    show_password: 'Show password',
    hide_password: 'Hide password',
} as const;
const t = computed(() => (props.locale === 'de' ? de : en));

const form = useForm<{ email: string; password: string; remember: boolean }>({
    email: '',
    password: '',
    remember: false,
});

const showPassword = ref(false);

function submit(): void {
    form.post('/partner/login', {
        preserveScroll: true,
        onFinish: () => form.reset('password'),
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
        <!-- Card -->
        <div class="rounded-xl border border-[var(--linen)] bg-[var(--white)] p-6 shadow-sm sm:p-8">
            <div class="mb-6">
                <h1 class="text-2xl font-bold text-[var(--midnight)]">{{ t.card_title }}</h1>
                <p class="mt-1.5 text-sm text-[var(--slate)]">{{ t.card_subtitle }}</p>
            </div>

            <!-- Flash success -->
            <div
                v-if="flash.success"
                class="mb-5 flex items-start gap-2 rounded-lg border border-[var(--orange)]/25 bg-[var(--orange-soft)] px-3 py-2.5 text-xs text-[var(--midnight)]"
            >
                <AlertCircle class="mt-0.5 size-4 shrink-0 text-[var(--orange)]" />
                <span>{{ flash.success }}</span>
            </div>

            <form class="space-y-4" @submit.prevent="submit">
                <!-- Email -->
                <div>
                    <label for="partner-login-email" class="mb-1.5 block text-xs font-medium text-[var(--slate)]">
                        {{ t.label_email }}
                    </label>
                    <div class="relative">
                        <Mail class="pointer-events-none absolute left-3 top-1/2 size-4 -translate-y-1/2 text-[var(--slate-light)]" />
                        <input
                            id="partner-login-email"
                            v-model="form.email"
                            type="email"
                            required
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

                <!-- Password -->
                <div>
                    <div class="mb-1.5 flex items-center justify-between">
                        <label for="partner-login-password" class="block text-xs font-medium text-[var(--slate)]">
                            {{ t.label_password }}
                        </label>
                        <Link
                            href="/partner/forgot-password"
                            class="text-xs font-medium text-[var(--orange)] hover:underline"
                        >
                            {{ t.forgot }}
                        </Link>
                    </div>
                    <div class="relative">
                        <Lock class="pointer-events-none absolute left-3 top-1/2 size-4 -translate-y-1/2 text-[var(--slate-light)]" />
                        <input
                            id="partner-login-password"
                            v-model="form.password"
                            :type="showPassword ? 'text' : 'password'"
                            required
                            autocomplete="current-password"
                            :placeholder="t.password_placeholder"
                            class="w-full rounded-lg border border-[var(--linen)] bg-[var(--white)] py-2.5 pl-10 pr-10 text-sm text-[var(--midnight)] placeholder:text-[var(--slate-light)] transition-colors focus:border-[var(--orange)] focus:outline-none focus:ring-2 focus:ring-[var(--orange)]/15"
                            :class="{ 'border-[var(--orange)]': !!form.errors.password }"
                        />
                        <button
                            type="button"
                            class="absolute right-2 top-1/2 -translate-y-1/2 rounded-md p-1.5 text-[var(--slate-light)] hover:bg-[var(--paper)] hover:text-[var(--slate)]"
                            :aria-label="showPassword ? t.hide_password : t.show_password"
                            @click="showPassword = !showPassword"
                        >
                            <EyeOff v-if="showPassword" class="size-4" />
                            <Eye v-else class="size-4" />
                        </button>
                    </div>
                    <p v-if="form.errors.password" class="mt-1.5 text-xs text-[var(--orange)]">
                        {{ form.errors.password }}
                    </p>
                </div>

                <!-- Remember -->
                <label class="flex items-center gap-2 text-sm text-[var(--slate)]">
                    <input
                        v-model="form.remember"
                        type="checkbox"
                        class="size-4 rounded border-[var(--linen)] text-[var(--orange)] accent-[var(--orange)] focus:ring-[var(--orange)]/25"
                    />
                    {{ t.label_remember }}
                </label>

                <!-- Submit -->
                <button
                    type="submit"
                    :disabled="form.processing"
                    class="flex w-full items-center justify-center gap-2 rounded-lg bg-[var(--orange)] px-4 py-2.5 text-sm font-semibold text-[var(--white)] transition-colors hover:bg-[color-mix(in_srgb,var(--orange)_85%,black)] disabled:cursor-not-allowed disabled:opacity-60"
                >
                    <Loader2 v-if="form.processing" class="size-4 animate-spin" />
                    <span>{{ form.processing ? t.submitting : t.submit }}</span>
                </button>
            </form>

            <div class="mt-6 border-t border-[var(--linen)] pt-4 text-center text-sm text-[var(--slate)]">
                {{ t.no_account }}
                <Link
                    href="/partner/register"
                    class="ml-1 font-semibold text-[var(--orange)] hover:underline"
                >
                    {{ t.register }}
                </Link>
            </div>
        </div>
    </PartnerAuthShell>
</template>
