<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import {
    ArrowLeft,
    Briefcase,
    Globe,
    Mail,
    MapPin,
    Phone,
    Star,
    ThumbsDown,
    ThumbsUp,
    Upload,
} from 'lucide-vue-next';
import { computed, ref } from 'vue';
import { localizedUrl } from '@/lib/localizedUrl';

type CompanySummary = {
    id: number;
    name: string;
    permalink: string | null;
    logo: string | null;
    street: string | null;
    postal_code: string | null;
    city_name: string | null;
    phone: string | null;
    email: string | null;
    website: string | null;
};

const props = defineProps<{
    locale: string;
    company: CompanySummary;
    tags: string[];
    sources: string[];
}>();

const t = computed(() => ({
    home: props.locale === 'de' ? 'Startseite' : 'Home',
    back_profile: props.locale === 'de' ? 'Zurück zum Profil' : 'Back to profile',
    breadcrumb_list: props.locale === 'de' ? 'Umzugsunternehmen' : 'Moving companies',
    heading: (name: string) => props.locale === 'de'
        ? `${name} bewerten`
        : `${name} review`,
    section_rating: props.locale === 'de' ? 'Wie würden Sie Ihre Erfahrung bewerten?' : 'How would you rate your experience?',
    section_body: props.locale === 'de' ? 'Beschreiben Sie Ihre Erfahrung' : 'Describe your experience',
    body_placeholder: props.locale === 'de' ? 'Ihre Bewertung...' : 'Your review...',
    chars_remaining: (n: number) => props.locale === 'de'
        ? `${n} Zeichen verbleibend`
        : `${n} characters remaining`,
    section_pros_cons: props.locale === 'de' ? 'Vor- und Nachteile (optional)' : 'Advantages and disadvantages (optional)',
    pros_placeholder: props.locale === 'de' ? 'Auswählen' : 'Choose',
    cons_placeholder: props.locale === 'de' ? 'Auswählen' : 'Choose',
    section_source: props.locale === 'de' ? 'Wie sind Sie auf dieses Unternehmen aufmerksam geworden?' : 'How did you find this moving company?',
    section_you: props.locale === 'de' ? 'Ihre Daten' : 'Your data',
    you_note: props.locale === 'de'
        ? 'Wir verwenden diese E-Mail-Adresse ausschließlich zur Bestätigung der Bewertung und um Sie zu informieren, ob das Unternehmen geantwortet hat.'
        : 'We will only use this email address to confirm the review and to inform you whether the moving company has responded.',
    name_placeholder: props.locale === 'de' ? 'Name*' : 'name*',
    email_placeholder: props.locale === 'de' ? 'E-Mail-Adresse*' : 'e-mail address*',
    anonymous_label: props.locale === 'de'
        ? 'Ich möchte meine Bewertung anonym veröffentlichen. Nur meine Initialen sollen sichtbar sein.'
        : 'I would like to publish my review anonymously. Only my initials should be visible.',
    section_proof: props.locale === 'de' ? 'Umzugsnachweis (empfohlen)' : 'Proof of relocation (recommended)',
    proof_note: props.locale === 'de'
        ? 'Wir behandeln Ihre Dokumente vertraulich und geben sie nicht an Dritte weiter, sondern verwenden sie nur zu Prüfzwecken.'
        : 'We treat your documents confidentially and will not pass them on to third parties, but will only use them for verification purposes.',
    proof_button: props.locale === 'de' ? 'Dokument hochladen' : 'Upload your document',
    submit: props.locale === 'de' ? 'Bewertung absenden' : 'Submit rating',
    terms: props.locale === 'de'
        ? 'Moovato ist berechtigt, Ihre Bewertung zu veröffentlichen. Sie bestätigen, dass Sie mit diesem Unternehmen umgezogen sind.'
        : 'Moovato is authorized to publish your review. You declare that you have booked a move with this moving company.',
    tag_labels: {
        friendly: props.locale === 'de' ? 'Freundlich' : 'Friendly',
        professional: props.locale === 'de' ? 'Professionell' : 'Professional',
        fast: props.locale === 'de' ? 'Schnell' : 'Fast',
        'on-time': props.locale === 'de' ? 'Pünktlich' : 'On-time',
        reliable: props.locale === 'de' ? 'Zuverlässig' : 'Reliable',
        careful: props.locale === 'de' ? 'Sorgfältig' : 'Careful',
        'fair-pricing': props.locale === 'de' ? 'Faire Preise' : 'Fair pricing',
        communicative: props.locale === 'de' ? 'Kommunikativ' : 'Communicative',
        'value-for-money': props.locale === 'de' ? 'Preis-Leistung' : 'Value for money',
    } as Record<string, string>,
    tag_labels_negative: {
        friendly: props.locale === 'de' ? 'Unfreundlich' : 'Unfriendly',
        professional: props.locale === 'de' ? 'Unprofessionell' : 'Unprofessional',
        fast: props.locale === 'de' ? 'Langsam' : 'Slow',
        'on-time': props.locale === 'de' ? 'Verspätet' : 'Late arrival',
        reliable: props.locale === 'de' ? 'Unzuverlässig' : 'Unreliable',
        careful: props.locale === 'de' ? 'Unachtsam' : 'Careless',
        'fair-pricing': props.locale === 'de' ? 'Zu teuer' : 'Overpriced',
        communicative: props.locale === 'de' ? 'Schlechte Kommunikation' : 'Poor communication',
        'value-for-money': props.locale === 'de' ? 'Schlechtes Preis-Leistungs-Verhältnis' : 'Poor value for money',
    } as Record<string, string>,
    source_labels: {
        google: props.locale === 'de' ? 'Google-Suche' : 'Google search',
        referred: props.locale === 'de' ? 'Empfehlung' : 'Recommendation',
        website: props.locale === 'de' ? 'Direkt auf Moovato' : 'Directly on Moovato',
        social: props.locale === 'de' ? 'Soziale Medien' : 'Social media',
        other: props.locale === 'de' ? 'Andere' : 'Other',
    } as Record<string, string>,
    source_placeholder: props.locale === 'de' ? 'Auswählen' : 'Choose',
}));

const BODY_MAX = 5000;

const form = useForm({
    rating: 0,
    body: '',
    advantages: [] as string[],
    disadvantages: [] as string[],
    source: '',
    author_name: '',
    author_email: '',
    is_anonymous: false,
    proof_document: null as File | null,
    website_url: '',
});

const hoveredStar = ref<number>(0);
const bodyRemaining = computed(() => Math.max(0, BODY_MAX - form.body.length));

function setRating(stars: number): void {
    form.rating = stars;
}

function toggleTag(list: 'advantages' | 'disadvantages', tag: string): void {
    const current = form[list];
    const idx = current.indexOf(tag);
    if (idx >= 0) {
        form[list] = current.filter((t) => t !== tag);
    } else if (current.length < 3) {
        form[list] = [...current, tag];
    }
}

function onFileChange(event: Event): void {
    const input = event.target as HTMLInputElement;
    form.proof_document = input.files && input.files.length > 0 ? input.files[0] : null;
}

function submit(): void {
    form.post(localizedUrl(props.locale, `/company/${props.company.permalink}/review`), {
        forceFormData: true,
        preserveScroll: true,
    });
}

function assetUrl(path: string | null): string | null {
    if (!path) return null;

    return path.startsWith('http') ? path : `/storage/${path}`;
}

const detailUrl = computed(() =>
    localizedUrl(props.locale, `/company/${props.company.permalink}`),
);
</script>

<template>
    <Head :title="t.heading(company.name)" />

    <div class="mv-review-new bg-[var(--paper)] py-8 md:py-12">
        <div class="container-xl">
            <!-- Header band -->
            <div class="mb-6 flex items-start justify-between gap-4">
                <div>
                    <Link
                        :href="detailUrl"
                        class="mb-2 inline-flex items-center gap-1.5 text-sm text-[var(--slate)] transition-colors hover:text-[var(--orange)]"
                    >
                        <ArrowLeft class="size-4" />
                        {{ t.back_profile }}
                    </Link>
                    <h1 class="text-3xl font-bold text-[var(--midnight)] md:text-4xl">
                        {{ t.heading(company.name) }}
                    </h1>
                </div>
            </div>

            <div class="grid gap-6 lg:grid-cols-[1fr_320px]">
                <form class="min-w-0 space-y-8" @submit.prevent="submit">
                    <div
                        class="pointer-events-none absolute -left-[9999px] size-0 opacity-0"
                        aria-hidden="true"
                    >
                        <label>
                            Website URL — please leave empty
                            <input
                                v-model="form.website_url"
                                type="text"
                                autocomplete="off"
                                tabindex="-1"
                            />
                        </label>
                    </div>

                    <!-- 1. Rating -->
                    <section class="rounded-lg border border-[var(--linen)] bg-white p-6 shadow-sm">
                        <h2 class="mb-3 text-base font-bold text-[var(--midnight)]">
                            {{ t.section_rating }}
                        </h2>
                        <div class="flex items-center gap-1">
                            <button
                                v-for="n in 5"
                                :key="n"
                                type="button"
                                class="rounded-full p-0.5 transition-transform hover:scale-110"
                                :aria-label="`${n} / 5`"
                                @mouseenter="hoveredStar = n"
                                @mouseleave="hoveredStar = 0"
                                @click="setRating(n)"
                            >
                                <Star
                                    class="size-8"
                                    :class="(hoveredStar || form.rating) >= n
                                        ? 'fill-amber-400 text-amber-400'
                                        : 'text-[var(--linen)]'"
                                />
                            </button>
                        </div>
                        <p v-if="form.errors.rating" class="mt-2 text-xs text-red-600">
                            {{ form.errors.rating }}
                        </p>
                    </section>

                    <!-- 2. Body -->
                    <section class="rounded-lg border border-[var(--linen)] bg-white p-6 shadow-sm">
                        <h2 class="mb-3 text-base font-bold text-[var(--midnight)]">
                            {{ t.section_body }}
                        </h2>
                        <textarea
                            v-model="form.body"
                            :placeholder="t.body_placeholder"
                            :maxlength="BODY_MAX"
                            rows="6"
                            class="w-full rounded-md border border-[var(--linen)] px-3 py-2.5 text-sm text-[var(--midnight)] focus:border-[var(--orange)] focus:outline-none"
                            :class="{ 'border-red-400': !!form.errors.body }"
                        />
                        <div class="mt-1 flex items-center justify-between">
                            <p v-if="form.errors.body" class="text-xs text-red-600">
                                {{ form.errors.body }}
                            </p>
                            <p v-else class="text-xs text-[var(--slate-light)]"></p>
                            <p class="text-xs text-[var(--slate-light)]">
                                {{ t.chars_remaining(bodyRemaining) }}
                            </p>
                        </div>
                    </section>

                    <!-- 3. Advantages / Disadvantages -->
                    <section class="rounded-lg border border-[var(--linen)] bg-white p-6 shadow-sm">
                        <h2 class="mb-3 text-base font-bold text-[var(--midnight)]">
                            {{ t.section_pros_cons }}
                        </h2>
                        <div class="grid gap-4 md:grid-cols-2">
                            <!-- Advantages column -->
                            <div>
                                <div class="mb-2 flex items-center gap-2">
                                    <span class="flex size-8 items-center justify-center rounded-md bg-emerald-500 text-white">
                                        <ThumbsUp class="size-4" />
                                    </span>
                                    <span class="text-sm font-medium text-emerald-700">
                                        {{ form.advantages.length }} / 3
                                    </span>
                                </div>
                                <div class="flex flex-wrap gap-1.5">
                                    <button
                                        v-for="tag in tags"
                                        :key="tag"
                                        type="button"
                                        class="rounded-full border px-2.5 py-1 text-xs font-medium transition-colors"
                                        :class="form.advantages.includes(tag)
                                            ? 'border-emerald-500 bg-emerald-50 text-emerald-700'
                                            : 'border-[var(--linen)] bg-white text-[var(--slate)] hover:border-emerald-300'"
                                        :disabled="!form.advantages.includes(tag) && form.advantages.length >= 3"
                                        @click="toggleTag('advantages', tag)"
                                    >
                                        {{ t.tag_labels[tag] ?? tag }}
                                    </button>
                                </div>
                            </div>

                            <!-- Disadvantages column -->
                            <div>
                                <div class="mb-2 flex items-center gap-2">
                                    <span class="flex size-8 items-center justify-center rounded-md bg-red-500 text-white">
                                        <ThumbsDown class="size-4" />
                                    </span>
                                    <span class="text-sm font-medium text-red-700">
                                        {{ form.disadvantages.length }} / 3
                                    </span>
                                </div>
                                <div class="flex flex-wrap gap-1.5">
                                    <button
                                        v-for="tag in tags"
                                        :key="tag"
                                        type="button"
                                        class="rounded-full border px-2.5 py-1 text-xs font-medium transition-colors"
                                        :class="form.disadvantages.includes(tag)
                                            ? 'border-red-500 bg-red-50 text-red-700'
                                            : 'border-[var(--linen)] bg-white text-[var(--slate)] hover:border-red-300'"
                                        :disabled="!form.disadvantages.includes(tag) && form.disadvantages.length >= 3"
                                        @click="toggleTag('disadvantages', tag)"
                                    >
                                        {{ t.tag_labels_negative[tag] ?? tag }}
                                    </button>
                                </div>
                            </div>
                        </div>
                    </section>

                    <!-- 4. Source -->
                    <section class="rounded-lg border border-[var(--linen)] bg-white p-6 shadow-sm">
                        <h2 class="mb-3 text-base font-bold text-[var(--midnight)]">
                            {{ t.section_source }}
                        </h2>
                        <select
                            v-model="form.source"
                            class="w-full rounded-md border border-[var(--linen)] bg-white px-3 py-2 text-sm text-[var(--midnight)] focus:border-[var(--orange)] focus:outline-none"
                        >
                            <option value="">{{ t.source_placeholder }}</option>
                            <option v-for="s in sources" :key="s" :value="s">
                                {{ t.source_labels[s] ?? s }}
                            </option>
                        </select>
                    </section>

                    <!-- 5. Your data -->
                    <section class="rounded-lg border border-[var(--linen)] bg-white p-6 shadow-sm">
                        <h2 class="mb-2 text-base font-bold text-[var(--midnight)]">
                            {{ t.section_you }}
                        </h2>
                        <p class="mb-4 text-xs text-[var(--orange)]">
                            {{ t.you_note }}
                        </p>
                        <div class="grid gap-3 sm:grid-cols-2">
                            <div>
                                <input
                                    v-model="form.author_name"
                                    type="text"
                                    :placeholder="t.name_placeholder"
                                    autocomplete="name"
                                    class="w-full rounded-md border border-[var(--linen)] bg-white px-3 py-2 text-sm focus:border-[var(--orange)] focus:outline-none"
                                    :class="{ 'border-red-400': !!form.errors.author_name }"
                                />
                                <p v-if="form.errors.author_name" class="mt-1 text-xs text-red-600">
                                    {{ form.errors.author_name }}
                                </p>
                            </div>
                            <div>
                                <input
                                    v-model="form.author_email"
                                    type="email"
                                    :placeholder="t.email_placeholder"
                                    autocomplete="email"
                                    class="w-full rounded-md border border-[var(--linen)] bg-white px-3 py-2 text-sm focus:border-[var(--orange)] focus:outline-none"
                                    :class="{ 'border-red-400': !!form.errors.author_email }"
                                />
                                <p v-if="form.errors.author_email" class="mt-1 text-xs text-red-600">
                                    {{ form.errors.author_email }}
                                </p>
                            </div>
                        </div>
                        <label class="mt-3 flex cursor-pointer items-start gap-2 text-xs text-[var(--slate)]">
                            <input
                                v-model="form.is_anonymous"
                                type="checkbox"
                                class="mt-0.5 size-4 accent-[var(--orange)]"
                            />
                            {{ t.anonymous_label }}
                        </label>
                    </section>

                    <!-- 6. Proof (optional) -->
                    <section class="rounded-lg border border-[var(--linen)] bg-white p-6 shadow-sm">
                        <h2 class="mb-2 text-base font-bold text-[var(--midnight)]">
                            {{ t.section_proof }}
                        </h2>
                        <p class="mb-3 text-xs text-[var(--slate)]">{{ t.proof_note }}</p>
                        <label class="inline-flex cursor-pointer items-center gap-2 rounded-md border border-[var(--orange)] px-4 py-2 text-sm font-medium text-[var(--orange)] hover:bg-[var(--orange-soft)]">
                            <Upload class="size-4" />
                            {{ t.proof_button }}
                            <input
                                type="file"
                                accept=".pdf,.jpg,.jpeg,.png,.webp"
                                class="sr-only"
                                @change="onFileChange"
                            />
                        </label>
                        <p v-if="form.proof_document" class="mt-2 text-xs text-[var(--slate)]">
                            📎 {{ form.proof_document.name }}
                        </p>
                        <p v-if="form.errors.proof_document" class="mt-1 text-xs text-red-600">
                            {{ form.errors.proof_document }}
                        </p>
                    </section>

                    <!-- Submit -->
                    <div>
                        <button
                            type="submit"
                            :disabled="form.processing"
                            class="w-full rounded-md bg-[var(--orange)] px-6 py-3 text-sm font-semibold text-white transition-colors hover:bg-[color-mix(in_srgb,var(--orange)_85%,black)] disabled:opacity-60 sm:w-auto sm:min-w-64"
                        >
                            {{ t.submit }}
                        </button>
                        <p class="mt-3 text-xs text-[var(--orange)]">
                            {{ t.terms }}
                        </p>
                    </div>
                </form>

                <!-- Sticky company card -->
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
                            <p class="mb-3 text-sm font-semibold text-[var(--midnight)]">
                                {{ company.name }}
                            </p>
                            <div v-if="company.street || company.city_name" class="mb-2 flex items-start gap-2 text-xs text-[var(--slate)]">
                                <MapPin class="mt-0.5 size-3.5 shrink-0" />
                                <span>
                                    <span v-if="company.street">{{ company.street }}<br /></span>
                                    <span v-if="company.postal_code">{{ company.postal_code }} </span>
                                    <span v-if="company.city_name">{{ company.city_name }}</span>
                                </span>
                            </div>
                            <a
                                v-if="company.phone"
                                :href="`tel:${company.phone.replace(/\s+/g, '')}`"
                                class="mb-2 flex items-center gap-2 text-xs text-[var(--slate)] hover:text-[var(--orange)]"
                            >
                                <Phone class="size-3.5" />
                                {{ company.phone }}
                            </a>
                            <a
                                v-if="company.website"
                                :href="company.website.startsWith('http') ? company.website : `https://${company.website}`"
                                target="_blank"
                                rel="noopener"
                                class="mb-2 flex items-center gap-2 text-xs text-[var(--slate)] hover:text-[var(--orange)]"
                            >
                                <Globe class="size-3.5" />
                                <span class="truncate">{{ company.website }}</span>
                            </a>
                            <a
                                v-if="company.email"
                                :href="`mailto:${company.email}`"
                                class="flex items-center gap-2 text-xs text-[var(--slate)] hover:text-[var(--orange)]"
                            >
                                <Mail class="size-3.5" />
                                <span class="truncate">{{ company.email }}</span>
                            </a>
                        </div>
                    </div>
                </aside>
            </div>
        </div>
    </div>
</template>
