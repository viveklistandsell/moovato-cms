<script setup lang="ts">
import { router } from '@inertiajs/vue3';
import {
    ClipboardPaste,
    LayoutTemplate,
    Loader2,
    Plus,
    Save,
} from 'lucide-vue-next';
import { computed, nextTick, ref, watch } from 'vue';
import { toast } from 'vue-sonner';
import draggable from 'vuedraggable';
import type { LocaleOption } from '@/components/common/LocaleTabs.vue';
import { Button } from '@/components/ui/button';
import { useT } from '@/composables/useT';
import { useWidgetClipboard } from '@/composables/useWidgetClipboard';
import WidgetCard from './WidgetCard.vue';
import WidgetEditDrawer from './WidgetEditDrawer.vue';
import WidgetPickerModal from './WidgetPickerModal.vue';
import type { WidgetInstance, WidgetMeta } from '@/widgets/types';

const t = useT();
const clipboard = useWidgetClipboard();

const props = defineProps<{
    // When provided (edit mode), the canvas owns its Save button and syncs
    // directly to /admin/pages/{pageId}/widgets. When null (create mode), the
    // parent form bundles widgets into the main page submission.
    pageId?: number | null;
    availableWidgets: WidgetMeta[];
    languages: LocaleOption[];
}>();

// Controlled: parent supplies and receives the stack via v-model:widgets.
const widgets = defineModel<WidgetInstance[]>('widgets', { required: true });

// One-shot normalization so freshly-loaded widgets and locally-added ones both
// have a translation slot for every active language plus default settings.
watch(
    widgets,
    (val) => {
        if (!Array.isArray(val) || val.length === 0) return;
        let mutated = false;
        const normalized = val.map((w) => {
            const next = normalizeWidget(w);
            if (next !== w) mutated = true;
            return next;
        });
        if (mutated) {
            widgets.value = normalized;
        }
    },
    { immediate: true },
);

const pickerOpen = ref(false);
const drawerOpen = ref(false);
const editingIndex = ref<number | null>(null);
const saving = ref(false);

const hasSaveEndpoint = computed(() => typeof props.pageId === 'number');

const defaultLang = computed<string>(
    () =>
        props.languages.find((l) => l.is_default)?.code ??
        props.languages[0]?.code ??
        'de',
);

const metaByType = computed<Record<string, WidgetMeta>>(() => {
    const map: Record<string, WidgetMeta> = {};
    for (const w of props.availableWidgets) {
        map[w.type] = w;
    }
    return map;
});

const editingWidget = computed<WidgetInstance | null>(() =>
    editingIndex.value !== null
        ? (widgets.value[editingIndex.value] ?? null)
        : null,
);

const editingMeta = computed<WidgetMeta | null>(() =>
    editingWidget.value
        ? (metaByType.value[editingWidget.value.type] ?? null)
        : null,
);

/** Ensure every active language has a translation slot for editing. */
function normalizeWidget(w: WidgetInstance): WidgetInstance {
    const meta = props.availableWidgets.find((m) => m.type === w.type);
    const translations = { ...(w.translations ?? {}) };
    let translationsChanged = false;
    for (const lang of props.languages) {
        if (!translations[lang.code]) {
            translations[lang.code] = { ...(meta?.default_data ?? {}) };
            translationsChanged = true;
        }
    }
    const settingsKeys = Object.keys(meta?.default_settings ?? {});
    const settingsNeedsDefaults = settingsKeys.some(
        (k) => !(k in (w.settings ?? {})),
    );
    const visibilityMissing =
        !w.visibility ||
        w.visibility.desktop === undefined ||
        w.visibility.tablet === undefined ||
        w.visibility.mobile === undefined;
    const cssClassMissing = w.css_class === undefined;

    if (
        !translationsChanged &&
        !settingsNeedsDefaults &&
        !visibilityMissing &&
        !cssClassMissing &&
        w.id !== undefined &&
        w.is_active !== undefined
    ) {
        return w;
    }
    return {
        ...w,
        id: w.id ?? null,
        is_active: w.is_active ?? true,
        settings: { ...(meta?.default_settings ?? {}), ...(w.settings ?? {}) },
        translations,
        visibility: {
            desktop: w.visibility?.desktop ?? true,
            tablet: w.visibility?.tablet ?? true,
            mobile: w.visibility?.mobile ?? true,
        },
        css_class: w.css_class ?? '',
    };
}

function onPick(meta: WidgetMeta): void {
    const translations: Record<string, Record<string, unknown>> = {};
    for (const lang of props.languages) {
        translations[lang.code] = { ...meta.default_data };
    }

    // Capture the landing index BEFORE the push. Because `widgets` is a
    // defineModel that proxies through the parent's prop, reading
    // `widgets.value.length` immediately after the assignment returns the
    // pre-push length until the parent's reactivity flows back next tick.
    const newIndex = widgets.value.length;

    const instance: WidgetInstance = {
        id: null,
        type: meta.type,
        position: newIndex,
        is_active: true,
        settings: { ...meta.default_settings },
        translations,
        visibility: { desktop: true, tablet: true, mobile: true },
        css_class: '',
    };
    widgets.value = [...widgets.value, instance];

    // Auto-open the editor on the newly added widget. We defer to nextTick so
    // the parent has already re-emitted the new array — otherwise the drawer
    // would read a stale `widgets[newIndex]` (the previous tail, or undefined
    // when the stack was empty), leaving the loading spinner stuck.
    void nextTick(() => {
        editingIndex.value = newIndex;
        drawerOpen.value = true;
    });
}

function edit(index: number): void {
    editingIndex.value = index;
    drawerOpen.value = true;
}

function applyEdit(updated: WidgetInstance): void {
    if (editingIndex.value === null) return;
    const next = widgets.value.slice();
    next[editingIndex.value] = updated;
    widgets.value = next;
}

function duplicate(index: number): void {
    const source = widgets.value[index];
    const copy: WidgetInstance = JSON.parse(JSON.stringify(source));
    copy.id = null;
    const next = widgets.value.slice();
    next.splice(index + 1, 0, copy);
    widgets.value = next;
}

/** Copy a widget snapshot to the cross-tab clipboard. */
function copyToClipboard(index: number): void {
    const source = widgets.value[index];
    if (!source) return;
    const snapshot: WidgetInstance = JSON.parse(JSON.stringify(source));
    clipboard.copy(snapshot);
    const label = metaByType.value[source.type]?.label ?? source.type;
    toast.success(t('widgets.copied_toast', { label }));
}

const pasteEnabled = computed<boolean>(() => {
    const w = clipboard.contents.value;
    if (!w) return false;
    return w.type in metaByType.value;
});

const pasteButtonTitle = computed<string>(() => {
    const w = clipboard.contents.value;
    if (!w) return t('widgets.paste_disabled_empty');
    if (!(w.type in metaByType.value)) {
        return t('widgets.paste_disabled_unavailable', { type: w.type });
    }
    return t('widgets.paste_tooltip');
});

function paste(): void {
    const clipboardWidget = clipboard.read();
    if (!clipboardWidget) {
        toast.error(t('widgets.paste_no_clipboard'));
        return;
    }
    const meta = metaByType.value[clipboardWidget.type];
    if (!meta) {
        toast.error(
            t('widgets.paste_type_unavailable', { type: clipboardWidget.type }),
        );
        return;
    }

    const sourceTranslations = clipboardWidget.translations ?? {};
    const translations: Record<string, Record<string, unknown>> = {};
    for (const lang of props.languages) {
        translations[lang.code] = sourceTranslations[lang.code]
            ? { ...sourceTranslations[lang.code] }
            : { ...meta.default_data };
    }

    const newIndex = widgets.value.length;
    const pasted: WidgetInstance = {
        id: null,
        type: clipboardWidget.type,
        position: newIndex,
        is_active: clipboardWidget.is_active ?? true,
        settings: {
            ...meta.default_settings,
            ...(clipboardWidget.settings ?? {}),
        },
        translations,
        visibility: {
            desktop: clipboardWidget.visibility?.desktop ?? true,
            tablet: clipboardWidget.visibility?.tablet ?? true,
            mobile: clipboardWidget.visibility?.mobile ?? true,
        },
        css_class: clipboardWidget.css_class ?? '',
    };
    widgets.value = [...widgets.value, pasted];
    toast.success(t('widgets.pasted_toast', { label: meta.label }));
}

function remove(index: number): void {
    widgets.value = widgets.value.filter((_, i) => i !== index);
}

function toggleActive(index: number): void {
    const next = widgets.value.slice();
    next[index] = { ...next[index], is_active: !next[index].is_active };
    widgets.value = next;
}

function save(): void {
    if (!hasSaveEndpoint.value || props.pageId == null) return;
    saving.value = true;

    // Re-number positions based on current order; the server is authoritative
    // but sending the array index keeps things explicit. Visibility + css_class
    // travel with each row so per-device toggles in the drawer actually persist.
    //
    // We deep-clone via JSON to strip any Vue reactive proxies — proxies
    // serialize fine via JSON.stringify in theory, but unwrapping here is the
    // belt-and-suspenders fix for the "second-save loses the toggle" bug.
    const snapshot: WidgetInstance[] = JSON.parse(
        JSON.stringify(widgets.value),
    );
    const payload = snapshot.map((w, i) => ({
        id: w.id ?? null,
        type: w.type,
        position: i,
        is_active: w.is_active,
        settings: w.settings ?? {},
        translations: w.translations ?? {},
        visibility: {
            desktop: w.visibility?.desktop ?? true,
            tablet: w.visibility?.tablet ?? true,
            mobile: w.visibility?.mobile ?? true,
        },
        css_class: w.css_class ?? '',
    }));

    router.post(
        `/admin/pages/${props.pageId}/widgets`,
        // Inertia accepts plain objects; cast to satisfy its FormDataConvertible.
        { widgets: payload } as never,
        {
            preserveScroll: true,
            preserveState: true,
            onSuccess: () => {
                toast.success('Widgets saved.');
            },
            onError: () => {
                toast.error(
                    'Could not save widgets. Check the form for errors.',
                );
            },
            onFinish: () => {
                saving.value = false;
            },
        },
    );
}
</script>

<template>
    <div class="space-y-4">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div class="flex items-center gap-2 text-sm text-muted-foreground">
                <LayoutTemplate class="size-4" />
                {{ t('widgets.count', { count: widgets.length }) }}
            </div>
            <div class="flex items-center gap-2">
                <Button
                    type="button"
                    variant="outline"
                    size="sm"
                    @click="pickerOpen = true"
                >
                    <Plus class="size-4" />
                    {{ t('widgets.add') }}
                </Button>
                <Button
                    type="button"
                    variant="outline"
                    size="sm"
                    :disabled="!pasteEnabled"
                    :title="pasteButtonTitle"
                    @click="paste"
                >
                    <ClipboardPaste class="size-4" />
                    {{ t('widgets.paste') }}
                </Button>
                <Button
                    v-if="hasSaveEndpoint"
                    type="button"
                    size="sm"
                    :disabled="saving"
                    @click="save"
                >
                    <Loader2 v-if="saving" class="size-4 animate-spin" />
                    <Save v-else class="size-4" />
                    {{ t('widgets.save') }}
                </Button>
            </div>
        </div>

        <p
            v-if="!hasSaveEndpoint"
            class="rounded-md border border-dashed bg-muted/30 px-3 py-2 text-xs text-muted-foreground"
        >
            {{ t('widgets.create_hint') }}
        </p>

        <div
            v-if="widgets.length === 0"
            class="flex flex-col items-center justify-center gap-3 rounded-lg border border-dashed bg-muted/30 px-6 py-12 text-center"
        >
            <LayoutTemplate class="size-8 text-muted-foreground/50" />
            <div class="text-sm font-medium">{{ t('widgets.empty_title') }}</div>
            <p class="max-w-sm text-xs text-muted-foreground">
                {{ t('widgets.empty_hint') }}
            </p>
            <Button
                type="button"
                variant="outline"
                size="sm"
                @click="pickerOpen = true"
            >
                <Plus class="size-4" />
                {{ t('widgets.add_first') }}
            </Button>
        </div>

        <draggable
            v-else
            v-model="widgets"
            item-key="position"
            handle=".widget-drag-handle"
            :animation="180"
            ghost-class="opacity-40"
            class="space-y-2"
        >
            <template #item="{ element, index }">
                <WidgetCard
                    :widget="element"
                    :meta="metaByType[element.type] ?? null"
                    :default-lang="defaultLang"
                    @edit="edit(index)"
                    @duplicate="duplicate(index)"
                    @copy-to-clipboard="copyToClipboard(index)"
                    @remove="remove(index)"
                    @toggle-active="toggleActive(index)"
                />
            </template>
        </draggable>

        <WidgetPickerModal
            v-model:open="pickerOpen"
            :widgets="availableWidgets"
            @pick="onPick"
        />

        <WidgetEditDrawer
            v-model:open="drawerOpen"
            :widget="editingWidget"
            :meta="editingMeta"
            :languages="languages"
            @save="applyEdit"
        />
    </div>
</template>
