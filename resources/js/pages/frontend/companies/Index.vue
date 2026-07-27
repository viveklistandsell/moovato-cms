<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import {
    BadgeCheck,
    Briefcase,
    ChevronRight,
    Search,
    Star,
} from 'lucide-vue-next';
import { computed, ref } from 'vue';
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
    heading: props.locale === 'de' ? 'Umzugsunternehmen finden' : 'Find moving companies',
    subheading: props.locale === 'de'
        ? 'Vergleichen Sie geprüfte Umzugsunternehmen — Preise, Bewertungen, Verfügbarkeit auf einen Blick.'
        : 'Compare vetted moving companies — prices, ratings and availability at a glance.',
    total_hits: (n: number) => props.locale === 'de' ? `${n} Ergebnisse` : `${n} results`,
    results: props.locale === 'de' ? 'Ergebnisse' : 'Results',
    filters: props.locale === 'de' ? 'Filter' : 'Filters',
    city: props.locale === 'de' ? 'Stadt' : 'City',
    service: props.locale === 'de' ? 'Leistung' : 'Service',
    all_cities: props.locale === 'de' ? 'Alle Städte' : 'All cities',
    all_services: props.locale === 'de' ? 'Alle Leistungen' : 'All services',
    verified_only: props.locale === 'de' ? 'Nur verifiziert' : 'Verified only',
    min_rating: props.locale === 'de' ? 'Mindestbewertung' : 'Min. rating',
    reset: props.locale === 'de' ? 'Filter zurücksetzen' : 'Reset filters',
    sort_by: props.locale === 'de' ? 'Sortieren' : 'Sort by',
    sort_relevant: props.locale === 'de' ? 'Am relevantesten' : 'Most relevant',
    sort_rating: props.locale === 'de' ? 'Beste Bewertung' : 'Highest rating',
    sort_newest: props.locale === 'de' ? 'Neueste zuerst' : 'Newest first',
    request_quote: props.locale === 'de' ? 'Angebot anfordern' : 'Request a quote',
    details: props.locale === 'de' ? 'Details' : 'Details',
    top_rated: props.locale === 'de' ? 'Top-bewertetes Umzugsunternehmen' : 'Top-rated moving company',
    verified: props.locale === 'de' ? 'Verifiziert' : 'Verified',
    reviews_short: props.locale === 'de' ? 'Bewertungen' : 'reviews',
    empty: props.locale === 'de'
        ? 'Keine Unternehmen für die aktuelle Auswahl gefunden. Filter zurücksetzen und erneut versuchen.'
        : 'No companies match the current filters. Reset and try again.',
    search_placeholder: props.locale === 'de' ? 'Umzugsunternehmen…' : 'Moving companies…',
}));

const localCity = ref<number | null>(props.activeCityId);
const localService = ref<number | null>(props.activeServiceId);
const localVerified = ref<boolean>(props.verifiedOnly);
const localMinRating = ref<number>(props.minRating);
const localSort = ref<string>(props.sort);

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

function detailUrl(c: Company): string {
    return localizedUrl(props.locale, `/company/${c.permalink}`);
}
function assetUrl(path: string | null): string | null {
    if (!path) return null;
    return path.startsWith('http') ? path : `/storage/${path}`;
}
</script>

<template>
    <Head :title="t.breadcrumb" />

    <div class="mv-companies-index bg-[var(--paper)] py-8 md:py-12">
        <div class="container-xl">
            <!-- Breadcrumb -->
            <nav class="mb-4 flex items-center gap-1.5 text-xs text-[var(--slate)]">
                <Link :href="localizedUrl(locale, '/')" class="hover:text-[var(--midnight)] hover:underline">
                    {{ t.home }}
                </Link>
                <span>›</span>
                <span class="text-[var(--midnight)]">{{ t.breadcrumb }}</span>
            </nav>

            <!-- Heading -->
            <div class="mb-8">
                <h1 class="text-3xl font-semibold text-[var(--midnight)] md:text-4xl">
                    {{ t.heading }}
                </h1>
                <p class="mt-2 max-w-2xl text-sm text-[var(--slate)] md:text-base">
                    {{ t.subheading }}
                </p>
            </div>

            <div class="grid gap-6 lg:grid-cols-[280px_1fr]">
                <!-- Filter sidebar -->
                <aside class="mv-filters space-y-4">
                    <div class="rounded-lg border border-[var(--linen)] bg-white p-4 shadow-sm">
                        <h2 class="mb-3 text-sm font-semibold text-[var(--midnight)]">
                            {{ t.filters }}
                        </h2>

                        <div class="space-y-4">
                            <div>
                                <label class="mb-1.5 block text-xs font-medium text-[var(--slate)]">
                                    {{ t.city }}
                                </label>
                                <select
                                    v-model="localCity"
                                    class="w-full rounded-md border border-[var(--linen)] bg-white px-2.5 py-2 text-sm focus:border-[var(--orange)] focus:outline-none"
                                    @change="applyFilters"
                                >
                                    <option :value="null">{{ t.all_cities }}</option>
                                    <option v-for="c in cities" :key="c.id" :value="c.id">
                                        {{ c.name }}
                                    </option>
                                </select>
                            </div>

                            <div>
                                <label class="mb-1.5 block text-xs font-medium text-[var(--slate)]">
                                    {{ t.service }}
                                </label>
                                <select
                                    v-model="localService"
                                    class="w-full rounded-md border border-[var(--linen)] bg-white px-2.5 py-2 text-sm focus:border-[var(--orange)] focus:outline-none"
                                    @change="applyFilters"
                                >
                                    <option :value="null">{{ t.all_services }}</option>
                                    <option v-for="s in services" :key="s.id" :value="s.id">
                                        {{ s.name }}
                                    </option>
                                </select>
                            </div>

                            <div>
                                <label class="mb-1.5 block text-xs font-medium text-[var(--slate)]">
                                    {{ t.min_rating }}: {{ localMinRating > 0 ? localMinRating.toFixed(1) : '—' }}
                                </label>
                                <input
                                    v-model.number="localMinRating"
                                    type="range"
                                    min="0"
                                    max="10"
                                    step="0.5"
                                    class="w-full accent-[var(--orange)]"
                                    @change="applyFilters"
                                />
                            </div>

                            <label class="flex cursor-pointer items-center gap-2">
                                <input
                                    v-model="localVerified"
                                    type="checkbox"
                                    class="size-4 accent-[var(--orange)]"
                                    @change="applyFilters"
                                />
                                <span class="text-sm text-[var(--midnight)]">
                                    {{ t.verified_only }}
                                </span>
                            </label>

                            <button
                                v-if="hasFilters"
                                type="button"
                                class="w-full rounded-md border border-[var(--linen)] px-3 py-1.5 text-xs font-medium text-[var(--slate)] hover:border-[var(--orange)] hover:text-[var(--orange)]"
                                @click="resetFilters"
                            >
                                {{ t.reset }}
                            </button>
                        </div>
                    </div>
                </aside>

                <!-- Result list -->
                <div class="mv-results min-w-0">
                    <div class="mb-4 flex items-center justify-between gap-4">
                        <h2 class="text-lg font-semibold text-[var(--midnight)]">
                            {{ t.results }}
                            <span class="ml-1 text-sm font-normal text-[var(--slate)]">
                                ({{ t.total_hits(pagination.total) }})
                            </span>
                        </h2>
                        <div class="flex items-center gap-2">
                            <span class="hidden text-xs text-[var(--slate)] md:inline">{{ t.sort_by }}:</span>
                            <select
                                v-model="localSort"
                                class="rounded-md border border-[var(--linen)] bg-white px-2.5 py-1.5 text-sm font-medium text-[var(--midnight)] focus:border-[var(--orange)] focus:outline-none"
                                @change="applyFilters"
                            >
                                <option value="relevant">{{ t.sort_relevant }}</option>
                                <option value="rating">{{ t.sort_rating }}</option>
                                <option value="newest">{{ t.sort_newest }}</option>
                            </select>
                        </div>
                    </div>

                    <!-- Empty state -->
                    <div
                        v-if="companies.length === 0"
                        class="rounded-lg border border-dashed border-[var(--linen)] bg-white p-12 text-center"
                    >
                        <Search class="mx-auto mb-3 size-8 text-[var(--slate-light)]" />
                        <p class="text-sm text-[var(--slate)]">{{ t.empty }}</p>
                    </div>

                    <!-- Card list -->
                    <ul v-else class="flex flex-col gap-4">
                        <li
                            v-for="c in companies"
                            :key="c.id"
                            class="mv-card relative overflow-hidden rounded-lg border border-[var(--linen)] bg-white shadow-sm transition-shadow hover:shadow-md"
                        >
                            <!--
                                Top-rated badge — inline pill anchored to the
                                top-left corner. Only the pill (not the whole
                                card header band) has the dark background, so
                                the rest of the card stays clean.
                            -->
                            <span
                                v-if="c.is_top_rated"
                                class="absolute top-0 left-0 inline-flex items-center gap-1 rounded-br-md bg-[var(--midnight)] px-3 py-1 text-[10px] font-semibold tracking-wide text-white uppercase shadow-sm"
                            >
                                <Star class="size-3 fill-amber-400 text-amber-400" />
                                {{ t.top_rated }}
                            </span>

                            <div class="grid grid-cols-1 gap-4 p-4 md:grid-cols-[160px_1fr_auto] md:items-center pt-7">
                                <!-- Logo / cover thumbnail -->
                                <Link :href="detailUrl(c)" class="block">
                                    <div
                                        class="flex aspect-[16/10] w-full items-center justify-center overflow-hidden rounded-md border border-[var(--linen)] bg-[var(--linen)]/40 md:aspect-[4/3]"
                                    >
                                        <img
                                            v-if="c.logo"
                                            :src="assetUrl(c.logo)!"
                                            :alt="c.name"
                                            class="size-full object-cover"
                                        />
                                        <Briefcase v-else class="size-8 text-[var(--slate-light)]" />
                                    </div>
                                </Link>

                                <!-- Middle content -->
                                <div class="min-w-0">
                                    <div class="mb-1.5 flex flex-wrap items-center gap-2">
                                        <span class="text-lg font-bold text-[var(--midnight)]">
                                            {{ c.rating_avg > 0 ? c.rating_avg.toFixed(1) : '—' }}
                                        </span>
                                        <div v-if="c.rating_avg > 0" class="flex items-center gap-0.5 text-amber-400">
                                            <Star
                                                v-for="n in 5"
                                                :key="n"
                                                class="size-4"
                                                :class="c.rating_avg / 2 >= n ? 'fill-current' : ''"
                                            />
                                        </div>
                                        <span v-if="c.review_count > 0" class="text-sm text-[var(--slate)]">
                                            {{ c.review_count }}
                                        </span>
                                    </div>
                                    <Link
                                        :href="detailUrl(c)"
                                        class="flex flex-wrap items-center gap-1.5"
                                    >
                                        <h3 class="text-xl font-bold text-[var(--midnight)] hover:text-[var(--orange)]">
                                            {{ c.name }}
                                        </h3>
                                        <BadgeCheck v-if="c.verified" class="size-5 fill-blue-500 text-white" :title="t.verified" />
                                    </Link>
                                    <p
                                        v-if="c.coverage_cities.length > 0"
                                        class="text-sm text-[var(--slate)]"
                                    >
                                        {{ c.coverage_cities.slice(0, 3).join(' · ') }}
                                        <span
                                            v-if="c.coverage_cities.length > 3"
                                            class="text-xs text-[var(--slate-light)]"
                                        >
                                            + {{ c.coverage_cities.length - 3 }} more
                                        </span>
                                    </p>
                                    <p
                                        v-else-if="c.city_name"
                                        class="text-sm text-[var(--slate)]"
                                    >
                                        {{ c.city_name }}
                                    </p>
                                    <p
                                        v-if="c.short_description"
                                        class="mt-2 line-clamp-2 text-sm text-[var(--slate)]"
                                    >
                                        {{ c.short_description }}
                                    </p>
                                    <div
                                        v-if="c.primary_services.length > 0"
                                        class="mt-2 flex flex-wrap items-center gap-1.5"
                                    >
                                        <span
                                            v-for="(name, sIdx) in c.primary_services"
                                            :key="sIdx"
                                            class="inline-flex items-center gap-1 rounded-full bg-emerald-50 px-2 py-0.5 text-xs font-medium text-emerald-700"
                                        >
                                            <Star class="size-3 fill-emerald-500 text-emerald-500" />
                                            {{ name }}
                                        </span>
                                    </div>
                                </div>

                                <!-- CTA buttons -->
                                <div class="flex w-full flex-col gap-2 md:w-40">
                                    <Link
                                        :href="detailUrl(c)"
                                        class="mv-btn-primary flex items-center justify-center gap-1.5 rounded-md bg-[var(--orange)] px-4 py-2 text-sm font-medium text-white hover:bg-[color-mix(in_srgb,var(--orange)_85%,black)]"
                                    >
                                        {{ t.request_quote }}
                                        <ChevronRight class="size-3.5" />
                                    </Link>
                                    <Link
                                        :href="detailUrl(c)"
                                        class="flex items-center justify-center gap-1.5 rounded-md border border-[var(--orange)] px-4 py-2 text-sm font-medium text-[var(--orange)] hover:bg-[var(--orange-soft)]"
                                    >
                                        {{ t.details }}
                                        <ChevronRight class="size-3.5" />
                                    </Link>
                                </div>
                            </div>
                        </li>
                    </ul>

                    <!-- Pagination -->
                    <nav
                        v-if="pagination.last_page > 1"
                        class="mt-6 flex items-center justify-center gap-1"
                    >
                        <Link
                            v-for="(link, idx) in pagination.links"
                            :key="idx"
                            :href="link.url ?? '#'"
                            :class="[
                                'inline-flex min-w-9 items-center justify-center rounded-md border border-[var(--linen)] px-3 py-1.5 text-sm',
                                link.active
                                    ? 'border-[var(--orange)] bg-[var(--orange)] text-white'
                                    : link.url
                                      ? 'bg-white text-[var(--midnight)] hover:border-[var(--orange)] hover:text-[var(--orange)]'
                                      : 'cursor-not-allowed bg-white text-[var(--slate-light)]',
                            ]"
                            :aria-disabled="!link.url"
                            :tabindex="link.url ? 0 : -1"
                            v-html="link.label"
                        />
                    </nav>
                </div>
            </div>
        </div>
    </div>
</template>
