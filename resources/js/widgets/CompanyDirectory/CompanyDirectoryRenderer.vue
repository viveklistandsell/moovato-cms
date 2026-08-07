<script setup lang="ts">
import { Building2, Star, ThumbsDown, ThumbsUp } from 'lucide-vue-next';
import { computed, ref } from 'vue';

type Company = {
    logo_url?: string | null;
    name?: string;
    ribbon_label?: string | null;
    score?: string;
    reviews_count?: number;
    pro_reviews_count?: number;
    description?: string;
    address?: string;
    services?: string;
    pros?: string;
    cons?: string;
    quote_url?: string;
};

type Settings = {
    companies?: Company[];
};

type Data = {
    heading?: string;
    subheading?: string;
    all_label?: string;
    score_label?: string;
    pro_reviews_label?: string;
    rating_prefix?: string;
    pros_heading?: string;
    cons_heading?: string;
    quote_label?: string;
};

const props = defineProps<{ settings: Settings; data: Data }>();

const companies = computed<Company[]>(() => props.settings.companies ?? []);

function splitTags(value?: string): string[] {
    return (value ?? '')
        .split(',')
        .map((tag) => tag.trim())
        .filter((tag) => tag.length > 0);
}

const services = computed<string[]>(() => {
    const seen = new Set<string>();
    for (const company of companies.value) {
        for (const service of splitTags(company.services)) {
            seen.add(service);
        }
    }
    return [...seen];
});

const activeService = ref<string | null>(null);

const visibleCompanies = computed<Company[]>(() => {
    if (!activeService.value) {
        return companies.value;
    }
    return companies.value.filter((company) =>
        splitTags(company.services).includes(activeService.value as string),
    );
});

function scoreToStars(score?: string): number {
    const value = parseFloat((score ?? '').replace(',', '.'));
    if (Number.isNaN(value)) {
        return 5;
    }
    return Math.max(0, Math.min(5, Math.round(value / 2)));
}

function reviewsLabel(count?: number): string {
    return new Intl.NumberFormat('de-DE').format(count ?? 0);
}
</script>

<template>
    <section v-reveal class="mv-directory section-py">
        <div class="container-xl">
            <header class="mv-directory__head">
                <span v-if="data.heading" class="mv-directory__eyebrow mb-4">Geprüfte Anbieter</span>
                <h2 v-if="data.heading" class="mv-directory__title mv-section-heading">
                    {{ data.heading }}
                </h2>
                <p v-if="data.subheading" class="mv-directory__subtitle">
                    {{ data.subheading }}
                </p>
            </header>

            <div
                v-if="services.length"
                class="mv-directory__filters"
                role="tablist"
            >
                <button
                    type="button"
                    class="mv-directory__pill"
                    :class="{ 'is-active': activeService === null }"
                    @click="activeService = null"
                >
                    {{ data.all_label || 'Alle' }}
                </button>
                <button
                    v-for="service in services"
                    :key="service"
                    type="button"
                    class="mv-directory__pill"
                    :class="{ 'is-active': activeService === service }"
                    @click="activeService = service"
                >
                    {{ service }}
                </button>
            </div>

            <ol class="mv-directory__list">
                <li
                    v-for="(company, i) in visibleCompanies"
                    :key="i"
                    class="mv-directory__entry"
                >
                    <h3 class="mv-directory__name">
                        <span class="mv-directory__rank">{{ i + 1 }}</span>
                        {{ company.name }}
                    </h3>

                    <div
                        v-if="company.description"
                        class="mv-rte mv-directory__desc"
                        v-html="company.description"
                    />

                    <div class="mv-directory__card">
                        <div class="mv-directory__card-inner">
                            <span
                                v-if="company.ribbon_label"
                                class="mv-directory__ribbon"
                            >
                                {{ company.ribbon_label }}
                            </span>

                            <div class="mv-directory__card-grid">
                                <div class="mv-directory__logo">
                                    <img
                                        v-if="company.logo_url"
                                        :src="company.logo_url"
                                        :alt="company.name || 'Unternehmen'"
                                    />
                                    <Building2 v-else :size="36" />
                                </div>

                                <div class="mv-directory__info">
                                    <div class="mv-directory__score">
                                        <span class="mv-directory__score-value">
                                            {{ company.score }}
                                        </span>
                                        <span class="mv-directory__stars">
                                            <Star
                                                v-for="s in 5"
                                                :key="s"
                                                :size="14"
                                                :class="
                                                    s <=
                                                    scoreToStars(company.score)
                                                        ? 'is-on'
                                                        : ''
                                                "
                                            />
                                        </span>
                                        <span class="mv-directory__count">
                                            {{ reviewsLabel(company.reviews_count) }}
                                        </span>
                                    </div>

                                    <p class="mv-directory__companyname">
                                        {{ company.name }}
                                    </p>
                                    <p v-if="company.address" class="mv-directory__location">
                                        {{ company.address }}
                                    </p>

                                    <p
                                        v-if="company.pro_reviews_count"
                                        class="mv-directory__proreviews"
                                    >
                                        {{ reviewsLabel(company.pro_reviews_count) }}
                                        {{ data.pro_reviews_label || 'Bewertungen als Profi' }}
                                    </p>
                                </div>

                                <div class="mv-directory__actions">
                                    <a
                                        :href="company.quote_url || '#'"
                                        class="mv-directory__btn mv-directory__btn--solid"
                                    >
                                        {{ data.quote_label || 'Angebot anfordern' }}
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div
                        v-if="splitTags(company.pros).length || splitTags(company.cons).length"
                        class="mv-directory__rating"
                    >
                        <h4 class="mv-directory__rating-title">
                            {{ data.rating_prefix || 'So bewerten Kunden' }}
                            {{ company.name }}
                        </h4>

                        <div class="mv-directory__rating-grid">
                            <div class="mv-directory__rating-col mv-directory__rating-col--pros">
                                <span class="mv-directory__rating-head">
                                    {{ data.pros_heading || 'Vorteile' }}
                                </span>
                                <ul>
                                    <li v-for="pro in splitTags(company.pros)" :key="pro">
                                        <ThumbsUp :size="14" />
                                        <span>{{ pro }}</span>
                                    </li>
                                </ul>
                            </div>
                            <div class="mv-directory__rating-col mv-directory__rating-col--cons">
                                <span class="mv-directory__rating-head">
                                    {{ data.cons_heading || 'Nachteile' }}
                                </span>
                                <ul>
                                    <li v-for="con in splitTags(company.cons)" :key="con">
                                        <ThumbsDown :size="14" />
                                        <span>{{ con }}</span>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </li>
            </ol>
        </div>
    </section>
</template>
