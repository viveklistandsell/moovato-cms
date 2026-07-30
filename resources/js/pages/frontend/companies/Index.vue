<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import {
    ArrowRight,
    Building2,
    MapPin,
    RotateCcw,
    Search,
    ShieldCheck,
    SlidersHorizontal,
    Star,
} from 'lucide-vue-next';
import { computed, onBeforeUnmount, onMounted, ref } from 'vue';
import { localizedUrl } from '@/lib/localizedUrl';

type City = { id: number; name: string };
type Service = { id: number; name: string };

type Company = {
    id: number;
    name: string;
    permalink: string | null;
    short_description: string | null;
    logo: string | null;
    cover: string | null;
    verified: boolean;
    is_top_rated: boolean;
    plan_tier: string;
    rating_avg: number;
    review_count: number;
    recommend_pct: number;
    city_name: string | null;
    coverage_cities: string[];
    primary_services: string[];
    primary_phone: string | null;
};

type PaginationLink = { url: string | null; label: string; active: boolean };

const props = defineProps<{
    locale: string;
    companies: Company[];
    cities: City[];
    services: Service[];
    activeCityId: number | null;
    activeServiceId: number | null;
    verifiedOnly: boolean;
    minRating: number;
    sort: string;
    pagination: {
        current_page: number;
        last_page: number;
        total: number;
        per_page: number;
        links: PaginationLink[];
    };
}>();

const t = computed(() => ({
    home: props.locale === 'de' ? 'Startseite' : 'Home',
    breadcrumb: props.locale === 'de' ? 'Umzugsunternehmen' : 'Moving companies',
    eyebrow: props.locale === 'de' ? 'Geprüfte Partner in Berlin' : 'Vetted partners in Berlin',
    heading_lead: props.locale === 'de' ? 'Umzugsunternehmen' : 'Find moving',
    heading_accent: props.locale === 'de' ? 'finden' : 'companies',
    subheading: props.locale === 'de'
        ? 'Vergleichen Sie geprüfte Umzugsunternehmen — Preise, Bewertungen, Verfügbarkeit auf einen Blick.'
        : 'Compare vetted moving companies — prices, ratings and availability at a glance.',
    total_hits: (n: number) => props.locale === 'de' ? `${n} Ergebnisse` : `${n} results`,
    results: props.locale === 'de' ? 'Ergebnisse' : 'Results',
    filters: props.locale === 'de' ? 'Filter' : 'Filters',
    filters_toggle: props.locale === 'de' ? 'Filter anzeigen' : 'Show filters',
    city: props.locale === 'de' ? 'Stadt' : 'City',
    service: props.locale === 'de' ? 'Leistung' : 'Service',
    all_cities: props.locale === 'de' ? 'Alle Städte' : 'All cities',
    all_services: props.locale === 'de' ? 'Alle Leistungen' : 'All services',
    verified_only: props.locale === 'de' ? 'Nur verifiziert' : 'Verified only',
    min_rating: props.locale === 'de' ? 'Mindestbewertung' : 'Min. rating',
    reset: props.locale === 'de' ? 'Filter zurücksetzen' : 'Reset filters',
    sort_by: props.locale === 'de' ? 'Sortieren' : 'Sort by',
    sort_relevant: props.locale === 'de' ? 'Relevanz' : 'Relevance',
    sort_rating: props.locale === 'de' ? 'Beste Bewertung' : 'Highest rating',
    sort_newest: props.locale === 'de' ? 'Neueste' : 'Newest',
    request_quote: props.locale === 'de' ? 'Angebot anfordern' : 'Request a quote',
    details: props.locale === 'de' ? 'Details' : 'Details',
    top_rated: props.locale === 'de' ? 'Top-bewertetes Umzugsunternehmen' : 'Top-rated moving company',
    top_rated_short: props.locale === 'de' ? 'Top bewertet' : 'Top rated',
    verified: props.locale === 'de' ? 'Verifiziert' : 'Verified',
    reviews_short: props.locale === 'de' ? 'Bewertungen' : 'reviews',
    stat_offers: props.locale === 'de' ? 'Anbieter im Vergleich' : 'Providers compared',
    stat_cities: props.locale === 'de' ? 'Städte & Bezirke' : 'Cities & districts',
    stat_services: props.locale === 'de' ? 'Leistungen' : 'Services',
    more_cities: (n: number) => props.locale === 'de' ? `+${n} weitere` : `+${n} more`,
    empty: props.locale === 'de'
        ? 'Keine Unternehmen für die aktuelle Auswahl gefunden. Filter zurücksetzen und erneut versuchen.'
        : 'No companies match the current filters. Reset and try again.',
    empty_title: props.locale === 'de' ? 'Keine Treffer' : 'No matches',
    search_placeholder: props.locale === 'de' ? 'Umzugsunternehmen…' : 'Moving companies…',
}));

const localCity = ref<number | null>(props.activeCityId);
const localService = ref<number | null>(props.activeServiceId);
const localVerified = ref<boolean>(props.verifiedOnly);
const localMinRating = ref<number>(props.minRating);
const localSort = ref<string>(props.sort);
const filtersOpen = ref<boolean>(false);

const sortOptions = computed(() => [
    { value: 'relevant', label: t.value.sort_relevant },
    { value: 'rating', label: t.value.sort_rating },
    { value: 'newest', label: t.value.sort_newest },
]);

function applyFilters(): void {
    const params: Record<string, string | number> = {};
    if (localCity.value !== null) params.city = localCity.value;
    if (localService.value !== null) params.service = localService.value;
    if (localVerified.value) params.verified = '1';
    if (localMinRating.value > 0) params.min_rating = localMinRating.value;
    if (localSort.value !== 'relevant') params.sort = localSort.value;

    router.get(localizedUrl(props.locale, '/companies'), params, {
        preserveScroll: true,
        preserveState: false,
    });
}

function selectSort(value: string): void {
    if (localSort.value === value) {
        return;
    }
    localSort.value = value;
    applyFilters();
}

function resetFilters(): void {
    localCity.value = null;
    localService.value = null;
    localVerified.value = false;
    localMinRating.value = 0;
    localSort.value = 'relevant';
    router.get(localizedUrl(props.locale, '/companies'));
}

const hasFilters = computed(
    () =>
        localCity.value !== null ||
        localService.value !== null ||
        localVerified.value ||
        localMinRating.value > 0 ||
        localSort.value !== 'relevant',
);

const activeFilterCount = computed(() => {
    let count = 0;
    if (localCity.value !== null) count++;
    if (localService.value !== null) count++;
    if (localVerified.value) count++;
    if (localMinRating.value > 0) count++;
    return count;
});

const ratingProgress = computed(() => `${(localMinRating.value / 10) * 100}%`);

function detailUrl(c: Company): string {
    return localizedUrl(props.locale, `/company/${c.permalink}`);
}
function assetUrl(path: string | null): string | null {
    if (!path) return null;
    return path.startsWith('http') ? path : `/storage/${path}`;
}

const sectionRef = ref<HTMLElement | null>(null);
let revealObserver: IntersectionObserver | null = null;

onMounted(() => {
    const root = sectionRef.value;
    if (!root) {
        return;
    }
    const targets = root.querySelectorAll<HTMLElement>('[data-mv-reveal]');
    if (typeof IntersectionObserver === 'undefined') {
        targets.forEach((el) => el.classList.add('is-revealed'));
        return;
    }

    revealObserver = new IntersectionObserver(
        (entries) => {
            entries.forEach((entry) => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('is-revealed');
                    revealObserver?.unobserve(entry.target);
                }
            });
        },
        { rootMargin: '0px 0px -8% 0px', threshold: 0.08 },
    );

    targets.forEach((el) => revealObserver!.observe(el));
});

onBeforeUnmount(() => {
    revealObserver?.disconnect();
    revealObserver = null;
});
</script>

<template>
    <Head :title="t.breadcrumb" />

    <div
        ref="sectionRef"
        class="mv-companies-index relative overflow-hidden bg-[var(--paper)] px-0 pt-8 pb-20 md:pt-14 md:pb-28"
    >
        <div class="container-xl relative">
            <nav
                class="mv-ci-crumb flex items-center gap-2 text-[11px] font-medium tracking-[0.14em] text-[var(--slate-light)] uppercase"
            >
                <Link :href="localizedUrl(locale, '/')" class="transition-colors duration-500 ease-[cubic-bezier(0.32,0.72,0,1)] hover:text-[var(--orange)]">
                    {{ t.home }}
                </Link>
                <span aria-hidden="true" class="text-[var(--linen)]">—</span>
                <span class="text-[var(--midnight)]">{{ t.breadcrumb }}</span>
            </nav>

            <header
                class="mt-8 grid items-end gap-10 md:mt-12 lg:grid-cols-[1.4fr_1fr] lg:gap-16"
                data-mv-reveal
            >
                <div class="max-w-2xl">
                    <span class="mv-ci-eyebrow">
                        <span aria-hidden="true" class="mv-ci-eyebrow-dot"></span>
                        {{ t.eyebrow }}
                    </span>
                    <h1 class="mv-ci-title mt-5">
                        {{ t.heading_lead }}
                        <em>{{ t.heading_accent }}</em>
                    </h1>
                    <p class="mt-5 max-w-xl text-base leading-relaxed text-[var(--slate)] md:text-lg">
                        {{ t.subheading }}
                    </p>
                </div>

                <ul class="mv-ci-stats grid grid-cols-3 gap-2 sm:gap-3">
                    <li class="mv-ci-stat-shell">
                        <div class="mv-ci-stat-core">
                            <span class="mv-ci-stat-value">{{ pagination.total }}</span>
                            <span class="mv-ci-stat-label">{{ t.stat_offers }}</span>
                        </div>
                    </li>
                    <li class="mv-ci-stat-shell">
                        <div class="mv-ci-stat-core">
                            <span class="mv-ci-stat-value">{{ cities.length }}</span>
                            <span class="mv-ci-stat-label">{{ t.stat_cities }}</span>
                        </div>
                    </li>
                    <li class="mv-ci-stat-shell">
                        <div class="mv-ci-stat-core">
                            <span class="mv-ci-stat-value">{{ services.length }}</span>
                            <span class="mv-ci-stat-label">{{ t.stat_services }}</span>
                        </div>
                    </li>
                </ul>
            </header>

            <div class="mt-12 grid gap-6 md:mt-16 lg:grid-cols-[300px_1fr] lg:gap-6">
                <aside class="mv-ci-rail self-start lg:sticky lg:top-24" data-mv-reveal>
                    <button
                        type="button"
                        class="mv-ci-filter-toggle flex w-full items-center justify-between gap-3 lg:hidden"
                        :aria-expanded="filtersOpen"
                        aria-controls="mv-ci-filter-panel"
                        @click="filtersOpen = !filtersOpen"
                    >
                        <span class="flex items-center gap-2.5">
                            <SlidersHorizontal :stroke-width="1.25" class="size-4 text-[var(--orange)]" />
                            {{ filtersOpen ? t.filters : t.filters_toggle }}
                        </span>
                        <span v-if="activeFilterCount > 0" class="mv-ci-count">{{ activeFilterCount }}</span>
                    </button>

                    <div
                        id="mv-ci-filter-panel"
                        class="mv-ci-shell mt-3 lg:mt-0"
                        :class="filtersOpen ? 'block' : 'hidden lg:block'"
                    >
                        <div class="mv-ci-core p-5 md:p-6">
                            <div class="flex items-center justify-between gap-3">
                                <h2 class="flex items-center gap-2.5 text-sm font-semibold tracking-tight text-[var(--midnight)]">
                                    <SlidersHorizontal :stroke-width="1.25" class="size-4 text-[var(--orange)]" />
                                    {{ t.filters }}
                                </h2>
                                <span v-if="activeFilterCount > 0" class="mv-ci-count">{{ activeFilterCount }}</span>
                            </div>

                            <div class="mt-6 space-y-6">
                                <div>
                                    <label for="mv-ci-city" class="mv-ci-label">{{ t.city }}</label>
                                    <div class="mv-ci-select">
                                        <select id="mv-ci-city" v-model="localCity" @change="applyFilters">
                                            <option :value="null">{{ t.all_cities }}</option>
                                            <option v-for="c in cities" :key="c.id" :value="c.id">
                                                {{ c.name }}
                                            </option>
                                        </select>
                                    </div>
                                </div>

                                <div>
                                    <label for="mv-ci-service" class="mv-ci-label">{{ t.service }}</label>
                                    <div class="mv-ci-select">
                                        <select id="mv-ci-service" v-model="localService" @change="applyFilters">
                                            <option :value="null">{{ t.all_services }}</option>
                                            <option v-for="s in services" :key="s.id" :value="s.id">
                                                {{ s.name }}
                                            </option>
                                        </select>
                                    </div>
                                </div>

                                <div>
                                    <div class="flex items-baseline justify-between gap-3">
                                        <label for="mv-ci-rating" class="mv-ci-label mb-0">{{ t.min_rating }}</label>
                                        <span class="mv-ci-rating-chip">
                                            {{ localMinRating > 0 ? localMinRating.toFixed(1) : '—' }}
                                        </span>
                                    </div>
                                    <input
                                        id="mv-ci-rating"
                                        v-model.number="localMinRating"
                                        type="range"
                                        min="0"
                                        max="10"
                                        step="0.5"
                                        class="mv-ci-range mt-4"
                                        :style="{ '--mv-ci-progress': ratingProgress }"
                                        @change="applyFilters"
                                    />
                                </div>

                                <label class="mv-ci-toggle group">
                                    <input
                                        v-model="localVerified"
                                        type="checkbox"
                                        class="peer sr-only"
                                        @change="applyFilters"
                                    />
                                    <span class="mv-ci-toggle-track">
                                        <span class="mv-ci-toggle-knob"></span>
                                    </span>
                                    <span class="flex items-center gap-1.5 text-sm font-medium text-[var(--midnight)]">
                                        <ShieldCheck :stroke-width="1.25" class="size-4 text-[var(--orange)]" />
                                        {{ t.verified_only }}
                                    </span>
                                </label>

                                <button
                                    v-if="hasFilters"
                                    type="button"
                                    class="mv-ci-reset group flex w-full items-center justify-center gap-2"
                                    @click="resetFilters"
                                >
                                    <RotateCcw
                                        :stroke-width="1.25"
                                        class="size-3.5 transition-transform duration-700 ease-[cubic-bezier(0.32,0.72,0,1)] group-hover:-rotate-180"
                                    />
                                    {{ t.reset }}
                                </button>
                            </div>
                        </div>
                    </div>
                </aside>

                <div class="mv-ci-results min-w-0">
                    <div
                        class="flex flex-col gap-4 border-b border-[color-mix(in_srgb,var(--linen)_70%,transparent)] pb-5 sm:flex-row sm:items-end sm:justify-between"
                        data-mv-reveal
                    >
                        <h2 class="text-xl font-semibold tracking-tight text-[var(--midnight)]">
                            {{ t.results }}
                            <span class="ml-1.5 text-sm font-normal text-[var(--slate-light)]">
                                {{ t.total_hits(pagination.total) }}
                            </span>
                        </h2>

                        <div class="flex items-center gap-3">
                            <span class="hidden text-[11px] font-medium tracking-[0.14em] text-[var(--slate-light)] uppercase md:inline">
                                {{ t.sort_by }}
                            </span>
                            <div class="mv-ci-segment" role="group" :aria-label="t.sort_by">
                                <button
                                    v-for="option in sortOptions"
                                    :key="option.value"
                                    type="button"
                                    class="mv-ci-segment-btn"
                                    :class="{ 'is-active': localSort === option.value }"
                                    :aria-pressed="localSort === option.value"
                                    @click="selectSort(option.value)"
                                >
                                    {{ option.label }}
                                </button>
                            </div>
                        </div>
                    </div>

                    <div v-if="companies.length === 0" class="mv-ci-shell mt-8" data-mv-reveal>
                        <div class="mv-ci-core mv-ci-empty">
                            <span class="mv-ci-empty-orb">
                                <Search :stroke-width="1.25" class="size-6 text-[var(--orange)]" />
                            </span>
                            <h3 class="mt-6 text-lg font-semibold tracking-tight text-[var(--midnight)]">
                                {{ t.empty_title }}
                            </h3>
                            <p class="mx-auto mt-2 max-w-sm text-sm leading-relaxed text-[var(--slate)]">
                                {{ t.empty }}
                            </p>
                            <button
                                v-if="hasFilters"
                                type="button"
                                class="mv-ci-reset mx-auto mt-6 inline-flex items-center justify-center gap-2"
                                @click="resetFilters"
                            >
                                <RotateCcw :stroke-width="1.25" class="size-3.5" />
                                {{ t.reset }}
                            </button>
                        </div>
                    </div>

                    <ul v-else class="mt-8 flex flex-col gap-5">
                        <li
                            v-for="(c, idx) in companies"
                            :key="c.id"
                            class="mv-ci-shell mv-ci-card group"
                            :class="{ 'is-top': c.is_top_rated }"
                            :style="{ '--mv-ci-delay': `${Math.min(idx, 6) * 70}ms` }"
                            data-mv-reveal
                        >
                            <article class="mv-ci-core grid grid-cols-1 gap-5 p-4 sm:p-5 md:grid-cols-[minmax(0,150px)_1fr] lg:grid-cols-[minmax(0,150px)_1fr_auto] lg:items-center lg:gap-7">
                                <Link :href="detailUrl(c)" class="mv-ci-thumb group/thumb" :aria-label="c.name">
                                    <span class="mv-ci-thumb-core">
                                        <img
                                            v-if="c.logo"
                                            :src="assetUrl(c.logo)!"
                                            :alt="c.name"
                                            loading="lazy"
                                            class="size-full object-cover transition-transform duration-700 ease-[cubic-bezier(0.32,0.72,0,1)] group-hover/thumb:scale-[1.04]"
                                        />
                                        <Building2 v-else :stroke-width="1" class="size-9 text-[var(--slate-light)]" />
                                    </span>
                                </Link>

                                <div class="min-w-0">
                                    <div class="flex flex-wrap items-center gap-x-3 gap-y-2">
                                        <span v-if="c.rating_avg > 0" class="mv-ci-score">
                                            <span class="mv-ci-score-value">{{ c.rating_avg.toFixed(1) }}</span>
                                            <span class="mv-ci-stars" :aria-label="`${c.rating_avg.toFixed(1)} / 10`">
                                                <Star
                                                    v-for="n in 5"
                                                    :key="n"
                                                    :stroke-width="1.25"
                                                    class="size-3.5"
                                                    :class="c.rating_avg / 2 >= n
                                                        ? 'fill-[var(--yellow-dark)] text-[var(--yellow-dark)]'
                                                        : 'text-[var(--linen)]'"
                                                />
                                            </span>
                                        </span>
                                        <span v-if="c.review_count > 0" class="text-xs text-[var(--slate-light)]">
                                            {{ c.review_count }} {{ t.reviews_short }}
                                        </span>
                                        <span v-if="c.is_top_rated" class="mv-ci-pill-top" :title="t.top_rated">
                                            <Star :stroke-width="0" class="size-3 fill-[var(--yellow-dark)]" />
                                            {{ t.top_rated_short }}
                                        </span>
                                    </div>

                                    <h3 class="mt-3 flex flex-wrap items-center gap-2">
                                        <Link :href="detailUrl(c)" class="mv-ci-name">
                                            {{ c.name }}
                                        </Link>
                                        <ShieldCheck
                                            v-if="c.verified"
                                            :stroke-width="1.5"
                                            class="size-4 shrink-0 text-[var(--orange)]"
                                            :aria-label="t.verified"
                                        />
                                    </h3>

                                    <p
                                        v-if="c.coverage_cities.length > 0 || c.city_name"
                                        class="mt-2 flex items-center gap-1.5 text-sm text-[var(--slate)]"
                                    >
                                        <MapPin :stroke-width="1.25" class="size-3.5 shrink-0 text-[var(--slate-light)]" />
                                        <span class="min-w-0 truncate">
                                            {{ c.coverage_cities.length > 0
                                                ? c.coverage_cities.slice(0, 3).join(' · ')
                                                : c.city_name }}
                                        </span>
                                        <span
                                            v-if="c.coverage_cities.length > 3"
                                            class="shrink-0 text-xs text-[var(--slate-light)]"
                                        >
                                            {{ t.more_cities(c.coverage_cities.length - 3) }}
                                        </span>
                                    </p>

                                    <p
                                        v-if="c.short_description"
                                        class="mt-3 line-clamp-2 max-w-xl text-sm leading-relaxed text-[var(--slate)]"
                                    >
                                        {{ c.short_description }}
                                    </p>

                                    <ul
                                        v-if="c.primary_services.length > 0"
                                        class="mt-4 flex flex-wrap items-center gap-2"
                                    >
                                        <li
                                            v-for="(name, sIdx) in c.primary_services"
                                            :key="sIdx"
                                            class="mv-ci-pill-service"
                                        >
                                            <span aria-hidden="true" class="mv-ci-pill-dot"></span>
                                            {{ name }}
                                        </li>
                                    </ul>
                                </div>

                                <div class="flex w-full flex-col gap-2.5 md:flex-row lg:w-44 lg:flex-col">
                                    <Link
                                        :href="detailUrl(c)"
                                        class="mv-directory__btn mv-directory__btn--solid"
                                    >
                                        {{ t.request_quote }}
                                        <ArrowRight :stroke-width="2" class="size-4 shrink-0" />
                                    </Link>
                                    <Link
                                        :href="detailUrl(c)"
                                        class="mv-directory__btn mv-directory__btn--ghost"
                                    >
                                        {{ t.details }}
                                    </Link>
                                </div>
                            </article>
                        </li>
                    </ul>

                    <nav
                        v-if="pagination.last_page > 1"
                        class="mt-10 flex justify-center"
                        :aria-label="t.results"
                        data-mv-reveal
                    >
                        <div class="mv-ci-pager-shell">
                            <Link
                                v-for="(link, idx) in pagination.links"
                                :key="idx"
                                :href="link.url ?? '#'"
                                class="mv-ci-pager-link"
                                :class="{ 'is-active': link.active, 'is-disabled': !link.url }"
                                :aria-current="link.active ? 'page' : undefined"
                                :aria-disabled="!link.url"
                                :tabindex="link.url ? 0 : -1"
                                v-html="link.label"
                            />
                        </div>
                    </nav>
                </div>
            </div>
        </div>
    </div>
</template>
