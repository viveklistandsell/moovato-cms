<script setup lang="ts">

import { ChevronRight, Loader2, MapPin, Search } from 'lucide-vue-next';
import { computed, onBeforeUnmount, ref, watch } from 'vue';

type Settings = {
    alignment?: 'left' | 'center';
    background?: 'orange-soft' | 'midnight' | 'paper' | 'none';
};

type Data = {
    eyebrow?: string;
    title?: string;
    subtitle?: string;
    placeholder?: string;
    button_label?: string;
    no_match_hint?: string;
};

const props = defineProps<{ settings: Settings; data: Data }>();

type Suggestion = {
    kind: 'city' | 'district' | 'state' | 'country';
    id: number;
    name: string;
    subtitle: string;
    url: string;
};

const query = ref('');
const results = ref<Suggestion[]>([]);
const loading = ref(false);
const open = ref(false);
const highlight = ref(-1);
const containerRef = ref<HTMLDivElement | null>(null);
let controller: AbortController | null = null;
let debounceTimer: ReturnType<typeof setTimeout> | null = null;

const MIN_CHARS = 2;

async function runSearch(q: string): Promise<void> {
    const trimmed = q.trim();
    if (trimmed.length < MIN_CHARS) {
        results.value = [];
        open.value = trimmed.length > 0;
        loading.value = false;
        return;
    }

    controller?.abort();
    controller = new AbortController();
    loading.value = true;

    try {
        const res = await fetch(`/location-search/suggest?q=${encodeURIComponent(trimmed)}`, {
            signal: controller.signal,
            headers: { Accept: 'application/json' },
        });
        if (!res.ok) throw new Error(`HTTP ${res.status}`);
        const json = (await res.json()) as { results?: Suggestion[] };
        results.value = Array.isArray(json.results) ? json.results : [];
        open.value = true;
        highlight.value = results.value.length > 0 ? 0 : -1;
    } catch (err) {
        if ((err as { name?: string }).name !== 'AbortError') {
            results.value = [];
            open.value = false;
        }
    } finally {
        loading.value = false;
    }
}

watch(query, (q) => {
    if (debounceTimer) clearTimeout(debounceTimer);
    debounceTimer = setTimeout(() => runSearch(q), 180);
});

function pick(result: Suggestion): void {
    if (result?.url) {
        window.location.assign(result.url);
    }
}

function onSubmit(): void {
    if (results.value.length > 0 && highlight.value >= 0) {
        pick(results.value[highlight.value]);
    }
}

function onKeyDown(event: KeyboardEvent): void {
    if (!open.value || results.value.length === 0) return;
    if (event.key === 'ArrowDown') {
        event.preventDefault();
        highlight.value = (highlight.value + 1) % results.value.length;
    } else if (event.key === 'ArrowUp') {
        event.preventDefault();
        highlight.value = (highlight.value - 1 + results.value.length) % results.value.length;
    } else if (event.key === 'Escape') {
        open.value = false;
    }
}

function onDocClick(event: MouseEvent): void {
    if (!containerRef.value) return;
    if (!containerRef.value.contains(event.target as Node)) {
        open.value = false;
    }
}

if (typeof document !== 'undefined') {
    document.addEventListener('click', onDocClick);
}

onBeforeUnmount(() => {
    document.removeEventListener('click', onDocClick);
    controller?.abort();
    if (debounceTimer) clearTimeout(debounceTimer);
});

const showDropdown = computed<boolean>(
    () => open.value && (loading.value || results.value.length > 0 || query.value.trim().length > 0),
);

const isCenter = computed<boolean>(() => (props.settings.alignment ?? 'center') === 'center');

const backgroundClass = computed<string>(() => {
    switch (props.settings.background ?? 'orange-soft') {
        case 'midnight':
            return 'mv-locsearch--midnight';
        case 'paper':
            return 'mv-locsearch--paper';
        case 'none':
            return 'mv-locsearch--none';
        default:
            return 'mv-locsearch--orange-soft';
    }
});

function kindLabel(kind: Suggestion['kind']): string {
    switch (kind) {
        case 'city': return 'Stadt';
        case 'district': return 'Bezirk';
        case 'state': return 'Bundesland';
        case 'country': return 'Land';
        default: return '';
    }
}
</script>

<template>
    <section v-reveal class="mv-locsearch section-py" :class="backgroundClass">
        <div class="container-xl">
            <div
                class="mv-locsearch__inner mx-auto max-w-3xl"
                :class="isCenter ? 'text-center' : 'text-left'"
            >
                <p v-if="data.eyebrow" class="mv-locsearch__eyebrow">
                    {{ data.eyebrow }}
                </p>
                <h2 v-if="data.title" class="mv-locsearch__title mv-section-heading">
                    {{ data.title }}
                </h2>
                <p v-if="data.subtitle" class="mv-locsearch__subtitle">
                    {{ data.subtitle }}
                </p>

                <div ref="containerRef" class="mv-locsearch__form-wrap relative mt-6">
                    <form
                        class="mv-locsearch__form"
                        role="search"
                        action="/location-search/go"
                        method="GET"
                        @submit="onSubmit"
                    >
                        <input
                            v-model="query"
                            type="text"
                            name="q"
                            :placeholder="data.placeholder ?? 'Stadt, Bezirk, Bundesland oder Land …'"
                            autocomplete="off"
                            aria-label="Standort suchen"
                            aria-autocomplete="list"
                            :aria-expanded="open"
                            aria-controls="mv-locsearch-listbox"
                            @focus="open = query.length > 0 || results.length > 0"
                            @keydown="onKeyDown"
                        />
                        <button type="submit" :aria-label="data.button_label ?? 'Suchen'">
                            <Loader2 v-if="loading" class="animate-spin" :size="18" />
                            <Search v-else :size="18" />
                            <span class="mv-locsearch__button-label">
                                {{ data.button_label ?? 'Suchen' }}
                            </span>
                        </button>
                    </form>

                    <ul
                        v-if="showDropdown"
                        id="mv-locsearch-listbox"
                        role="listbox"
                        class="mv-locsearch__results"
                    >
                        <li
                            v-if="!loading && query.trim().length > 0 && query.trim().length < 2"
                            class="mv-locsearch__empty"
                        >
                            Bitte mindestens 2 Zeichen eingeben…
                        </li>
                        <li
                            v-else-if="!loading && results.length === 0 && query.trim().length >= 2"
                            class="mv-locsearch__empty"
                        >
                            {{ data.no_match_hint ?? 'Kein passender Ort — wir suchen trotzdem für Sie.' }}
                        </li>
                        <li
                            v-for="(result, i) in results"
                            :key="`${result.kind}-${result.id}`"
                            role="option"
                            :aria-selected="i === highlight"
                            class="mv-locsearch__result"
                            :class="{ 'is-active': i === highlight }"
                            @mouseenter="highlight = i"
                            @mousedown.prevent="pick(result)"
                        >
                            <span class="mv-locsearch__result-icon">
                                <MapPin :size="16" />
                            </span>
                            <span class="mv-locsearch__result-text">
                                <span class="mv-locsearch__result-name">{{ result.name }}</span>
                                <span class="mv-locsearch__result-sub">{{ result.subtitle }}</span>
                            </span>
                            <span class="mv-locsearch__result-kind">
                                {{ kindLabel(result.kind) }}
                            </span>
                            <ChevronRight :size="16" class="mv-locsearch__result-chevron" />
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </section>
</template>
