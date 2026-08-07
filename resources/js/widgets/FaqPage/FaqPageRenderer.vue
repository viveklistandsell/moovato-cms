<script setup lang="ts">
import { ChevronDown, Search } from 'lucide-vue-next';
import { computed, ref } from 'vue';

type Item = { question?: string; answer?: string };
type Category = { name?: string; items?: Item[] };

type Settings = {
    show_search: boolean;
    show_categories: boolean;
    first_open: boolean;
};

type Data = {
    eyebrow?: string;
    heading?: string;
    subheading?: string;
    search_placeholder?: string;
    categories?: Category[];
};

const props = defineProps<{ settings: Settings; data: Data }>();

const categories = computed<Category[]>(() => props.data.categories ?? []);

const query = ref('');
const activeCategory = ref<number | null>(null);
const openKey = ref<string | null>(props.settings.first_open ? '0-0' : null);

function toggle(key: string): void {
    openKey.value = openKey.value === key ? null : key;
}

function stripHtml(value: string): string {
    return value.replace(/<[^>]*>/g, ' ');
}

function matches(item: Item, term: string): boolean {
    if (!term) {
        return true;
    }
    const haystack = `${item.question ?? ''} ${stripHtml(item.answer ?? '')}`;
    return haystack.toLowerCase().includes(term);
}

const visibleCategories = computed(() => {
    const term = query.value.trim().toLowerCase();

    return categories.value
        .map((category, ci) => ({
            ci,
            name: category.name,
            items: (category.items ?? [])
                .map((item, ii) => ({ item, key: `${ci}-${ii}` }))
                .filter(({ item }) => matches(item, term)),
        }))
        .filter(
            ({ ci, items }) =>
                items.length > 0 &&
                (activeCategory.value === null || activeCategory.value === ci),
        );
});

const hasResults = computed(() =>
    visibleCategories.value.some((c) => c.items.length > 0),
);
</script>

<template>
    <section v-reveal class="mv-faqpage w-full section-py">
        <div class="container-xl">
            <header class="mx-auto max-w-3xl text-center">
                <span v-if="data.eyebrow" class="mv-howitworks__eyebrow">
                    {{ data.eyebrow }}
                </span>
                <h1
                    v-if="data.heading"
                    class="mv-faqpage-title text-4xl font-bold tracking-tight sm:text-5xl"
                >
                    {{ data.heading }}
                </h1>
                <p v-if="data.subheading" class="mv-faqpage-sub mt-4 text-lg">
                    {{ data.subheading }}
                </p>
            </header>

            <div
                v-if="settings.show_search"
                class="mv-faqpage-search mx-auto mt-8 flex max-w-xl items-center gap-3"
            >
                <Search class="mv-faqpage-search-icon size-5 shrink-0" />
                <input
                    v-model="query"
                    type="search"
                    :placeholder="data.search_placeholder || 'Suchen …'"
                    class="w-full bg-transparent text-base outline-none"
                    aria-label="FAQ durchsuchen"
                />
            </div>

            <div
                v-if="settings.show_categories && categories.length > 1"
                class="mt-8 flex flex-wrap justify-center gap-2"
            >
                <button
                    type="button"
                    class="mv-faqpage-pill"
                    :class="{ 'is-active': activeCategory === null }"
                    @click="activeCategory = null"
                >
                    Alle
                </button>
                <button
                    v-for="(category, ci) in categories"
                    :key="ci"
                    type="button"
                    class="mv-faqpage-pill"
                    :class="{ 'is-active': activeCategory === ci }"
                    @click="activeCategory = ci"
                >
                    {{ category.name }}
                </button>
            </div>

            <div class="mx-auto mt-10 max-w-4xl space-y-10">
                <div
                    v-for="group in visibleCategories"
                    :key="group.ci"
                    class="mb-3"
                >
                    <ul class="mv-faqpage-list">
                        <li
                            v-for="{ item, key } in group.items"
                            :key="key"
                            class="mv-faqpage-item"
                            :class="{ 'is-open': openKey === key }"
                        >
                            <button
                                type="button"
                                class="mv-faqpage-q"
                                :aria-expanded="openKey === key"
                                @click="toggle(key)"
                            >
                                <span>{{ item.question }}</span>
                                <ChevronDown
                                    class="mv-faqpage-chevron size-5 shrink-0"
                                />
                            </button>
                            <div v-show="openKey === key" class="mv-faqpage-a">
                                <div class="mv-rte" v-html="item.answer"></div>
                            </div>
                        </li>
                    </ul>
                </div>

                <p v-if="!hasResults" class="mv-faqpage-empty text-center">
                    Keine passenden Fragen gefunden.
                </p>
            </div>
        </div>
    </section>
</template>
