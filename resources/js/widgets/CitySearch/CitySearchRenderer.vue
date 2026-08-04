<script setup lang="ts">
import { ChevronRight, Search } from 'lucide-vue-next';

type City = {
    path?: string | null;
    url?: string | null;
    alt?: string;
    name?: string;
    link?: string;
};

type Settings = {
    cities?: City[];
};

type Data = {
    title?: string;
    subtitle?: string;
    card_prefix?: string;
    search_placeholder?: string;
    search_url?: string;
};

defineProps<{ settings: Settings; data: Data }>();
</script>

<template>
    <section v-reveal class="mv-citysearch">
        <div class="mv-citysearch-head">
            <div class="container-xl mx-auto max-w-3xl text-center">
                <h2 v-if="data.title" class="mv-citysearch-title">
                    {{ data.title }}
                </h2>
                <p v-if="data.subtitle" class="mv-citysearch-subtitle">
                    {{ data.subtitle }}
                </p>
            </div>
        </div>

        <div class="mv-citysearch-body">
            <div class="container-xl">
                <ul
                    v-if="(settings.cities ?? []).length"
                    class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4"
                >
                    <li v-for="(city, i) in settings.cities" :key="i">
                        <a :href="city.link || '#'" class="mv-city-card">
                            <img
                                v-if="city.url"
                                :src="city.url"
                                :alt="city.alt || city.name || ''"
                                loading="lazy"
                                decoding="async"
                            />
                            <span class="mv-city-caption">
                                <span class="mv-city-prefix">{{
                                    data.card_prefix
                                }}</span>
                                <span class="mv-city-name">
                                    {{ city.name }}
                                    <ChevronRight :size="18" />
                                </span>
                            </span>
                        </a>
                    </li>
                </ul>

                <form
                    class="mv-citysearch-form"
                    :action="data.search_url || '#'"
                    @submit.prevent
                >
                    <input
                        type="text"
                        :placeholder="data.search_placeholder"
                    />
                    <button type="submit" aria-label="Suchen">
                        <Search :size="18" />
                    </button>
                </form>
            </div>
        </div>
    </section>
</template>
