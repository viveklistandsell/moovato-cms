<script setup lang="ts">
import { computed, onBeforeUnmount, onMounted, reactive, ref, useId } from 'vue';
import { Home, Loader2, MapPin, RefreshCw } from 'lucide-vue-next';
import { resolve as resolvePlace, suggest as suggestPlaces } from '@/routes/places';

type LocationOption = {
    name?: string;
    lat?: number | string;
    lng?: number | string;
};

type Settings = {
    currency?: string;
    base_price?: number | string;
    price_per_sqm?: number | string;
    price_per_km?: number | string;
    spread_percent?: number | string;
    locations?: LocationOption[];
};

type Data = {
    title?: string;
    from_label?: string;
    from_placeholder?: string;
    to_label?: string;
    to_placeholder?: string;
    area_label?: string;
    area_placeholder?: string;
    area_unit?: string;
    calculate_label?: string;
    recalculate_label?: string;
    result_title?: string;
    volume_label?: string;
    distance_label?: string;
    empty_text?: string;
    error_text?: string;
    cta_text?: string;
    cta_label?: string;
    cta_url?: string;
};

type Place = { name: string; lat: number; lng: number };

type Suggestion = { place_id?: string; name: string; lat?: number; lng?: number };

type Field = {
    query: string;
    place: Place | null;
    suggestions: Suggestion[];
    open: boolean;
    loading: boolean;
};

type Estimate = {
    low: number;
    high: number;
    distance: number;
    area: number;
};

const props = defineProps<{ settings: Settings; data: Data }>();

const uid = useId();
const root = ref<HTMLElement | null>(null);
const area = ref('');
const estimate = ref<Estimate | null>(null);
const hasError = ref(false);
const calculating = ref(false);

const fromField = reactive<Field>(createField());
const toField = reactive<Field>(createField());
const timers = new Map<Field, ReturnType<typeof setTimeout>>();

const priceFormat = new Intl.NumberFormat('de-DE', {
    maximumFractionDigits: 0,
});
const distanceFormat = new Intl.NumberFormat('de-DE', {
    minimumFractionDigits: 1,
    maximumFractionDigits: 1,
});

const currency = computed(() => props.settings.currency || '€');
const unit = computed(() => props.data.area_unit || 'm²');

const fallbackPlaces = computed<Place[]>(() =>
    (props.settings.locations ?? [])
        .filter(
            (place) =>
                typeof place?.name === 'string' &&
                place.name.trim() !== '' &&
                Number.isFinite(Number(place.lat)) &&
                Number.isFinite(Number(place.lng)),
        )
        .map((place) => ({
            name: place.name!.trim(),
            lat: Number(place.lat),
            lng: Number(place.lng),
        })),
);

const buttonLabel = computed(() =>
    estimate.value
        ? props.data.recalculate_label || props.data.calculate_label || ''
        : props.data.calculate_label || '',
);

function createField(): Field {
    return {
        query: '',
        place: null,
        suggestions: [],
        open: false,
        loading: false,
    };
}

function toNumber(value: unknown, fallback = 0): number {
    const parsed = Number(value);

    return Number.isFinite(parsed) ? parsed : fallback;
}

/**
 * Diacritic-insensitive key so "thuringen" matches "Thüringen" and
 * "muenchen" matches "München".
 */
function fold(value: string): string {
    return value
        .trim()
        .toLowerCase()
        .replace(/ß/g, 'ss')
        .normalize('NFD')
        .replace(/\p{Diacritic}/gu, '')
        .replace(/ae/g, 'a')
        .replace(/oe/g, 'o')
        .replace(/ue/g, 'u')
        .normalize('NFC');
}

function localMatches(query: string): Suggestion[] {
    const needle = fold(query);

    if (needle === '') {
        return [];
    }

    return fallbackPlaces.value
        .filter((place) => fold(place.name).includes(needle))
        .slice(0, 8);
}

function localPlace(query: string): Place | null {
    const needle = fold(query);

    if (needle === '') {
        return null;
    }

    return (
        fallbackPlaces.value.find((place) => fold(place.name) === needle) ??
        fallbackPlaces.value.find((place) =>
            fold(place.name).startsWith(needle),
        ) ??
        fallbackPlaces.value.find((place) => fold(place.name).includes(needle)) ??
        null
    );
}

async function search(field: Field): Promise<void> {
    const query = field.query.trim();

    if (query.length < 2) {
        field.suggestions = [];
        field.open = false;

        return;
    }

    field.loading = true;

    try {
        const response = await fetch(suggestPlaces.url({ query: { q: query } }), {
            headers: { Accept: 'application/json' },
        });
        const payload = await response.json();
        const remote: Suggestion[] = Array.isArray(payload?.suggestions)
            ? payload.suggestions
            : [];

        field.suggestions = remote.length > 0 ? remote : localMatches(query);
    } catch {
        field.suggestions = localMatches(query);
    } finally {
        field.loading = false;
        field.open = field.suggestions.length > 0;
    }
}

function onInput(field: Field): void {
    field.place = null;
    hasError.value = false;

    const pending = timers.get(field);

    if (pending) {
        clearTimeout(pending);
    }

    timers.set(
        field,
        setTimeout(() => void search(field), 250),
    );
}

async function pick(field: Field, suggestion: Suggestion): Promise<void> {
    field.query = suggestion.name;
    field.suggestions = [];
    field.open = false;

    if (
        Number.isFinite(Number(suggestion.lat)) &&
        Number.isFinite(Number(suggestion.lng))
    ) {
        field.place = {
            name: suggestion.name,
            lat: Number(suggestion.lat),
            lng: Number(suggestion.lng),
        };

        return;
    }

    if (!suggestion.place_id) {
        return;
    }

    field.loading = true;

    try {
        const response = await fetch(
            resolvePlace.url({ query: { place_id: suggestion.place_id } }),
            { headers: { Accept: 'application/json' } },
        );
        const payload = await response.json();

        if (payload?.place) {
            field.place = {
                name: payload.place.name || suggestion.name,
                lat: Number(payload.place.lat),
                lng: Number(payload.place.lng),
            };
        }
    } catch {
        field.place = null;
    } finally {
        field.loading = false;
    }
}

function selectFirst(field: Field): void {
    if (field.open && field.suggestions.length > 0) {
        void pick(field, field.suggestions[0]);
    }
}

function closeAll(event: MouseEvent): void {
    if (root.value && !root.value.contains(event.target as Node)) {
        fromField.open = false;
        toField.open = false;
    }
}

function distanceBetween(start: Place, target: Place): number {
    const radius = 6371;
    const toRadians = (value: number) => (value * Math.PI) / 180;
    const deltaLat = toRadians(target.lat - start.lat);
    const deltaLng = toRadians(target.lng - start.lng);

    const a =
        Math.sin(deltaLat / 2) ** 2 +
        Math.cos(toRadians(start.lat)) *
            Math.cos(toRadians(target.lat)) *
            Math.sin(deltaLng / 2) ** 2;

    return radius * 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
}

/**
 * Resolve a field the visitor typed but never picked from the dropdown: try the
 * built-in list first, then fall back to the first search suggestion.
 */
async function ensurePlace(field: Field): Promise<Place | null> {
    if (field.place) {
        return field.place;
    }

    const local = localPlace(field.query);

    if (local) {
        return local;
    }

    if (field.query.trim().length < 2) {
        return null;
    }

    await search(field);
    field.open = false;

    const first = field.suggestions[0];

    if (!first) {
        return null;
    }

    await pick(field, first);

    return field.place;
}

async function calculate(): Promise<void> {
    if (calculating.value) {
        return;
    }

    calculating.value = true;

    const [start, target] = await Promise.all([
        ensurePlace(fromField),
        ensurePlace(toField),
    ]).finally(() => {
        calculating.value = false;
    });
    const size = toNumber(area.value);

    if (!start || !target || size <= 0) {
        estimate.value = null;
        hasError.value = true;

        return;
    }

    hasError.value = false;

    const distance = distanceBetween(start, target);
    const total =
        toNumber(props.settings.base_price) +
        size * toNumber(props.settings.price_per_sqm) +
        distance * toNumber(props.settings.price_per_km);
    const spread = toNumber(props.settings.spread_percent) / 100;

    estimate.value = {
        low: Math.round(total * (1 - spread)),
        high: Math.round(total * (1 + spread)),
        distance,
        area: size,
    };
}

onMounted(() => {
    document.addEventListener('click', closeAll);
});

onBeforeUnmount(() => {
    document.removeEventListener('click', closeAll);
    timers.forEach((timer) => clearTimeout(timer));
});
</script>

<template>
    <section ref="root" v-reveal class="mv-calc section-py">
        <div class="container-xl">
            <h2 v-if="data.title" class="mv-calc-title">{{ data.title }}</h2>

            <form class="mv-calc-form" @submit.prevent="calculate">
                <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
                    <div class="mv-calc-field" :class="{ 'is-open': fromField.open }">
                        <label :for="`${uid}-from`">
                            <MapPin :size="14" />
                            {{ data.from_label }}
                        </label>
                        <input
                            :id="`${uid}-from`"
                            v-model="fromField.query"
                            type="text"
                            autocomplete="off"
                            role="combobox"
                            :aria-expanded="fromField.open"
                            :placeholder="data.from_placeholder"
                            @input="onInput(fromField)"
                            @focus="fromField.open = fromField.suggestions.length > 0"
                            @keydown.enter.prevent="selectFirst(fromField)"
                        />
                        <Loader2 v-if="fromField.loading" class="mv-calc-spin" :size="16" />
                        <ul v-if="fromField.open" class="mv-calc-options" role="listbox">
                            <li
                                v-for="(suggestion, i) in fromField.suggestions"
                                :key="i"
                                role="option"
                                @click="pick(fromField, suggestion)"
                            >
                                {{ suggestion.name }}
                            </li>
                        </ul>
                    </div>

                    <div class="mv-calc-field" :class="{ 'is-open': toField.open }">
                        <label :for="`${uid}-to`">
                            <MapPin :size="14" />
                            {{ data.to_label }}
                        </label>
                        <input
                            :id="`${uid}-to`"
                            v-model="toField.query"
                            type="text"
                            autocomplete="off"
                            role="combobox"
                            :aria-expanded="toField.open"
                            :placeholder="data.to_placeholder"
                            @input="onInput(toField)"
                            @focus="toField.open = toField.suggestions.length > 0"
                            @keydown.enter.prevent="selectFirst(toField)"
                        />
                        <Loader2 v-if="toField.loading" class="mv-calc-spin" :size="16" />
                        <ul v-if="toField.open" class="mv-calc-options" role="listbox">
                            <li
                                v-for="(suggestion, i) in toField.suggestions"
                                :key="i"
                                role="option"
                                @click="pick(toField, suggestion)"
                            >
                                {{ suggestion.name }}
                            </li>
                        </ul>
                    </div>

                    <div class="mv-calc-field">
                        <label :for="`${uid}-area`">
                            <Home :size="14" />
                            {{ data.area_label }}
                        </label>
                        <input
                            :id="`${uid}-area`"
                            v-model="area"
                            type="number"
                            min="1"
                            step="1"
                            inputmode="numeric"
                            :placeholder="data.area_placeholder"
                        />
                    </div>
                </div>

                <p v-if="hasError" class="mv-calc-error" role="alert">
                    {{ data.error_text }}
                </p>

                <button type="submit" class="mv-calc-btn">
                    {{ buttonLabel }}
                    <RefreshCw :size="16" />
                </button>
            </form>
        </div>

        <div class="mv-calc-divider" aria-hidden="true">
            <svg viewBox="0 0 1440 110" preserveAspectRatio="none">
                <path
                    d="M0,52 C240,110 480,4 720,30 C960,56 1200,108 1440,58 L1440,110 L0,110 Z"
                />
            </svg>
        </div>

        <div class="mv-calc-panel">
            <div class="container-xl">
                <template v-if="estimate">
                    <div class="mv-calc-result">
                        <h3 v-if="data.result_title" class="mv-calc-result-title">
                            {{ data.result_title }}
                        </h3>
                        <p class="mv-calc-price">
                            {{ currency }} {{ priceFormat.format(estimate.low) }}
                            &ndash;
                            {{ currency }} {{ priceFormat.format(estimate.high) }}
                        </p>
                        <ul class="mv-calc-meta">
                            <li>
                                {{ data.volume_label }}: {{ estimate.area }}
                                {{ unit }}
                            </li>
                            <li>
                                {{ data.distance_label }}:
                                {{ distanceFormat.format(estimate.distance) }} km
                            </li>
                        </ul>
                    </div>

                    <div
                        v-if="data.cta_text"
                        class="mv-rte mv-calc-cta-text"
                        v-html="data.cta_text"
                    />

                    <a
                        v-if="data.cta_label"
                        class="mv-calc-cta"
                        :href="data.cta_url || '#'"
                    >
                        {{ data.cta_label }}
                    </a>
                </template>

                <p v-else class="mv-calc-empty">{{ data.empty_text }}</p>
            </div>
        </div>
    </section>
</template>
