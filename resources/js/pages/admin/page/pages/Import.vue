<script setup lang="ts">
import { Head, useForm, usePage } from '@inertiajs/vue3';
import { AlertTriangle, CheckCircle2, X } from 'lucide-vue-next';
import { computed, ref } from 'vue';
import { useT } from '@/composables/useT';

defineOptions({});

defineProps<{ aliases: string[]; nativeWidgets: string[] }>();

const t = useT();

type ImportResultShape = {
    created: number;
    updated: number;
    skipped: number;
    widgets_added: number;
    widgets_skipped: number;
    warnings: Array<{ row: number; kind: string; message: string; values: Record<string, string> }>;
    errors: Array<{ row: number; errors: Record<string, string[]>; values: Record<string, string> }>;
};

const form = useForm<{
    file: File | null;
    lang: 'de' | 'en';
    chunk_size: number;
}>({
    file: null,
    lang: 'de',
    chunk_size: 50,
});

function toggleLang(lang: 'de' | 'en'): void {
    form.lang = lang;
}

const fileInputRef = ref<HTMLInputElement | null>(null);
const filenameLabel = computed(() =>
    form.file?.name ?? t('pages.import_file_empty'),
);

function onFileSelected(event: Event): void {
    const input = event.target as HTMLInputElement;
    form.file = input.files?.[0] ?? null;
}

function submit(): void {
    if (!form.file) {
        return;
    }
    form.post('/admin/pages/import', {
        forceFormData: true,
        preserveScroll: true,
        onFinish: () => {
            if (fileInputRef.value) {
                fileInputRef.value.value = '';
            }
        },
    });
}

const page = usePage();
const importResult = computed<ImportResultShape | null>(() => {
    const flash = (page.props as { flash?: { importResult?: unknown } }).flash;
    return (flash?.importResult as ImportResultShape | null) ?? null;
});
const dismissedResult = ref(false);
const showResult = computed(() => !dismissedResult.value && importResult.value !== null);
</script>

<template>
    <Head :title="t('pages.import_title')" />

    <div class="flex flex-col gap-6 p-4">
        <div
            v-if="showResult && importResult"
            class="rounded-md border p-4"
            :class="importResult.skipped === 0 && importResult.errors.length === 0
                ? 'border-emerald-200 bg-emerald-50 dark:border-emerald-900 dark:bg-emerald-950/30'
                : 'border-amber-200 bg-amber-50 dark:border-amber-900 dark:bg-amber-950/30'"
        >
            <div class="flex items-start justify-between gap-3">
                <div class="flex items-start gap-2.5">
                    <CheckCircle2
                        v-if="importResult.skipped === 0 && importResult.errors.length === 0"
                        class="mt-0.5 size-5 text-emerald-600"
                    />
                    <AlertTriangle v-else class="mt-0.5 size-5 text-amber-600" />
                    <div>
                        <p class="text-sm font-semibold">{{ t('pages.import_result_finished') }}</p>
                        <p class="mt-0.5 text-xs text-muted-foreground">
                            {{ t('pages.import_result_summary', {
                                created: importResult.created,
                                updated: importResult.updated,
                                widgets: importResult.widgets_added,
                                skipped: importResult.skipped,
                            }) }}
                            <span v-if="importResult.warnings.length > 0">
                                · {{ t('pages.import_result_warnings', { n: importResult.warnings.length }) }}
                            </span>
                        </p>
                    </div>
                </div>
                <button type="button" class="rounded p-1 text-muted-foreground hover:bg-white/40" @click="dismissedResult = true">
                    <X class="size-4" />
                </button>
            </div>

            <div
                v-if="importResult.errors.length > 0 || importResult.warnings.length > 0"
                class="mt-3 grid gap-3 md:grid-cols-2"
            >
                <div v-if="importResult.errors.length > 0" class="rounded border bg-white/70 dark:bg-black/20">
                    <p class="border-b bg-white/40 px-3 py-1.5 text-xs font-semibold">{{ t('pages.import_errors_heading') }}</p>
                    <table class="w-full text-xs">
                        <tbody>
                            <template v-for="err in importResult.errors" :key="err.row">
                                <tr v-for="(msgs, field) in err.errors" :key="`${err.row}-${field}`" class="border-t">
                                    <td class="w-10 px-2 py-1 font-mono">{{ err.row }}</td>
                                    <td class="px-2 py-1 font-mono text-muted-foreground">{{ field }}</td>
                                    <td class="px-2 py-1 text-destructive">{{ msgs.join(' · ') }}</td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>
                <div v-if="importResult.warnings.length > 0" class="rounded border bg-white/70 dark:bg-black/20">
                    <p class="border-b bg-white/40 px-3 py-1.5 text-xs font-semibold">{{ t('pages.import_warnings_heading') }}</p>
                    <table class="w-full text-xs">
                        <tbody>
                            <tr v-for="(w, i) in importResult.warnings" :key="`${w.row}-${i}`" class="border-t">
                                <td class="w-10 px-2 py-1 font-mono">{{ w.row }}</td>
                                <td class="px-2 py-1 font-mono text-amber-800">{{ w.kind }}</td>
                                <td class="px-2 py-1">{{ w.message }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="grid gap-6 lg:grid-cols-[minmax(0,1fr)_380px]">
            <!-- =============== LEFT: form card =============== -->
            <div class="rounded-md border bg-card shadow-sm">
                <div class="border-b px-6 py-4">
                    <h2 class="text-base font-semibold">{{ t('pages.import_card_title') }}</h2>
                </div>

                <div class="divide-y">
                    <!-- Excel/CSV File -->
                    <div class="grid grid-cols-1 items-start gap-3 px-6 py-5 md:grid-cols-[180px_1fr] md:items-center">
                        <label class="text-sm font-medium">{{ t('pages.import_file_label') }}</label>
                        <div>
                            <div class="flex items-center gap-3 rounded border bg-muted/30 px-2 py-2">
                                <button
                                    type="button"
                                    class="group rounded-md border border-[var(--orange)]/40 bg-gradient-to-br from-[var(--orange-soft)] to-[color-mix(in_srgb,var(--orange-soft)_60%,white)] px-3 py-1.5 text-sm font-medium text-[var(--orange)] shadow-sm transition-all duration-200 hover:-translate-y-0.5 hover:border-[var(--orange)] hover:from-[var(--orange)] hover:to-[color-mix(in_srgb,var(--orange)_75%,black)] hover:text-white hover:shadow-md hover:shadow-[var(--orange)]/25"
                                    @click="fileInputRef?.click()"
                                >
                                    {{ t('pages.import_file_button') }}
                                </button>
                                <span class="truncate text-sm text-muted-foreground">
                                    {{ filenameLabel }}
                                </span>
                                <input
                                    ref="fileInputRef"
                                    type="file"
                                    accept=".csv,text/csv,text/plain"
                                    class="hidden"
                                    @change="onFileSelected"
                                />
                            </div>
                            <p class="mt-1.5 text-xs text-muted-foreground">
                                {{ t('pages.import_file_hint') }}
                            </p>
                            <p v-if="form.errors.file" class="mt-1 text-xs text-destructive">
                                {{ form.errors.file }}
                            </p>
                        </div>
                    </div>

                    <!-- Chunk size -->
                    <div class="grid grid-cols-1 items-start gap-3 px-6 py-5 md:grid-cols-[180px_1fr]">
                        <label class="pt-2 text-sm font-medium">{{ t('pages.import_chunk_label') }}</label>
                        <div>
                            <input
                                v-model.number="form.chunk_size"
                                type="number"
                                min="1"
                                max="500"
                                class="block w-full rounded border px-3 py-2 text-sm focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/20"
                            />
                            <p class="mt-1.5 text-xs text-muted-foreground">
                                {{ t('pages.import_chunk_hint') }}
                            </p>
                        </div>
                    </div>

                    <!-- Select Languages -->
                    <div class="grid grid-cols-1 items-start gap-3 px-6 py-5 md:grid-cols-[180px_1fr]">
                        <label class="pt-1 text-sm font-medium">{{ t('pages.import_lang_label') }}</label>
                        <div>
                            <div class="flex flex-wrap items-center gap-6">
                                <label class="inline-flex cursor-pointer items-center gap-2 text-sm">
                                    <input
                                        type="checkbox"
                                        class="size-4 accent-primary"
                                        :checked="form.lang === 'en'"
                                        @change="toggleLang('en')"
                                    />
                                    <span>English (en)</span>
                                </label>
                                <label class="inline-flex cursor-pointer items-center gap-2 text-sm">
                                    <input
                                        type="checkbox"
                                        class="size-4 accent-primary"
                                        :checked="form.lang === 'de'"
                                        @change="toggleLang('de')"
                                    />
                                    <span>Deutsch (de)</span>
                                    <span class="rounded bg-gradient-to-r from-[var(--orange)] to-[color-mix(in_srgb,var(--orange)_70%,black)] px-1.5 py-0.5 text-[10px] font-semibold text-white shadow-sm">
                                        {{ t('pages.import_lang_default') }}
                                    </span>
                                </label>
                            </div>
                            <p class="mt-2 text-xs text-muted-foreground">
                                {{ t('pages.import_lang_hint') }}
                            </p>
                            <button
                                type="button"
                                class="group relative mt-5 inline-flex items-center gap-2 overflow-hidden rounded-full bg-gradient-to-r from-[var(--orange)] via-[color-mix(in_srgb,var(--orange)_75%,var(--midnight))] to-[var(--midnight)] px-7 py-2.5 text-sm font-semibold text-white shadow-md shadow-[var(--orange)]/25 transition-all duration-300 hover:-translate-y-0.5 hover:shadow-lg hover:shadow-[var(--orange)]/40 disabled:cursor-not-allowed disabled:opacity-60 disabled:hover:translate-y-0"
                                :disabled="!form.file || form.processing"
                                @click="submit"
                            >
                                <span class="relative z-10">
                                    {{ form.processing ? t('pages.import_button_processing') : t('pages.import_button') }}
                                </span>
                                <span
                                    aria-hidden="true"
                                    class="pointer-events-none absolute inset-y-0 -left-1/3 w-1/3 skew-x-[-20deg] bg-gradient-to-r from-transparent via-white/30 to-transparent transition-transform duration-700 ease-out group-hover:translate-x-[400%]"
                                ></span>
                            </button>
                        </div>
                    </div>
                    <div class="grid grid-cols-1 items-start gap-3 px-6 py-5 md:grid-cols-[180px_1fr] md:items-center">
                        <label class="text-sm font-medium">{{ t('pages.import_templates_label') }}</label>
                        <div class="flex flex-wrap gap-2">
                            <a
                                href="/admin/pages/import/template?lang=de&format=csv"
                                class="inline-flex items-center gap-2 rounded-full bg-gradient-to-br from-[var(--orange)] to-[color-mix(in_srgb,var(--orange)_65%,black)] px-5 py-2 text-sm font-semibold text-white shadow-md shadow-[var(--orange)]/25 transition-all duration-200 hover:-translate-y-0.5 hover:shadow-lg hover:shadow-[var(--orange)]/40 hover:brightness-110"
                            >
                                {{ t('pages.import_templates_de') }}
                            </a>
                            <a
                                href="/admin/pages/import/template?lang=en&format=csv"
                                class="inline-flex items-center gap-2 rounded-full bg-gradient-to-br from-[var(--midnight)] to-[color-mix(in_srgb,var(--midnight)_65%,var(--slate))] px-5 py-2 text-sm font-semibold text-white shadow-md shadow-[var(--midnight)]/25 transition-all duration-200 hover:-translate-y-0.5 hover:shadow-lg hover:shadow-[var(--midnight)]/40 hover:brightness-110"
                            >
                                {{ t('pages.import_templates_en') }}
                            </a>
                        </div>
                    </div>
                    <div class="grid grid-cols-1 items-start gap-3 px-6 py-5 md:grid-cols-[180px_1fr]">
                        <label class="pt-2 text-sm font-medium">{{ t('pages.import_json_templates_label') }}</label>
                        <div>
                            <div class="flex flex-wrap gap-2">
                                <a
                                    href="/admin/pages/import/template?lang=de&format=json"
                                    class="inline-flex items-center gap-2 rounded-full border border-[var(--orange)]/40 bg-gradient-to-br from-[var(--orange-soft)] to-[color-mix(in_srgb,var(--orange-soft)_55%,white)] px-5 py-2 text-sm font-semibold text-[var(--orange)] shadow-sm transition-all duration-200 hover:-translate-y-0.5 hover:border-[var(--orange)] hover:from-[var(--orange)] hover:to-[color-mix(in_srgb,var(--orange)_75%,black)] hover:text-white hover:shadow-md hover:shadow-[var(--orange)]/25"
                                >
                                    {{ t('pages.import_json_templates_de') }}
                                </a>
                                <a
                                    href="/admin/pages/import/template?lang=en&format=json"
                                    class="inline-flex items-center gap-2 rounded-full border border-[var(--midnight)]/40 bg-gradient-to-br from-[var(--paper)] to-[var(--linen)] px-5 py-2 text-sm font-semibold text-[var(--midnight)] shadow-sm transition-all duration-200 hover:-translate-y-0.5 hover:border-[var(--midnight)] hover:from-[var(--midnight)] hover:to-[color-mix(in_srgb,var(--midnight)_75%,var(--slate))] hover:text-white hover:shadow-md hover:shadow-[var(--midnight)]/25 dark:from-[var(--midnight-soft)] dark:to-[color-mix(in_srgb,var(--midnight-soft)_75%,black)] dark:text-[var(--linen)]"
                                >
                                    {{ t('pages.import_json_templates_en') }}
                                </a>
                            </div>
                            <p class="mt-2 text-xs text-muted-foreground">
                                {{ t('pages.import_json_templates_hint') }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- =============== RIGHT: instructions =============== -->
            <aside class="space-y-4">
                <div class="rounded-md border bg-card p-5 shadow-sm">
                    <h3 class="text-base font-semibold">{{ t('pages.import_instructions_title') }}</h3>
                    <div class="mt-4">
                        <p class="text-sm font-semibold">{{ t('pages.import_columns_title') }}:</p>
                        <dl class="mt-2 space-y-1.5 text-xs">
                            <div>
                                <dt class="inline font-mono font-semibold">type:</dt>
                                <dd class="inline text-muted-foreground"> {{ t('pages.import_col_type') }}</dd>
                            </div>
                            <div>
                                <dt class="inline font-mono font-semibold">page_title:</dt>
                                <dd class="inline text-muted-foreground"> {{ t('pages.import_col_page_title') }}</dd>
                            </div>
                            <div>
                                <dt class="inline font-mono font-semibold">page_slug:</dt>
                                <dd class="inline text-muted-foreground"> {{ t('pages.import_col_page_slug') }}</dd>
                            </div>
                            <div>
                                <dt class="inline font-mono font-semibold">german_page_slug:</dt>
                                <dd class="inline text-muted-foreground"> {{ t('pages.import_col_german_page_slug') }}</dd>
                            </div>
                            <div>
                                <dt class="inline font-mono font-semibold">template:</dt>
                                <dd class="inline text-muted-foreground"> {{ t('pages.import_col_template') }}</dd>
                            </div>
                            <div>
                                <dt class="inline font-mono font-semibold">sections:</dt>
                                <dd class="inline text-muted-foreground"> {{ t('pages.import_col_sections') }}</dd>
                            </div>
                            <div>
                                <dt class="inline font-mono font-semibold">title:</dt>
                                <dd class="inline text-muted-foreground"> {{ t('pages.import_col_title') }}</dd>
                            </div>
                            <div>
                                <dt class="inline font-mono font-semibold">description:</dt>
                                <dd class="inline text-muted-foreground"> {{ t('pages.import_col_description') }}</dd>
                            </div>
                            <div>
                                <dt class="inline font-mono font-semibold">image:</dt>
                                <dd class="inline text-muted-foreground"> {{ t('pages.import_col_image') }}</dd>
                            </div>
                            <div>
                                <dt class="inline font-mono font-semibold">image_alt:</dt>
                                <dd class="inline text-muted-foreground"> {{ t('pages.import_col_image_alt') }}</dd>
                            </div>
                            <div>
                                <dt class="inline font-mono font-semibold">image_title:</dt>
                                <dd class="inline text-muted-foreground"> {{ t('pages.import_col_image_title') }}</dd>
                            </div>
                            <div>
                                <dt class="inline font-mono font-semibold">meta_title:</dt>
                                <dd class="inline text-muted-foreground"> {{ t('pages.import_col_meta_title') }}</dd>
                            </div>
                            <div>
                                <dt class="inline font-mono font-semibold">meta_desc:</dt>
                                <dd class="inline text-muted-foreground"> {{ t('pages.import_col_meta_desc') }}</dd>
                            </div>
                            <div>
                                <dt class="inline font-mono font-semibold">meta_image:</dt>
                                <dd class="inline text-muted-foreground"> {{ t('pages.import_col_meta_image') }}</dd>
                            </div>
                            <div>
                                <dt class="inline font-mono font-semibold">header_menu:</dt>
                                <dd class="inline text-muted-foreground"> {{ t('pages.import_col_header_menu') }}</dd>
                            </div>
                            <div>
                                <dt class="inline font-mono font-semibold">qa_score:</dt>
                                <dd class="inline text-muted-foreground"> {{ t('pages.import_col_qa_score') }}</dd>
                            </div>
                            <div>
                                <dt class="inline font-mono font-semibold">brand_size:</dt>
                                <dd class="inline text-muted-foreground"> {{ t('pages.import_col_brand_size') }}</dd>
                            </div>
                        </dl>
                    </div>
                </div>

                <!-- Translation Import callout -->
                <div class="rounded-md border border-emerald-200 bg-emerald-50 p-4 text-xs dark:border-emerald-900 dark:bg-emerald-950/30">
                    <p class="font-semibold text-emerald-900 dark:text-emerald-200">
                        {{ t('pages.import_translation_title') }}:
                    </p>
                    <p class="mt-1 text-emerald-900/80 dark:text-emerald-200/80">
                        {{ t('pages.import_translation_body') }}
                    </p>
                </div>

                <!-- Tip callout -->
                <div class="rounded-md border border-sky-200 bg-sky-50 p-4 text-xs dark:border-sky-900 dark:bg-sky-950/30">
                    <p class="font-semibold text-sky-900 dark:text-sky-200">{{ t('pages.import_tip_title') }}:</p>
                    <p class="mt-1 text-sky-900/80 dark:text-sky-200/80">
                        {{ t('pages.import_tip_body') }}
                    </p>
                </div>

                <!-- JSON Template Format callout -->
                <div class="rounded-md border border-violet-200 bg-violet-50 p-4 text-xs dark:border-violet-900 dark:bg-violet-950/30">
                    <p class="font-semibold text-violet-900 dark:text-violet-200">
                        {{ t('pages.import_json_format_title') }}:
                    </p>
                    <p class="mt-1 text-violet-900/80 dark:text-violet-200/80">
                        {{ t('pages.import_json_format_body') }}
                    </p>
                    <pre class="mt-2 overflow-x-auto rounded bg-white/70 p-2 text-[10px] dark:bg-black/30">{ "widget_type": "faq", "items": [{ "question": "Q1", "answer": "A1" }] }</pre>
                </div>

                <!-- Supported widget slugs -->
                <div class="rounded-md border bg-card p-5 shadow-sm">
                    <p class="text-sm font-semibold">{{ t('pages.import_aliases_title') }}</p>
                    <p class="mt-1 text-xs text-muted-foreground">
                        {{ t('pages.import_aliases_hint') }}
                    </p>
                    <ul class="mt-3 flex flex-wrap gap-1.5">
                        <li
                            v-for="slug in aliases"
                            :key="slug"
                            class="rounded bg-muted px-2 py-1 font-mono text-xs"
                        >
                            {{ slug }}
                        </li>
                    </ul>

                    <p class="mt-4 text-sm font-semibold">{{ t('pages.import_native_title') }}</p>
                    <p class="mt-1 text-xs text-muted-foreground">
                        {{ t('pages.import_native_hint') }}
                    </p>
                    <ul class="mt-3 flex flex-wrap gap-1.5">
                        <li
                            v-for="slug in nativeWidgets"
                            :key="`native-${slug}`"
                            class="rounded border border-emerald-200 bg-emerald-50 px-2 py-1 font-mono text-xs text-emerald-900 dark:border-emerald-900 dark:bg-emerald-950/50 dark:text-emerald-200"
                        >
                            {{ slug }}
                        </li>
                    </ul>
                </div>
            </aside>
        </div>
    </div>
</template>
