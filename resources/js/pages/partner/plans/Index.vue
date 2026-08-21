<script setup lang="ts">

import { Head, Link, router, useForm, usePage } from '@inertiajs/vue3';
import { AlertTriangle, ArrowLeft, CheckCircle2, Clock, LayoutDashboard, Loader2, XCircle } from 'lucide-vue-next';
import { computed } from 'vue';
import PlanPickerCards from '@/components/partner/PlanPickerCards.vue';

type Tier = {
    slug: 'basic' | 'premium' | 'gold';
    label: string;
    price: number;
    currency: string;
    period: string;
    positioning: string;
    placement: string;
    features: Record<string, boolean>;
    caps: Record<string, number | null>;
    is_free: boolean;
};

type UserProp = {
    first_name: string;
    full_name: string;
    email: string;
    company_id: number | null;
};

type CompanyProp = {
    id: number;
    name: string;
    portal_url: string;
} | null;

type PendingRequest = {
    id: number;
    from_tier: string;
    from_label: string;
    to_tier: string;
    to_label: string;
    created_at: string | null;
};

const props = defineProps<{
    locale: string;
    user: UserProp | null;
    company: CompanyProp;
    currentTier: string;
    pendingRequest: PendingRequest | null;
    plans: Tier[];
    loginUrl: string;
    registerUrl: string;
}>();

type FlashBag = { success?: string; warning?: string; error?: string };
const flash = computed<FlashBag>(
    () => (usePage().props as { flash?: FlashBag }).flash ?? {},
);

const de = {
    title: 'Ihren Plan verwalten',
    subtitle: 'Wählen Sie den gewünschten Plan aus. Ihre Anfrage wird von unserem Team geprüft und freigeschaltet.',
    apply: 'Plan anfragen',
    applying: 'Wird gesendet…',
    already_on: 'Sie sind bereits auf diesem Plan.',
    downgrade_note: 'Hinweis: Ein Downgrade entfernt Inhalte, die die neuen Limits überschreiten (z. B. zusätzliche Fotos, Kontakte, FAQs) — sobald der Wechsel bestätigt wurde.',
    no_company: 'Kein Unternehmen mit Ihrem Konto verknüpft — bitte kontaktieren Sie den Support.',
    approval_notice: 'Plan-Wechsel benötigen die Freigabe unseres Teams. Nach dem Absenden erhalten Sie eine E-Mail, sobald die Änderung aktiv ist.',
    pending_title: 'Anfrage in Prüfung',
    pending_body: 'Ihre Anfrage von {from} auf {to} liegt bei unserem Team. Sobald ein Admin sie freigibt, wird Ihr Plan automatisch umgestellt.',
    pending_cancel: 'Anfrage zurückziehen',
    pending_confirm_cancel: 'Möchten Sie diese Plan-Anfrage wirklich zurückziehen?',
    back_to_dashboard: 'Zurück zum Dashboard',
    open_dashboard: 'Dashboard öffnen',
    guest_notice: 'Melden Sie sich als Partner an, um einen Plan-Wechsel zu beantragen.',
    login_cta: 'Als Partner anmelden',
    register_cta: 'Neu registrieren',
} as const;
const en = {
    title: 'Manage your plan',
    subtitle: 'Pick the plan you want. Our team will review your request and activate it.',
    apply: 'Request plan',
    applying: 'Sending…',
    already_on: 'You are already on this plan.',
    downgrade_note: 'Note: downgrading removes content beyond the new limits (extra photos, contacts, FAQs, etc.) — once the change is approved.',
    no_company: 'No company linked to your account — please contact support.',
    approval_notice: 'Plan changes need admin approval. After you submit, you will receive an email as soon as the change is live.',
    pending_title: 'Request under review',
    pending_body: 'Your request from {from} to {to} is with our team. As soon as an admin approves it your plan will switch automatically.',
    pending_cancel: 'Cancel request',
    pending_confirm_cancel: 'Do you really want to cancel this plan request?',
    back_to_dashboard: 'Back to dashboard',
    open_dashboard: 'Open dashboard',
    guest_notice: 'Sign in as a partner to request a plan change.',
    login_cta: 'Sign in as partner',
    register_cta: 'Sign up',
} as const;
const t = computed(() => (props.locale === 'de' ? de : en));

function interpolate(msg: string, vars: Record<string, string>): string {
    return msg.replace(/\{(\w+)\}/g, (_, k) => vars[k] ?? '');
}

const form = useForm<{ plan_tier: 'basic' | 'premium' | 'gold' }>({
    plan_tier: (props.currentTier as 'basic' | 'premium' | 'gold') || 'basic',
});

const hasPending = computed(() => props.pendingRequest !== null);
const isGuest = computed(() => props.user === null);

const canSubmit = computed(
    () =>
        form.plan_tier !== props.currentTier
        && !form.processing
        && props.company !== null
        && !hasPending.value
        && !isGuest.value,
);

const isDowngrade = computed(() => {
    const rank = (slug: string): number => ['basic', 'premium', 'gold'].indexOf(slug);
    return rank(form.plan_tier) < rank(props.currentTier);
});

function submit(): void {
    if (!canSubmit.value) return;
    form.post('/partner/plans/choose', { preserveScroll: true });
}

function cancelPending(): void {
    if (props.pendingRequest === null) return;
    if (!confirm(t.value.pending_confirm_cancel)) return;
    router.delete(`/partner/plans/request/${props.pendingRequest.id}`, { preserveScroll: true });
}
</script>

<template>
    <Head :title="t.title" />

    <div class="min-h-svh bg-[var(--paper)]">
        <main class="mx-auto max-w-6xl px-4 py-8 sm:px-6 lg:py-10">
            <!-- Back link — only shown when the visitor is a signed-in
                 partner (guests don't have a dashboard to return to). -->
            <Link
                v-if="!isGuest"
                href="/partner/dashboard"
                class="mb-4 inline-flex items-center gap-1.5 text-sm font-medium text-[var(--slate)] hover:text-[var(--orange)]"
            >
                <ArrowLeft class="size-4" />
                {{ t.back_to_dashboard }}
            </Link>


            <header class="mb-6">
                <h1 class="text-2xl font-bold text-[var(--midnight)] sm:text-3xl">{{ t.title }}</h1>
                <p class="mt-1 text-sm text-[var(--slate)]">{{ t.subtitle }}</p>
            </header>

            <!-- Flash success / warning -->
            <div
                v-if="flash.success"
                class="mb-4 flex items-start gap-2 rounded-lg border border-[var(--orange)]/25 bg-[var(--orange-soft)] px-3 py-2.5 text-sm text-[var(--midnight)]"
            >
                <CheckCircle2 class="mt-0.5 size-4 shrink-0 text-[var(--orange)]" />
                <span>{{ flash.success }}</span>
            </div>
            <div
                v-if="flash.warning"
                class="mb-4 flex items-start gap-2 rounded-lg border border-amber-300 bg-amber-50 px-3 py-2.5 text-sm text-amber-900"
            >
                <AlertTriangle class="mt-0.5 size-4 shrink-0 text-amber-600" />
                <span>{{ flash.warning }}</span>
            </div>

            <!-- Pending request card — visible when the partner has an open request. -->
            <div
                v-if="pendingRequest"
                class="mb-6 flex flex-col gap-3 rounded-xl border border-amber-300 bg-amber-50 p-4 sm:flex-row sm:items-center sm:justify-between"
            >
                <div class="flex items-start gap-3">
                    <Clock class="mt-0.5 size-5 shrink-0 text-amber-600" />
                    <div>
                        <p class="font-semibold text-amber-900">{{ t.pending_title }}</p>
                        <p class="mt-0.5 text-sm text-amber-800">
                            {{ interpolate(t.pending_body, { from: pendingRequest.from_label, to: pendingRequest.to_label }) }}
                        </p>
                    </div>
                </div>
                <button
                    type="button"
                    class="inline-flex items-center gap-1.5 self-start rounded-lg border border-amber-400 bg-white px-3 py-1.5 text-xs font-semibold text-amber-900 hover:bg-amber-100 sm:self-auto"
                    @click="cancelPending"
                >
                    <XCircle class="size-3.5" />
                    {{ t.pending_cancel }}
                </button>
            </div>
            <div
                v-if="!pendingRequest && !isGuest"
                class="mb-6 flex items-start gap-2 rounded-lg border border-[var(--linen)] bg-[var(--white)] p-3 text-xs text-[var(--slate)]"
            >
                <AlertTriangle class="mt-0.5 size-4 shrink-0 text-[var(--orange)]" />
                <span>{{ t.approval_notice }}</span>
            </div>
            <div
                v-if="!company && !isGuest"
                class="mb-6 rounded-lg border border-[var(--linen)] bg-[var(--white)] p-4 text-sm text-[var(--slate)]"
            >
                {{ t.no_company }}
            </div>

            <!-- The picker itself -->
            <PlanPickerCards
                v-model="form.plan_tier"
                :tiers="props.plans"
                :locale="props.locale"
                :current-tier="props.currentTier"
                :disabled="!isGuest && (form.processing || !company || hasPending)"
            />

            <!-- Downgrade warning -->
            <div
                v-if="isDowngrade && canSubmit"
                class="mt-6 rounded-lg border border-[var(--orange)]/25 bg-[var(--orange-soft)]/50 p-3 text-xs text-[var(--midnight)]"
            >
                {{ t.downgrade_note }}
            </div>
            <div
                v-if="!isGuest"
                class="mt-6 flex flex-wrap items-center justify-end gap-2"
            >
                <p v-if="form.plan_tier === props.currentTier" class="mr-auto text-xs text-[var(--slate)]">
                    {{ t.already_on }}
                </p>
                <Link
                    href="/partner/dashboard"
                    class="inline-flex items-center gap-1.5 rounded-lg border border-[var(--linen)] bg-[var(--white)] px-4 py-2.5 text-sm font-medium text-[var(--slate)] transition-colors hover:border-[var(--orange)] hover:text-[var(--orange)]"
                >
                    <LayoutDashboard class="size-4" />
                    {{ t.open_dashboard }}
                </Link>
                <button
                    type="button"
                    class="inline-flex items-center gap-2 rounded-lg bg-[var(--orange)] px-5 py-2.5 text-sm font-semibold text-[var(--white)] transition-colors hover:bg-[color-mix(in_srgb,var(--orange)_85%,black)] disabled:cursor-not-allowed disabled:opacity-60"
                    :disabled="!canSubmit"
                    @click="submit"
                >
                    <Loader2 v-if="form.processing" class="size-4 animate-spin" />
                    {{ form.processing ? t.applying : t.apply }}
                </button>
            </div>
        </main>
    </div>
</template>
