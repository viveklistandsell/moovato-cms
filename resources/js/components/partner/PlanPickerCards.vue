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
import { CheckCircle2, CircleX, Crown, Sparkles, Truck } from 'lucide-vue-next';
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

const iconBySlug: Record<string, typeof Truck> = {
    basic: Truck,
    premium: Sparkles,
    gold: Crown,
};

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
        feat_lead: 'Lead-Button',
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
        feat_lead: 'Lead button',
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
    'lead',
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
    <div class="mv-plan-picker grid gap-6 md:grid-cols-3">
        <div
            v-for="tier in tiers"
            :key="tier.slug"
            class="mv-plan-picker__card"
            :class="[
                modelValue === tier.slug ? 'is-selected' : '',
                tier.slug === highlightSlug ? 'is-highlight' : '',
                disabled ? 'is-disabled' : '',
            ]"
            role="button"
            :tabindex="disabled ? -1 : 0"
            @click="select(tier.slug)"
            @keydown.enter="select(tier.slug)"
            @keydown.space.prevent="select(tier.slug)"
        >
            <div class="mv-plan-picker__shape" aria-hidden="true" />
            <div class="mv-plan-picker__content">
                <span v-if="tier.slug === highlightSlug" class="mv-plan-picker__ribbon">
                    {{ t.most_popular }}
                </span>

                <div class="mv-plan-picker__inner">
                    <div class="mv-plan-picker__icon">
                        <component :is="iconBySlug[tier.slug] ?? Truck" :size="22" />
                    </div>

                    <p class="mv-plan-picker__label">{{ tier.label }}</p>
                    <div class="mv-plan-picker__price">
                        {{ priceLabel(tier) }}
                        <span v-if="!tier.is_free" class="mv-plan-picker__period">
                            / {{ t.per }} {{ tier.period }}
                        </span>
                    </div>
                    <p class="mv-plan-picker__positioning">{{ tier.positioning }}</p>

                    <div v-if="currentTier === tier.slug" class="mv-plan-picker__current">
                        <CheckCircle2 :size="12" />
                        {{ t.current_plan }}
                    </div>

                    <p class="mv-plan-picker__placement">
                        <CheckCircle2 :size="14" />
                        {{ placementLabel(tier.placement) }}
                    </p>

                    <div class="mv-plan-picker__section">
                        <p class="mv-plan-picker__section-heading">{{ t.caps_heading }}</p>
                        <ul class="mv-plan-picker__caps">
                            <li v-for="row in capRows" :key="row">
                                <span>{{ capLabel(row) }}</span>
                                <span>{{ capValue(tier, row) }}</span>
                            </li>
                        </ul>
                    </div>

                    <div class="mv-plan-picker__section mv-plan-picker__section--grow">
                        <p class="mv-plan-picker__section-heading">{{ t.features_heading }}</p>
                        <ul class="mv-plan-picker__features">
                            <li
                                v-for="row in featureRows"
                                :key="row"
                                :class="{ 'is-muted': !tier.features[row] }"
                            >
                                <CheckCircle2 v-if="tier.features[row]" :size="14" />
                                <CircleX v-else :size="14" />
                                <span>{{ featureLabel(row) }}</span>
                            </li>
                        </ul>
                    </div>

                    <button
                        type="button"
                        class="mv-plan-picker__btn"
                        :disabled="disabled"
                        @click.stop="select(tier.slug)"
                    >
                        {{ modelValue === tier.slug ? t.selected : t.select }}
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>
