<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import {
    BadgeCheck,
    Briefcase,
    Building2,
    ChevronLeft,
    ChevronRight,
    Globe,
    Mail,
    MapPin,
    MessageCircle,
    MessageSquareQuote,
    Phone,
    Star,
    Truck,
    X,
} from 'lucide-vue-next';
import { computed, ref } from 'vue';
import { localizedUrl } from '@/lib/localizedUrl';

type Contact = {
    type: 'phone' | 'email' | 'website' | 'whatsapp';
    value: string;
    label: string | null;
    is_primary: boolean;
};
type Service = {
    id: number;
    name: string;
    parent_name: string | null;
    is_primary: boolean;
    price_from: number | string | null;
    price_unit: string | null;
};
type ServiceArea = {
    city_id: number;
    city_name: string;
    districts: { id: number; name: string }[];
};
type Faq = { question: string; answer: string };
type GalleryItem = { url: string | null; name: string | null };

type Company = {
    id: number;
    name: string;
    permalink: string | null;
    about: string | null;
    short_description: string | null;
    logo: string | null;
    cover: string | null;
    verified: boolean;
    is_top_rated: boolean;
    plan_tier: string;
    rating_avg: number;
    review_count: number;
    recommend_pct: number;
    rating_breakdown: Record<string, number> | null;
    google_rating: number | string | null;
    google_review_count: number;
    founded_year: number | null;
    employee_count: number | null;
    street: string | null;
    postal_code: string | null;
    city_name: string | null;
    district_name: string | null;
    state_name: string | null;
    country_name: string | null;
    contacts: Contact[];
    services: Service[];
    service_areas: ServiceArea[];
    gallery: GalleryItem[];
    faqs: Faq[];
};

const props = defineProps<{
    locale: string;
    company: Company;
}>();

const t = computed(() => ({
    home: props.locale === 'de' ? 'Startseite' : 'Home',
    breadcrumb_list: props.locale === 'de' ? 'Umzugsunternehmen' : 'Moving companies',
    based_on: (n: number) => props.locale === 'de' ? `basierend auf ${n} Bewertungen` : `based on ${n} reviews`,
    request_quote: props.locale === 'de' ? 'Angebot anfordern' : 'Request a quote',
    write_review: props.locale === 'de' ? 'Bewertung schreiben' : 'Write a review',
    top_rated: props.locale === 'de' ? 'Top-bewertetes Umzugsunternehmen' : 'Top-rated moving company',
    verified: props.locale === 'de' ? 'Verifiziert' : 'Verified',
    section_services: props.locale === 'de' ? 'Leistungen' : 'Services',
    section_about: props.locale === 'de' ? 'Über uns' : 'About',
    section_gallery: props.locale === 'de' ? 'Galerie' : 'Gallery',
    section_areas: props.locale === 'de' ? 'Einsatzgebiete' : 'Service areas',
    section_faq: props.locale === 'de' ? 'Häufige Fragen' : 'Frequently asked',
    section_reviews: props.locale === 'de' ? 'Bewertungen' : 'Reviews',
    contact: props.locale === 'de' ? 'Kontakt' : 'Contact',
    call: props.locale === 'de' ? 'Anrufen' : 'Call',
    email: 'E-Mail',
    website: 'Website',
    whatsapp: 'WhatsApp',
    price_from: props.locale === 'de' ? 'ab' : 'from',
    recommend: (p: number) => props.locale === 'de'
        ? `${p}% empfehlen dieses Unternehmen weiter.`
        : `${p}% recommend this company.`,
    reviews_coming_soon: props.locale === 'de'
        ? 'Öffentliche Bewertungen erscheinen hier, sobald das Bewertungsmodul aktiviert ist.'
        : 'Public reviews will appear here once the reviews module is live.',
    founded: props.locale === 'de' ? 'Gegründet' : 'Founded',
    employees: props.locale === 'de' ? 'Mitarbeiter' : 'Employees',
    empty_faqs: props.locale === 'de' ? 'Keine FAQs hinterlegt.' : 'No FAQs yet.',
    empty_gallery: props.locale === 'de' ? 'Keine Galerie-Bilder.' : 'No gallery photos yet.',
    empty_services: props.locale === 'de' ? 'Keine Leistungen hinterlegt.' : 'No services listed.',
    write_review_cta: props.locale === 'de'
        ? 'Sind Sie mit diesem Unternehmen umgezogen? Teilen Sie Ihre Erfahrungen.'
        : 'Did you move with this company? Tell us about your experience.',
    address_line: props.locale === 'de' ? 'Adresse' : 'Address',
    call_cta_prefix: props.locale === 'de' ? 'Tel.' : 'Tel.',
}));

// Contact grouped by type for the sidebar card.
const contactsByType = computed(() => {
    const map: Record<string, Contact[]> = { phone: [], email: [], website: [], whatsapp: [] };
    for (const c of props.company.contacts) {
        map[c.type]?.push(c);
    }
    return map;
});

function contactIcon(type: string) {
    return type === 'email' ? Mail : type === 'website' ? Globe : type === 'whatsapp' ? MessageCircle : Phone;
}
function contactHref(c: Contact): string {
    if (c.type === 'phone') return `tel:${c.value.replace(/\s+/g, '')}`;
    if (c.type === 'email') return `mailto:${c.value}`;
    if (c.type === 'whatsapp') return `https://wa.me/${c.value.replace(/\D/g, '')}`;
    return c.value.startsWith('http') ? c.value : `https://${c.value}`;
}

function assetUrl(path: string | null): string | null {
    if (!path) return null;
    return path.startsWith('http') ? path : `/storage/${path}`;
}

const openFaqIdx = ref<number | null>(props.company.faqs.length > 0 ? 0 : null);
function toggleFaq(idx: number): void {
    openFaqIdx.value = openFaqIdx.value === idx ? null : idx;
}

const lightboxIdx = ref<number | null>(null);
function openLightbox(idx: number): void {
    lightboxIdx.value = idx;
}
function closeLightbox(): void {
    lightboxIdx.value = null;
}
function nextImage(): void {
    if (lightboxIdx.value === null) return;
    lightboxIdx.value = (lightboxIdx.value + 1) % props.company.gallery.length;
}
function prevImage(): void {
    if (lightboxIdx.value === null) return;
    const n = props.company.gallery.length;
    lightboxIdx.value = (lightboxIdx.value - 1 + n) % n;
}
function onLightboxKey(event: KeyboardEvent): void {
    if (lightboxIdx.value === null) return;
    if (event.key === 'Escape') closeLightbox();
    else if (event.key === 'ArrowRight') nextImage();
    else if (event.key === 'ArrowLeft') prevImage();
}
if (typeof window !== 'undefined') {
    window.addEventListener('keydown', onLightboxKey);
}

// Rating breakdown bar chart data.
const ratingBreakdown = computed(() => {
    const breakdown = props.company.rating_breakdown ?? {};
    const total = props.company.review_count > 0 ? props.company.review_count : 1;
    return [5, 4, 3, 2, 1].map((n) => ({
        stars: n,
        count: Number(breakdown[String(n)] ?? 0),
        pct: (Number(breakdown[String(n)] ?? 0) / total) * 100,
    }));
});
</script>

<template>
    <Head :title="company.name" />

    <div class="mv-company-show bg-[var(--paper)]">
        <!-- Header band with breadcrumb + name -->
        <div class="border-b border-[var(--linen)] bg-[color-mix(in_srgb,var(--orange-soft)_60%,white)] py-6">
            <div class="container-xl">
                <nav class="mb-3 flex flex-wrap items-center gap-1.5 text-xs text-[var(--slate)]">
                    <Link :href="localizedUrl(locale, '/')" class="hover:text-[var(--midnight)] hover:underline">
                        {{ t.home }}
                    </Link>
                    <span>›</span>
                    <Link :href="localizedUrl(locale, '/companies')" class="hover:text-[var(--midnight)] hover:underline">
                        {{ t.breadcrumb_list }}
                    </Link>
                    <span v-if="company.city_name">›</span>
                    <Link
                        v-if="company.city_name"
                        :href="localizedUrl(locale, `/companies?city=${company.id}`)"
                        class="hover:text-[var(--midnight)] hover:underline"
                    >
                        {{ t.breadcrumb_list }} {{ company.city_name }}
                    </Link>
                    <span>›</span>
                    <span class="text-[var(--midnight)]">{{ company.name }}</span>
                </nav>

                <div v-if="company.is_top_rated" class="mb-2">
                    <span class="inline-flex items-center gap-1 rounded bg-[var(--midnight)] px-2.5 py-1 text-[10px] font-semibold tracking-wide text-white uppercase">
                        <Star class="size-3 fill-amber-400 text-amber-400" />
                        {{ t.top_rated }}
                    </span>
                </div>

                <div class="grid gap-4 md:grid-cols-[1fr_auto]">
                    <div>
                        <h1 class="flex flex-wrap items-center gap-2 text-3xl font-bold text-[var(--midnight)] md:text-4xl">
                            {{ company.name }}
                            <BadgeCheck v-if="company.verified" class="size-6 fill-blue-500 text-white" :title="t.verified" />
                        </h1>
                        <div v-if="company.review_count > 0" class="mt-2 flex flex-wrap items-center gap-2 text-sm">
                            <span class="text-2xl font-bold text-[var(--midnight)]">
                                {{ company.rating_avg.toFixed(1) }}
                            </span>
                            <div class="flex items-center gap-0.5 text-amber-400">
                                <Star
                                    v-for="n in 5"
                                    :key="n"
                                    class="size-4"
                                    :class="company.rating_avg / 2 >= n ? 'fill-current' : ''"
                                />
                            </div>
                            <span class="text-[var(--slate)]">{{ t.based_on(company.review_count) }}</span>
                            <span
                                v-if="company.google_review_count > 0"
                                class="text-xs text-[var(--slate-light)]"
                            >
                                + Google
                            </span>
                        </div>

                        <div class="mt-4 flex flex-wrap gap-2">
                            <a
                                v-if="contactsByType.phone[0]"
                                :href="contactHref(contactsByType.phone[0])"
                                class="inline-flex items-center gap-1.5 rounded-md bg-[var(--orange)] px-4 py-2 text-sm font-medium text-white hover:bg-[color-mix(in_srgb,var(--orange)_85%,black)]"
                            >
                                <Phone class="size-3.5" />
                                {{ t.request_quote }}
                            </a>
                            <button
                                type="button"
                                class="inline-flex items-center gap-1.5 rounded-md border border-[var(--orange)] px-4 py-2 text-sm font-medium text-[var(--orange)] hover:bg-[var(--orange-soft)]"
                                disabled
                                :title="t.reviews_coming_soon"
                            >
                                {{ t.write_review }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Body -->
        <div class="container-xl py-8">
            <div class="grid gap-6 lg:grid-cols-[1fr_320px]">
                <!-- Main content -->
                <div class="min-w-0 space-y-6">
                    <!-- Services grid -->
                    <section class="rounded-lg border border-[var(--linen)] bg-white p-6 shadow-sm">
                        <h2 class="mb-4 text-xl font-bold text-[var(--midnight)]">
                            {{ t.section_services }}
                        </h2>
                        <div
                            v-if="company.services.length === 0"
                            class="text-sm text-[var(--slate)]"
                        >
                            {{ t.empty_services }}
                        </div>
                        <ul v-else class="grid grid-cols-2 gap-3 md:grid-cols-3">
                            <li
                                v-for="s in company.services"
                                :key="s.id"
                                class="flex items-start gap-3 rounded-md border border-[var(--linen)] p-3"
                                :class="{ 'ring-2 ring-[var(--orange)]/50': s.is_primary }"
                            >
                                <div class="mt-0.5 flex size-8 shrink-0 items-center justify-center rounded-md bg-[var(--orange-soft)] text-[var(--orange)]">
                                    <Truck class="size-4" />
                                </div>
                                <div class="min-w-0">
                                    <div class="text-sm font-medium text-[var(--midnight)]">
                                        {{ s.name }}
                                        <Star
                                            v-if="s.is_primary"
                                            class="ml-1 inline size-3 fill-amber-400 text-amber-400"
                                        />
                                    </div>
                                    <div v-if="s.price_from" class="text-xs text-[var(--slate)]">
                                        {{ t.price_from }} {{ s.price_from }} €
                                        <span v-if="s.price_unit">/ {{ s.price_unit }}</span>
                                    </div>
                                    <div v-else-if="s.parent_name" class="text-xs text-[var(--slate-light)]">
                                        {{ s.parent_name }}
                                    </div>
                                </div>
                            </li>
                        </ul>
                    </section>

                    <!-- Description (rich-text HTML from TipTap in admin) -->
                    <section v-if="company.about" class="rounded-lg border border-[var(--linen)] bg-white p-6 shadow-sm">
                        <h2 class="mb-3 text-xl font-bold text-[var(--midnight)]">
                            {{ t.section_about }}
                        </h2>
                        <div
                            class="mv-prose text-sm leading-relaxed text-[var(--slate)] md:text-base"
                            v-html="company.about"
                        />
                    </section>

                    <!-- Gallery -->
                    <section class="rounded-lg border border-[var(--linen)] bg-white p-6 shadow-sm">
                        <h2 class="mb-4 text-xl font-bold text-[var(--midnight)]">
                            {{ t.section_gallery }}
                        </h2>
                        <div
                            v-if="company.gallery.length === 0"
                            class="text-sm text-[var(--slate)]"
                        >
                            {{ t.empty_gallery }}
                        </div>
                        <div v-else class="grid grid-cols-2 gap-3 md:grid-cols-3">
                            <button
                                v-for="(g, idx) in company.gallery"
                                :key="idx"
                                type="button"
                                class="group aspect-square cursor-zoom-in overflow-hidden rounded-md border border-[var(--linen)]"
                                @click="openLightbox(idx)"
                            >
                                <img
                                    v-if="g.url"
                                    :src="g.url"
                                    :alt="g.name ?? ''"
                                    class="size-full object-cover transition-transform group-hover:scale-105"
                                />
                            </button>
                        </div>
                    </section>

                    <!-- FAQ -->
                    <section v-if="company.faqs.length > 0" class="rounded-lg border border-[var(--linen)] bg-white p-6 shadow-sm">
                        <h2 class="mb-4 text-xl font-bold text-[var(--midnight)]">
                            {{ t.section_faq }}
                        </h2>
                        <ul class="divide-y divide-[var(--linen)]">
                            <li v-for="(f, idx) in company.faqs" :key="idx" class="py-3">
                                <button
                                    type="button"
                                    class="flex w-full items-start justify-between gap-4 text-left"
                                    @click="toggleFaq(idx)"
                                >
                                    <span class="text-sm font-semibold text-[var(--midnight)] md:text-base">
                                        {{ f.question }}
                                    </span>
                                    <ChevronRight
                                        class="size-4 shrink-0 text-[var(--slate)] transition-transform"
                                        :class="openFaqIdx === idx ? 'rotate-90' : ''"
                                    />
                                </button>
                                <div
                                    v-if="openFaqIdx === idx"
                                    class="mt-2 whitespace-pre-line text-sm leading-relaxed text-[var(--slate)]"
                                >
                                    {{ f.answer }}
                                </div>
                            </li>
                        </ul>
                    </section>

                    <!-- Service areas -->
                    <section v-if="company.service_areas.length > 0" class="rounded-lg border border-[var(--linen)] bg-white p-6 shadow-sm">
                        <h2 class="mb-4 text-xl font-bold text-[var(--midnight)]">
                            {{ t.section_areas }}
                        </h2>
                        <div class="space-y-4">
                            <div v-for="area in company.service_areas" :key="area.city_id">
                                <div class="mb-1.5 flex items-center gap-2 text-sm font-semibold text-[var(--midnight)]">
                                    <Building2 class="size-4 text-[var(--slate)]" />
                                    {{ area.city_name }}
                                    <span class="text-xs font-normal text-[var(--slate-light)]">
                                        ({{ area.districts.length }})
                                    </span>
                                </div>
                                <div class="flex flex-wrap gap-1.5">
                                    <span
                                        v-for="d in area.districts"
                                        :key="d.id"
                                        class="rounded-full border border-[var(--linen)] bg-[var(--linen)]/40 px-2.5 py-0.5 text-xs text-[var(--slate)]"
                                    >
                                        {{ d.name }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </section>

                    <!-- Reviews section (placeholder — reviews module ships later) -->
                    <section class="rounded-lg border border-[var(--linen)] bg-white p-6 shadow-sm">
                        <h2 class="mb-4 text-xl font-bold text-[var(--midnight)]">
                            {{ t.section_reviews }}
                            <span v-if="company.review_count > 0" class="ml-2 text-sm font-normal text-[var(--slate)]">
                                ({{ company.review_count }})
                            </span>
                        </h2>

                        <div v-if="company.review_count > 0" class="mb-4">
                            <div class="mb-2 flex items-baseline gap-2">
                                <span class="text-3xl font-bold text-[var(--midnight)]">
                                    {{ company.rating_avg.toFixed(1) }}
                                </span>
                                <div class="flex items-center gap-0.5 text-amber-400">
                                    <Star
                                        v-for="n in 5"
                                        :key="n"
                                        class="size-5"
                                        :class="company.rating_avg / 2 >= n ? 'fill-current' : ''"
                                    />
                                </div>
                            </div>
                            <p
                                v-if="company.recommend_pct > 0"
                                class="mb-4 text-sm text-emerald-700"
                            >
                                {{ t.recommend(company.recommend_pct) }}
                            </p>
                            <div v-if="ratingBreakdown" class="space-y-1.5">
                                <div
                                    v-for="row in ratingBreakdown"
                                    :key="row.stars"
                                    class="flex items-center gap-2 text-xs"
                                >
                                    <span class="w-3 text-[var(--midnight)]">{{ row.stars }}</span>
                                    <Star class="size-3 fill-amber-400 text-amber-400" />
                                    <div class="h-2 flex-1 overflow-hidden rounded-full bg-[var(--linen)]">
                                        <div
                                            class="h-full bg-amber-400"
                                            :style="{ width: `${row.pct}%` }"
                                        />
                                    </div>
                                    <span class="w-8 text-right text-[var(--slate)]">{{ row.count }}</span>
                                </div>
                            </div>
                        </div>

                        <div
                            class="mt-4 flex items-start gap-3 rounded-md border border-[var(--linen)] bg-[var(--paper)] p-4"
                        >
                            <MessageSquareQuote class="mt-0.5 size-5 shrink-0 text-[var(--orange)]" />
                            <div class="flex-1">
                                <p class="text-sm text-[var(--midnight)]">
                                    {{ t.write_review_cta }}
                                </p>
                                <p class="mt-1 text-xs text-[var(--slate)]">
                                    {{ t.reviews_coming_soon }}
                                </p>
                            </div>
                        </div>
                    </section>
                </div>

                <!-- Sticky right sidebar: contact card -->
                <aside class="lg:sticky lg:top-6 lg:h-fit">
                    <div class="overflow-hidden rounded-lg border border-[var(--linen)] bg-white shadow-sm">
                        <div class="flex aspect-[16/10] items-center justify-center bg-[var(--linen)]/40">
                            <img
                                v-if="company.logo"
                                :src="assetUrl(company.logo)!"
                                :alt="company.name"
                                class="max-h-full max-w-full object-contain p-4"
                            />
                            <Briefcase v-else class="size-10 text-[var(--slate-light)]" />
                        </div>
                        <div class="p-4">
                            <!-- Address -->
                            <div v-if="company.street || company.city_name" class="mb-3">
                                <div class="flex items-start gap-2 text-sm text-[var(--midnight)]">
                                    <MapPin class="mt-0.5 size-4 shrink-0 text-[var(--slate)]" />
                                    <span>
                                        <span v-if="company.street">{{ company.street }}<br /></span>
                                        <span v-if="company.postal_code">{{ company.postal_code }} </span>
                                        <span v-if="company.city_name">{{ company.city_name }}</span>
                                    </span>
                                </div>
                            </div>

                            <!-- Contact rows -->
                            <ul class="space-y-2 text-sm">
                                <li
                                    v-for="(c, idx) in company.contacts"
                                    :key="idx"
                                    class="flex items-start gap-2"
                                >
                                    <component
                                        :is="contactIcon(c.type)"
                                        class="mt-0.5 size-4 shrink-0 text-[var(--slate)]"
                                    />
                                    <a
                                        :href="contactHref(c)"
                                        class="min-w-0 truncate text-[var(--midnight)] hover:text-[var(--orange)] hover:underline"
                                        :target="c.type === 'website' || c.type === 'whatsapp' ? '_blank' : undefined"
                                        rel="noopener"
                                    >
                                        {{ c.value }}
                                    </a>
                                </li>
                            </ul>

                            <div v-if="contactsByType.phone[0]" class="mt-4">
                                <a
                                    :href="contactHref(contactsByType.phone[0])"
                                    class="flex items-center justify-center gap-2 rounded-md bg-[var(--orange)] px-4 py-2 text-sm font-medium text-white hover:bg-[color-mix(in_srgb,var(--orange)_85%,black)]"
                                >
                                    <Phone class="size-4" />
                                    {{ t.request_quote }}
                                </a>
                            </div>

                            <!-- Facts -->
                            <div v-if="company.founded_year || company.employee_count" class="mt-4 border-t border-[var(--linen)] pt-3 text-xs text-[var(--slate)]">
                                <div v-if="company.founded_year" class="flex justify-between">
                                    <span>{{ t.founded }}</span>
                                    <span class="font-medium text-[var(--midnight)]">{{ company.founded_year }}</span>
                                </div>
                                <div v-if="company.employee_count" class="mt-1 flex justify-between">
                                    <span>{{ t.employees }}</span>
                                    <span class="font-medium text-[var(--midnight)]">{{ company.employee_count }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </aside>
            </div>
        </div>
        <div
            v-if="lightboxIdx !== null && company.gallery[lightboxIdx]?.url"
            class="fixed inset-0 z-50 flex items-center justify-center bg-black/90 p-4"
            role="dialog"
            aria-modal="true"
            @click.self="closeLightbox"
        >
            <button
                type="button"
                class="absolute top-4 right-4 flex size-10 items-center justify-center rounded-full bg-white/10 text-white transition hover:bg-white/20"
                aria-label="Close"
                @click="closeLightbox"
            >
                <X class="size-5" />
            </button>
            <button
                v-if="company.gallery.length > 1"
                type="button"
                class="absolute left-4 flex size-10 items-center justify-center rounded-full bg-white/10 text-white transition hover:bg-white/20"
                aria-label="Previous"
                @click="prevImage"
            >
                <ChevronLeft class="size-5" />
            </button>
            <button
                v-if="company.gallery.length > 1"
                type="button"
                class="absolute right-4 flex size-10 items-center justify-center rounded-full bg-white/10 text-white transition hover:bg-white/20"
                aria-label="Next"
                @click="nextImage"
            >
                <ChevronRight class="size-5" />
            </button>
            <img
                :src="company.gallery[lightboxIdx].url ?? ''"
                :alt="company.gallery[lightboxIdx].name ?? ''"
                class="max-h-[90vh] max-w-[90vw] rounded-md object-contain shadow-2xl"
            />
            <div
                v-if="company.gallery.length > 1"
                class="absolute bottom-4 left-1/2 -translate-x-1/2 rounded-full bg-white/10 px-3 py-1 text-xs text-white"
            >
                {{ lightboxIdx + 1 }} / {{ company.gallery.length }}
            </div>
        </div>
    </div>
</template>
