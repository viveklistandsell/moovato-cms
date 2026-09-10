<script setup lang="ts">
import { usePage } from '@inertiajs/vue3';
import { ArrowUpRight, Check, Crown, Sparkles, Truck, X } from 'lucide-vue-next';
import { computed, ref } from 'vue';

type Plan = {
    slug: 'basic' | 'premium' | 'gold';
    label: string;
    price: number;
    currency: string;
    period: string;
    positioning: string | null;
    placement: string;
    features: Record<string, boolean>;
    caps: Record<string, number | null>;
    is_free: boolean;
};

type Settings = {
    highlight?: 'basic' | 'premium' | 'gold' | null;
    cta_url?: string;
    plans?: Plan[];
};

type Data = {
    eyebrow?: string;
    heading?: string;
    description?: string;
    cta_label?: string;
};

const props = defineProps<{ settings: Settings; data: Data }>();

const page = usePage();
const locale = computed(() => (page.props.locale as string | undefined) ?? 'de');

// Live plan data is injected by PageController::resolveLivePlans() at render
// time and is absent in the admin builder's client-only preview — fall back
// to a realistic demo set (mirrors the seeded `plans` rows) so editors still
// see a populated card layout while placing the widget.
const demoPlans: Plan[] = [
    {
        slug: 'basic',
        label: 'Basic',
        price: 0,
        currency: '€',
        period: 'month',
        positioning: 'Kostenlose Präsenz für jeden Partner.',
        placement: 'standard',
        features: {
            short_description: false, about: false, founded: false,
            employees: false, faqs: false, cover: false, trust: false, google: false,
        },
        caps: { contacts: 1, services: 1, areas: 3, gallery: 3, faqs: 0, reply_reviews: 10 },
        is_free: true,
    },
    {
        slug: 'premium',
        label: 'Premium',
        price: 19,
        currency: '€',
        period: 'month',
        positioning: 'Der beliebteste Plan für aktive Umzugsunternehmen.',
        placement: 'boosted',
        features: {
            short_description: true, about: true, founded: true,
            employees: true, faqs: true, cover: true, trust: false, google: true,
        },
        caps: { contacts: 5, services: 5, areas: 10, gallery: 10, faqs: 10, reply_reviews: null },
        is_free: false,
    },
    {
        slug: 'gold',
        label: 'Gold',
        price: 49,
        currency: '€',
        period: 'month',
        positioning: 'Alles an, Top-Platzierung, Vertrauens-Badges.',
        placement: 'featured',
        features: {
            short_description: true, about: true, founded: true,
            employees: true, faqs: true, cover: true, trust: true, google: true,
        },
        caps: { contacts: null, services: null, areas: null, gallery: null, faqs: null, reply_reviews: null },
        is_free: false,
    },
];

const plans = computed<Plan[]>(() =>
    props.settings.plans && props.settings.plans.length > 0
        ? props.settings.plans
        : demoPlans,
);

const highlightSlug = computed(() => props.settings.highlight ?? 'premium');

// Clicking a card pins the "filled" hover treatment so a visitor can compare
// plans one at a time without needing to keep the mouse hovered.
const selectedSlug = ref<string | null>(null);

function toggleSelect(slug: string): void {
    selectedSlug.value = selectedSlug.value === slug ? null : slug;
}

const iconBySlug: Record<string, typeof Truck> = {
    basic: Truck,
    premium: Sparkles,
    gold: Crown,
};

const t = computed(() => (locale.value === 'de'
    ? {
        most_popular: 'Meistgewählt',
        free: 'Kostenlos',
        per: 'pro',
        unlimited: 'Unbegrenzt',
        cap_gallery: 'Fotos', cap_contacts: 'Kontakte', cap_services: 'Leistungen',
        cap_areas: 'Einsatzgebiete', cap_faqs: 'FAQs', cap_reply_reviews: 'Antworten auf Bewertungen',
        feat_short_description: 'Kurzbeschreibung', feat_about: 'Über uns / Beschreibung',
        feat_founded: 'Gründungsjahr', feat_employees: 'Mitarbeiterzahl', feat_faqs: 'FAQs',
        feat_cover: 'Titelbild', feat_trust: 'Vertrauens-Abzeichen', feat_google: 'Google-Bewertungen',
    }
    : {
        most_popular: 'Most popular',
        free: 'Free',
        per: 'per',
        unlimited: 'Unlimited',
        cap_gallery: 'Photos', cap_contacts: 'Contacts', cap_services: 'Services',
        cap_areas: 'Service areas', cap_faqs: 'FAQs', cap_reply_reviews: 'Review replies',
        feat_short_description: 'Short description', feat_about: 'About / description',
        feat_founded: 'Founded year', feat_employees: 'Employee count', feat_faqs: 'FAQs',
        feat_cover: 'Cover image', feat_trust: 'Trust badges', feat_google: 'Google reviews',
    }));

const FEATURE_KEYS = ['short_description', 'about', 'founded', 'employees', 'faqs', 'cover', 'trust', 'google'] as const;
const CAP_KEYS = ['gallery', 'contacts', 'services', 'areas', 'faqs', 'reply_reviews'] as const;

type Row = { label: string; included: boolean };

function rowsFor(plan: Plan): Row[] {
    const rows: Row[] = FEATURE_KEYS
        .filter((key) => key in plan.features)
        .map((key) => ({
            label: (t.value as Record<string, string>)[`feat_${key}`] ?? key,
            included: !!plan.features[key],
        }));

    for (const key of CAP_KEYS) {
        const value = plan.caps[key];
        if (value === undefined) continue;
        const label = (t.value as Record<string, string>)[`cap_${key}`] ?? key;
        rows.push({
            label: value === null ? `${label} — ${t.value.unlimited}` : `${label}: ${value}`,
            included: value === null || value > 0,
        });
    }

    return rows;
}

function priceLabel(plan: Plan): string {
    return plan.is_free ? t.value.free : `${plan.currency}${plan.price}`;
}

const ctaHref = computed(() => props.settings.cta_url || '/partner/register');
</script>

<template>
    <section v-reveal class="mv-pricing section-py">
        <div class="container-xl">
            <div class="mv-pricing__intro">
                <span v-if="data.eyebrow" class="mv-pricing__eyebrow">
                    {{ data.eyebrow }}
                </span>
                <h2 v-if="data.heading" class="mv-pricing__title mv-section-heading">
                    {{ data.heading }}
                </h2>
                <div
                    v-if="data.description"
                    class="mv-rte mt-4 text-base leading-relaxed text-[var(--slate)]"
                    v-html="data.description"
                />
            </div>

            <div class="grid grid-cols-1 gap-8 md:grid-cols-2 lg:grid-cols-3">
                <article
                    v-for="plan in plans"
                    :key="plan.slug"
                    class="mv-pricing__card"
                    :class="{ 'is-selected': selectedSlug === plan.slug }"
                    role="button"
                    tabindex="0"
                    @click="toggleSelect(plan.slug)"
                    @keydown.enter="toggleSelect(plan.slug)"
                    @keydown.space.prevent="toggleSelect(plan.slug)"
                >
                    <div class="mv-pricing__card-shape" aria-hidden="true" />
                    <div class="mv-pricing__card-content">
                        <span v-if="plan.slug === highlightSlug" class="mv-pricing__ribbon">
                            {{ t.most_popular }}
                        </span>

                        <div class="mv-pricing__card-inner">
                            <div class="mv-pricing__icon">
                                <component :is="iconBySlug[plan.slug] ?? Truck" :size="28" />
                            </div>

                            <h3 class="mv-pricing__name">{{ plan.label }}</h3>
                            <div class="mv-pricing__price">
                                {{ priceLabel(plan) }}
                                <span v-if="!plan.is_free" class="mv-pricing__period">
                                    / {{ t.per }} {{ plan.period }}
                                </span>
                            </div>
                            <p v-if="plan.positioning" class="mv-pricing__positioning">
                                {{ plan.positioning }}
                            </p>

                            <ul class="mv-pricing__features">
                                <li
                                    v-for="(row, fi) in rowsFor(plan)"
                                    :key="fi"
                                    :class="{ 'is-muted': !row.included }"
                                >
                                    <Check v-if="row.included" :size="18" />
                                    <X v-else :size="18" />
                                    <span>{{ row.label }}</span>
                                </li>
                            </ul>

                            <a
                                v-if="data.cta_label"
                                :href="ctaHref"
                                class="mv-pricing__btn"
                                :class="{ 'mv-pricing__btn--gold': plan.slug === 'gold' }"
                                @click.stop
                            >
                                <span>{{ data.cta_label }}</span>
                                <ArrowUpRight :size="16" />
                            </a>
                        </div>
                    </div>
                </article>
            </div>
        </div>
    </section>
</template>
