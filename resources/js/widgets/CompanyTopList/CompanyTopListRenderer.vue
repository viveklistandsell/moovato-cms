<script setup lang="ts">
import { computed, ref } from 'vue';
import { BadgeCheck, Building2, ChevronDown, ChevronRight, Star } from 'lucide-vue-next';

type CompanyService = { id: number; name: string };

type CompanyItem = {
    id: number;
    name: string;
    href: string;
    city: string | null;
    logo_url: string | null;
    verified: boolean;
    is_top_rated: boolean;
    rating_avg: number;
    review_count: number;
    recommend_pct: number;
    services: CompanyService[];
};

type Settings = {
    mode?: 'auto' | 'manual';
    company_ids?: number[];
    limit?: number;
    only_verified?: boolean;
    show_filters?: boolean;
    show_sort?: boolean;
    items?: CompanyItem[];
};

type Data = {
    heading?: string;
    subheading?: string;
    badge_label?: string;
    reviews_label?: string;
    recommend_label?: string;
    quote_label?: string;
    quote_url?: string;
    details_label?: string;
    empty_text?: string;
    count_text?: string;
    filters_title?: string;
    results_title?: string;
    services_title?: string;
    rating_title?: string;
    features_title?: string;
    nearby_title?: string;
    nearby_label?: string;
    verified_label?: string;
    top_rated_label?: string;
    no_rating_label?: string;
    reset_label?: string;
    no_results_text?: string;
    sort_label?: string;
    sort_relevance_label?: string;
    sort_rating_label?: string;
    sort_reviews_label?: string;
    sort_name_label?: string;
};

type SortKey = 'relevance' | 'rating' | 'reviews' | 'name';

const props = defineProps<{ settings: Settings; data: Data }>();

/**
 * Fallback copy so a widget instance saved before a label existed still renders
 * readable filters instead of empty chrome. Anything the editor actually filled
 * in wins.
 */
const FALLBACK_COPY: Required<Omit<Data, 'heading' | 'subheading' | 'quote_url'>> = {
    badge_label: 'Bestbewertetes Umzugsunternehmen',
    reviews_label: 'Bewertungen',
    recommend_label: 'Weiterempfehlung',
    quote_label: 'Angebot anfordern',
    details_label: 'Details',
    empty_text: 'Derzeit sind keine Umzugsunternehmen verfügbar.',
    count_text: '{count} Umzugsunternehmen gefunden',
    filters_title: 'Filter',
    results_title: 'Ergebnisse',
    services_title: 'Dienstleistungen',
    rating_title: 'Bewertung',
    features_title: 'Merkmale',
    nearby_title: 'In der Nähe',
    nearby_label: 'Zeige Unternehmen in der Nähe von {city}',
    verified_label: 'Verifiziert',
    top_rated_label: 'Top bewertet',
    no_rating_label: 'Keine Bewertungen',
    reset_label: 'Filter zurücksetzen',
    no_results_text: 'Keine Umzugsunternehmen entsprechen Ihren Filtern.',
    sort_label: 'Sortieren nach:',
    sort_relevance_label: 'Am relevantesten',
    sort_rating_label: 'Beste Bewertung',
    sort_reviews_label: 'Meiste Bewertungen',
    sort_name_label: 'Name A–Z',
};

const copy = computed(() => {
    const filled = Object.fromEntries(
        Object.entries(props.data ?? {}).filter(
            ([, value]) => typeof value === 'string' && value.trim() !== '',
        ),
    );

    return { ...FALLBACK_COPY, ...filled } as Required<
        Omit<Data, 'heading' | 'subheading' | 'quote_url'>
    >;
});

const scoreFormat = new Intl.NumberFormat('de-DE', {
    minimumFractionDigits: 1,
    maximumFractionDigits: 1,
});

const items = computed<CompanyItem[]>(() => props.settings.items ?? []);
const showFilters = computed(() => props.settings.show_filters !== false);
const showSort = computed(() => props.settings.show_sort !== false);

const selectedServices = ref<number[]>([]);
const selectedStars = ref<number[]>([]);
const onlyVerified = ref(false);
const onlyTopRated = ref(false);
const nearbyOnly = ref(false);
const sortKey = ref<SortKey>('relevance');
const sortOpen = ref(false);

/** rating_avg is a 0–10 score; the star row is the 0–5 equivalent. */
function filledStars(rating: number): number {
    return Math.round(rating / 2);
}

const nearbyCity = computed<string | null>(
    () => items.value.find((item) => item.city)?.city ?? null,
);

const serviceFacets = computed(() => {
    const counts = new Map<number, { id: number; name: string; count: number }>();

    items.value.forEach((item) => {
        item.services?.forEach((service) => {
            const entry = counts.get(service.id);

            if (entry) {
                entry.count += 1;
            } else {
                counts.set(service.id, { ...service, count: 1 });
            }
        });
    });

    return [...counts.values()].sort((a, b) => b.count - a.count);
});

const starFacets = computed(() =>
    [5, 4, 3, 2, 1, 0].map((stars) => ({
        stars,
        count: items.value.filter((item) =>
            stars === 0
                ? item.review_count === 0
                : item.review_count > 0 && filledStars(item.rating_avg) === stars,
        ).length,
    })).filter((facet) => facet.count > 0),
);

const verifiedCount = computed(
    () => items.value.filter((item) => item.verified).length,
);
const topRatedCount = computed(
    () => items.value.filter((item) => item.is_top_rated).length,
);
const nearbyCount = computed(
    () => items.value.filter((item) => item.city === nearbyCity.value).length,
);

const hasActiveFilters = computed(
    () =>
        selectedServices.value.length > 0 ||
        selectedStars.value.length > 0 ||
        onlyVerified.value ||
        onlyTopRated.value ||
        nearbyOnly.value,
);

const visible = computed<CompanyItem[]>(() => {
    const filtered = items.value.filter((item) => {
        if (
            selectedServices.value.length > 0 &&
            !(item.services ?? []).some((service) =>
                selectedServices.value.includes(service.id),
            )
        ) {
            return false;
        }

        if (selectedStars.value.length > 0) {
            const bucket = item.review_count === 0 ? 0 : filledStars(item.rating_avg);

            if (!selectedStars.value.includes(bucket)) {
                return false;
            }
        }

        if (onlyVerified.value && !item.verified) {
            return false;
        }

        if (onlyTopRated.value && !item.is_top_rated) {
            return false;
        }

        if (nearbyOnly.value && item.city !== nearbyCity.value) {
            return false;
        }

        return true;
    });

    const sorted = [...filtered];

    if (sortKey.value === 'rating') {
        sorted.sort((a, b) => b.rating_avg - a.rating_avg);
    } else if (sortKey.value === 'reviews') {
        sorted.sort((a, b) => b.review_count - a.review_count);
    } else if (sortKey.value === 'name') {
        sorted.sort((a, b) => a.name.localeCompare(b.name, 'de'));
    }

    return sorted;
});

const sortOptions = computed<{ key: SortKey; label: string }[]>(() =>
    [
        { key: 'relevance' as SortKey, label: copy.value.sort_relevance_label },
        { key: 'rating' as SortKey, label: copy.value.sort_rating_label },
        { key: 'reviews' as SortKey, label: copy.value.sort_reviews_label },
        { key: 'name' as SortKey, label: copy.value.sort_name_label },
    ].filter((option): option is { key: SortKey; label: string } =>
        Boolean(option.label),
    ),
);

const activeSortLabel = computed(
    () =>
        sortOptions.value.find((option) => option.key === sortKey.value)?.label ??
        '',
);

const countText = computed(() =>
    (copy.value.count_text).replace('{count}', String(visible.value.length)),
);

const nearbyLabel = computed(() =>
    (copy.value.nearby_label).replace('{city}', nearbyCity.value ?? ''),
);

function toggle(list: number[], value: number): number[] {
    return list.includes(value)
        ? list.filter((entry) => entry !== value)
        : [...list, value];
}

function resetFilters(): void {
    selectedServices.value = [];
    selectedStars.value = [];
    onlyVerified.value = false;
    onlyTopRated.value = false;
    nearbyOnly.value = false;
}

function chooseSort(key: SortKey): void {
    sortKey.value = key;
    sortOpen.value = false;
}
</script>

<template>
    <section class="mv-toplist section-py">
        <div class="container-xl">
            <header class="mv-toplist-head">
                <h2 v-if="data.heading" class="mv-toplist-title">
                    {{ data.heading }}
                </h2>
                <p v-if="data.subheading" class="mv-toplist-sub">
                    {{ data.subheading }}
                </p>
                <p v-if="countText" class="mv-toplist-count">
                    {{ countText }}
                </p>
            </header>

            <div
                class="grid grid-cols-1 gap-8"
                :class="{ 'lg:grid-cols-[250px_1fr]': showFilters }"
            >
                <aside v-if="showFilters" class="mv-toplist-filters">
                    <div class="mv-toplist-filters-head">
                        <h3 v-if="copy.filters_title">
                            {{ copy.filters_title }}
                        </h3>
                        <button
                            v-if="hasActiveFilters && copy.reset_label"
                            type="button"
                            class="mv-toplist-reset"
                            @click="resetFilters"
                        >
                            {{ copy.reset_label }}
                        </button>
                    </div>

                    <div
                        v-if="serviceFacets.length > 0"
                        class="mv-toplist-group"
                    >
                        <h4>{{ copy.services_title }}</h4>
                        <ul>
                            <li v-for="facet in serviceFacets" :key="facet.id">
                                <label>
                                    <input
                                        type="checkbox"
                                        :checked="
                                            selectedServices.includes(facet.id)
                                        "
                                        @change="
                                            selectedServices = toggle(
                                                selectedServices,
                                                facet.id,
                                            )
                                        "
                                    />
                                    <span class="lbl">{{ facet.name }}</span>
                                    <span class="cnt">({{ facet.count }})</span>
                                </label>
                            </li>
                        </ul>
                    </div>

                    <div v-if="starFacets.length > 0" class="mv-toplist-group">
                        <h4>{{ copy.rating_title }}</h4>
                        <ul>
                            <li v-for="facet in starFacets" :key="facet.stars">
                                <label>
                                    <input
                                        type="checkbox"
                                        :checked="
                                            selectedStars.includes(facet.stars)
                                        "
                                        @change="
                                            selectedStars = toggle(
                                                selectedStars,
                                                facet.stars,
                                            )
                                        "
                                    />
                                    <span
                                        v-if="facet.stars > 0"
                                        class="lbl stars"
                                        aria-hidden="true"
                                    >
                                        <Star
                                            v-for="n in 5"
                                            :key="n"
                                            :size="14"
                                            :class="{ 'is-on': n <= facet.stars }"
                                        />
                                    </span>
                                    <span v-else class="lbl">
                                        {{ copy.no_rating_label }}
                                    </span>
                                    <span class="cnt">({{ facet.count }})</span>
                                </label>
                            </li>
                        </ul>
                    </div>

                    <div
                        v-if="verifiedCount > 0 || topRatedCount > 0"
                        class="mv-toplist-group"
                    >
                        <h4>{{ copy.features_title }}</h4>
                        <ul>
                            <li v-if="verifiedCount > 0">
                                <label>
                                    <input
                                        v-model="onlyVerified"
                                        type="checkbox"
                                    />
                                    <span class="lbl">
                                        {{ copy.verified_label }}
                                    </span>
                                    <span class="cnt">
                                        ({{ verifiedCount }})
                                    </span>
                                </label>
                            </li>
                            <li v-if="topRatedCount > 0">
                                <label>
                                    <input
                                        v-model="onlyTopRated"
                                        type="checkbox"
                                    />
                                    <span class="lbl">
                                        {{ copy.top_rated_label }}
                                    </span>
                                    <span class="cnt">
                                        ({{ topRatedCount }})
                                    </span>
                                </label>
                            </li>
                        </ul>
                    </div>

                    <div
                        v-if="nearbyCity && nearbyCount > 0"
                        class="mv-toplist-group"
                    >
                        <h4>{{ copy.nearby_title }}</h4>
                        <ul>
                            <li>
                                <label>
                                    <input
                                        v-model="nearbyOnly"
                                        type="checkbox"
                                    />
                                    <span class="lbl">{{ nearbyLabel }}</span>
                                </label>
                            </li>
                        </ul>
                    </div>
                </aside>

                <div class="mv-toplist-results">
                    <div
                        v-if="copy.results_title || showSort"
                        class="mv-toplist-toolbar"
                    >
                        <h3 v-if="copy.results_title">
                            {{ copy.results_title }}
                        </h3>
                        <div
                            v-if="showSort && sortOptions.length > 0"
                            class="mv-toplist-sort"
                            :class="{ 'is-open': sortOpen }"
                        >
                            <span v-if="copy.sort_label">
                                {{ copy.sort_label }}
                            </span>
                            <button
                                type="button"
                                :aria-expanded="sortOpen"
                                @click="sortOpen = !sortOpen"
                            >
                                {{ activeSortLabel }}
                                <ChevronDown :size="16" />
                            </button>
                            <ul v-if="sortOpen" role="listbox">
                                <li
                                    v-for="option in sortOptions"
                                    :key="option.key"
                                    role="option"
                                    :aria-selected="sortKey === option.key"
                                    :class="{ 'is-selected': sortKey === option.key }"
                                    @click="chooseSort(option.key)"
                                >
                                    {{ option.label }}
                                </li>
                            </ul>
                        </div>
                    </div>

                    <ul v-if="visible.length > 0" class="mv-toplist-items">
                        <li
                            v-for="company in visible"
                            :key="company.id"
                            class="mv-toplist-card"
                            :class="{ 'is-top': company.is_top_rated }"
                        >
                            <span
                                v-if="company.is_top_rated && copy.badge_label"
                                class="mv-toplist-ribbon"
                            >
                                {{ copy.badge_label }}
                            </span>

                            <div class="mv-toplist-body">
                                <a class="mv-toplist-logo" :href="company.href">
                                    <img
                                        v-if="company.logo_url"
                                        :src="company.logo_url"
                                        :alt="company.name"
                                        loading="lazy"
                                        decoding="async"
                                    />
                                    <Building2 v-else :size="30" />
                                </a>

                                <div class="mv-toplist-info">
                                    <p class="mv-toplist-rating">
                                        <span class="score">
                                            {{
                                                scoreFormat.format(
                                                    company.rating_avg,
                                                )
                                            }}
                                        </span>
                                        <span class="stars" aria-hidden="true">
                                            <Star
                                                v-for="n in 5"
                                                :key="n"
                                                :size="15"
                                                :class="{
                                                    'is-on':
                                                        n <=
                                                        filledStars(
                                                            company.rating_avg,
                                                        ),
                                                }"
                                            />
                                        </span>
                                        <span class="count">
                                            {{ company.review_count }}
                                        </span>
                                    </p>

                                    <h3 class="mv-toplist-name">
                                        <a :href="company.href">
                                            {{ company.name }}
                                        </a>
                                        <BadgeCheck
                                            v-if="company.verified"
                                            :size="17"
                                            class="mv-toplist-verified"
                                        />
                                    </h3>

                                    <p
                                        v-if="company.city"
                                        class="mv-toplist-city"
                                    >
                                        {{ company.city }}
                                    </p>

                                    <p
                                        v-if="company.recommend_pct > 0"
                                        class="mv-toplist-recommend"
                                    >
                                        {{ company.recommend_pct }} %
                                        <span>{{ copy.recommend_label }}</span>
                                    </p>
                                </div>

                                <div class="mv-toplist-actions">
                                    <a
                                        v-if="copy.quote_label"
                                        class="mv-toplist-btn"
                                        :href="data.quote_url || '#'"
                                    >
                                        {{ copy.quote_label }}
                                        <ChevronRight :size="16" />
                                    </a>
                                    <a
                                        v-if="copy.details_label"
                                        class="mv-toplist-btn is-ghost"
                                        :href="company.href"
                                    >
                                        {{ copy.details_label }}
                                        <ChevronRight :size="16" />
                                    </a>
                                </div>
                            </div>
                        </li>
                    </ul>

                    <p v-else class="mv-toplist-empty">
                        {{
                            hasActiveFilters
                                ? copy.no_results_text
                                : copy.empty_text
                        }}
                    </p>
                </div>
            </div>
        </div>
    </section>
</template>
