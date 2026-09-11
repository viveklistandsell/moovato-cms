<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import {
    ArrowLeft,
    BadgeCheck,
    Building2,
    ChevronDown,
    ChevronUp,
    ExternalLink,
    HelpCircle,
    ImageIcon,
    ImagePlus,
    Info,
    Mail,
    MessageCircle,
    Phone,
    Plus,
    Save,
    Star,
    Trash2,
    X,
} from 'lucide-vue-next';
import { computed, ref, watch } from 'vue';
import { toast } from 'vue-sonner';
import Heading from '@/components/Heading.vue';
import CompanyReviewsSection, {
    type ReviewsPayload,
} from '@/components/admin/CompanyReviewsSection.vue';
import FlagImage from '@/components/common/FlagImage.vue';
import InputError from '@/components/InputError.vue';
import LocaleTabs from '@/components/common/LocaleTabs.vue';
import MediaPicker from '@/components/common/MediaPicker.vue';
import RichTextEditor from '@/components/common/RichTextEditor.vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import {
    Card,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import { Switch } from '@/components/ui/switch';
import { Textarea } from '@/components/ui/textarea';
import { setBreadcrumbs } from '@/composables/common/useBreadcrumbs';
import { useT } from '@/composables/useT';

const t = useT();

type Language = {
    code: string;
    name: string;
    native_name: string;
    flag?: string | null;
    is_default: boolean;
};
type Country = { id: number; name: string; iso_code: string };
type State = { id: number; country_id: number; name: string; code: string };
type City = { id: number; state_id: number; name: string; permalink: string };
type District = { id: number; city_id: number; name: string; permalink: string };
type ParentCat = { id: number; name: string };
type ServiceCat = { id: number; parent_category_id: number; name: string };

type Translation = {
    lang: string;
    name: string;
    permalink: string;
    short_description: string | undefined;
    about: string | null;
};
type Contact = {
    type: 'phone' | 'email' | 'website' | 'whatsapp';
    value: string;
    label: string | null;
    is_primary: boolean;
};
type ServicePivot = {
    service_category_id: number;
    name: string;
    parent_category_id: number;
    is_primary: boolean;
    price_from: number | string | null;
    price_unit: string | null;
};
type ServiceArea = {
    district_id: number;
    district_name: string;
    city_id: number;
    city_name: string | null;
    is_home_base: boolean;
    response_hours: number | null;
};
type MediaEntry = {
    media_file_id: number;
    kind: 'logo' | 'cover' | 'gallery';
    url?: string | null;
    name?: string | null;
};
type FaqEntry = {
    id?: number;
    status: string;
    translations: { lang: string; question: string; answer: string }[];
};

type DayKey = 'mon' | 'tue' | 'wed' | 'thu' | 'fri' | 'sat' | 'sun';
type DayHours = { closed: boolean; open: string | null; close: string | null };
type OpeningHours = Record<DayKey, DayHours>;

const DAY_KEYS: readonly DayKey[] = ['mon', 'tue', 'wed', 'thu', 'fri', 'sat', 'sun'];

function makeDefaultOpeningHours(): OpeningHours {
    return {
        mon: { closed: false, open: '09:00', close: '18:00' },
        tue: { closed: false, open: '09:00', close: '18:00' },
        wed: { closed: false, open: '09:00', close: '18:00' },
        thu: { closed: false, open: '09:00', close: '18:00' },
        fri: { closed: false, open: '09:00', close: '18:00' },
        sat: { closed: false, open: '10:00', close: '14:00' },
        sun: { closed: true, open: null, close: null },
    };
}

function normalizeOpeningHours(incoming: OpeningHours | null | undefined): OpeningHours {
    const base = makeDefaultOpeningHours();
    if (!incoming) return base;
    for (const k of DAY_KEYS) {
        const row = incoming[k];
        if (row && typeof row === 'object') {
            base[k] = {
                closed: Boolean(row.closed),
                open: row.closed ? null : (row.open ?? base[k].open),
                close: row.closed ? null : (row.close ?? base[k].close),
            };
        }
    }
    return base;
}

type CompanyForEdit = {
    id: number;
    primary_city_id: number | null;
    primary_district_id: number | null;
    state_id: number | null;
    country_id: number | null;
    street: string | null;
    postal_code: string | null;
    logo: string | null;
    cover: string | null;
    verified: boolean;
    is_top_rated: boolean;
    plan_tier: string;
    rating_avg: number | string | null;
    review_count: number;
    recommend_pct: number;
    rating_breakdown: Record<string, number> | null;
    google_rating: number | string | null;
    google_review_count: number;
    founded_year: number | null;
    employee_count: number | null;
    opening_hours: OpeningHours | null;
    status: string;
    sort_order: number;
    translations: Translation[];
    contacts: Contact[];
    services: ServicePivot[];
    service_areas: ServiceArea[];
    media: MediaEntry[];
    faqs: FaqEntry[];
};

const props = defineProps<{
    company: CompanyForEdit | null;
    languages: Language[];
    countries: Country[];
    states: State[];
    cities: City[];
    districts: District[];
    parentCategories: ParentCat[];
    serviceCategories: ServiceCat[];
    nextSortOrder: number;
    reviews?: ReviewsPayload;
}>();

const isEditing = computed(() => props.company !== null);

setBreadcrumbs(() => [
    { title: t('sidebar.dashboard'), href: '/dashboard' },
    { title: t('sidebar.companies'), href: '/admin/companies' },
    {
        title: isEditing.value
            ? t('companies.company_edit_title')
            : t('companies.company_create_title'),
        href: '#',
    },
]);

function seedTranslations(): Translation[] {
    return props.languages.map((lang) => {
        const existing = props.company?.translations.find((t) => t.lang === lang.code);
        return (
            existing ?? {
                lang: lang.code,
                name: '',
                permalink: '',
                short_description: '',
                about: '',
            }
        );
    });
}

function seedContacts(): Contact[] {
    return props.company?.contacts ?? [];
}

function seedFaqs(): FaqEntry[] {
    if (!props.company?.faqs.length) return [];
    return props.company.faqs.map((f) => ({
        status: f.status,
        translations: props.languages.map((lang) => {
            const existing = f.translations.find((t) => t.lang === lang.code);
            return existing ?? { lang: lang.code, question: '', answer: '' };
        }),
    }));
}

const form = useForm({
    primary_city_id: props.company?.primary_city_id ?? null,
    primary_district_id: props.company?.primary_district_id ?? null,
    street: props.company?.street ?? '',
    postal_code: props.company?.postal_code ?? '',
    logo: props.company?.logo ?? '',
    cover: props.company?.cover ?? '',
    verified: props.company?.verified ?? false,
    is_top_rated: props.company?.is_top_rated ?? false,
    plan_tier: props.company?.plan_tier ?? 'basic',
    rating_avg: props.company?.rating_avg ?? null,
    review_count: props.company?.review_count ?? 0,
    recommend_pct: props.company?.recommend_pct ?? 0,
    rating_breakdown: {
        5: Number(props.company?.rating_breakdown?.['5'] ?? 0),
        4: Number(props.company?.rating_breakdown?.['4'] ?? 0),
        3: Number(props.company?.rating_breakdown?.['3'] ?? 0),
        2: Number(props.company?.rating_breakdown?.['2'] ?? 0),
        1: Number(props.company?.rating_breakdown?.['1'] ?? 0),
    } as Record<string, number>,
    google_rating: props.company?.google_rating ?? undefined,
    google_review_count: props.company?.google_review_count ?? 0,
    founded_year: props.company?.founded_year ?? null,
    employee_count: props.company?.employee_count ?? null,
    opening_hours: normalizeOpeningHours(props.company?.opening_hours ?? null),
    status: props.company?.status ?? 'published',
    sort_order: props.company?.sort_order ?? props.nextSortOrder,
    translations: seedTranslations(),
    contacts: seedContacts(),
    services: (props.company?.services ?? []) as ServicePivot[],
    service_areas: (props.company?.service_areas ?? []) as ServiceArea[],
    media: (props.company?.media ?? []) as MediaEntry[],
    faqs: seedFaqs(),
});

/* --------------------------------------------------- Locale-tab state */

const currentLang = ref(
    props.languages.find((l) => l.is_default)?.code ?? props.languages[0]?.code ?? 'de',
);

const urlPrefixes = computed<Record<string, string>>(() => {
    const origin = typeof window !== 'undefined' ? window.location.origin : '';
    const map: Record<string, string> = {};
    for (const lang of props.languages) {
        map[lang.code] = lang.is_default
            ? `${origin}/company/`
            : `${origin}/${lang.code}/company/`;
    }

    return map;
});

function slugify(input: string): string {
    return input
        .toLowerCase()
        .normalize('NFD')
        .replace(/[̀-ͯ]/g, '')
        .replace(/[äöüß]/g, (c) => ({ ä: 'ae', ö: 'oe', ü: 'ue', ß: 'ss' })[c] ?? c)
        .replace(/[^a-z0-9]+/g, '-')
        .replace(/^-+|-+$/g, '');
}

const permalinkTouched = ref<Record<string, boolean>>(
    Object.fromEntries(
        props.languages.map((lang) => {
            const existing = props.company?.translations.find((t) => t.lang === lang.code);
            return [lang.code, Boolean(existing?.permalink)];
        }),
    ),
);

function onNameInput(lang: string, val: string): void {
    const idx = form.translations.findIndex((t) => t.lang === lang);
    if (idx === -1) return;
    form.translations[idx].name = val;
    if (!permalinkTouched.value[lang]) {
        form.translations[idx].permalink = slugify(val);
    }
}

function onPermalinkInput(code: string): void {
    const idx = form.translations.findIndex((t) => t.lang === code);
    permalinkTouched.value[code] = idx === -1 ? true : Boolean(form.translations[idx].permalink);
}

/* -------------------------------------------------- Address cascade */

const addressCountryId = ref<number | null>(props.company?.country_id ?? null);
const addressStateId = ref<number | null>(props.company?.state_id ?? null);
const filteredStates = computed(() =>
    addressCountryId.value === null
        ? []
        : props.states.filter((s) => s.country_id === addressCountryId.value),
);
const filteredCities = computed(() =>
    addressStateId.value === null
        ? []
        : props.cities.filter((c) => c.state_id === addressStateId.value),
);
const addressDistrictOptions = computed(() =>
    form.primary_city_id === null
        ? []
        : props.districts.filter((d) => d.city_id === form.primary_city_id),
);

watch(addressCountryId, () => {
    addressStateId.value = null;
    form.primary_city_id = null;
    form.primary_district_id = null;
});
watch(addressStateId, () => {
    form.primary_city_id = null;
    form.primary_district_id = null;
});
watch(
    () => form.primary_city_id,
    () => {
        form.primary_district_id = null;
    },
);

/* -------------------------------------------------- Services chip picker */

const serviceParentPicker = ref<number | null>(null);
const serviceCategoryPicker = ref<number | null>(null);
const servicePricePicker = ref<string>('');
const servicePriceUnitPicker = ref<string>('');
const PRICE_UNIT_OPTIONS = [
    'pro Umzug',
    'pro Stunde',
    'pro Auftrag',
    'pro m³',
    'pro Karton',
    'pro Fahrt',
    'pro Person',
    'pauschal',
] as const;

const filteredServiceCategories = computed(() =>
    serviceParentPicker.value === null
        ? []
        : props.serviceCategories.filter(
              (s) => s.parent_category_id === serviceParentPicker.value,
          ),
);

watch(serviceParentPicker, () => {
    serviceCategoryPicker.value = null;
});

function addService(): void {
    if (serviceCategoryPicker.value === null) return;
    if (form.services.some((s) => s.service_category_id === serviceCategoryPicker.value)) {
        toast.warning(t('companies.service_already_added'));
        return;
    }
    const cat = props.serviceCategories.find((s) => s.id === serviceCategoryPicker.value);
    if (!cat) return;
    form.services.push({
        service_category_id: cat.id,
        parent_category_id: cat.parent_category_id,
        name: cat.name,
        is_primary: form.services.length === 0,
        price_from: servicePricePicker.value === '' ? null : Number(servicePricePicker.value),
        price_unit: servicePriceUnitPicker.value || null,
    });
    serviceParentPicker.value = null;
    serviceCategoryPicker.value = null;
    servicePricePicker.value = '';
    servicePriceUnitPicker.value = '';
}

function removeService(idx: number): void {
    form.services.splice(idx, 1);
}

function togglePrimaryService(idx: number): void {
    const target = form.services[idx];
    if (target) {
        target.is_primary = !target.is_primary;
    }
}

/* -------------------------------------------- Service-areas district picker */

const areaCountryId = ref<number | null>(null);
const areaStateId = ref<number | null>(null);
const areaCityId = ref<number | null>(null);

const areaStates = computed(() =>
    areaCountryId.value === null
        ? []
        : props.states.filter((s) => s.country_id === areaCountryId.value),
);
const areaCities = computed(() =>
    areaStateId.value === null
        ? []
        : props.cities.filter((c) => c.state_id === areaStateId.value),
);
const areaDistricts = computed(() =>
    areaCityId.value === null
        ? []
        : props.districts.filter((d) => d.city_id === areaCityId.value),
);
const areaCityName = computed(
    () => props.cities.find((c) => c.id === areaCityId.value)?.name ?? '',
);

watch(areaCountryId, () => {
    areaStateId.value = null;
    areaCityId.value = null;
});
watch(areaStateId, () => {
    areaCityId.value = null;
});

function hasArea(districtId: number): boolean {
    return form.service_areas.some((a) => a.district_id === districtId);
}
function toggleArea(district: District): void {
    const idx = form.service_areas.findIndex((a) => a.district_id === district.id);
    if (idx >= 0) {
        form.service_areas.splice(idx, 1);
        return;
    }
    form.service_areas.push({
        district_id: district.id,
        district_name: district.name,
        city_id: district.city_id,
        city_name: areaCityName.value,
        is_home_base: false,
        response_hours: null,
    });
}
function selectAllInCity(): void {
    areaDistricts.value.forEach((d) => {
        if (!hasArea(d.id)) toggleArea(d);
    });
}
function deselectAllInCity(): void {
    const cityDistrictIds = new Set(areaDistricts.value.map((d) => d.id));
    form.service_areas = form.service_areas.filter((a) => !cityDistrictIds.has(a.district_id));
}
const allDistrictsInCitySelected = computed<boolean>(() => {
    if (areaDistricts.value.length === 0) return false;
    return areaDistricts.value.every((d) => hasArea(d.id));
});
function removeArea(idx: number): void {
    form.service_areas.splice(idx, 1);
}

const groupedAreas = computed(() => {
    const buckets = new Map<number, { city_id: number; city_name: string; items: (ServiceArea & { originalIndex: number })[] }>();
    form.service_areas.forEach((a, originalIndex) => {
        const b = buckets.get(a.city_id) ?? {
            city_id: a.city_id,
            city_name: a.city_name ?? '—',
            items: [],
        };
        b.items.push({ ...a, originalIndex });
        buckets.set(a.city_id, b);
    });
    return Array.from(buckets.values());
});

/* ---------------------------------------------------- Contacts repeater */

function addContact(type: Contact['type'] = 'phone'): void {
    form.contacts.push({ type, value: '', label: null, is_primary: form.contacts.length === 0 });
}
function removeContact(idx: number): void {
    form.contacts.splice(idx, 1);
}
function contactIcon(type: string) {
    return type === 'email'
        ? Mail
        : type === 'website'
          ? Info
          : type === 'whatsapp'
            ? MessageCircle
            : Phone;
}

/* --------------------------------------------------- Media picker + Gallery */

type PickerTarget = 'logo' | 'cover' | 'gallery';
const pickerOpen = ref(false);
const pickerTarget = ref<PickerTarget>('logo');

function openPicker(target: PickerTarget): void {
    pickerTarget.value = target;
    pickerOpen.value = true;
}
function onMediaPicked(file: { path: string; url: string; name: string }): void {
    if (pickerTarget.value === 'logo') {
        form.logo = file.path;
    } else if (pickerTarget.value === 'cover') {
        form.cover = file.path;
    }
    pickerOpen.value = false;
}

async function appendGalleryFromPickedFile(file: { path: string; url: string; name: string }): Promise<void> {
    try {
        const res = await fetch(
            `/admin/media/lookup?path=${encodeURIComponent(file.path)}`,
        );
        const data = (await res.json()) as { id: number | null };
        if (!data.id) {
            throw new Error('no id');
        }
        if (form.media.some((m) => m.media_file_id === data.id && m.kind === 'gallery')) {
            return;
        }
        form.media.push({
            media_file_id: data.id,
            kind: 'gallery',
            url: file.url,
            name: file.name,
        });
    } catch {
        toast.error(t('companies.gallery_lookup_failed'));
    }
}

function onMediaPickedMany(files: { path: string; url: string; name: string }[]): void {
    Promise.all(files.map((f) => appendGalleryFromPickedFile(f))).then(() => {
        toast.success(t('companies.gallery_added', { count: files.length }));
    });
    pickerOpen.value = false;
}
function removeGallery(idx: number): void {
    form.media.splice(idx, 1);
}
function moveGallery(idx: number, dir: -1 | 1): void {
    const target = idx + dir;
    if (target < 0 || target >= form.media.length) return;
    const [row] = form.media.splice(idx, 1);
    form.media.splice(target, 0, row);
}
const galleryItems = computed(() => form.media.filter((m) => m.kind === 'gallery'));

/* ---------------------------------------------------- FAQ repeater */

function addFaq(): void {
    form.faqs.push({
        status: 'published',
        translations: props.languages.map((l) => ({ lang: l.code, question: '', answer: '' })),
    });
}
function removeFaq(idx: number): void {
    form.faqs.splice(idx, 1);
}
function moveFaq(idx: number, dir: -1 | 1): void {
    const target = idx + dir;
    if (target < 0 || target >= form.faqs.length) return;
    const [row] = form.faqs.splice(idx, 1);
    form.faqs.splice(target, 0, row);
}

/* -------------------------------------------- Validation-error helpers */

function translationIndex(code: string): number {
    return form.translations.findIndex((t) => t.lang === code);
}
function translationError(code: string, field: 'name' | 'permalink'): string | null {
    const key = `translations.${translationIndex(code)}.${field}`;
    const errs = form.errors as Record<string, string | undefined>;

    return errs[key] ?? null;
}
function fieldError(key: string): string | null {
    const errs = form.errors as Record<string, string | undefined>;

    return errs[key] ?? null;
}

/* ---------------------------------------------------- Submit */

function reportValidationErrors(): void {
    const first = Object.values(form.errors).find((v) => typeof v === 'string' && v.length > 0);
    if (first) {
        toast.error(String(first));
    }
    if (typeof window !== 'undefined') {
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }
}

function submit(): void {
    if (isEditing.value && props.company) {
        form.put(`/admin/companies/${props.company.id}`, {
            preserveScroll: true,
            onError: reportValidationErrors,
        });
    } else {
        form.post('/admin/companies', {
            preserveScroll: true,
            onError: reportValidationErrors,
        });
    }
}

/**
 * Save without leaving the editor. Uses `?stay=1` so the controller
 * redirects back to `admin.companies.edit` instead of the index, and
 * `preserveState: true` keeps the local form drafts across the redirect.
 */
function saveAndStay(): void {
    if (!isEditing.value || !props.company) return;
    form.put(`/admin/companies/${props.company.id}?stay=1`, {
        preserveScroll: true,
        preserveState: true,
        onError: reportValidationErrors,
    });
}

/**
 * Full public URL for a given language tab — used by the clickable
 * permalink preview below each URL field. Returns null when the tab has
 * no permalink typed yet so we can render static text instead of a
 * link-to-nowhere.
 */
function livePermalinkUrl(code: string): string | null {
    const idx = form.translations.findIndex((t) => t.lang === code);
    const slug = idx === -1 ? '' : (form.translations[idx].permalink ?? '');
    if (!slug) return null;
    const prefix = urlPrefixes.value[code] ?? '';
    return `${prefix}${slug}`;
}
</script>

<template>
    <Head
        :title="
            isEditing
                ? t('companies.company_edit_title')
                : t('companies.company_create_title')
        "
    />

    <form class="flex flex-col gap-6 p-4" @submit.prevent="submit">
        <div class="flex items-start gap-3">
            <Button variant="ghost" size="icon-sm" as-child>
                <Link href="/admin/companies">
                    <ArrowLeft class="size-4" />
                </Link>
            </Button>
            <Heading
                :title="
                    isEditing
                        ? t('companies.company_edit_title')
                        : t('companies.company_create_title')
                "
                :description="
                    isEditing
                        ? t('companies.company_edit_description')
                        : t('companies.company_create_description')
                "
            />
        </div>
        <div class="grid gap-6 lg:grid-cols-[1fr_360px]">
        <div class="flex min-w-0 flex-col gap-6">
        <!-- 2. Locale-tabbed identity -->
        <Card>
            <CardHeader>
                <CardTitle>{{ t('companies.section_identity') }}</CardTitle>
                <CardDescription>{{ t('companies.section_identity_desc') }}</CardDescription>
            </CardHeader>
            <CardContent>
                <LocaleTabs v-model="currentLang" :languages="languages">
                    <template #default="{ code }">
                        <template v-for="(trans, idx) in form.translations" :key="trans.lang">
                            <div
                                v-if="trans.lang === code"
                                class="space-y-5 pt-4"
                            >
                                <!-- Company name -->
                                <div class="grid gap-2">
                                    <Label :for="`name-${code}`">
                                        {{ t('companies.field_name') }}
                                        <span
                                            v-if="languages.find((l) => l.code === code)?.is_default"
                                            class="text-destructive"
                                        >*</span>
                                    </Label>
                                    <Input
                                        :id="`name-${code}`"
                                        :model-value="form.translations[idx].name"
                                        @update:model-value="(v) => onNameInput(code, String(v))"
                                        :placeholder="t('companies.field_name_placeholder')"
                                        :class="translationError(code, 'name') ? 'border-destructive' : ''"
                                    />
                                    <InputError :message="translationError(code, 'name') ?? undefined" />
                                </div>

                                <!-- Permalink -->
                                <div class="grid gap-2">
                                    <Label :for="`permalink-${code}`">
                                        {{ t('companies.field_permalink') }}
                                        <span
                                            v-if="languages.find((l) => l.code === code)?.is_default"
                                            class="text-destructive"
                                        >*</span>
                                    </Label>
                                    <div class="flex w-full items-stretch">
                                        <span
                                            class="inline-flex shrink-0 items-center rounded-l-md border border-r-0 border-input bg-muted px-3 text-sm text-muted-foreground"
                                        >
                                            {{ urlPrefixes[code] }}
                                        </span>
                                        <input
                                            :id="`permalink-${code}`"
                                            v-model="form.translations[idx].permalink"
                                            :placeholder="t('companies.field_permalink_placeholder')"
                                            class="h-9 w-full min-w-0 rounded-r-md border border-input bg-transparent px-3 py-1 text-base shadow-xs transition-[color,box-shadow] outline-none placeholder:text-muted-foreground focus-visible:border-ring focus-visible:ring-[3px] focus-visible:ring-ring/50 md:text-sm dark:bg-input/30"
                                            :class="translationError(code, 'permalink') ? 'border-destructive' : ''"
                                            @input="onPermalinkInput(code)"
                                        />
                                    </div>
                                    <p class="text-xs text-muted-foreground">
                                        {{ t('companies.preview_label') }}:
                                        <a
                                            v-if="livePermalinkUrl(code)"
                                            :href="livePermalinkUrl(code)!"
                                            target="_blank"
                                            rel="noopener"
                                            class="inline-flex items-center gap-1 font-medium text-[var(--orange)] underline underline-offset-4 hover:text-[color-mix(in_srgb,var(--orange)_80%,black)]"
                                        >
                                            {{ urlPrefixes[code] }}{{ form.translations[idx].permalink }}
                                            <ExternalLink class="size-3" />
                                        </a>
                                        <span v-else class="text-[var(--orange)]">
                                            {{ urlPrefixes[code] }}{{ t('companies.field_permalink_placeholder') }}
                                        </span>
                                    </p>
                                    <InputError :message="translationError(code, 'permalink') ?? undefined" />
                                </div>

                                <div class="grid gap-2">
                                    <Label :for="`short-desc-${code}`">
                                        {{ t('companies.field_short_description') }}
                                    </Label>
                                    <Textarea
                                        :id="`short-desc-${code}`"
                                        v-model="form.translations[idx].short_description"
                                        :rows="2"
                                        :placeholder="t('companies.field_short_description_placeholder')"
                                    />
                                </div>

                                <div class="grid gap-2">
                                    <Label>{{ t('companies.field_about') }}</Label>
                                    <RichTextEditor
                                        :model-value="form.translations[idx].about"
                                        :placeholder="t('companies.field_about_placeholder')"
                                        @update:model-value="(v) => (form.translations[idx].about = v)"
                                    />
                                </div>
                            </div>
                        </template>
                    </template>
                </LocaleTabs>
            </CardContent>
        </Card>

        <!-- 4. Contacts (repeater) -->
        <Card>
            <CardHeader class="flex flex-row items-start justify-between space-y-0">
                <div>
                    <CardTitle>{{ t('companies.section_contacts') }}</CardTitle>
                    <CardDescription>{{ t('companies.section_contacts_desc') }}</CardDescription>
                </div>
                <div class="flex gap-2">
                    <Button type="button" variant="outline" size="sm" @click="addContact('phone')">
                        <Plus class="size-3" /> {{ t('companies.add_phone') }}
                    </Button>
                    <Button type="button" variant="outline" size="sm" @click="addContact('email')">
                        <Plus class="size-3" /> {{ t('companies.add_email') }}
                    </Button>
                    <Button type="button" variant="outline" size="sm" @click="addContact('website')">
                        <Plus class="size-3" /> {{ t('companies.add_website') }}
                    </Button>
                    <Button type="button" variant="outline" size="sm" @click="addContact('whatsapp')">
                        <Plus class="size-3" /> {{ t('companies.add_whatsapp') }}
                    </Button>
                </div>
            </CardHeader>
            <CardContent class="flex flex-col gap-3">
                <div
                    v-if="form.contacts.length === 0"
                    class="rounded-md border border-dashed p-4 text-center text-sm text-muted-foreground"
                >
                    {{ t('companies.contacts_empty') }}
                </div>
                <div
                    v-for="(c, idx) in form.contacts"
                    :key="idx"
                    class="grid grid-cols-12 items-center gap-2 rounded-md border p-3"
                >
                    <Select v-model="c.type" class="col-span-2">
                        <SelectTrigger>
                            <component :is="contactIcon(c.type)" class="size-4" />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem value="phone">
                                <Phone class="mr-2 size-3.5 inline" /> {{ t('companies.contact_phone') }}
                            </SelectItem>
                            <SelectItem value="email">
                                <Mail class="mr-2 size-3.5 inline" /> {{ t('companies.contact_email') }}
                            </SelectItem>
                            <SelectItem value="website">
                                <Info class="mr-2 size-3.5 inline" /> {{ t('companies.contact_website') }}
                            </SelectItem>
                            <SelectItem value="whatsapp">
                                <MessageCircle class="mr-2 size-3.5 inline" /> {{ t('companies.contact_whatsapp') }}
                            </SelectItem>
                        </SelectContent>
                    </Select>
                    <Input v-model="c.value" class="col-span-5" :placeholder="t('companies.contact_value')" />
                    <Input v-model="c.label" class="col-span-3" :placeholder="t('companies.contact_label')" />
                    <div class="col-span-1 flex justify-center">
                        <Checkbox
                            v-model="c.is_primary"
                            :title="t('companies.contact_is_primary')"
                        />
                    </div>
                    <Button
                        type="button"
                        variant="ghost"
                        size="icon-sm"
                        class="col-span-1 text-destructive"
                        @click="removeContact(idx)"
                    >
                        <Trash2 class="size-4" />
                    </Button>
                </div>
            </CardContent>
        </Card>
        <Card>
            <CardHeader>
                <CardTitle>{{ t('companies.section_opening_hours') }}</CardTitle>
                <CardDescription>{{ t('companies.section_opening_hours_desc') }}</CardDescription>
            </CardHeader>
            <CardContent>
                <div class="space-y-2">
                    <div
                        v-for="day in DAY_KEYS"
                        :key="day"
                        class="grid grid-cols-1 items-center gap-3 rounded-md border border-input bg-muted/30 p-3 sm:grid-cols-[110px_1fr_auto]"
                    >
                        <div class="text-sm font-semibold uppercase tracking-wider text-muted-foreground">
                            {{ t(`companies.day_${day}`) }}
                        </div>
                        <div class="flex items-center gap-2">
                            <Input
                                v-model="form.opening_hours[day].open"
                                type="time"
                                :disabled="form.opening_hours[day].closed"
                                class="w-32"
                            />
                            <span class="text-xs text-muted-foreground">—</span>
                            <Input
                                v-model="form.opening_hours[day].close"
                                type="time"
                                :disabled="form.opening_hours[day].closed"
                                class="w-32"
                            />
                        </div>
                        <label class="flex cursor-pointer items-center gap-2 text-xs font-medium">
                            <Switch
                                :model-value="form.opening_hours[day].closed"
                                @update:model-value="(v) => {
                                    form.opening_hours[day].closed = v;
                                    if (v) {
                                        form.opening_hours[day].open = null;
                                        form.opening_hours[day].close = null;
                                    } else {
                                        form.opening_hours[day].open = form.opening_hours[day].open ?? '09:00';
                                        form.opening_hours[day].close = form.opening_hours[day].close ?? '18:00';
                                    }
                                }"
                            />
                            {{ t('companies.opening_hours_closed') }}
                        </label>
                    </div>
                </div>
                <p class="mt-3 text-[11px] text-muted-foreground">
                    {{ t('companies.opening_hours_hint') }}
                </p>
            </CardContent>
        </Card>

        <!-- 5. Services (cascading dropdown → chip list) -->
        <Card>
            <CardHeader>
                <CardTitle>{{ t('companies.section_services') }}</CardTitle>
                <CardDescription>{{ t('companies.section_services_desc') }}</CardDescription>
            </CardHeader>
            <CardContent class="flex flex-col gap-4">
                <div class="grid grid-cols-1 items-end gap-3 md:grid-cols-12">
                    <div class="md:col-span-3">
                        <Label>{{ t('sidebar.service_parent_categories') }}</Label>
                        <Select
                            :model-value="serviceParentPicker?.toString() ?? ''"
                            @update:model-value="(v) => (serviceParentPicker = v ? Number(v) : null)"
                        >
                            <SelectTrigger>
                                <SelectValue :placeholder="t('companies.pick_parent')" />
                            </SelectTrigger>
                            <SelectContent>
                                <SelectItem
                                    v-for="p in parentCategories"
                                    :key="p.id"
                                    :value="p.id.toString()"
                                >
                                    {{ p.name }}
                                </SelectItem>
                            </SelectContent>
                        </Select>
                    </div>
                    <div class="md:col-span-3">
                        <Label>{{ t('sidebar.service_categories') }}</Label>
                        <Select
                            :model-value="serviceCategoryPicker?.toString() ?? ''"
                            :disabled="serviceParentPicker === null"
                            @update:model-value="(v) => (serviceCategoryPicker = v ? Number(v) : null)"
                        >
                            <SelectTrigger>
                                <SelectValue :placeholder="t('companies.pick_service')" />
                            </SelectTrigger>
                            <SelectContent>
                                <SelectItem
                                    v-for="s in filteredServiceCategories"
                                    :key="s.id"
                                    :value="s.id.toString()"
                                >
                                    {{ s.name }}
                                </SelectItem>
                            </SelectContent>
                        </Select>
                    </div>
                    <div class="md:col-span-2">
                        <Label>{{ t('companies.field_price_from') }}</Label>
                        <Input v-model="servicePricePicker" type="number" placeholder="299" />
                    </div>
                    <div class="md:col-span-2">
                        <Label>{{ t('companies.field_price_unit') }}</Label>
                        <Select
                            :model-value="servicePriceUnitPicker"
                            @update:model-value="(v) => (servicePriceUnitPicker = (v as string) ?? '')"
                        >
                            <SelectTrigger class="w-full">
                                <SelectValue :placeholder="t('companies.field_price_unit_placeholder')" />
                            </SelectTrigger>
                            <SelectContent>
                                <SelectItem
                                    v-for="unit in PRICE_UNIT_OPTIONS"
                                    :key="unit"
                                    :value="unit"
                                >
                                    {{ unit }}
                                </SelectItem>
                            </SelectContent>
                        </Select>
                    </div>
                    <div class="md:col-span-2">
                        <Button
                            type="button"
                            :disabled="serviceCategoryPicker === null"
                            class="w-full"
                            @click="addService"
                        >
                            <Plus class="size-4" /> {{ t('companies.add_service') }}
                        </Button>
                    </div>
                </div>

                <div v-if="form.services.length === 0" class="rounded-md border border-dashed p-4 text-center text-sm text-muted-foreground">
                    {{ t('companies.services_empty') }}
                </div>
                <div v-else class="flex flex-col gap-2">
                    <div
                        v-for="(s, idx) in form.services"
                        :key="s.service_category_id"
                        class="flex items-center gap-3 rounded-md border p-3"
                    >
                        <Button
                            type="button"
                            variant="ghost"
                            size="icon-sm"
                            :class="s.is_primary ? 'text-amber-500' : 'text-muted-foreground'"
                            :title="t('companies.service_primary_hint')"
                            @click="togglePrimaryService(idx)"
                        >
                            <Star :class="s.is_primary ? 'size-4 fill-amber-400' : 'size-4'" />
                        </Button>
                        <div class="flex-1">
                            <div class="font-medium">{{ s.name }}</div>
                            <div class="text-xs text-muted-foreground">
                                <span v-if="s.price_from !== null">ab {{ s.price_from }} €</span>
                                <span v-if="s.price_unit"> · {{ s.price_unit }}</span>
                                <span v-if="s.price_from === null && !s.price_unit">
                                    {{ t('companies.no_price') }}
                                </span>
                            </div>
                        </div>
                        <Button
                            type="button"
                            variant="ghost"
                            size="icon-sm"
                            class="text-destructive"
                            @click="removeService(idx)"
                        >
                            <X class="size-4" />
                        </Button>
                    </div>
                </div>
            </CardContent>
        </Card>

        <!-- 6. Service areas (cascading dropdown + district checkboxes) -->
        <Card>
            <CardHeader>
                <CardTitle>{{ t('companies.section_service_areas') }}</CardTitle>
                <CardDescription>{{ t('companies.section_service_areas_desc') }}</CardDescription>
            </CardHeader>
            <CardContent class="flex flex-col gap-4">
                <div class="grid gap-3 md:grid-cols-3">
                    <Select
                        :model-value="areaCountryId?.toString() ?? ''"
                        @update:model-value="(v) => (areaCountryId = v ? Number(v) : null)"
                    >
                        <SelectTrigger>
                            <SelectValue :placeholder="t('locations.field_country')" />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem v-for="c in countries" :key="c.id" :value="c.id.toString()">
                                <FlagImage :code="c.iso_code" size="sm" class="mr-2 inline align-middle" />
                                {{ c.name }}
                            </SelectItem>
                        </SelectContent>
                    </Select>
                    <Select
                        :model-value="areaStateId?.toString() ?? ''"
                        :disabled="areaCountryId === null"
                        @update:model-value="(v) => (areaStateId = v ? Number(v) : null)"
                    >
                        <SelectTrigger>
                            <SelectValue :placeholder="t('locations.field_state')" />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem v-for="s in areaStates" :key="s.id" :value="s.id.toString()">
                                {{ s.code }} · {{ s.name }}
                            </SelectItem>
                        </SelectContent>
                    </Select>
                    <Select
                        :model-value="areaCityId?.toString() ?? ''"
                        :disabled="areaStateId === null"
                        @update:model-value="(v) => (areaCityId = v ? Number(v) : null)"
                    >
                        <SelectTrigger>
                            <SelectValue :placeholder="t('locations.field_city')" />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem v-for="c in areaCities" :key="c.id" :value="c.id.toString()">
                                {{ c.name }}
                            </SelectItem>
                        </SelectContent>
                    </Select>
                </div>

                <div v-if="areaCityId !== null && areaDistricts.length > 0" class="rounded-md border p-3">
                    <div class="mb-2 flex items-center justify-between">
                        <p class="text-sm font-medium">
                            {{ t('companies.districts_of', { city: areaCityName }) }}
                        </p>
                        <Button
                            type="button"
                            variant="outline"
                            size="sm"
                            @click="allDistrictsInCitySelected ? deselectAllInCity() : selectAllInCity()"
                        >
                            {{
                                allDistrictsInCitySelected
                                    ? t('companies.deselect_all_districts', { count: areaDistricts.length })
                                    : t('companies.select_all_districts', { count: areaDistricts.length })
                            }}
                        </Button>
                    </div>
                    <div class="grid grid-cols-2 gap-2 md:grid-cols-4">
                        <label
                            v-for="d in areaDistricts"
                            :key="d.id"
                            class="flex cursor-pointer items-center gap-2 rounded p-1.5 hover:bg-muted"
                        >
                            <Checkbox
                                :model-value="hasArea(d.id)"
                                @update:model-value="() => toggleArea(d)"
                            />
                            <span class="text-sm">{{ d.name }}</span>
                        </label>
                    </div>
                </div>

                <div v-if="form.service_areas.length === 0" class="rounded-md border border-dashed p-4 text-center text-sm text-muted-foreground">
                    {{ t('companies.areas_empty') }}
                </div>
                <div v-else class="flex flex-col gap-3">
                    <div
                        v-for="group in groupedAreas"
                        :key="group.city_id"
                        class="rounded-md border p-3"
                    >
                        <div class="mb-2 flex items-center gap-2">
                            <Building2 class="size-4 text-muted-foreground" />
                            <span class="text-sm font-semibold">{{ group.city_name }}</span>
                            <Badge variant="outline" class="text-xs">
                                {{ group.items.length }}
                            </Badge>
                        </div>
                        <div class="flex flex-wrap gap-1.5">
                            <Badge
                                v-for="item in group.items"
                                :key="item.district_id"
                                variant="secondary"
                                class="gap-1"
                            >
                                {{ item.district_name }}
                                <button
                                    type="button"
                                    class="rounded-full hover:bg-destructive/20"
                                    @click="removeArea(item.originalIndex)"
                                >
                                    <X class="size-3" />
                                </button>
                            </Badge>
                        </div>
                    </div>
                </div>
            </CardContent>
        </Card>

        <!-- 7. Logo + Cover + Gallery -->
        <Card>
            <CardHeader>
                <CardTitle>{{ t('companies.section_media') }}</CardTitle>
                <CardDescription>{{ t('companies.section_media_desc') }}</CardDescription>
            </CardHeader>
            <CardContent class="flex flex-col gap-6">
                <div class="grid gap-4 md:grid-cols-2">
                    <div>
                        <Label>{{ t('companies.field_logo') }}</Label>
                        <div class="mt-1 flex items-center gap-3">
                            <div class="flex size-16 items-center justify-center overflow-hidden rounded-md border bg-muted">
                                <img
                                    v-if="form.logo"
                                    :src="form.logo.startsWith('http') ? form.logo : `/storage/${form.logo}`"
                                    class="size-full object-cover"
                                />
                                <ImageIcon v-else class="size-6 text-muted-foreground" />
                            </div>
                            <div class="flex gap-2">
                                <Button type="button" variant="outline" size="sm" @click="openPicker('logo')">
                                    {{ t('companies.pick_image') }}
                                </Button>
                                <Button
                                    v-if="form.logo"
                                    type="button"
                                    variant="ghost"
                                    size="sm"
                                    class="text-destructive"
                                    @click="form.logo = ''"
                                >
                                    <X class="size-4" />
                                </Button>
                            </div>
                        </div>
                    </div>
                    <div>
                        <Label>{{ t('companies.field_cover') }}</Label>
                        <div class="mt-1 flex items-center gap-3">
                            <div class="flex h-16 w-32 items-center justify-center overflow-hidden rounded-md border bg-muted">
                                <img
                                    v-if="form.cover"
                                    :src="form.cover.startsWith('http') ? form.cover : `/storage/${form.cover}`"
                                    class="size-full object-cover"
                                />
                                <ImageIcon v-else class="size-6 text-muted-foreground" />
                            </div>
                            <div class="flex gap-2">
                                <Button type="button" variant="outline" size="sm" @click="openPicker('cover')">
                                    {{ t('companies.pick_image') }}
                                </Button>
                                <Button
                                    v-if="form.cover"
                                    type="button"
                                    variant="ghost"
                                    size="sm"
                                    class="text-destructive"
                                    @click="form.cover = ''"
                                >
                                    <X class="size-4" />
                                </Button>
                            </div>
                        </div>
                    </div>
                </div>

                <div>
                    <div class="mb-2 flex items-center justify-between">
                        <Label>{{ t('companies.field_gallery') }}</Label>
                        <Button type="button" variant="outline" size="sm" @click="openPicker('gallery')">
                            <ImagePlus class="size-4" /> {{ t('companies.gallery_add') }}
                        </Button>
                    </div>
                    <div
                        v-if="galleryItems.length === 0"
                        class="rounded-md border border-dashed p-6 text-center text-sm text-muted-foreground"
                    >
                        {{ t('companies.gallery_empty') }}
                    </div>
                    <div v-else class="grid grid-cols-3 gap-3 md:grid-cols-6">
                        <div
                            v-for="(m, idx) in form.media.filter((x) => x.kind === 'gallery')"
                            :key="`${m.media_file_id}-${idx}`"
                            class="group relative aspect-square overflow-hidden rounded-md border"
                        >
                            <img
                                v-if="m.url"
                                :src="m.url"
                                :alt="m.name ?? ''"
                                class="size-full object-cover"
                            />
                            <div
                                class="absolute inset-0 flex items-center justify-center gap-1 bg-black/60 opacity-0 transition-opacity group-hover:opacity-100"
                            >
                                <Button
                                    type="button"
                                    variant="ghost"
                                    size="icon-sm"
                                    class="text-white hover:bg-white/20 hover:text-white"
                                    :disabled="idx === 0"
                                    @click="moveGallery(form.media.indexOf(m), -1)"
                                >
                                    <ChevronUp class="size-4" />
                                </Button>
                                <Button
                                    type="button"
                                    variant="ghost"
                                    size="icon-sm"
                                    class="text-white hover:bg-white/20 hover:text-white"
                                    :disabled="idx === galleryItems.length - 1"
                                    @click="moveGallery(form.media.indexOf(m), 1)"
                                >
                                    <ChevronDown class="size-4" />
                                </Button>
                                <Button
                                    type="button"
                                    variant="ghost"
                                    size="icon-sm"
                                    class="text-white hover:bg-white/20 hover:text-white"
                                    @click="removeGallery(form.media.indexOf(m))"
                                >
                                    <Trash2 class="size-4" />
                                </Button>
                            </div>
                        </div>
                    </div>
                </div>
            </CardContent>
        </Card>

        <!-- 8. FAQ — single card with inline rows, each row has its own locale-tabs strip so every FAQ can be translated independently. -->
        <Card>
            <CardHeader class="flex flex-row items-start justify-between space-y-0">
                <div>
                    <CardTitle>{{ t('companies.section_faq') }}</CardTitle>
                    <CardDescription>{{ t('companies.section_faq_desc') }}</CardDescription>
                </div>
                <Button type="button" variant="outline" size="sm" @click="addFaq">
                    <Plus class="size-4" /> {{ t('companies.faq_add') }}
                </Button>
            </CardHeader>
            <CardContent class="flex flex-col gap-4">
                <div v-if="form.faqs.length === 0" class="rounded-md border border-dashed p-4 text-center text-sm text-muted-foreground">
                    {{ t('companies.faq_empty') }}
                </div>

                <!-- Single locale-tabs strip drives all FAQ rows at once. -->
                <LocaleTabs
                    v-if="form.faqs.length > 0"
                    v-model="currentLang"
                    :languages="languages"
                >
                    <template #default="{ code }">
                        <div class="flex flex-col divide-y divide-border pt-4">
                            <div
                                v-for="(faq, fIdx) in form.faqs"
                                :key="fIdx"
                                class="grid gap-3 py-4 first:pt-0 last:pb-0"
                            >
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-2 text-sm font-medium text-muted-foreground">
                                        <HelpCircle class="size-4" />
                                        {{ t('companies.faq_item_number', { n: fIdx + 1 }) }}
                                    </div>
                                    <div class="flex items-center gap-1">
                                        <Button
                                            type="button"
                                            variant="ghost"
                                            size="icon-sm"
                                            :disabled="fIdx === 0"
                                            @click="moveFaq(fIdx, -1)"
                                        >
                                            <ChevronUp class="size-4" />
                                        </Button>
                                        <Button
                                            type="button"
                                            variant="ghost"
                                            size="icon-sm"
                                            :disabled="fIdx === form.faqs.length - 1"
                                            @click="moveFaq(fIdx, 1)"
                                        >
                                            <ChevronDown class="size-4" />
                                        </Button>
                                        <Button
                                            type="button"
                                            variant="ghost"
                                            size="icon-sm"
                                            class="text-destructive"
                                            @click="removeFaq(fIdx)"
                                        >
                                            <Trash2 class="size-4" />
                                        </Button>
                                    </div>
                                </div>
                                <template v-for="(ft, tIdx) in faq.translations" :key="ft.lang">
                                    <div v-if="ft.lang === code" class="grid gap-3">
                                        <div class="grid gap-2">
                                            <Label>{{ t('companies.faq_question') }} *</Label>
                                            <Input
                                                v-model="form.faqs[fIdx].translations[tIdx].question"
                                                :placeholder="t('companies.faq_question_placeholder')"
                                            />
                                        </div>
                                        <div class="grid gap-2">
                                            <Label>{{ t('companies.faq_answer') }} *</Label>
                                            <Textarea
                                                v-model="form.faqs[fIdx].translations[tIdx].answer"
                                                :rows="3"
                                                :placeholder="t('companies.faq_answer_placeholder')"
                                            />
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </template>
                </LocaleTabs>
            </CardContent>
        </Card>
        <!-- 9. Reviews — inline moderation for this company's reviews. -->
        <CompanyReviewsSection
            v-if="isEditing && props.reviews && props.company"
            :company-id="props.company.id"
            :reviews="props.reviews"
            :reload-url="`/admin/companies/${props.company.id}/edit`"
        />

        </div>
        <aside class="flex flex-col gap-6 lg:sticky lg:top-4 lg:self-start">
            <Card>
                <CardHeader>
                    <CardTitle>{{ t('companies.publish_title') }}</CardTitle>
                </CardHeader>
                <CardContent class="flex flex-col gap-2">
                    <Button type="submit" :disabled="form.processing" class="w-full">
                        <Save class="size-4" />
                        {{ t('companies.save_exit') }}
                    </Button>
                    <Button
                        v-if="isEditing"
                        type="button"
                        variant="secondary"
                        :disabled="form.processing"
                        class="w-full"
                        @click="saveAndStay"
                    >
                        <Save class="size-4" />
                        {{ t('common.save') }}
                    </Button>
                    <Button
                        as-child
                        type="button"
                        variant="outline"
                        class="w-full"
                    >
                        <Link href="/admin/companies">
                            <X class="size-4" />
                            {{ t('companies.cancel') }}
                        </Link>
                    </Button>
                </CardContent>
            </Card>
            <Card>
                <CardHeader>
                    <CardTitle>
                        {{ t('table.col_status') }}
                        <span class="text-destructive">*</span>
                    </CardTitle>
                </CardHeader>
                <CardContent class="grid gap-2">
                    <Select
                        :model-value="form.status"
                        @update:model-value="(v) => (form.status = v as string)"
                    >
                        <SelectTrigger class="w-full">
                            <SelectValue />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem value="published">{{ t('status.published') }}</SelectItem>
                            <SelectItem value="draft">{{ t('status.draft') }}</SelectItem>
                            <SelectItem value="inactive">{{ t('status.inactive') }}</SelectItem>
                        </SelectContent>
                    </Select>
                    <InputError :message="fieldError('status') ?? undefined" />
                </CardContent>
            </Card>

            <!-- Trust + tier + facts (no Status here — moved to its own card) -->
            <Card>
                <CardHeader>
                    <CardTitle>{{ t('companies.section_basic') }}</CardTitle>
                    <CardDescription>{{ t('companies.section_basic_desc') }}</CardDescription>
                </CardHeader>
                <CardContent class="flex flex-col gap-3">
                    <div class="flex items-center gap-3 rounded-md border p-3">
                        <BadgeCheck class="size-5 text-blue-500" />
                        <div class="flex-1">
                            <Label class="font-medium">{{ t('companies.field_verified') }}</Label>
                            <p class="text-xs text-muted-foreground">
                                {{ t('companies.field_verified_hint') }}
                            </p>
                        </div>
                        <Switch v-model="form.verified" />
                    </div>
                    <div class="flex items-center gap-3 rounded-md border p-3">
                        <Star class="size-5 fill-amber-400 text-amber-500" />
                        <div class="flex-1">
                            <Label class="font-medium">{{ t('companies.field_top_rated') }}</Label>
                            <p class="text-xs text-muted-foreground">
                                {{ t('companies.field_top_rated_hint') }}
                            </p>
                        </div>
                        <Switch v-model="form.is_top_rated" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="plan-tier">{{ t('companies.field_plan_tier') }}</Label>
                        <Select v-model="form.plan_tier">
                            <SelectTrigger id="plan-tier" class="w-full">
                                <SelectValue />
                            </SelectTrigger>
                            <SelectContent>
                                <SelectItem value="basic">Basic (€0)</SelectItem>
                                <SelectItem value="premium">Premium (€19)</SelectItem>
                                <SelectItem value="gold">Gold (€49)</SelectItem>
                            </SelectContent>
                        </Select>
                        <p class="text-[11px] text-muted-foreground">
                            {{ t('companies.field_plan_tier_hint') }}
                        </p>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div class="grid gap-2">
                            <Label for="founded-year">{{ t('companies.field_founded_year') }}</Label>
                            <Input id="founded-year" v-model="form.founded_year" type="number" placeholder="2007" />
                        </div>
                        <div class="grid gap-2">
                            <Label for="employee-count">{{ t('companies.field_employee_count') }}</Label>
                            <Input id="employee-count" v-model="form.employee_count" type="number" placeholder="25" />
                        </div>
                    </div>
                </CardContent>
            </Card>

            <!-- Address (moved from main column, single-column layout for sidebar width) -->
            <Card>
                <CardHeader>
                    <CardTitle>{{ t('companies.section_address') }}</CardTitle>
                    <CardDescription>{{ t('companies.section_address_desc') }}</CardDescription>
                </CardHeader>
                <CardContent class="flex flex-col gap-4">
                    <div class="grid gap-2">
                        <Label for="addr-country">
                            {{ t('locations.field_country') }}
                            <span class="text-destructive">*</span>
                        </Label>
                        <Select
                            :model-value="addressCountryId?.toString() ?? ''"
                            @update:model-value="(v) => (addressCountryId = v ? Number(v) : null)"
                        >
                            <SelectTrigger id="addr-country" class="w-full">
                                <SelectValue :placeholder="t('locations.field_country')" />
                            </SelectTrigger>
                            <SelectContent>
                                <SelectItem v-for="c in countries" :key="c.id" :value="c.id.toString()">
                                    <FlagImage :code="c.iso_code" size="sm" class="mr-2 inline align-middle" />
                                {{ c.name }}
                                </SelectItem>
                            </SelectContent>
                        </Select>
                    </div>
                    <div class="grid gap-2">
                        <Label for="addr-state">
                            {{ t('locations.field_state') }}
                            <span class="text-destructive">*</span>
                        </Label>
                        <Select
                            :model-value="addressStateId?.toString() ?? ''"
                            :disabled="addressCountryId === null"
                            @update:model-value="(v) => (addressStateId = v ? Number(v) : null)"
                        >
                            <SelectTrigger id="addr-state" class="w-full">
                                <SelectValue :placeholder="t('locations.field_state')" />
                            </SelectTrigger>
                            <SelectContent>
                                <SelectItem
                                    v-for="s in filteredStates"
                                    :key="s.id"
                                    :value="s.id.toString()"
                                >
                                    {{ s.code }} · {{ s.name }}
                                </SelectItem>
                            </SelectContent>
                        </Select>
                    </div>
                    <div class="grid gap-2">
                        <Label for="addr-city" :class="fieldError('primary_city_id') ? 'text-destructive' : ''">
                            {{ t('locations.field_city') }}
                            <span class="text-destructive">*</span>
                        </Label>
                        <Select
                            :model-value="form.primary_city_id?.toString() ?? ''"
                            :disabled="addressStateId === null"
                            @update:model-value="(v) => (form.primary_city_id = v ? Number(v) : null)"
                        >
                            <SelectTrigger
                                id="addr-city"
                                class="w-full"
                                :class="fieldError('primary_city_id') ? 'border-destructive' : ''"
                            >
                                <SelectValue :placeholder="t('locations.field_city')" />
                            </SelectTrigger>
                            <SelectContent>
                                <SelectItem
                                    v-for="ci in filteredCities"
                                    :key="ci.id"
                                    :value="ci.id.toString()"
                                >
                                    {{ ci.name }}
                                </SelectItem>
                            </SelectContent>
                        </Select>
                        <InputError :message="fieldError('primary_city_id') ?? undefined" />
                    </div>
                    <div class="grid gap-2">
                        <Label for="addr-district">
                            {{ t('locations.districts_title') }}
                            <span class="text-muted-foreground">({{ t('table.optional') }})</span>
                        </Label>
                        <Select
                            :model-value="form.primary_district_id?.toString() ?? ''"
                            :disabled="form.primary_city_id === null"
                            @update:model-value="(v) => (form.primary_district_id = v ? Number(v) : null)"
                        >
                            <SelectTrigger id="addr-district" class="w-full">
                                <SelectValue :placeholder="t('locations.districts_title')" />
                            </SelectTrigger>
                            <SelectContent>
                                <SelectItem
                                    v-for="d in addressDistrictOptions"
                                    :key="d.id"
                                    :value="d.id.toString()"
                                >
                                    {{ d.name }}
                                </SelectItem>
                            </SelectContent>
                        </Select>
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div class="grid gap-2">
                            <Label for="addr-street">{{ t('companies.field_street') }}</Label>
                            <Input id="addr-street" v-model="form.street" placeholder="Schwedenstraße 18" />
                        </div>
                        <div class="grid gap-2">
                            <Label for="addr-postal">{{ t('companies.field_postal_code') }}</Label>
                            <Input id="addr-postal" v-model="form.postal_code" placeholder="13357" />
                        </div>
                    </div>
                </CardContent>
            </Card>
            <Card>
                <CardHeader>
                    <CardTitle>{{ t('companies.section_internal_ratings') }}</CardTitle>
                    <CardDescription>{{ t('companies.section_internal_ratings_desc') }}</CardDescription>
                </CardHeader>
                <CardContent class="flex flex-col gap-4">
                    <div class="grid grid-cols-2 gap-3">
                        <div class="grid gap-2">
                            <Label for="rating-avg">{{ t('companies.field_rating_avg') }}</Label>
                            <Input
                                id="rating-avg"
                                v-model="form.rating_avg"
                                type="number"
                                step="0.1"
                                min="0"
                                max="5"
                                placeholder="4.5"
                            />
                        </div>
                        <div class="grid gap-2">
                            <Label for="review-count">{{ t('companies.field_review_count') }}</Label>
                            <Input
                                id="review-count"
                                v-model="form.review_count"
                                type="number"
                                min="0"
                                placeholder="284"
                            />
                        </div>
                    </div>
                    <div class="grid gap-2">
                        <Label for="recommend-pct">
                            {{ t('companies.field_recommend_pct') }}
                            <span class="text-xs text-muted-foreground">(0–100)</span>
                        </Label>
                        <Input
                            id="recommend-pct"
                            v-model="form.recommend_pct"
                            type="number"
                            min="0"
                            max="100"
                            placeholder="98"
                        />
                    </div>
                    <div class="grid gap-2">
                        <Label>{{ t('companies.field_rating_breakdown') }}</Label>
                        <div class="flex flex-col gap-1.5">
                            <div
                                v-for="stars in [5, 4, 3, 2, 1]"
                                :key="stars"
                                class="flex items-center gap-2"
                            >
                                <span class="flex w-6 items-center gap-0.5 text-xs font-medium text-muted-foreground">
                                    {{ stars }}
                                    <Star class="size-3 fill-amber-400 text-amber-400" />
                                </span>
                                <Input
                                    :id="`breakdown-${stars}`"
                                    v-model.number="form.rating_breakdown[stars]"
                                    type="number"
                                    min="0"
                                    placeholder="0"
                                    class="!h-8 flex-1 !text-sm"
                                />
                            </div>
                        </div>
                    </div>
                </CardContent>
            </Card>

            <!-- External reviews / Google -->
            <Card>
                <CardHeader>
                    <CardTitle>{{ t('companies.section_external_reviews') }}</CardTitle>
                    <CardDescription>{{ t('companies.section_external_reviews_desc') }}</CardDescription>
                </CardHeader>
                <CardContent class="flex flex-col gap-4">
                    <div class="grid gap-2">
                        <Label for="google-rating">{{ t('companies.field_google_rating') }}</Label>
                        <Input
                            id="google-rating"
                            v-model="form.google_rating"
                            type="number"
                            step="0.1"
                            min="0"
                            max="5"
                            placeholder="4.8"
                        />
                    </div>
                    <div class="grid gap-2">
                        <Label for="google-count">{{ t('companies.field_google_review_count') }}</Label>
                        <Input
                            id="google-count"
                            v-model="form.google_review_count"
                            type="number"
                            min="0"
                            placeholder="245"
                        />
                    </div>
                </CardContent>
            </Card>
        </aside>
        </div>
        <MediaPicker
            v-model:open="pickerOpen"
            :multiple="pickerTarget === 'gallery'"
            @pick="onMediaPicked"
            @pick-many="onMediaPickedMany"
        />
    </form>
</template>
