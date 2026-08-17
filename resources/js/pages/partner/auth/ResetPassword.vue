<script setup lang="ts">
/**
 * Set / reset password form.
 *
 * `isSetup=true` means this is the first-time password setup after
 * an admin accepted the application (copy switches to "Welcome —
 * set your password"). Otherwise it's a standard reset flow.
 */
import { Head, useForm } from '@inertiajs/vue3';
import { CheckCircle2, Eye, EyeOff, KeyRound, Loader2, Lock, Mail } from 'lucide-vue-next';
import { computed, ref } from 'vue';
import PartnerAuthShell from '@/components/partner/PartnerAuthShell.vue';

const props = defineProps<{
    locale: string;
    token: string;
    email: string;
    isSetup: boolean;
}>();

const de = {
    brand_setup_title: 'Willkommen an Bord!',
    brand_setup_subtitle: 'Ihre Registrierung wurde freigegeben. Legen Sie jetzt Ihr Passwort fest, um sich anzumelden.',
    brand_reset_title: 'Neues Passwort festlegen',
    brand_reset_subtitle: 'Wählen Sie ein sicheres Passwort. Danach werden Sie automatisch angemeldet.',
    setup_title: 'Passwort einrichten',
    setup_subtitle: 'Bitte legen Sie ein sicheres Passwort für Ihr Moovato Partner-Konto fest.',
    reset_title: 'Passwort zurücksetzen',
    reset_subtitle: 'Bitte wählen Sie ein neues Passwort für Ihr Konto.',
    label_email: 'E-Mail-Adresse',
    label_password: 'Neues Passwort',
    label_confirm: 'Passwort bestätigen',
    password_hint: 'Mindestens 8 Zeichen mit Buchstaben und Zahlen.',
    setup_submit: 'Passwort speichern & anmelden',
    reset_submit: 'Passwort zurücksetzen',
    submitting: 'Wird gespeichert…',
    show_password: 'Passwort anzeigen',
    hide_password: 'Passwort verbergen',
} as const;

const en = {
    brand_setup_title: 'Welcome aboard!',
    brand_setup_subtitle: 'Your registration has been approved. Set your password to sign in.',
    brand_reset_title: 'Set a new password',
    brand_reset_subtitle: 'Choose a secure password. You will be signed in automatically afterwards.',
    setup_title: 'Set your password',
    setup_subtitle: 'Please choose a secure password for your Moovato partner account.',
    reset_title: 'Reset password',
    reset_subtitle: 'Please choose a new password for your account.',
    label_email: 'Email address',
    label_password: 'New password',
    label_confirm: 'Confirm password',
    password_hint: 'At least 8 characters with letters and numbers.',
    setup_submit: 'Save password & sign in',
    reset_submit: 'Reset password',
    submitting: 'Saving…',
    show_password: 'Show password',
    hide_password: 'Hide password',
} as const;

const t = computed(() => (props.locale === 'de' ? de : en));

const brandTitle = computed(() => (props.isSetup ? t.value.brand_setup_title : t.value.brand_reset_title));
const brandSubtitle = computed(() => (props.isSetup ? t.value.brand_setup_subtitle : t.value.brand_reset_subtitle));
const cardTitle = computed(() => (props.isSetup ? t.value.setup_title : t.value.reset_title));
const cardSubtitle = computed(() => (props.isSetup ? t.value.setup_subtitle : t.value.reset_subtitle));
const submitLabel = computed(() => (props.isSetup ? t.value.setup_submit : t.value.reset_submit));

const form = useForm<{
    token: string;
    email: string;
    password: string;
    password_confirmation: string;
}>({
    token: props.token,
    email: props.email,
    password: '',
    password_confirmation: '',
});

const showPassword = ref(false);
const showConfirm = ref(false);

function submit(): void {
    form.post('/partner/reset-password', { preserveScroll: true });
}
</script>

<template>
    <Head :title="cardTitle" />

    <PartnerAuthShell
        :locale="props.locale"
        :title="brandTitle"
        :subtitle="brandSubtitle"
    >
        <div class="rounded-xl border border-[var(--linen)] bg-[var(--white)] p-6 shadow-sm sm:p-8">
            <div class="mb-6 flex items-start gap-3">
                <div class="flex size-10 shrink-0 items-center justify-center rounded-lg bg-[var(--orange-soft)] text-[var(--orange)]">
                    <KeyRound class="size-5" />
                </div>
                <div>
                    <h1 class="text-2xl font-bold text-[var(--midnight)]">{{ cardTitle }}</h1>
                    <p class="mt-1 text-sm text-[var(--slate)]">{{ cardSubtitle }}</p>
                </div>
            </div>

            <form class="space-y-4" @submit.prevent="submit">
                <!-- Email (readonly — carried from the reset link) -->
                <div>
                    <label class="mb-1.5 block text-xs font-medium text-[var(--slate)]">
                        {{ t.label_email }}
                    </label>
                    <div class="relative">
                        <Mail class="pointer-events-none absolute left-3 top-1/2 size-4 -translate-y-1/2 text-[var(--slate-light)]" />
                        <input
                            v-model="form.email"
                            type="email"
                            required
                            readonly
                            autocomplete="email"
                            class="w-full cursor-not-allowed rounded-lg border border-[var(--linen)] bg-[var(--paper)] py-2.5 pl-10 pr-3 text-sm text-[var(--slate)] focus:outline-none"
                        />
                    </div>
                    <p v-if="form.errors.email" class="mt-1.5 text-xs text-[var(--orange)]">{{ form.errors.email }}</p>
                </div>

                <!-- New password -->
                <div>
                    <label for="partner-reset-pw" class="mb-1.5 block text-xs font-medium text-[var(--slate)]">
                        {{ t.label_password }}
                    </label>
                    <div class="relative">
                        <Lock class="pointer-events-none absolute left-3 top-1/2 size-4 -translate-y-1/2 text-[var(--slate-light)]" />
                        <input
                            id="partner-reset-pw"
                            v-model="form.password"
                            :type="showPassword ? 'text' : 'password'"
                            required
                            autofocus
                            autocomplete="new-password"
                            class="w-full rounded-lg border border-[var(--linen)] bg-[var(--white)] py-2.5 pl-10 pr-10 text-sm text-[var(--midnight)] transition-colors focus:border-[var(--orange)] focus:outline-none focus:ring-2 focus:ring-[var(--orange)]/15"
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
                    <p class="mt-1.5 text-[11px] text-[var(--slate-light)]">{{ t.password_hint }}</p>
                    <p v-if="form.errors.password" class="mt-1 text-xs text-[var(--orange)]">{{ form.errors.password }}</p>
                </div>

                <!-- Confirm -->
                <div>
                    <label for="partner-reset-pw2" class="mb-1.5 block text-xs font-medium text-[var(--slate)]">
                        {{ t.label_confirm }}
                    </label>
                    <div class="relative">
                        <Lock class="pointer-events-none absolute left-3 top-1/2 size-4 -translate-y-1/2 text-[var(--slate-light)]" />
                        <input
                            id="partner-reset-pw2"
                            v-model="form.password_confirmation"
                            :type="showConfirm ? 'text' : 'password'"
                            required
                            autocomplete="new-password"
                            class="w-full rounded-lg border border-[var(--linen)] bg-[var(--white)] py-2.5 pl-10 pr-10 text-sm text-[var(--midnight)] transition-colors focus:border-[var(--orange)] focus:outline-none focus:ring-2 focus:ring-[var(--orange)]/15"
                        />
                        <button
                            type="button"
                            class="absolute right-2 top-1/2 -translate-y-1/2 rounded-md p-1.5 text-[var(--slate-light)] hover:bg-[var(--paper)] hover:text-[var(--slate)]"
                            :aria-label="showConfirm ? t.hide_password : t.show_password"
                            @click="showConfirm = !showConfirm"
                        >
                            <EyeOff v-if="showConfirm" class="size-4" />
                            <Eye v-else class="size-4" />
                        </button>
                    </div>
                </div>

                <button
                    type="submit"
                    :disabled="form.processing"
                    class="flex w-full items-center justify-center gap-2 rounded-lg bg-[var(--orange)] px-4 py-3 text-sm font-semibold text-[var(--white)] transition-colors hover:bg-[color-mix(in_srgb,var(--orange)_85%,black)] disabled:cursor-not-allowed disabled:opacity-60"
                >
                    <Loader2 v-if="form.processing" class="size-4 animate-spin" />
                    <CheckCircle2 v-else class="size-4" />
                    <span>{{ form.processing ? t.submitting : submitLabel }}</span>
                </button>
            </form>
        </div>
    </PartnerAuthShell>
</template>
