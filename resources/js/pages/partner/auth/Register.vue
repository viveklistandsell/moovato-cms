<script setup lang="ts">
/**
 * Partner registration form.
 *
 * ⚠️ THIS IS A PHASE 2 PLACEHOLDER — layout is intentionally
 * plain / functional so the register + thanks flow can be
 * end-to-end tested before we invest in the screenshot-matching
 * design (that lands in Phase 4).
 *
 * Fields:
 *   first_name, last_name, email, phone
 *   company_name, street, postal_code, city_id (dropdown)
 *   documents[] (up to 5 files, optional)
 *   accept_terms (required)
 *   website_url (honeypot — must stay empty)
 */
import { Head, Link, useForm } from '@inertiajs/vue3';
import { Loader2, Upload, X } from 'lucide-vue-next';
import { computed, ref } from 'vue';

type CityOption = { id: number; name: string };

const props = defineProps<{
    locale: string;
    cities: CityOption[];
}>();

const de = {
    title: 'Partner-Registrierung',
    subtitle: 'Melden Sie Ihr Umzugsunternehmen an. Nach der Prüfung erhalten Sie eine E-Mail zum Setzen Ihres Passworts.',
    section_you: 'Ihre Kontaktdaten',
    section_company: 'Unternehmensdaten',
    section_docs: 'Dokumente (optional, max. 5 Dateien)',
    section_legal: 'Rechtliches',
    label_first: 'Vorname',
    label_last: 'Nachname',
    label_email: 'E-Mail',
    label_phone: 'Telefon',
    label_company: 'Firmenname',
    label_street: 'Straße & Nr.',
    label_postal: 'PLZ',
    label_city: 'Stadt',
    label_docs: 'Datei wählen',
    label_terms: 'Ich akzeptiere die Datenschutzerklärung und AGB.',
    pick_file: 'Dateien auswählen',
    remove_file: 'Entfernen',
    submit: 'Als Partner registrieren',
    submitting: 'Wird gesendet…',
    already: 'Bereits Partner?',
    login: 'Jetzt anmelden',
    city_placeholder: 'Bitte wählen',
} as const;
const en = {
    title: 'Partner registration',
    subtitle: 'Register your moving business. After review we email you a link to set your password.',
    section_you: 'Your contact details',
    section_company: 'Company details',
    section_docs: 'Documents (optional, up to 5 files)',
    section_legal: 'Legal',
    label_first: 'First name',
    label_last: 'Last name',
    label_email: 'Email',
    label_phone: 'Phone',
    label_company: 'Company name',
    label_street: 'Street & number',
    label_postal: 'Postal code',
    label_city: 'City',
    label_docs: 'Choose files',
    label_terms: 'I accept the privacy policy and terms & conditions.',
    pick_file: 'Choose files',
    remove_file: 'Remove',
    submit: 'Register as partner',
    submitting: 'Submitting…',
    already: 'Already a partner?',
    login: 'Log in',
    city_placeholder: 'Please choose',
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
});

const fileInput = ref<HTMLInputElement | null>(null);

function onFilePicked(event: Event): void {
    const input = event.target as HTMLInputElement;
    const picked = Array.from(input.files ?? []);
    // Cap at 5 total; drop the tail if the user selected more.
    form.documents = [...form.documents, ...picked].slice(0, 5);
    if (fileInput.value) fileInput.value.value = '';
}

function removeFile(idx: number): void {
    form.documents = form.documents.filter((_, i) => i !== idx);
}

function submit(): void {
    form.post('/partner/register', {
        forceFormData: true, // multipart for the file uploads
        preserveScroll: true,
    });
}
</script>

<template>
    <Head :title="t.title" />

    <div class="min-h-screen bg-[var(--paper)] px-4 py-8">
        <div class="mx-auto max-w-xl rounded-lg border border-[var(--linen)] bg-white p-6 shadow-sm">
            <div class="mb-6 text-center">
                <h1 class="text-xl font-bold text-[var(--midnight)]">{{ t.title }}</h1>
                <p class="mt-1 text-sm text-[var(--slate)]">{{ t.subtitle }}</p>
            </div>

            <form class="space-y-6" @submit.prevent="submit">
                <!-- Honeypot — invisible to humans, tempting to bots. -->
                <div class="pointer-events-none absolute -left-[9999px] size-0 opacity-0" aria-hidden="true">
                    <label>
                        Website URL — leave empty
                        <input v-model="form.website_url" type="text" autocomplete="off" tabindex="-1" />
                    </label>
                </div>

                <!-- Contact -->
                <section>
                    <h2 class="mb-3 text-sm font-semibold text-[var(--midnight)]">
                        {{ t.section_you }}
                    </h2>
                    <div class="grid gap-3 sm:grid-cols-2">
                        <div>
                            <label class="mb-1 block text-xs text-[var(--slate)]">{{ t.label_first }}*</label>
                            <input
                                v-model="form.first_name"
                                type="text"
                                required
                                class="w-full rounded-md border border-[var(--linen)] px-3 py-2 text-sm focus:border-[var(--orange)] focus:outline-none"
                                :class="{ 'border-red-400': !!form.errors.first_name }"
                            />
                            <p v-if="form.errors.first_name" class="mt-1 text-xs text-red-600">{{ form.errors.first_name }}</p>
                        </div>
                        <div>
                            <label class="mb-1 block text-xs text-[var(--slate)]">{{ t.label_last }}*</label>
                            <input
                                v-model="form.last_name"
                                type="text"
                                required
                                class="w-full rounded-md border border-[var(--linen)] px-3 py-2 text-sm focus:border-[var(--orange)] focus:outline-none"
                                :class="{ 'border-red-400': !!form.errors.last_name }"
                            />
                            <p v-if="form.errors.last_name" class="mt-1 text-xs text-red-600">{{ form.errors.last_name }}</p>
                        </div>
                        <div>
                            <label class="mb-1 block text-xs text-[var(--slate)]">{{ t.label_email }}*</label>
                            <input
                                v-model="form.email"
                                type="email"
                                required
                                autocomplete="email"
                                class="w-full rounded-md border border-[var(--linen)] px-3 py-2 text-sm focus:border-[var(--orange)] focus:outline-none"
                                :class="{ 'border-red-400': !!form.errors.email }"
                            />
                            <p v-if="form.errors.email" class="mt-1 text-xs text-red-600">{{ form.errors.email }}</p>
                        </div>
                        <div>
                            <label class="mb-1 block text-xs text-[var(--slate)]">{{ t.label_phone }}</label>
                            <input
                                v-model="form.phone"
                                type="tel"
                                autocomplete="tel"
                                class="w-full rounded-md border border-[var(--linen)] px-3 py-2 text-sm focus:border-[var(--orange)] focus:outline-none"
                                :class="{ 'border-red-400': !!form.errors.phone }"
                            />
                            <p v-if="form.errors.phone" class="mt-1 text-xs text-red-600">{{ form.errors.phone }}</p>
                        </div>
                    </div>
                </section>

                <!-- Company -->
                <section>
                    <h2 class="mb-3 text-sm font-semibold text-[var(--midnight)]">
                        {{ t.section_company }}
                    </h2>
                    <div class="grid gap-3">
                        <div>
                            <label class="mb-1 block text-xs text-[var(--slate)]">{{ t.label_company }}*</label>
                            <input
                                v-model="form.company_name"
                                type="text"
                                required
                                class="w-full rounded-md border border-[var(--linen)] px-3 py-2 text-sm focus:border-[var(--orange)] focus:outline-none"
                                :class="{ 'border-red-400': !!form.errors.company_name }"
                            />
                            <p v-if="form.errors.company_name" class="mt-1 text-xs text-red-600">{{ form.errors.company_name }}</p>
                        </div>
                        <div>
                            <label class="mb-1 block text-xs text-[var(--slate)]">{{ t.label_street }}</label>
                            <input
                                v-model="form.street"
                                type="text"
                                class="w-full rounded-md border border-[var(--linen)] px-3 py-2 text-sm focus:border-[var(--orange)] focus:outline-none"
                            />
                        </div>
                        <div class="grid gap-3 sm:grid-cols-[120px_1fr]">
                            <div>
                                <label class="mb-1 block text-xs text-[var(--slate)]">{{ t.label_postal }}</label>
                                <input
                                    v-model="form.postal_code"
                                    type="text"
                                    class="w-full rounded-md border border-[var(--linen)] px-3 py-2 text-sm focus:border-[var(--orange)] focus:outline-none"
                                />
                            </div>
                            <div>
                                <label class="mb-1 block text-xs text-[var(--slate)]">{{ t.label_city }}*</label>
                                <select
                                    v-model.number="form.city_id"
                                    required
                                    class="w-full rounded-md border border-[var(--linen)] bg-white px-3 py-2 text-sm focus:border-[var(--orange)] focus:outline-none"
                                    :class="{ 'border-red-400': !!form.errors.city_id }"
                                >
                                    <option :value="null">{{ t.city_placeholder }}</option>
                                    <option v-for="c in cities" :key="c.id" :value="c.id">
                                        {{ c.name }}
                                    </option>
                                </select>
                                <p v-if="form.errors.city_id" class="mt-1 text-xs text-red-600">{{ form.errors.city_id }}</p>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Docs -->
                <section>
                    <h2 class="mb-3 text-sm font-semibold text-[var(--midnight)]">
                        {{ t.section_docs }}
                    </h2>
                    <label class="inline-flex cursor-pointer items-center gap-1.5 rounded-md border border-[var(--orange)] px-3 py-1.5 text-xs font-medium text-[var(--orange)] hover:bg-[var(--orange-soft)]">
                        <Upload class="size-3.5" />
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
                    <ul v-if="form.documents.length > 0" class="mt-2 space-y-1">
                        <li
                            v-for="(f, i) in form.documents"
                            :key="i"
                            class="flex items-center justify-between gap-2 rounded-md border border-[var(--linen)] px-2 py-1.5 text-xs"
                        >
                            <span class="truncate">📎 {{ f.name }}</span>
                            <button type="button" class="text-red-600 hover:text-red-700" @click="removeFile(i)">
                                <X class="size-3.5" />
                            </button>
                        </li>
                    </ul>
                    <p v-if="form.errors.documents" class="mt-1 text-xs text-red-600">{{ form.errors.documents }}</p>
                </section>

                <!-- Legal -->
                <section>
                    <label class="flex items-start gap-2 text-xs text-[var(--slate)]">
                        <input v-model="form.accept_terms" type="checkbox" class="mt-0.5" />
                        {{ t.label_terms }}
                    </label>
                    <p v-if="form.errors.accept_terms" class="mt-1 text-xs text-red-600">{{ form.errors.accept_terms }}</p>
                </section>

                <!-- Submit -->
                <button
                    type="submit"
                    :disabled="form.processing"
                    class="flex w-full items-center justify-center gap-2 rounded-md bg-[var(--orange)] px-4 py-2.5 text-sm font-semibold text-white transition-colors hover:bg-[color-mix(in_srgb,var(--orange)_85%,black)] disabled:opacity-60"
                >
                    <Loader2 v-if="form.processing" class="size-4 animate-spin" />
                    {{ form.processing ? t.submitting : t.submit }}
                </button>

                <p class="text-center text-xs text-[var(--slate)]">
                    {{ t.already }}
                    <Link href="/partner/login" class="font-medium text-[var(--orange)] hover:underline">
                        {{ t.login }}
                    </Link>
                </p>
            </form>
        </div>
    </div>
</template>
