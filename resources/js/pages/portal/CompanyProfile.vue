<script setup lang="ts">
/**
 *
 * Lives OUTSIDE the admin dashboard — no admin sidebar, no public
 * site header.
 *
 * Edit pencils currently jump to the full admin edit form; per-section
 * modals are a follow-up phase.
 */
import { Head, Link, router } from '@inertiajs/vue3';
import {
    AlignLeft,
    ArrowLeft,
    BadgeCheck,
    Building2,
    Calendar,
    ExternalLink,
    FileText,
    Globe,
    HelpCircle,
    IdCard,
    Image as ImageIcon,
    ImagePlus,
    Info,
    Layers,
    MapPin,
    Map as MapIcon,
    Pencil,
    Phone,
    Plus,
    Star,
    Users,
} from 'lucide-vue-next';
import { ref } from 'vue';
import EditAboutModal from '@/components/portal/EditAboutModal.vue';
import EditAddressModal from '@/components/portal/EditAddressModal.vue';
import EditAreasModal from '@/components/portal/EditAreasModal.vue';
import EditBrandingModal from '@/components/portal/EditBrandingModal.vue';
import EditContactsModal from '@/components/portal/EditContactsModal.vue';
import EditFaqsModal from '@/components/portal/EditFaqsModal.vue';
import EditGalleryModal from '@/components/portal/EditGalleryModal.vue';
import EditGoogleModal from '@/components/portal/EditGoogleModal.vue';
import EditNameModal from '@/components/portal/EditNameModal.vue';
import EditServicesModal from '@/components/portal/EditServicesModal.vue';
import EditShortDescriptionModal from '@/components/portal/EditShortDescriptionModal.vue';
import EditTrustModal from '@/components/portal/EditTrustModal.vue';
import SimpleFieldModal from '@/components/portal/SimpleFieldModal.vue';

type Translation = {
    lang: string;
    name: string;
    permalink: string;
    about: string;
    short_description: string;
};

type Contact = {
    type: 'phone' | 'email' | 'whatsapp';
    value: string;
    label: string;
};

type CompanyHeader = {
    id: number;
    name: string;
    permalink: string | null;
    logo: string | null;
    cover: string | null;
    city_name: string | null;
    verified: boolean;
    is_top_rated: boolean;
    translations: Translation[];
    founded_year: number | null;
    employee_count: number | null;
    website: string;
    street: string;
    postal_code: string;
    contacts: Contact[];
    google_rating: number | string | null;
    google_review_count: number;
};

type Row = {
    id: string;
    label: string;
    icon: 'IdCard' | 'Phone' | 'MapPin' | 'Layers' | 'Map' | 'Calendar'
        | 'Users' | 'Globe' | 'FileText' | 'HelpCircle' | 'Image'
        | 'ImagePlus' | 'BadgeCheck' | 'AlignLeft' | 'Star';
    preview: string | null;
    is_missing: boolean;
    edit_anchor: string;
};

type ServiceOption = { id: number; name: string; parent_name: string | null };
type DistrictCityGroup = {
    city_id: number;
    city_name: string;
    districts: { id: number; name: string }[];
};
type FaqTranslation = { lang: string; question: string; answer: string };
type Faq = { id?: number; sort_order: number; translations: FaqTranslation[] };
type GalleryItem = { id: number; name: string; url: string | null };

const props = defineProps<{
    company: CompanyHeader;
    rows: Row[];
    editUrl: string;
    locale: string;
    availableServices?: ServiceOption[];
    districtsByCity?: DistrictCityGroup[];
    selectedServiceIds?: number[];
    selectedDistrictIds?: number[];
    faqs?: Faq[];
    gallery?: GalleryItem[];
}>();

const ICONS = {
    IdCard,
    Phone,
    MapPin,
    Layers,
    Map: MapIcon,
    Calendar,
    Users,
    Globe,
    FileText,
    HelpCircle,
    Image: ImageIcon,
    ImagePlus,
    BadgeCheck,
    AlignLeft,
    Star,
} as const;

/**
 * Inline translation dictionary — kept local because the portal is
 * chrome-less and doesn't wire the admin `t()` helper. Two locales
 * only (matches the rest of the app).
 */
const de: Record<string, string> = {
    no_cover_yet: 'Noch kein Titelbild',
    add_photos_videos: 'Fotos / Videos hinzufügen',
    empty: 'Nicht angegeben',
    missing_info: 'Fehlende Info',
    edit_row: 'Diesen Abschnitt bearbeiten',
    edit_coming_soon: 'Bearbeitung folgt in Kürze',
    footer_note: 'Ein Klick auf den Stift öffnet ein Bearbeitungsfenster direkt auf dieser Seite. Weitere Abschnitte folgen.',
    back: 'Zurück',
    founded_title: 'Gründungsjahr bearbeiten',
    founded_label: 'Gründungsjahr',
    founded_placeholder: 'z. B. 2011',
    employees_title: 'Mitarbeiterzahl bearbeiten',
    employees_label: 'Anzahl Mitarbeiter',
    employees_placeholder: 'z. B. 32',
    website_title: 'Website bearbeiten',
    website_desc: 'Vollständige URL inklusive https://. Leer lassen, um die Website zu entfernen.',
    website_label: 'Website-URL',
    website_placeholder: 'https://ihre-firma.de',
};
const en: Record<string, string> = {
    no_cover_yet: 'No cover image yet',
    add_photos_videos: 'Add Photos / Videos',
    empty: 'Not set',
    missing_info: 'Missing Info',
    edit_row: 'Edit this section',
    edit_coming_soon: 'Editing coming soon',
    footer_note: 'Clicking the pencil opens an inline edit dialog on this page. More sections coming soon.',
    back: 'Back',
    founded_title: 'Edit year of establishment',
    founded_label: 'Year founded',
    founded_placeholder: 'e.g. 2011',
    employees_title: 'Edit employee count',
    employees_label: 'Number of employees',
    employees_placeholder: 'e.g. 32',
    website_title: 'Edit business website',
    website_desc: 'Full URL including https://. Leave blank to remove the website.',
    website_label: 'Website URL',
    website_placeholder: 'https://your-company.com',
};
const t = (k: string): string => (props.locale === 'de' ? de : en)[k] ?? k;

function assetUrl(path: string | null): string | null {
    if (!path) return null;

    return path.startsWith('http') ? path : `/storage/${path}`;
}

/* ------------------------------------------------ edit modals */

type EditableSection =
    | 'name'
    | 'website'
    | 'founded'
    | 'employees'
    | 'trust'
    | 'about'
    | 'address'
    | 'contacts'
    | 'services'
    | 'areas'
    | 'faqs'
    | 'branding'
    | 'gallery'
    | 'short_description'
    | 'google';
const openSection = ref<EditableSection | null>(null);
const INLINE_EDITABLE: EditableSection[] = [
    'name', 'website', 'founded', 'employees',
    'trust', 'about', 'address', 'contacts',
    'services', 'areas', 'faqs', 'branding', 'gallery',
    'short_description', 'google',
];

const lazyLoaded = ref<Set<string>>(new Set());

function ensureLazyProps(keys: string[]): void {
    const needed = keys.filter((k) => !lazyLoaded.value.has(k));
    if (needed.length === 0) return;

    router.reload({
        only: needed,
        onSuccess: () => {
            needed.forEach((k) => lazyLoaded.value.add(k));
        },
    });
}

const availableLanguages = props.company.translations.map((t) => t.lang);

function isInlineEditable(rowId: string): rowId is EditableSection {
    return (INLINE_EDITABLE as string[]).includes(rowId);
}
</script>

<template>
    <Head :title="company.name" />

    <div class="min-h-screen bg-[var(--paper)]">
        <header class="sticky top-0 z-10 border-b border-[var(--linen)] bg-white/95 backdrop-blur">
            <div class="mx-auto flex max-w-3xl items-center justify-between gap-3 px-4 py-3">
                <div class="flex items-center gap-2 min-w-0">
                    <button
                        type="button"
                        class="rounded-md p-1.5 text-[var(--slate)] hover:bg-[var(--paper)] hover:text-[var(--midnight)]"
                        :title="t('back')"
                        @click="() => history.length > 1 ? history.back() : null"
                    >
                        <ArrowLeft class="size-5" />
                    </button>
                    <div class="min-w-0">
                        <p class="truncate text-sm font-semibold text-[var(--midnight)]">
                            {{ company.name }}
                        </p>
                        <p v-if="company.city_name" class="truncate text-xs text-[var(--slate)]">
                            {{ company.city_name }}
                        </p>
                    </div>
                </div>
            </div>
        </header>

        <div class="mx-auto max-w-3xl px-4 py-6">
            <!-- Cover + logo band -->
            <div class="relative aspect-[6/1] w-full overflow-hidden rounded-lg border border-[var(--linen)] bg-[var(--linen)]">
                <img
                    v-if="company.cover"
                    :src="assetUrl(company.cover)!"
                    :alt="`${company.name} — Cover`"
                    class="size-full object-cover"
                />
                <div v-else class="flex size-full items-center justify-center text-sm text-[var(--slate)]">
                    <Building2 class="mr-2 size-5" />
                    {{ t('no_cover_yet') }}
                </div>

                <div class="absolute inset-0 bg-gradient-to-t from-black/30 to-transparent" />

                <div class="absolute inset-x-0 bottom-0 flex items-end justify-between gap-3 p-3">
                    <img
                        v-if="company.logo"
                        :src="assetUrl(company.logo)!"
                        :alt="company.name"
                        class="size-14 rounded-md border-2 border-white bg-white object-contain shadow"
                    />
                    <div v-else class="flex size-14 items-center justify-center rounded-md border-2 border-white bg-white text-[var(--slate)] shadow">
                        <Building2 class="size-6" />
                    </div>
                    <span
                        class="inline-flex cursor-not-allowed items-center gap-1.5 rounded-full bg-black/40 px-3 py-1.5 text-xs font-medium text-white backdrop-blur"
                        :title="t('edit_coming_soon')"
                    >
                        <Plus class="size-3.5" />
                        {{ t('add_photos_videos') }}
                    </span>
                </div>
            </div>

            <!-- Row list. Each row is one field group. -->
            <div class="mt-4 flex flex-col divide-y divide-[var(--linen)] overflow-hidden rounded-lg border border-[var(--linen)] bg-white">
                <div
                    v-for="row in rows"
                    :key="row.id"
                    class="flex items-start gap-3 p-4 transition-colors hover:bg-[var(--paper)] md:items-center"
                >
                    <!-- Icon -->
                    <div class="mt-0.5 flex size-9 shrink-0 items-center justify-center rounded-md bg-[var(--orange-soft)]/60 text-[var(--orange)] md:mt-0">
                        <component :is="ICONS[row.icon]" class="size-4" />
                    </div>

                    <!-- Label + preview -->
                    <div class="min-w-0 flex-1">
                        <p class="text-sm font-semibold text-[var(--midnight)]">
                            {{ row.label }}
                        </p>
                        <p
                            v-if="row.preview"
                            class="mt-0.5 truncate text-xs text-[var(--slate)]"
                            :title="row.preview"
                        >
                            {{ row.preview }}
                        </p>
                        <p
                            v-else
                            class="mt-0.5 text-xs italic text-[var(--slate)]/70"
                        >
                            {{ t('empty') }}
                        </p>
                    </div>

                    <!-- Missing-info pill -->
                    <div v-if="row.is_missing" class="shrink-0">
                        <span class="inline-flex items-center gap-1 rounded-full border border-red-200 bg-red-50 px-2 py-0.5 text-[10px] font-semibold text-red-700">
                            <Info class="size-3" />
                            {{ t('missing_info') }}
                        </span>
                    </div>
                    <button
                        v-if="isInlineEditable(row.id)"
                        type="button"
                        class="ml-2 flex size-8 shrink-0 items-center justify-center rounded-full border border-[var(--linen)] text-[var(--slate)] transition-colors hover:border-[var(--orange)] hover:text-[var(--orange)]"
                        :title="t('edit_row')"
                        @click="openSection = row.id as EditableSection"
                    >
                        <Pencil class="size-3.5" />
                    </button>
                    <span
                        v-else
                        class="ml-2 flex size-8 shrink-0 cursor-not-allowed items-center justify-center rounded-full border border-[var(--linen)] text-[var(--slate)]/40"
                        :title="t('edit_coming_soon')"
                    >
                        <Pencil class="size-3.5" />
                    </span>
                </div>
            </div>

            <!-- Footer note -->
            <p class="mt-4 flex items-center gap-1.5 text-xs text-[var(--slate)]">
                <ExternalLink class="size-3.5" />
                {{ t('footer_note') }}
            </p>
        </div>

        <EditNameModal
            v-if="openSection === 'name'"
            :open="openSection === 'name'"
            :company-id="company.id"
            :translations="company.translations"
            :locale="locale"
            @update:open="(v: boolean) => (openSection = v ? 'name' : null)"
        />

        <SimpleFieldModal
            v-if="openSection === 'founded'"
            :open="openSection === 'founded'"
            :endpoint="`/company-portal/${company.id}/founded`"
            field-name="founded_year"
            :value="company.founded_year"
            type="number"
            :title="t('founded_title')"
            :label="t('founded_label')"
            :placeholder="t('founded_placeholder')"
            :min="1800"
            :max="new Date().getFullYear()"
            :locale="locale"
            @update:open="(v: boolean) => (openSection = v ? 'founded' : null)"
        />

        <SimpleFieldModal
            v-if="openSection === 'employees'"
            :open="openSection === 'employees'"
            :endpoint="`/company-portal/${company.id}/employees`"
            field-name="employee_count"
            :value="company.employee_count"
            type="number"
            :title="t('employees_title')"
            :label="t('employees_label')"
            :placeholder="t('employees_placeholder')"
            :min="0"
            :max="100000"
            :locale="locale"
            @update:open="(v: boolean) => (openSection = v ? 'employees' : null)"
        />

        <SimpleFieldModal
            v-if="openSection === 'website'"
            :open="openSection === 'website'"
            :endpoint="`/company-portal/${company.id}/website`"
            field-name="website"
            :value="company.website"
            type="url"
            :title="t('website_title')"
            :description="t('website_desc')"
            :label="t('website_label')"
            :placeholder="t('website_placeholder')"
            :locale="locale"
            @update:open="(v: boolean) => (openSection = v ? 'website' : null)"
        />

        <EditTrustModal
            v-if="openSection === 'trust'"
            :open="openSection === 'trust'"
            :company-id="company.id"
            :verified="company.verified"
            :is-top-rated="company.is_top_rated"
            :locale="locale"
            @update:open="(v: boolean) => (openSection = v ? 'trust' : null)"
        />

        <EditAboutModal
            v-if="openSection === 'about'"
            :open="openSection === 'about'"
            :company-id="company.id"
            :translations="company.translations"
            :locale="locale"
            @update:open="(v: boolean) => (openSection = v ? 'about' : null)"
        />

        <EditAddressModal
            v-if="openSection === 'address'"
            :open="openSection === 'address'"
            :company-id="company.id"
            :street="company.street"
            :postal-code="company.postal_code"
            :city-name="company.city_name"
            :locale="locale"
            @update:open="(v: boolean) => (openSection = v ? 'address' : null)"
        />

        <EditContactsModal
            v-if="openSection === 'contacts'"
            :open="openSection === 'contacts'"
            :company-id="company.id"
            :contacts="company.contacts"
            :locale="locale"
            @update:open="(v: boolean) => (openSection = v ? 'contacts' : null)"
        />

        <EditServicesModal
            v-if="openSection === 'services'"
            :open="openSection === 'services'"
            :company-id="company.id"
            :available-services="availableServices ?? []"
            :selected-service-ids="selectedServiceIds ?? []"
            :locale="locale"
            :lazy-loaded="lazyLoaded.has('availableServices') && lazyLoaded.has('selectedServiceIds')"
            @update:open="(v: boolean) => (openSection = v ? 'services' : null)"
            @lazy-load="ensureLazyProps(['availableServices', 'selectedServiceIds'])"
        />

        <EditAreasModal
            v-if="openSection === 'areas'"
            :open="openSection === 'areas'"
            :company-id="company.id"
            :districts-by-city="districtsByCity ?? []"
            :selected-district-ids="selectedDistrictIds ?? []"
            :locale="locale"
            :lazy-loaded="lazyLoaded.has('districtsByCity') && lazyLoaded.has('selectedDistrictIds')"
            @update:open="(v: boolean) => (openSection = v ? 'areas' : null)"
            @lazy-load="ensureLazyProps(['districtsByCity', 'selectedDistrictIds'])"
        />

        <EditFaqsModal
            v-if="openSection === 'faqs'"
            :open="openSection === 'faqs'"
            :company-id="company.id"
            :faqs="faqs ?? []"
            :languages="availableLanguages"
            :locale="locale"
            :lazy-loaded="lazyLoaded.has('faqs')"
            @update:open="(v: boolean) => (openSection = v ? 'faqs' : null)"
            @lazy-load="ensureLazyProps(['faqs'])"
        />

        <EditBrandingModal
            v-if="openSection === 'branding'"
            :open="openSection === 'branding'"
            :company-id="company.id"
            :logo="company.logo"
            :cover="company.cover"
            :locale="locale"
            @update:open="(v: boolean) => (openSection = v ? 'branding' : null)"
        />

        <EditGalleryModal
            v-if="openSection === 'gallery'"
            :open="openSection === 'gallery'"
            :company-id="company.id"
            :gallery="gallery ?? []"
            :locale="locale"
            :lazy-loaded="lazyLoaded.has('gallery')"
            @update:open="(v: boolean) => (openSection = v ? 'gallery' : null)"
            @lazy-load="ensureLazyProps(['gallery'])"
        />

        <EditShortDescriptionModal
            v-if="openSection === 'short_description'"
            :open="openSection === 'short_description'"
            :company-id="company.id"
            :translations="company.translations"
            :locale="locale"
            @update:open="(v: boolean) => (openSection = v ? 'short_description' : null)"
        />

        <EditGoogleModal
            v-if="openSection === 'google'"
            :open="openSection === 'google'"
            :company-id="company.id"
            :google-rating="company.google_rating"
            :google-review-count="company.google_review_count"
            :locale="locale"
            @update:open="(v: boolean) => (openSection = v ? 'google' : null)"
        />
    </div>
</template>
