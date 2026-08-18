<script setup lang="ts">
/**
 * Partner registration form. Same split-panel shell as Login /
 * ForgotPassword. Keeps every field the backend expects — the
 * drag-and-drop docs list and honeypot input.
 *
 * reCAPTCHA is deferred to Phase 7 (needs client Google keys); the
 * form structure leaves room for it under the Legal section.
 */
import { Head, Link, useForm } from '@inertiajs/vue3';
import {
    Building2,
    FileText,
    Loader2,
    Mail,
    MapPin,
    Phone,
    Upload,
    User,
    X,
} from 'lucide-vue-next';
import { computed, onBeforeUnmount, ref } from 'vue';
import PartnerAuthShell from '@/components/partner/PartnerAuthShell.vue';
import Recaptcha from '@/components/partner/Recaptcha.vue';

type CityOption = { id: number; name: string };

const props = defineProps<{
    locale: string;
    cities: CityOption[];
    recaptchaSiteKey: string | null;
}>();

const de = {
    brand_title: 'Werden Sie Moovato Partner',
    brand_subtitle: 'Registrieren Sie Ihr Umzugsunternehmen in wenigen Minuten — nach der Prüfung durch unser Team schalten wir Ihr Profil frei.',
    highlights: [
        'Kostenloses Firmenprofil auf Moovato',
        'Direkte Anfragen von Umzugskunden',
        'Bewertungen, Fotos und Leistungen selbst pflegen',
    ],
    card_title: 'Partner-Registrierung',
    card_subtitle: 'Bitte füllen Sie das Formular aus. Nach der Freigabe erhalten Sie eine E-Mail zum Passwort einrichten.',
    section_you: 'Ihre Kontaktdaten',
    section_company: 'Unternehmensdaten',
    section_docs: 'Dokumente (optional)',
    section_docs_hint: 'PDF, JPG oder PNG · max. 5 Dateien',
    section_legal: 'Rechtliches',
    label_first: 'Vorname',
    label_last: 'Nachname',
    label_email: 'E-Mail-Adresse',
    label_phone: 'Telefon',
    label_company: 'Firmenname',
    label_street: 'Straße & Hausnr.',
    label_postal: 'PLZ',
    label_city: 'Stadt',
    label_terms_prefix: 'Ich akzeptiere die',
    label_terms_dp: 'Datenschutzerklärung',
    label_terms_and: 'und die',
    label_terms_agb: 'AGB',
    drop_hint: 'Dateien hierher ziehen oder',
    pick_file: 'auswählen',
    submit: 'Als Partner registrieren',
    submitting: 'Wird gesendet…',
    already: 'Bereits Partner?',
    login: 'Login',
    city_placeholder: 'Bitte wählen',
    max_files: '{n} von 5 Dateien',
    err_too_many: 'Maximal 5 Dateien erlaubt. Weitere Dateien wurden ignoriert.',
    err_too_large: '„{name}" ist zu groß. Maximale Dateigröße: 5 MB.',
    err_bad_type: '„{name}" hat einen nicht unterstützten Dateityp. Erlaubt sind PDF, JPG, PNG und WebP.',
    err_duplicate: '„{name}" wurde bereits ausgewählt.',
    err_dismiss: 'Ausblenden',
    upload_progress: 'Wird hochgeladen: {p}%',
} as const;

const en = {
    brand_title: 'Become a Moovato partner',
    brand_subtitle: 'Register your moving business in minutes — once approved by our team we publish your profile.',
    highlights: [
        'Free company profile on Moovato',
        'Receive requests directly from customers',
        'Manage reviews, photos and services yourself',
    ],
    card_title: 'Partner registration',
    card_subtitle: 'Please fill in the form. After approval we email you a link to set your password.',
    section_you: 'Your contact details',
    section_company: 'Company details',
    section_docs: 'Documents (optional)',
    section_docs_hint: 'PDF, JPG or PNG · max 5 files',
    section_legal: 'Legal',
    label_first: 'First name',
    label_last: 'Last name',
    label_email: 'Email address',
    label_phone: 'Phone',
    label_company: 'Company name',
    label_street: 'Street & number',
    label_postal: 'Postal code',
    label_city: 'City',
    label_terms_prefix: 'I accept the',
    label_terms_dp: 'privacy policy',
    label_terms_and: 'and the',
    label_terms_agb: 'terms & conditions',
    drop_hint: 'Drag files here or',
    pick_file: 'browse',
    submit: 'Register as partner',
    submitting: 'Submitting…',
    already: 'Already a partner?',
    login: 'Log in',
    city_placeholder: 'Please choose',
    max_files: '{n} of 5 files',
    err_too_many: 'Maximum of 5 files allowed. The extra files were ignored.',
    err_too_large: '"{name}" is too big. Maximum file size is 5 MB.',
    err_bad_type: '"{name}" has an unsupported file type. Allowed: PDF, JPG, PNG, WebP.',
    err_duplicate: '"{name}" is already selected.',
    err_dismiss: 'Dismiss',
    upload_progress: 'Uploading: {p}%',
} as const;
const t = computed(() => (props.locale === 'de' ? de : en));

const form = useForm<{
    first_name: string;
    last_name: string;
    email: string;
    phone: string;
    company_name: string;
    street: string;
    postal_code: string;
    city_id: number | null;
    documents: File[];
    accept_terms: boolean;
    website_url: string; // honeypot
    'g-recaptcha-response': string;
}>({
    first_name: '',
    last_name: '',
    email: '',
    phone: '',
    company_name: '',
    street: '',
    postal_code: '',
    city_id: null,
    documents: [],
    accept_terms: false,
    website_url: '',
    'g-recaptcha-response': '',
});

const fileInput = ref<HTMLInputElement | null>(null);
const isDragging = ref(false);
const recaptchaRef = ref<InstanceType<typeof Recaptcha> | null>(null);

const MAX_FILES = 5;
const MAX_FILE_BYTES = 5 * 1024 * 1024; // 5 MB
const ALLOWED_EXTS = ['pdf', 'jpg', 'jpeg', 'png', 'webp'] as const;
type AllowedExt = typeof ALLOWED_EXTS[number];

type PreviewEntry = {
    file: File;
    previewUrl: string | null;
};


const previews = ref<PreviewEntry[]>([]);

const clientErrors = ref<string[]>([]);

function fileKey(f: File): string {
    return `${f.name}::${f.size}::${f.lastModified}`;
}

function extOf(name: string): string {
    const dot = name.lastIndexOf('.');
    return dot >= 0 ? name.slice(dot + 1).toLowerCase() : '';
}

function isImage(name: string): boolean {
    const e = extOf(name);
    return e === 'jpg' || e === 'jpeg' || e === 'png' || e === 'webp';
}

function isPdf(name: string): boolean {
    return extOf(name) === 'pdf';
}

function addFiles(files: FileList | File[]): void {
    const picked = Array.from(files);
    const errors: string[] = [];
    const accepted: PreviewEntry[] = [];
    const existingKeys = new Set(previews.value.map((p) => fileKey(p.file)));

    for (const f of picked) {
        // Slot cap
        if (previews.value.length + accepted.length >= MAX_FILES) {
            errors.push(t.value.err_too_many);
            break;
        }
        // Type check
        const ext = extOf(f.name) as AllowedExt;
        if (!ALLOWED_EXTS.includes(ext)) {
            errors.push(t.value.err_bad_type.replace('{name}', f.name));
            continue;
        }
        // Size check
        if (f.size > MAX_FILE_BYTES) {
            errors.push(t.value.err_too_large.replace('{name}', f.name));
            continue;
        }
        // Duplicate
        const key = fileKey(f);
        if (existingKeys.has(key)) {
            errors.push(t.value.err_duplicate.replace('{name}', f.name));
            continue;
        }
        existingKeys.add(key);
        accepted.push({
            file: f,
            previewUrl: isImage(f.name) ? URL.createObjectURL(f) : null,
        });
    }

    previews.value = [...previews.value, ...accepted];
    form.documents = previews.value.map((p) => p.file);
    clientErrors.value = errors;
}

function onFilePicked(event: Event): void {
    const input = event.target as HTMLInputElement;
    if (input.files) addFiles(input.files);
    if (fileInput.value) fileInput.value.value = '';
}

function onDrop(event: DragEvent): void {
    event.preventDefault();
    isDragging.value = false;
    if (event.dataTransfer?.files) addFiles(event.dataTransfer.files);
}

function onDragOver(event: DragEvent): void {
    event.preventDefault();
    isDragging.value = true;
}

function onDragLeave(): void {
    isDragging.value = false;
}

function removeFile(idx: number): void {
    const removed = previews.value[idx];
    if (removed?.previewUrl) URL.revokeObjectURL(removed.previewUrl);
    previews.value = previews.value.filter((_, i) => i !== idx);
    form.documents = previews.value.map((p) => p.file);
}

function clearClientErrors(): void {
    clientErrors.value = [];
}

onBeforeUnmount(() => {
    for (const p of previews.value) {
        if (p.previewUrl) URL.revokeObjectURL(p.previewUrl);
    }
});

function humanSize(bytes: number): string {
    if (bytes < 1024) return `${bytes} B`;
    if (bytes < 1024 * 1024) return `${(bytes / 1024).toFixed(0)} KB`;
    return `${(bytes / 1024 / 1024).toFixed(1)} MB`;
}

function submit(): void {
    form.post('/partner/register', {
        forceFormData: true,
        preserveScroll: true,
        onError: () => {
            form['g-recaptcha-response'] = '';
            recaptchaRef.value?.reset();
        },
    });
}
</script>

<template>
    <Head :title="t.card_title" />

    <PartnerAuthShell
        :locale="props.locale"
        :title="t.brand_title"
        :subtitle="t.brand_subtitle"
        :highlights="[...t.highlights]"
    >
        <div class="rounded-xl border border-[var(--linen)] bg-[var(--white)] p-6 shadow-sm sm:p-8">
            <div class="mb-6">
                <h1 class="text-2xl font-bold text-[var(--midnight)]">{{ t.card_title }}</h1>
                <p class="mt-1.5 text-sm text-[var(--slate)]">{{ t.card_subtitle }}</p>
            </div>

            <form class="space-y-7" @submit.prevent="submit">
                <!-- Honeypot — hidden bot bait. Bots that auto-fill "url" fields
                     will complete this and get rejected server-side. -->
                <div class="pointer-events-none absolute -left-[9999px] size-0 opacity-0" aria-hidden="true">
                    <label>
                        Website URL — leave empty
                        <input v-model="form.website_url" type="text" autocomplete="off" tabindex="-1" />
                    </label>
                </div>

                <!-- === Contact === -->
                <section>
                    <header class="mb-3 flex items-center gap-2 border-b border-[var(--linen)] pb-2">
                        <User class="size-4 text-[var(--orange)]" />
                        <h2 class="text-sm font-semibold text-[var(--midnight)]">{{ t.section_you }}</h2>
                    </header>
                    <div class="grid gap-3 sm:grid-cols-2">
                        <div>
                            <label class="mb-1 block text-xs font-medium text-[var(--slate)]">
                                {{ t.label_first }} <span class="text-[var(--orange)]">*</span>
                            </label>
                            <input
                                v-model="form.first_name"
                                type="text"
                                required
                                autocomplete="given-name"
                                class="w-full rounded-lg border border-[var(--linen)] bg-[var(--white)] px-3 py-2 text-sm text-[var(--midnight)] transition-colors focus:border-[var(--orange)] focus:outline-none focus:ring-2 focus:ring-[var(--orange)]/15"
                                :class="{ 'border-[var(--orange)]': !!form.errors.first_name }"
                            />
                            <p v-if="form.errors.first_name" class="mt-1 text-xs text-[var(--orange)]">{{ form.errors.first_name }}</p>
                        </div>
                        <div>
                            <label class="mb-1 block text-xs font-medium text-[var(--slate)]">
                                {{ t.label_last }} <span class="text-[var(--orange)]">*</span>
                            </label>
                            <input
                                v-model="form.last_name"
                                type="text"
                                required
                                autocomplete="family-name"
                                class="w-full rounded-lg border border-[var(--linen)] bg-[var(--white)] px-3 py-2 text-sm text-[var(--midnight)] transition-colors focus:border-[var(--orange)] focus:outline-none focus:ring-2 focus:ring-[var(--orange)]/15"
                                :class="{ 'border-[var(--orange)]': !!form.errors.last_name }"
                            />
                            <p v-if="form.errors.last_name" class="mt-1 text-xs text-[var(--orange)]">{{ form.errors.last_name }}</p>
                        </div>
                        <div>
                            <label class="mb-1 block text-xs font-medium text-[var(--slate)]">
                                {{ t.label_email }} <span class="text-[var(--orange)]">*</span>
                            </label>
                            <div class="relative">
                                <Mail class="pointer-events-none absolute left-3 top-1/2 size-4 -translate-y-1/2 text-[var(--slate-light)]" />
                                <input
                                    v-model="form.email"
                                    type="email"
                                    required
                                    autocomplete="email"
                                    class="w-full rounded-lg border border-[var(--linen)] bg-[var(--white)] py-2 pl-10 pr-3 text-sm text-[var(--midnight)] transition-colors focus:border-[var(--orange)] focus:outline-none focus:ring-2 focus:ring-[var(--orange)]/15"
                                    :class="{ 'border-[var(--orange)]': !!form.errors.email }"
                                />
                            </div>
                            <p v-if="form.errors.email" class="mt-1 text-xs text-[var(--orange)]">{{ form.errors.email }}</p>
                        </div>
                        <div>
                            <label class="mb-1 block text-xs font-medium text-[var(--slate)]">{{ t.label_phone }}</label>
                            <div class="relative">
                                <Phone class="pointer-events-none absolute left-3 top-1/2 size-4 -translate-y-1/2 text-[var(--slate-light)]" />
                                <input
                                    v-model="form.phone"
                                    type="tel"
                                    autocomplete="tel"
                                    class="w-full rounded-lg border border-[var(--linen)] bg-[var(--white)] py-2 pl-10 pr-3 text-sm text-[var(--midnight)] transition-colors focus:border-[var(--orange)] focus:outline-none focus:ring-2 focus:ring-[var(--orange)]/15"
                                    :class="{ 'border-[var(--orange)]': !!form.errors.phone }"
                                />
                            </div>
                            <p v-if="form.errors.phone" class="mt-1 text-xs text-[var(--orange)]">{{ form.errors.phone }}</p>
                        </div>
                    </div>
                </section>

                <!-- === Company === -->
                <section>
                    <header class="mb-3 flex items-center gap-2 border-b border-[var(--linen)] pb-2">
                        <Building2 class="size-4 text-[var(--orange)]" />
                        <h2 class="text-sm font-semibold text-[var(--midnight)]">{{ t.section_company }}</h2>
                    </header>
                    <div class="grid gap-3">
                        <div>
                            <label class="mb-1 block text-xs font-medium text-[var(--slate)]">
                                {{ t.label_company }} <span class="text-[var(--orange)]">*</span>
                            </label>
                            <input
                                v-model="form.company_name"
                                type="text"
                                required
                                autocomplete="organization"
                                class="w-full rounded-lg border border-[var(--linen)] bg-[var(--white)] px-3 py-2 text-sm text-[var(--midnight)] transition-colors focus:border-[var(--orange)] focus:outline-none focus:ring-2 focus:ring-[var(--orange)]/15"
                                :class="{ 'border-[var(--orange)]': !!form.errors.company_name }"
                            />
                            <p v-if="form.errors.company_name" class="mt-1 text-xs text-[var(--orange)]">{{ form.errors.company_name }}</p>
                        </div>
                        <div>
                            <label class="mb-1 block text-xs font-medium text-[var(--slate)]">{{ t.label_street }}</label>
                            <div class="relative">
                                <MapPin class="pointer-events-none absolute left-3 top-1/2 size-4 -translate-y-1/2 text-[var(--slate-light)]" />
                                <input
                                    v-model="form.street"
                                    type="text"
                                    autocomplete="street-address"
                                    class="w-full rounded-lg border border-[var(--linen)] bg-[var(--white)] py-2 pl-10 pr-3 text-sm text-[var(--midnight)] transition-colors focus:border-[var(--orange)] focus:outline-none focus:ring-2 focus:ring-[var(--orange)]/15"
                                />
                            </div>
                        </div>
                        <div class="grid gap-3 sm:grid-cols-[140px_1fr]">
                            <div>
                                <label class="mb-1 block text-xs font-medium text-[var(--slate)]">{{ t.label_postal }}</label>
                                <input
                                    v-model="form.postal_code"
                                    type="text"
                                    autocomplete="postal-code"
                                    class="w-full rounded-lg border border-[var(--linen)] bg-[var(--white)] px-3 py-2 text-sm text-[var(--midnight)] transition-colors focus:border-[var(--orange)] focus:outline-none focus:ring-2 focus:ring-[var(--orange)]/15"
                                />
                            </div>
                            <div>
                                <label class="mb-1 block text-xs font-medium text-[var(--slate)]">
                                    {{ t.label_city }} <span class="text-[var(--orange)]">*</span>
                                </label>
                                <select
                                    v-model.number="form.city_id"
                                    required
                                    class="w-full rounded-lg border border-[var(--linen)] bg-[var(--white)] px-3 py-2 text-sm text-[var(--midnight)] transition-colors focus:border-[var(--orange)] focus:outline-none focus:ring-2 focus:ring-[var(--orange)]/15"
                                    :class="{ 'border-[var(--orange)]': !!form.errors.city_id }"
                                >
                                    <option :value="null">{{ t.city_placeholder }}</option>
                                    <option v-for="c in cities" :key="c.id" :value="c.id">
                                        {{ c.name }}
                                    </option>
                                </select>
                                <p v-if="form.errors.city_id" class="mt-1 text-xs text-[var(--orange)]">{{ form.errors.city_id }}</p>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- === Documents (drag & drop) === -->
                <section>
                    <header class="mb-3 flex items-center gap-2 border-b border-[var(--linen)] pb-2">
                        <FileText class="size-4 text-[var(--orange)]" />
                        <div>
                            <h2 class="text-sm font-semibold text-[var(--midnight)]">{{ t.section_docs }}</h2>
                            <p class="text-[11px] text-[var(--slate-light)]">{{ t.section_docs_hint }}</p>
                        </div>
                    </header>

                    <div
                        class="rounded-lg border-2 border-dashed p-5 text-center transition-colors"
                        :class="isDragging
                            ? 'border-[var(--orange)] bg-[var(--orange-soft)]'
                            : 'border-[var(--linen)] bg-[var(--paper)]'"
                        @dragover.prevent="onDragOver"
                        @dragleave.prevent="onDragLeave"
                        @drop="onDrop"
                    >
                        <Upload class="mx-auto size-6 text-[var(--slate-light)]" />
                        <p class="mt-2 text-sm text-[var(--slate)]">
                            {{ t.drop_hint }}
                            <label class="cursor-pointer font-semibold text-[var(--orange)] hover:underline">
                                {{ t.pick_file }}
                                <input
                                    ref="fileInput"
                                    type="file"
                                    accept=".pdf,.jpg,.jpeg,.png,.webp"
                                    multiple
                                    class="sr-only"
                                    @change="onFilePicked"
                                />
                            </label>
                        </p>
                        <p class="mt-1 text-[11px] text-[var(--slate-light)]">
                            {{ t.max_files.replace('{n}', String(previews.length)) }}
                        </p>
                    </div>

                    <!-- Client-side rejection callouts (file too big, wrong type, duplicate…) -->
                    <div
                        v-if="clientErrors.length > 0"
                        class="mt-3 rounded-md border border-[var(--orange)]/25 bg-[var(--orange-soft)] p-2.5 text-xs text-[var(--midnight)]"
                    >
                        <div class="flex items-start justify-between gap-2">
                            <ul class="min-w-0 flex-1 space-y-0.5">
                                <li v-for="(e, i) in clientErrors" :key="i" class="flex items-start gap-1.5">
                                    <span class="mt-0.5 size-1.5 shrink-0 rounded-full bg-[var(--orange)]"></span>
                                    <span>{{ e }}</span>
                                </li>
                            </ul>
                            <button
                                type="button"
                                class="shrink-0 rounded-md p-1 text-[var(--slate-light)] hover:bg-[var(--white)] hover:text-[var(--orange)]"
                                :aria-label="t.err_dismiss"
                                @click="clearClientErrors"
                            >
                                <X class="size-3.5" />
                            </button>
                        </div>
                    </div>

                    <!-- Selected files with image thumbnails / PDF icon -->
                    <ul v-if="previews.length > 0" class="mt-3 grid gap-2 sm:grid-cols-2">
                        <li
                            v-for="(p, i) in previews"
                            :key="fileKey(p.file)"
                            class="flex items-center gap-3 rounded-lg border border-[var(--linen)] bg-[var(--white)] p-2 text-xs"
                        >
                            <!-- Thumbnail: image preview OR PDF badge -->
                            <div class="flex size-12 shrink-0 items-center justify-center overflow-hidden rounded-md bg-[var(--paper)]">
                                <img
                                    v-if="p.previewUrl"
                                    :src="p.previewUrl"
                                    :alt="p.file.name"
                                    class="size-full object-cover"
                                />
                                <FileText v-else class="size-5 text-[var(--slate-light)]" />
                            </div>
                            <div class="min-w-0 flex-1">
                                <p class="truncate font-medium text-[var(--midnight)]">
                                    {{ p.file.name }}
                                </p>
                                <p class="mt-0.5 text-[10px] text-[var(--slate-light)]">
                                    <span v-if="isPdf(p.file.name)" class="mr-1 inline-block rounded bg-[var(--orange-soft)] px-1.5 py-0.5 font-semibold uppercase tracking-wide text-[var(--orange)]">PDF</span>
                                    {{ humanSize(p.file.size) }}
                                </p>
                            </div>
                            <button
                                type="button"
                                class="shrink-0 rounded-md p-1.5 text-[var(--slate-light)] hover:bg-[var(--orange-soft)] hover:text-[var(--orange)]"
                                :aria-label="`Remove ${p.file.name}`"
                                @click="removeFile(i)"
                            >
                                <X class="size-3.5" />
                            </button>
                        </li>
                    </ul>
                    <p v-if="form.errors.documents" class="mt-2 text-xs text-[var(--orange)]">
                        {{ form.errors.documents }}
                    </p>
                </section>

                <!-- === Legal === -->
                <section>
                    <label class="-m-2 flex cursor-pointer items-start gap-2.5 rounded-md p-2 hover:bg-[var(--paper)]">
                        <input
                            v-model="form.accept_terms"
                            type="checkbox"
                            class="mt-0.5 size-4 rounded border-[var(--linen)] text-[var(--orange)] accent-[var(--orange)] focus:ring-[var(--orange)]/25"
                        />
                        <span class="text-xs leading-relaxed text-[var(--slate)]">
                            {{ t.label_terms_prefix }}
                            <a href="/legal/privacy" target="_blank" class="font-medium text-[var(--orange)] hover:underline">
                                {{ t.label_terms_dp }}
                            </a>
                            {{ t.label_terms_and }}
                            <a href="/legal/terms" target="_blank" class="font-medium text-[var(--orange)] hover:underline">
                                {{ t.label_terms_agb }}
                            </a>.
                        </span>
                    </label>
                    <p v-if="form.errors.accept_terms" class="mt-1 text-xs text-[var(--orange)]">
                        {{ form.errors.accept_terms }}
                    </p>
                </section>

                <section v-if="props.recaptchaSiteKey">
                    <Recaptcha
                        ref="recaptchaRef"
                        v-model="form['g-recaptcha-response']"
                        :site-key="props.recaptchaSiteKey"
                        :locale="props.locale"
                    />
                    <p
                        v-if="form.errors['g-recaptcha-response']"
                        class="mt-1 text-xs text-[var(--orange)]"
                    >
                        {{ form.errors['g-recaptcha-response'] }}
                    </p>
                </section>

                <!-- === Submit === -->
                <div class="space-y-2">
                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="flex w-full items-center justify-center gap-2 rounded-lg bg-[var(--orange)] px-4 py-3 text-sm font-semibold text-[var(--white)] transition-colors hover:bg-[color-mix(in_srgb,var(--orange)_85%,black)] disabled:cursor-not-allowed disabled:opacity-60"
                    >
                        <Loader2 v-if="form.processing" class="size-4 animate-spin" />
                        <span>{{ form.processing ? t.submitting : t.submit }}</span>
                    </button>

                    <!-- Upload progress: only shown while multipart upload is in-flight -->
                    <div
                        v-if="form.progress && form.progress.percentage !== undefined && form.progress.percentage < 100"
                        class="space-y-1"
                    >
                        <div class="h-1.5 w-full overflow-hidden rounded-full bg-[var(--linen)]">
                            <div
                                class="h-full rounded-full bg-[var(--orange)] transition-all"
                                :style="{ width: `${form.progress.percentage}%` }"
                            ></div>
                        </div>
                        <p class="text-center text-[11px] text-[var(--slate)]">
                            {{ t.upload_progress.replace('{p}', String(form.progress.percentage)) }}
                        </p>
                    </div>
                </div>
            </form>

            <div class="mt-6 border-t border-[var(--linen)] pt-4 text-center text-sm text-[var(--slate)]">
                {{ t.already }}
                <Link
                    href="/partner/login"
                    class="ml-1 font-semibold text-[var(--orange)] hover:underline"
                >
                    {{ t.login }}
                </Link>
            </div>
        </div>
    </PartnerAuthShell>
</template>
