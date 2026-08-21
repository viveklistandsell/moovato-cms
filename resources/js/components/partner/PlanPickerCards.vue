<script setup lang="ts">
/**
 * Reusable 3-column plan picker.
 *
 * Used both at registration (`Register.vue` — pre-purchase choice)
 * and on the standalone plans page (`partner/plans/Index.vue` —
 * upgrade / downgrade). The rendering is identical; only the
 * `currentTier` differs.
 *
 * Interaction is v-model — parent controls which tier is
 * selected. The `disabled` prop disables the cards while a
 * submit is in flight.
 *
 * Copy is bilingual and inline (short strings, no need for a
 * lang file — matches the pattern in the auth pages).
 */
import { CheckCircle2, MinusCircle, Sparkles } from 'lucide-vue-next';
import { computed } from 'vue';

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

const props = defineProps<{
    modelValue: string;
    tiers: Tier[];
    currentTier?: string | null;
    disabled?: boolean;
    locale: string;
    highlight?: 'premium' | 'gold' | null;
}>();

const emit = defineEmits<{
    (e: 'update:modelValue', v: string): void;
}>();

const highlightSlug = computed(() => props.highlight ?? 'premium');

const t = computed(() => (props.locale === 'de'
    ? {
        most_popular: 'Meistgewählt',
        current_plan: 'Aktueller Plan',
        select: 'Auswählen',
        selected: 'Ausgewählt',
        free: 'Kostenlos',
        per: 'pro',
        cap_unlimited: 'Unbegrenzt',
        included: 'enthalten',
        not_included: 'nicht enthalten',
        caps_heading: 'Limits',
        features_heading: 'Funktionen',
        cap_gallery: 'Fotos',
        cap_contacts: 'Kontakte',
        cap_services: 'Leistungen',
        cap_areas: 'Einsatzgebiete',
        cap_faqs: 'FAQs',
        cap_reply_reviews: 'Antworten auf Bewertungen',
        feat_short_description: 'Kurzbeschreibung',
        feat_about: 'Über uns / Beschreibung',
        feat_founded: 'Gründungsjahr',
        feat_employees: 'Mitarbeiterzahl',
        feat_faqs: 'FAQs',
        feat_cover: 'Titelbild',
        feat_trust: 'Vertrauens-Abzeichen',
        feat_google: 'Google-Bewertungen',
        feat_reply_reviews: 'Auf Bewertungen antworten',
        placement_standard: 'Standard-Platzierung',
        placement_boosted: 'Bevorzugte Platzierung',
        placement_featured: 'Top-Platzierung + Hervorhebung',
    }
    : {
        most_popular: 'Most popular',
        current_plan: 'Current plan',
        select: 'Select',
        selected: 'Selected',
        free: 'Free',
        per: 'per',
        cap_unlimited: 'Unlimited',
        included: 'included',
        not_included: 'not included',
        caps_heading: 'Limits',
        features_heading: 'Features',
        cap_gallery: 'Photos',
        cap_contacts: 'Contacts',
        cap_services: 'Services',
        cap_areas: 'Service areas',
        cap_faqs: 'FAQs',
        cap_reply_reviews: 'Review replies',
        feat_short_description: 'Short description',
        feat_about: 'About / description',
        feat_founded: 'Founded year',
        feat_employees: 'Employee count',
        feat_faqs: 'FAQs',
        feat_cover: 'Cover image',
        feat_trust: 'Trust badges',
        feat_google: 'Google reviews',
        feat_reply_reviews: 'Reply to reviews',
        placement_standard: 'Standard placement',
        placement_boosted: 'Boosted placement',
        placement_featured: 'Top placement + featured',
    }));

/**
 * Fixed ordering so the sections match the config file & the caps
 * always compare like-for-like across the three columns.
 */
const featureRows = [
    'short_description',
    'about',
    'founded',
    'employees',
    'faqs',
    'cover',
    'trust',
    'google',
] as const;

// `reply_reviews` moved from features → caps so it can express a
// lifetime allowance (Basic: 10; Premium / Gold: unlimited).
const capRows = ['gallery', 'contacts', 'services', 'areas', 'faqs', 'reply_reviews'] as const;

function featureLabel(slug: string): string {
    return (t.value as Record<string, string>)[`feat_${slug}`] ?? slug;
}

function capLabel(slug: string): string {
    return (t.value as Record<string, string>)[`cap_${slug}`] ?? slug;
}

function placementLabel(slug: string): string {
    return (t.value as Record<string, string>)[`placement_${slug}`] ?? slug;
}

function capValue(tier: Tier, slug: string): string {
    const v = tier.caps[slug];
    if (v === null || v === undefined) return t.value.cap_unlimited;
    return String(v);
}

function priceLabel(tier: Tier): string {
    if (tier.is_free) return t.value.free;
    return `${tier.currency}${tier.price}`;
}

function select(slug: string): void {
    if (props.disabled) return;
    emit('update:modelValue', slug);
}
</script>

<template>
    <div class="grid gap-4 md:grid-cols-3">
        <div
            v-for="tier in tiers"
            :key="tier.slug"
            class="relative flex flex-col rounded-xl border-2 p-5 transition-all"
            :class="[
                modelValue === tier.slug
                    ? 'border-[var(--orange)] bg-[var(--orange-soft)]/40 shadow-md'
                    : 'border-[var(--linen)] bg-[var(--white)] hover:border-[var(--orange)]/40',
                disabled ? 'opacity-70' : '',
                tier.slug === highlightSlug ? 'md:-translate-y-1' : '',
            ]"
        >
            <!-- "Most popular" ribbon -->
            <div
                v-if="tier.slug === highlightSlug"
                class="absolute -top-3 left-1/2 -translate-x-1/2 rounded-full bg-[var(--orange)] px-3 py-1 text-[10px] font-semibold uppercase tracking-wider text-[var(--white)] shadow"
            >
                <Sparkles class="mr-1 inline size-3" />
                {{ t.most_popular }}
            </div>

            <!-- Header -->
            <div class="mb-4">
                <p class="text-xs font-semibold uppercase tracking-wider text-[var(--slate-light)]">
                    {{ tier.label }}
                </p>
                <div class="mt-2 flex items-baseline gap-1">
                    <span class="text-3xl font-bold text-[var(--midnight)]">
                        {{ priceLabel(tier) }}
                    </span>
                    <span v-if="!tier.is_free" class="text-xs text-[var(--slate)]">
                        / {{ t.per }} {{ tier.period }}
                    </span>
                </div>
                <p class="mt-2 min-h-[36px] text-xs leading-relaxed text-[var(--slate)]">
                    {{ tier.positioning }}
                </p>
            </div>

            <!-- Current-plan pill -->
            <div
                v-if="currentTier === tier.slug"
                class="mb-3 inline-flex w-fit items-center gap-1 rounded-full border border-[var(--orange)]/30 bg-[var(--orange-soft)] px-2 py-0.5 text-[10px] font-semibold uppercase tracking-wider text-[var(--orange)]"
            >
                <CheckCircle2 class="size-3" />
                {{ t.current_plan }}
            </div>

            <!-- Placement -->
            <p class="mb-4 flex items-center gap-1.5 text-[11px] font-medium text-[var(--midnight)]">
                <CheckCircle2 class="size-3.5 text-[var(--orange)]" />
                {{ placementLabel(tier.placement) }}
            </p>

            <!-- Caps -->
            <div class="mb-3">
                <p class="mb-1.5 text-[10px] font-semibold uppercase tracking-wider text-[var(--slate-light)]">
                    {{ t.caps_heading }}
                </p>
                <ul class="space-y-1 text-xs">
                    <li v-for="row in capRows" :key="row" class="flex items-center justify-between text-[var(--slate)]">
                        <span>{{ capLabel(row) }}</span>
                        <span class="font-medium text-[var(--midnight)]">
                            {{ capValue(tier, row) }}
                        </span>
                    </li>
                </ul>
            </div>

            <!-- Features -->
            <div class="mb-4 flex-1">
                <p class="mb-1.5 text-[10px] font-semibold uppercase tracking-wider text-[var(--slate-light)]">
                    {{ t.features_heading }}
                </p>
                <ul class="space-y-1 text-xs">
                    <li
                        v-for="row in featureRows"
                        :key="row"
                        class="flex items-start gap-1.5"
                        :class="tier.features[row] ? 'text-[var(--slate)]' : 'text-[var(--slate-light)] line-through'"
                    >
                        <CheckCircle2 v-if="tier.features[row]" class="mt-0.5 size-3.5 shrink-0 text-[var(--orange)]" />
                        <MinusCircle v-else class="mt-0.5 size-3.5 shrink-0 text-[var(--slate-light)]" />
                        {{ featureLabel(row) }}
                    </li>
                </ul>
            </div>

            <!-- Select button (bottom-anchored via flex-1 above) -->
            <button
                type="button"
                class="w-full rounded-lg px-4 py-2.5 text-sm font-semibold transition-colors disabled:cursor-not-allowed disabled:opacity-60"
                :class="modelValue === tier.slug
                    ? 'bg-[var(--orange)] text-[var(--white)] hover:bg-[color-mix(in_srgb,var(--orange)_85%,black)]'
                    : 'border border-[var(--orange)] text-[var(--orange)] hover:bg-[var(--orange-soft)]'"
                :disabled="disabled"
                @click="select(tier.slug)"
            >
                {{ modelValue === tier.slug ? t.selected : t.select }}
            </button>
        </div>
    </div>
</template>
