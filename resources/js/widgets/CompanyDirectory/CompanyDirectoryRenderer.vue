<script setup lang="ts">
import {
    ArrowRight,
    BadgeCheck,
    Building2,
    CalendarDays,
    MapPin,
    ShieldCheck,
    Star,
    Zap,
} from 'lucide-vue-next';
import { computed, ref } from 'vue';

type Company = {
    logo_url?: string | null;
    name?: string;
    verified?: boolean;
    top_pro?: boolean;
    score?: string;
    reviews_count?: number;
    badges?: string;
    description?: string;
    address?: string;
    founded?: string;
    services?: string;
    request_url?: string;
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
    request_label?: string;
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
                <span v-if="data.heading" class="mv-map-eyebrow mb-4">Geprüfte Anbieter</span>
                <h2 v-if="data.heading" class="mv-directory__title">
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
                    class="mv-directory__card"
                >
                    <div class="mv-directory__card-inner">
                        <div
                            class="grid gap-5 sm:grid-cols-[120px_1fr] lg:grid-cols-[150px_1fr]"
                        >
                            <div class="mv-directory__logo">
                                <img
                                    v-if="company.logo_url"
                                    :src="company.logo_url"
                                    :alt="company.name || 'Unternehmen'"
                                />
                                <Building2 v-else :size="40" />
                            </div>

                            <div class="min-w-0">
                                <div
                                    class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between"
                                >
                                    <div class="min-w-0">
                                        <h3 class="mv-directory__name">
                                            <span class="mv-directory__rank">
                                                {{ i + 1 }}
                                            </span>
                                            {{ company.name }}
                                            <BadgeCheck
                                                v-if="company.verified"
                                                class="mv-directory__verified"
                                                :size="18"
                                            />
                                        </h3>

                                        <div
                                            v-if="
                                                company.top_pro ||
                                                splitTags(company.badges).length
                                            "
                                            class="mv-directory__badges"
                                        >
                                            <span
                                                v-if="company.top_pro"
                                                class="mv-directory__toppro"
                                            >
                                                <ShieldCheck :size="13" />
                                                TOP PRO
                                            </span>
                                            <span
                                                v-for="badge in splitTags(
                                                    company.badges,
                                                )"
                                                :key="badge"
                                                class="mv-directory__chip"
                                            >
                                                <Zap :size="12" />
                                                {{ badge }}
                                            </span>
                                        </div>
                                    </div>

                                    <div class="mv-directory__score">
                                        <div class="mv-directory__score-badge">
                                            <span
                                                class="mv-directory__score-value"
                                            >
                                                {{ company.score }}
                                            </span>
                                            <span class="mv-directory__stars">
                                                <Star
                                                    v-for="s in 5"
                                                    :key="s"
                                                    :size="14"
                                                    :class="
                                                        s <=
                                                        scoreToStars(
                                                            company.score,
                                                        )
                                                            ? 'is-on'
                                                            : ''
                                                    "
                                                />
                                            </span>
                                        </div>
                                        <span class="mv-directory__count">
                                            {{
                                                reviewsLabel(
                                                    company.reviews_count,
                                                )
                                            }}
                                            {{
                                                data.score_label ||
                                                'Bewertungen'
                                            }}
                                        </span>
                                    </div>
                                </div>

                                <div
                                    v-if="company.description"
                                    class="mv-rte mv-directory__desc"
                                    v-html="company.description"
                                />

                                <div class="mv-directory__foot">
                                    <ul class="mv-directory__meta">
                                        <li v-if="company.address">
                                            <MapPin :size="15" />
                                            {{ company.address }}
                                        </li>
                                        <li v-if="company.founded">
                                            <CalendarDays :size="15" />
                                            {{ company.founded }}
                                        </li>
                                    </ul>

                                    <div class="mv-directory__actions">
                                        <a
                                            :href="company.request_url || '#'"
                                            class="mv-directory__btn mv-directory__btn--ghost"
                                        >
                                            {{
                                                data.request_label ||
                                                'Verfügbarkeit anfragen'
                                            }}
                                        </a>
                                        <a
                                            :href="company.quote_url || '#'"
                                            class="mv-directory__btn mv-directory__btn--solid"
                                        >
                                            {{
                                                data.quote_label ||
                                                'Angebot einholen'
                                            }}
                                            <span class="mv-directory__btn-ico">
                                                <ArrowRight :size="15" />
                                            </span>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </li>
            </ol>
        </div>
    </section>
</template>
