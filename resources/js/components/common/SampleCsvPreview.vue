<script setup lang="ts">
import { computed, ref, watch } from 'vue';
import {
    AlertCircle,
    Download,
    FileSpreadsheet,
    Loader2,
    Sparkles,
} from 'lucide-vue-next';
import { toast } from 'vue-sonner';
import { Button } from '@/components/ui/button';
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';
import { useT } from '@/composables/useT';

const props = defineProps<{
    open: boolean;
    url: string;
    fallbackFilename: string;
}>();

const emit = defineEmits<{
    'update:open': [value: boolean];
}>();

const t = useT();

const loading = ref(false);
const errorMessage = ref<string | null>(null);
const content = ref<string>('');
const filename = ref<string>('');

const PREVIEW_ROW_LIMIT = 20;

type ParsedCsv = { header: string[]; rows: string[][] };

const parsed = computed<ParsedCsv>(() => parseCsv(content.value));
const previewRows = computed(() => parsed.value.rows.slice(0, PREVIEW_ROW_LIMIT));
const totalRows = computed(() => parsed.value.rows.length);
const hasMore = computed(() => totalRows.value > PREVIEW_ROW_LIMIT);
const columnCount = computed(() => parsed.value.header.length);

/**
 * Columns that should render in monospace so short structured codes
 * (ISO country, state code, sort_order integer) don't lose alignment
 * or get confused with prose.
 */
const MONO_COLUMNS = new Set([
    'country_iso',
    'iso_code',
    'code',
    'sort_order',
    'id',
    'permalink',
    'lang',
]);

const STATUS_COLUMNS = new Set(['status']);

function isMonoColumn(col: string): boolean {
    return MONO_COLUMNS.has(col.toLowerCase());
}

function isStatusColumn(col: string): boolean {
    return STATUS_COLUMNS.has(col.toLowerCase());
}

function statusPillClass(value: string): string {
    switch (value.toLowerCase()) {
        case 'published':
            return 'bg-emerald-100 text-emerald-700 ring-emerald-200 dark:bg-emerald-950/60 dark:text-emerald-300 dark:ring-emerald-900';
        case 'draft':
            return 'bg-amber-100 text-amber-800 ring-amber-200 dark:bg-amber-950/60 dark:text-amber-300 dark:ring-amber-900';
        case 'inactive':
            return 'bg-rose-100 text-rose-700 ring-rose-200 dark:bg-rose-950/60 dark:text-rose-300 dark:ring-rose-900';
        default:
            return 'bg-muted text-muted-foreground ring-border';
    }
}

async function loadSample(): Promise<void> {
    loading.value = true;
    errorMessage.value = null;
    try {
        const res = await fetch(props.url, {
            headers: { Accept: 'text/csv, text/plain, */*' },
            credentials: 'same-origin',
        });
        if (!res.ok) throw new Error(`HTTP ${res.status}`);
        const disposition = res.headers.get('Content-Disposition') ?? '';
        const match = disposition.match(/filename\*?=(?:UTF-8'')?"?([^";]+)"?/i);
        filename.value = decodeURIComponent(match?.[1] ?? props.fallbackFilename);
        content.value = await res.text();
    } catch (e) {
        errorMessage.value = e instanceof Error ? e.message : String(e);
        content.value = '';
    } finally {
        loading.value = false;
    }
}

function download(): void {
    if (!content.value) return;
    const blob = new Blob([content.value], { type: 'text/csv;charset=utf-8' });
    const objectUrl = URL.createObjectURL(blob);
    const anchor = document.createElement('a');
    anchor.href = objectUrl;
    anchor.download = filename.value || props.fallbackFilename;
    document.body.appendChild(anchor);
    anchor.click();
    anchor.remove();
    setTimeout(() => URL.revokeObjectURL(objectUrl), 0);
    toast.success(
        t('locations.csv_download_success', { filename: anchor.download }),
    );
    emit('update:open', false);
}

watch(
    () => props.open,
    (isOpen) => {
        if (isOpen) {
            void loadSample();
        } else {
            content.value = '';
            errorMessage.value = null;
        }
    },
);

function parseCsv(text: string): ParsedCsv {
    if (!text) return { header: [], rows: [] };
    const stripped = text.replace(/^﻿/, '');
    const rows: string[][] = [];
    let current: string[] = [];
    let field = '';
    let inQuotes = false;

    for (let i = 0; i < stripped.length; i++) {
        const ch = stripped[i];
        if (inQuotes) {
            if (ch === '"' && stripped[i + 1] === '"') {
                field += '"';
                i++;
            } else if (ch === '"') {
                inQuotes = false;
            } else {
                field += ch;
            }
        } else if (ch === '"') {
            inQuotes = true;
        } else if (ch === ',') {
            current.push(field);
            field = '';
        } else if (ch === '\n' || ch === '\r') {
            if (ch === '\r' && stripped[i + 1] === '\n') i++;
            current.push(field);
            field = '';
            rows.push(current);
            current = [];
        } else {
            field += ch;
        }
    }
    if (field !== '' || current.length > 0) {
        current.push(field);
        rows.push(current);
    }

    const cleaned = rows.filter((row) =>
        row.some((cell) => cell.trim() !== ''),
    );
    if (cleaned.length === 0) return { header: [], rows: [] };
    return { header: cleaned[0], rows: cleaned.slice(1) };
}
</script>

<template>
    <Dialog :open="open" @update:open="(v) => emit('update:open', v)">
        <DialogContent
            class="w-[95vw] gap-0 overflow-hidden p-0 sm:max-w-6xl sm:rounded-2xl"
        >
            <DialogHeader
                class="space-y-0 border-b bg-gradient-to-br from-emerald-50/60 via-white to-emerald-50/40 px-6 py-5 dark:from-emerald-950/40 dark:via-background dark:to-emerald-950/20"
            >
                <div class="flex items-start gap-4">
                    <span
                        class="flex size-11 shrink-0 items-center justify-center rounded-xl bg-gradient-to-br from-emerald-500 to-emerald-600 text-white shadow-md shadow-emerald-500/30 ring-1 ring-white/40"
                    >
                        <FileSpreadsheet class="size-5" />
                    </span>
                    <div class="min-w-0 flex-1">
                        <DialogTitle
                            class="text-lg font-semibold tracking-tight"
                        >
                            {{ t('locations.csv_sample_preview_title') }}
                        </DialogTitle>
                        <DialogDescription
                            class="mt-1 text-sm text-muted-foreground"
                        >
                            {{
                                t('locations.csv_sample_preview_description')
                            }}
                        </DialogDescription>
                        <div
                            v-if="!loading && !errorMessage && columnCount > 0"
                            class="mt-3 flex flex-wrap items-center gap-1.5 text-[11px]"
                        >
                            <span
                                class="inline-flex items-center gap-1 rounded-full bg-white/70 px-2 py-0.5 font-mono font-medium text-foreground ring-1 ring-inset ring-border/60 dark:bg-background/60"
                            >
                                <Sparkles class="size-3 text-emerald-600" />
                                {{ filename || fallbackFilename }}
                            </span>
                            <span
                                class="inline-flex items-center rounded-full bg-emerald-100/70 px-2 py-0.5 font-medium text-emerald-800 ring-1 ring-inset ring-emerald-200 dark:bg-emerald-950/50 dark:text-emerald-300 dark:ring-emerald-900"
                            >
                                {{
                                    t('locations.csv_sample_preview_columns', {
                                        count: columnCount,
                                    })
                                }}
                            </span>
                            <span
                                class="inline-flex items-center rounded-full bg-blue-100/70 px-2 py-0.5 font-medium text-blue-800 ring-1 ring-inset ring-blue-200 dark:bg-blue-950/50 dark:text-blue-300 dark:ring-blue-900"
                            >
                                {{
                                    t('locations.csv_sample_preview_rows', {
                                        count: totalRows,
                                    })
                                }}
                            </span>
                        </div>
                    </div>
                </div>
            </DialogHeader>

            <!-- Body -->
            <div class="min-h-[240px] px-6 py-5">
                <div
                    v-if="loading"
                    class="flex flex-col items-center justify-center gap-2 py-16 text-sm text-muted-foreground"
                >
                    <Loader2 class="size-6 animate-spin text-emerald-600" />
                    <span>{{ t('locations.csv_download_starting') }}</span>
                </div>

                <div
                    v-else-if="errorMessage"
                    class="flex items-start gap-3 rounded-lg border border-destructive/40 bg-destructive/5 p-4 text-sm text-destructive"
                >
                    <AlertCircle class="mt-0.5 size-4 shrink-0" />
                    <span>
                        {{
                            t('locations.csv_download_error', {
                                error: errorMessage,
                            })
                        }}
                    </span>
                </div>

                <div
                    v-else-if="parsed.header.length === 0"
                    class="rounded-lg border border-dashed py-10 text-center text-sm text-muted-foreground"
                >
                    {{ t('locations.csv_sample_preview_empty') }}
                </div>

                <div v-else class="space-y-2">
                    <div
                        class="max-h-[52vh] overflow-auto rounded-xl border bg-card shadow-sm"
                    >
                        <table class="w-full border-collapse text-xs">
                            <thead
                                class="sticky top-0 z-10 border-b bg-muted/80 backdrop-blur"
                            >
                                <tr>
                                    <th
                                        v-for="col in parsed.header"
                                        :key="col"
                                        class="border-r border-border/50 px-3.5 py-2.5 text-left font-bold tracking-wide whitespace-nowrap text-foreground uppercase last:border-r-0"
                                        :class="[
                                            isMonoColumn(col)
                                                ? 'font-mono normal-case tracking-normal'
                                                : '',
                                        ]"
                                    >
                                        {{ col }}
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr
                                    v-for="(row, idx) in previewRows"
                                    :key="idx"
                                    class="border-t border-border/60 transition-colors hover:bg-accent/40"
                                    :class="
                                        idx % 2 === 1
                                            ? 'bg-muted/30 dark:bg-muted/10'
                                            : ''
                                    "
                                >
                                    <td
                                        v-for="(cell, cIdx) in row"
                                        :key="cIdx"
                                        class="border-r border-border/40 px-3.5 py-2 whitespace-nowrap last:border-r-0"
                                        :class="[
                                            isMonoColumn(
                                                parsed.header[cIdx] ?? '',
                                            )
                                                ? 'font-mono text-[11px]'
                                                : '',
                                            cell === ''
                                                ? 'text-muted-foreground/60 italic'
                                                : 'text-foreground/90',
                                        ]"
                                    >
                                        <span
                                            v-if="
                                                isStatusColumn(
                                                    parsed.header[cIdx] ?? '',
                                                ) && cell !== ''
                                            "
                                            :class="[
                                                'inline-flex items-center rounded-full px-2 py-0.5 text-[10px] font-semibold ring-1 ring-inset',
                                                statusPillClass(cell),
                                            ]"
                                        >
                                            {{ cell }}
                                        </span>
                                        <template v-else>
                                            {{ cell === '' ? '—' : cell }}
                                        </template>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <p
                        v-if="hasMore"
                        class="pr-1 text-right text-[11px] text-muted-foreground"
                    >
                        {{
                            t('locations.csv_sample_preview_more', {
                                shown: previewRows.length,
                                total: totalRows,
                            })
                        }}
                    </p>
                </div>
            </div>

            <DialogFooter
                class="items-center justify-between gap-3 border-t bg-muted/30 px-6 py-4 sm:flex sm:flex-row"
            >
                <p
                    class="hidden text-[11px] text-muted-foreground sm:block sm:flex-1"
                >
                    {{ t('locations.csv_sample_preview_hint') }}
                </p>
                <div class="flex items-center gap-2">
                    <Button
                        variant="outline"
                        @click="emit('update:open', false)"
                    >
                        {{ t('common.cancel') }}
                    </Button>
                    <Button
                        :disabled="loading || content === ''"
                        class="gap-2 bg-gradient-to-br from-emerald-500 to-emerald-600 text-white shadow-md shadow-emerald-500/30 transition-all hover:-translate-y-0.5 hover:from-emerald-600 hover:to-emerald-700 hover:shadow-lg hover:shadow-emerald-500/40 disabled:from-emerald-500/50 disabled:to-emerald-600/50 disabled:shadow-none"
                        @click="download"
                    >
                        <Download class="size-4" />
                        {{ t('locations.csv_sample_download') }}
                    </Button>
                </div>
            </DialogFooter>
        </DialogContent>
    </Dialog>
</template>
