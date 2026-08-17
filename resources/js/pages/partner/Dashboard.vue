<script setup lang="ts">
/**
 * Partner dashboard.
 */
import { Head, Link } from '@inertiajs/vue3';
import {
    AlertTriangle,
    ArrowUpRight,
    CheckCircle2,
    Circle,
    ExternalLink,
    HelpCircle,
    Image as ImageIcon,
    MapPin,
    MessageCircleReply,
    Palette,
    Star,
    Store,
    UserRound,
} from 'lucide-vue-next';
import type { Component } from 'vue';
import { computed } from 'vue';
import PartnerTopBar from '@/components/partner/PartnerTopBar.vue';

type CompanyProp = {
    id: number;
    name: string;
    permalink: string | null;
    short_description: string | null;
    about: string | null;
    street: string | null;
    postal_code: string | null;
    city: string | null;
    status: string;
    verified: boolean;
    is_top_rated: boolean;
    logo: string | null;
    cover: string | null;
    founded_year: number | null;
    employee_count: number | null;
    portal_url: string;
    admin_edit_url: string | null;
};

type UserProp = {
    id: number;
    first_name: string;
    last_name: string;
    full_name: string;
    email: string;
    company_id: number | null;
};

type CompletenessProp = {
    percent: number;
    done: number;
    total: number;
    missing: Array<{ slug: string; label: string }>;
};

type StatsProp = {
    review_count: number;
    rating_avg: number;
    recommend_pct: number;
    google_rating: number | null;
    google_review_count: number;
    photo_count: number;
    faq_count: number;
    service_area_count: number;
    unreplied_reviews: number;
};

type ReviewProp = {
    id: number;
    author_name: string;
    rating: number;
    body: string;
    has_reply: boolean;
    replied_at: string | null;
    published_at: string | null;
};

type QuickActionProp = {
    slug: string;
    title: string;
    description: string;
    href: string;
    icon: string;
};

const props = defineProps<{
    locale: string;
    user: UserProp | null;
    company: CompanyProp | null;
    completeness: CompletenessProp | null;
    stats: StatsProp | null;
    recentReviews: ReviewProp[];
    quickActions: QuickActionProp[];
}>();

const de = {
    title: 'Partner-Dashboard',
    welcome: 'Hallo',
    welcome_generic: 'Willkommen',
    welcome_hint: 'Hier ist ein Überblick über Ihr Firmenprofil.',
    no_company_title: 'Kein Unternehmen verknüpft',
    no_company_body: 'Ihrem Konto ist derzeit kein Unternehmen zugeordnet. Bitte wenden Sie sich an unser Team, damit Ihr Profil verknüpft wird.',
    view_portal: 'Öffentliches Profil öffnen',
    edit_profile: 'Profil bearbeiten',

    status_draft: 'Entwurf',
    status_published: 'Veröffentlicht',
    status_archived: 'Archiviert',
    status_inactive: 'Inaktiv',

    draft_callout_title: 'Ihr Profil ist noch nicht öffentlich',
    draft_callout_body: 'Vervollständigen Sie Ihr Profil und wenden Sie sich an unser Team, um es zu veröffentlichen.',

    completeness_title: 'Profil-Fortschritt',
    completeness_summary: '{done} von {total} Aufgaben erledigt',
    completeness_full: 'Fantastisch — Ihr Profil ist vollständig!',
    completeness_missing_title: 'Das fehlt noch',

    stats_reviews: 'Bewertungen',
    stats_rating: 'Durchschnitt',
    stats_recommend: 'Empfehlungsrate',
    stats_photos: 'Fotos',
    stats_areas: 'Einsatzgebiete',
    stats_faqs: 'FAQs',
    stats_google: 'Google',
    stats_no_reviews: 'Noch keine Bewertungen',

    reviews_title: 'Neueste Bewertungen',
    reviews_view_all: 'Alle ansehen',
    reviews_reply: 'Antworten',
    reviews_replied: 'Antwort gesendet',
    reviews_empty: 'Noch keine Bewertungen erhalten.',
    reviews_unreplied_hint: '{n} Bewertung wartet auf Ihre Antwort.|{n} Bewertungen warten auf Ihre Antwort.',
    reviews_anonymous_hint: 'Anonym',

    actions_title: 'Schnellzugriff',

    address_pieces: 'Adresse',
    founded_year: 'Gegründet',
    employees_label: 'Mitarbeiter',
} as const;

const en = {
    title: 'Partner dashboard',
    welcome: 'Hi',
    welcome_generic: 'Welcome',
    welcome_hint: 'Here\'s an overview of your company profile.',
    no_company_title: 'No company linked',
    no_company_body: 'There is no company linked to your account yet. Please contact our team so we can link your profile.',
    view_portal: 'Open public listing',
    edit_profile: 'Edit profile',

    status_draft: 'Draft',
    status_published: 'Published',
    status_archived: 'Archived',
    status_inactive: 'Inactive',

    draft_callout_title: 'Your listing is not public yet',
    draft_callout_body: 'Complete your profile and contact our team to publish it.',

    completeness_title: 'Profile progress',
    completeness_summary: '{done} of {total} items complete',
    completeness_full: 'Great — your profile is complete!',
    completeness_missing_title: 'Still missing',

    stats_reviews: 'Reviews',
    stats_rating: 'Average',
    stats_recommend: 'Recommend rate',
    stats_photos: 'Photos',
    stats_areas: 'Service areas',
    stats_faqs: 'FAQs',
    stats_google: 'Google',
    stats_no_reviews: 'No reviews yet',

    reviews_title: 'Recent reviews',
    reviews_view_all: 'View all',
    reviews_reply: 'Reply',
    reviews_replied: 'Reply sent',
    reviews_empty: 'No reviews received yet.',
    reviews_unreplied_hint: '{n} review is waiting for your reply.|{n} reviews are waiting for your reply.',
    reviews_anonymous_hint: 'Anonymous',

    actions_title: 'Quick actions',

    address_pieces: 'Address',
    founded_year: 'Founded',
    employees_label: 'Employees',
} as const;
const t = computed(() => (props.locale === 'de' ? de : en));

function statusLabel(status: string): string {
    switch (status) {
        case 'published': return t.value.status_published;
        case 'draft': return t.value.status_draft;
        case 'archived': return t.value.status_archived;
        case 'inactive': return t.value.status_inactive;
        default: return status;
    }
}

/**
 * Split a pipe-separated singular|plural template and interpolate
 * {n}. Mirrors Laravel's trans_choice on the client so we don't
 * ship an i18n library for a single string.
 */
function plural(template: string, n: number): string {
    const [singular, pluralForm] = template.split('|');
    const chosen = n === 1 ? singular : (pluralForm ?? singular);
    return chosen.replace('{n}', String(n));
}

function fmtRating(v: number): string {
    if (v <= 0) return '—';
    return v.toFixed(1);
}

function fmtDate(iso: string | null): string {
    if (!iso) return '';
    try {
        return new Date(iso).toLocaleDateString(props.locale === 'de' ? 'de-DE' : 'en-US', {
            year: 'numeric', month: 'short', day: 'numeric',
        });
    } catch {
        return iso;
    }
}

const address = computed(() => {
    if (!props.company) return '';
    const parts = [
        props.company.street,
        [props.company.postal_code, props.company.city].filter(Boolean).join(' '),
    ].filter((p): p is string => !!p && p.trim() !== '');
    return parts.join(', ');
});

const iconMap: Record<string, Component> = {
    store: Store,
    image: ImageIcon,
    'help-circle': HelpCircle,
    'map-pin': MapPin,
    star: Star,
    palette: Palette,
};
</script>

<template>
    <Head :title="t.title" />

    <div class="min-h-svh bg-[var(--paper)]">
        <PartnerTopBar :locale="props.locale" :user="user" :portal-url="company?.portal_url ?? null" />

        <main class="mx-auto max-w-6xl px-4 py-8 sm:px-6 lg:py-10">
            <!-- === No company linked === -->
            <div
                v-if="!company"
                class="rounded-xl border border-[var(--linen)] bg-[var(--white)] p-6 text-center shadow-sm sm:p-10"
            >
                <div class="mx-auto flex size-14 items-center justify-center rounded-full bg-[var(--orange-soft)] text-[var(--orange)]">
                    <AlertTriangle class="size-6" />
                </div>
                <h1 class="mt-4 text-xl font-bold text-[var(--midnight)]">{{ t.no_company_title }}</h1>
                <p class="mx-auto mt-2 max-w-md text-sm text-[var(--slate)]">{{ t.no_company_body }}</p>
            </div>

            <template v-else>
                <!-- === Welcome header === -->
                <header class="flex flex-wrap items-start justify-between gap-4">
                    <div class="min-w-0 flex-1">
                        <p class="text-xs font-medium uppercase tracking-wider text-[var(--slate-light)]">
                            {{ user ? `${t.welcome}, ${user.first_name}` : t.welcome_generic }}
                        </p>
                        <h1 class="mt-1 truncate text-2xl font-bold text-[var(--midnight)] sm:text-3xl">
                            {{ company.name }}
                        </h1>
                        <p class="mt-1 text-sm text-[var(--slate)]">{{ t.welcome_hint }}</p>
                        <div class="mt-3 flex flex-wrap items-center gap-2">
                            <span
                                class="inline-flex items-center gap-1 rounded-full px-2.5 py-1 text-[10px] font-semibold uppercase tracking-wider"
                                :class="company.status === 'published'
                                    ? 'bg-[var(--orange-soft)] text-[var(--orange)]'
                                    : 'bg-[var(--linen)] text-[var(--midnight)]'"
                            >
                                <span
                                    class="size-1.5 rounded-full"
                                    :class="company.status === 'published' ? 'bg-[var(--orange)]' : 'bg-[var(--slate-light)]'"
                                ></span>
                                {{ statusLabel(company.status) }}
                            </span>
                            <span v-if="company.verified" class="inline-flex items-center gap-1 rounded-full bg-[var(--orange-soft)] px-2.5 py-1 text-[10px] font-semibold uppercase tracking-wider text-[var(--orange)]">
                                <CheckCircle2 class="size-3" />
                                Verifiziert
                            </span>
                            <span v-if="address" class="inline-flex items-center gap-1 text-xs text-[var(--slate)]">
                                <MapPin class="size-3.5" />
                                {{ address }}
                            </span>
                        </div>
                    </div>
                    <a
                        v-if="company.portal_url"
                        :href="company.portal_url"
                        target="_blank"
                        rel="noopener"
                        class="inline-flex shrink-0 items-center gap-2 rounded-lg bg-[var(--orange)] px-4 py-2.5 text-sm font-semibold text-[var(--white)] transition-colors hover:bg-[color-mix(in_srgb,var(--orange)_85%,black)]"
                    >
                        <Store class="size-4" />
                        {{ t.view_portal }}
                        <ArrowUpRight class="size-4" />
                    </a>
                </header>

                <!-- === Draft-status callout === -->
                <div
                    v-if="company.status !== 'published'"
                    class="mt-6 flex items-start gap-3 rounded-xl border border-[var(--orange)]/25 bg-[var(--orange-soft)] p-4"
                >
                    <div class="flex size-9 shrink-0 items-center justify-center rounded-full bg-[var(--white)] text-[var(--orange)]">
                        <AlertTriangle class="size-4" />
                    </div>
                    <div class="min-w-0 flex-1">
                        <p class="text-sm font-semibold text-[var(--midnight)]">{{ t.draft_callout_title }}</p>
                        <p class="mt-0.5 text-xs text-[var(--slate)]">{{ t.draft_callout_body }}</p>
                    </div>
                </div>

                <!-- === Completeness meter === -->
                <section v-if="completeness" class="mt-6 rounded-xl border border-[var(--linen)] bg-[var(--white)] p-5 shadow-sm sm:p-6">
                    <div class="flex flex-wrap items-start justify-between gap-3">
                        <div>
                            <h2 class="text-sm font-semibold text-[var(--midnight)]">{{ t.completeness_title }}</h2>
                            <p class="mt-0.5 text-xs text-[var(--slate)]">
                                {{ t.completeness_summary.replace('{done}', String(completeness.done)).replace('{total}', String(completeness.total)) }}
                            </p>
                        </div>
                        <span class="text-3xl font-bold text-[var(--orange)]">{{ completeness.percent }}%</span>
                    </div>
                    <!-- Progress bar (theme tokens only) -->
                    <div class="mt-3 h-2 w-full overflow-hidden rounded-full bg-[var(--linen)]">
                        <div
                            class="h-full rounded-full bg-[var(--orange)] transition-all"
                            :style="{ width: `${completeness.percent}%` }"
                        ></div>
                    </div>

                    <div v-if="completeness.missing.length > 0" class="mt-5 border-t border-[var(--linen)] pt-4">
                        <p class="mb-3 text-xs font-semibold uppercase tracking-wider text-[var(--slate-light)]">
                            {{ t.completeness_missing_title }}
                        </p>
                        <ul class="grid gap-2 sm:grid-cols-2">
                            <li
                                v-for="m in completeness.missing"
                                :key="m.slug"
                                class="flex items-start gap-2 rounded-md border border-[var(--linen)] bg-[var(--paper)] px-3 py-2 text-xs text-[var(--slate)]"
                            >
                                <Circle class="mt-0.5 size-3.5 shrink-0 text-[var(--slate-light)]" />
                                <span>{{ m.label }}</span>
                            </li>
                        </ul>
                    </div>
                    <p v-else class="mt-4 flex items-center gap-2 rounded-md border border-[var(--orange)]/25 bg-[var(--orange-soft)] px-3 py-2 text-xs font-medium text-[var(--midnight)]">
                        <CheckCircle2 class="size-4 text-[var(--orange)]" />
                        {{ t.completeness_full }}
                    </p>
                </section>

                <!-- === Stat strip === -->
                <section v-if="stats" class="mt-6 grid gap-3 sm:grid-cols-2 lg:grid-cols-4">
                    <div class="rounded-xl border border-[var(--linen)] bg-[var(--white)] p-4 shadow-sm">
                        <div class="flex items-center gap-2 text-[var(--slate-light)]">
                            <Star class="size-4" />
                            <p class="text-[10px] font-semibold uppercase tracking-wider">{{ t.stats_rating }}</p>
                        </div>
                        <p class="mt-2 flex items-baseline gap-1 text-2xl font-bold text-[var(--midnight)]">
                            {{ fmtRating(stats.rating_avg) }}
                            <span class="text-xs font-normal text-[var(--slate-light)]">/ 5</span>
                        </p>
                        <p class="mt-1 text-[11px] text-[var(--slate)]">
                            {{ stats.review_count > 0
                                ? `${stats.recommend_pct}% ${t.stats_recommend.toLowerCase()}`
                                : t.stats_no_reviews }}
                        </p>
                    </div>

                    <div class="rounded-xl border border-[var(--linen)] bg-[var(--white)] p-4 shadow-sm">
                        <div class="flex items-center gap-2 text-[var(--slate-light)]">
                            <MessageCircleReply class="size-4" />
                            <p class="text-[10px] font-semibold uppercase tracking-wider">{{ t.stats_reviews }}</p>
                        </div>
                        <p class="mt-2 text-2xl font-bold text-[var(--midnight)]">
                            {{ stats.review_count }}
                        </p>
                        <p class="mt-1 text-[11px] text-[var(--slate)]">
                            <span v-if="stats.unreplied_reviews > 0" class="font-medium text-[var(--orange)]">
                                {{ plural(t.reviews_unreplied_hint, stats.unreplied_reviews) }}
                            </span>
                            <span v-else>—</span>
                        </p>
                    </div>

                    <div class="rounded-xl border border-[var(--linen)] bg-[var(--white)] p-4 shadow-sm">
                        <div class="flex items-center gap-2 text-[var(--slate-light)]">
                            <ImageIcon class="size-4" />
                            <p class="text-[10px] font-semibold uppercase tracking-wider">{{ t.stats_photos }}</p>
                        </div>
                        <p class="mt-2 text-2xl font-bold text-[var(--midnight)]">{{ stats.photo_count }}</p>
                        <p class="mt-1 text-[11px] text-[var(--slate)]">
                            {{ stats.faq_count }} {{ t.stats_faqs }}
                        </p>
                    </div>

                    <div class="rounded-xl border border-[var(--linen)] bg-[var(--white)] p-4 shadow-sm">
                        <div class="flex items-center gap-2 text-[var(--slate-light)]">
                            <MapPin class="size-4" />
                            <p class="text-[10px] font-semibold uppercase tracking-wider">{{ t.stats_areas }}</p>
                        </div>
                        <p class="mt-2 text-2xl font-bold text-[var(--midnight)]">{{ stats.service_area_count }}</p>
                        <p v-if="stats.google_rating !== null && stats.google_review_count > 0" class="mt-1 text-[11px] text-[var(--slate)]">
                            {{ t.stats_google }}: {{ fmtRating(stats.google_rating) }} · {{ stats.google_review_count }}
                        </p>
                        <p v-else class="mt-1 text-[11px] text-[var(--slate)]">—</p>
                    </div>
                </section>

                <!-- === Two column: reviews + quick actions === -->
                <section class="mt-6 grid gap-6 lg:grid-cols-[minmax(0,7fr)_minmax(0,5fr)]">
                    <!-- Recent reviews -->
                    <div class="rounded-xl border border-[var(--linen)] bg-[var(--white)] shadow-sm">
                        <header class="flex items-center justify-between border-b border-[var(--linen)] px-5 py-3">
                            <h2 class="text-sm font-semibold text-[var(--midnight)]">{{ t.reviews_title }}</h2>
                            <a
                                v-if="company.portal_url"
                                :href="`${company.portal_url}#reviews`"
                                target="_blank"
                                rel="noopener"
                                class="inline-flex items-center gap-1 text-xs font-medium text-[var(--orange)] hover:underline"
                            >
                                {{ t.reviews_view_all }}
                                <ExternalLink class="size-3" />
                            </a>
                        </header>

                        <div v-if="recentReviews.length === 0" class="px-5 py-10 text-center">
                            <div class="mx-auto flex size-12 items-center justify-center rounded-full bg-[var(--paper)] text-[var(--slate-light)]">
                                <Star class="size-5" />
                            </div>
                            <p class="mt-3 text-sm text-[var(--slate)]">{{ t.reviews_empty }}</p>
                        </div>

                        <ul v-else class="divide-y divide-[var(--linen)]">
                            <li v-for="r in recentReviews" :key="r.id" class="px-5 py-4">
                                <div class="flex items-start justify-between gap-3">
                                    <div class="flex min-w-0 items-start gap-3">
                                        <div class="flex size-9 shrink-0 items-center justify-center rounded-full bg-[var(--paper)] text-xs font-semibold text-[var(--slate)]">
                                            <UserRound class="size-4" />
                                        </div>
                                        <div class="min-w-0">
                                            <p class="truncate text-sm font-medium text-[var(--midnight)]">
                                                {{ r.author_name }}
                                            </p>
                                            <div class="mt-0.5 flex items-center gap-2">
                                                <div class="flex">
                                                    <Star
                                                        v-for="i in 5"
                                                        :key="i"
                                                        class="size-3.5"
                                                        :class="i <= r.rating
                                                            ? 'fill-[var(--orange)] text-[var(--orange)]'
                                                            : 'text-[var(--linen)]'"
                                                    />
                                                </div>
                                                <span class="text-[10px] text-[var(--slate-light)]">
                                                    {{ fmtDate(r.published_at) }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                    <span
                                        v-if="r.has_reply"
                                        class="inline-flex shrink-0 items-center gap-1 rounded-full bg-[var(--orange-soft)] px-2 py-0.5 text-[10px] font-semibold text-[var(--orange)]"
                                    >
                                        <CheckCircle2 class="size-3" />
                                        {{ t.reviews_replied }}
                                    </span>
                                </div>
                                <p v-if="r.body" class="mt-2 line-clamp-3 text-xs text-[var(--slate)]">
                                    {{ r.body }}
                                </p>
                            </li>
                        </ul>
                    </div>

                    <!-- Quick actions -->
                    <div class="rounded-xl border border-[var(--linen)] bg-[var(--white)] shadow-sm">
                        <header class="border-b border-[var(--linen)] px-5 py-3">
                            <h2 class="text-sm font-semibold text-[var(--midnight)]">{{ t.actions_title }}</h2>
                        </header>
                        <ul class="divide-y divide-[var(--linen)]">
                            <li v-for="a in quickActions" :key="a.slug">
                                <a
                                    :href="a.href"
                                    target="_blank"
                                    rel="noopener"
                                    class="group flex items-start gap-3 px-5 py-3 transition-colors hover:bg-[var(--paper)]"
                                >
                                    <div class="flex size-9 shrink-0 items-center justify-center rounded-lg bg-[var(--orange-soft)] text-[var(--orange)]">
                                        <component :is="iconMap[a.icon] ?? Store" class="size-4" />
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        <p class="text-sm font-semibold text-[var(--midnight)] group-hover:text-[var(--orange)]">
                                            {{ a.title }}
                                        </p>
                                        <p class="mt-0.5 text-[11px] text-[var(--slate)]">
                                            {{ a.description }}
                                        </p>
                                    </div>
                                    <ArrowUpRight class="mt-0.5 size-4 shrink-0 text-[var(--slate-light)] group-hover:text-[var(--orange)]" />
                                </a>
                            </li>
                        </ul>
                    </div>
                </section>
            </template>
        </main>
    </div>
</template>
