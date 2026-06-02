<script setup lang="ts">
import { Loader2, Monitor, Smartphone, Tablet } from 'lucide-vue-next';
import { computed, ref, watch } from 'vue';
import LocaleTabs, {
    type LocaleOption,
} from '@/components/common/LocaleTabs.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import {
    Sheet,
    SheetContent,
    SheetDescription,
    SheetFooter,
    SheetHeader,
    SheetTitle,
} from '@/components/ui/sheet';
import { Switch } from '@/components/ui/switch';
import { getWidgetEntry } from '@/widgets/registry';
import type { WidgetInstance, WidgetMeta } from '@/widgets/types';

const props = defineProps<{
    widget: WidgetInstance | null;
    meta: WidgetMeta | null;
    languages: LocaleOption[];
}>();

const open = defineModel<boolean>('open', { default: false });

const emit = defineEmits<{
    (e: 'save', widget: WidgetInstance): void;
}>();

const draft = ref<WidgetInstance | null>(null);

// Snapshot the widget into a local draft each time the drawer opens. Saving
// re-emits the draft; cancelling discards local edits. We explicitly null the
// draft on close so the next open ALWAYS re-clones the latest widget (which
// includes the visibility / css_class fields the user may have just toggled).
watch(
    [() => props.widget, open],
    ([w, isOpen]) => {
        if (w && isOpen) {
            draft.value = JSON.parse(JSON.stringify(w));
        } else if (!isOpen) {
            draft.value = null;
        }
    },
    { immediate: true },
);

const entry = computed(() =>
    props.meta ? getWidgetEntry(props.meta.type) : null,
);

const activeLocale = ref(
    props.languages.find((l) => l.is_default)?.code ??
        props.languages[0]?.code ??
        'de',
);

function dataFor(lang: string): Record<string, unknown> {
    if (!draft.value) return {};
    if (!draft.value.translations[lang]) {
        // Initialize from widget defaults if the language tab has no row yet.
        draft.value.translations = {
            ...draft.value.translations,
            [lang]: { ...(props.meta?.default_data ?? {}) },
        };
    }
    return draft.value.translations[lang];
}

function updateData(lang: string, value: Record<string, unknown>): void {
    if (!draft.value) return;
    draft.value.translations = {
        ...draft.value.translations,
        [lang]: value,
    };
}

// Replace the whole visibility object on each toggle so the parent-side
// reactive tracking sees a brand-new reference. Mutating a single nested
// property worked in theory but didn't always trigger the array round-trip
// in defineModel-based v-model chains.
function setVisibility(
    key: 'desktop' | 'tablet' | 'mobile',
    value: boolean,
): void {
    if (!draft.value) return;
    draft.value.visibility = {
        ...draft.value.visibility,
        [key]: value,
    };
}

function setCssClass(value: string): void {
    if (!draft.value) return;
    draft.value.css_class = value;
}

function save(): void {
    if (draft.value) {
        // Emit a deep clone so the parent stores a plain object — eliminates
        // any chance that the parent's array holds a reactive proxy pointing
        // back into the drawer's draft (which is about to be nulled on close).
        emit('save', JSON.parse(JSON.stringify(draft.value)));
        open.value = false;
    }
}
</script>

<template>
    <Sheet v-model:open="open">
        <SheetContent
            side="right"
            class="flex w-full flex-col gap-0 overflow-hidden p-0 sm:max-w-2xl"
        >
            <SheetHeader class="border-b px-6 py-4">
                <SheetTitle>
                    Edit widget — {{ meta?.label ?? 'Widget' }}
                </SheetTitle>
                <SheetDescription>
                    Tune layout settings (shared across languages) and fill in
                    each language's content separately.
                </SheetDescription>
            </SheetHeader>

            <div class="flex-1 overflow-y-auto px-6 py-5">
                <div
                    v-if="!draft || !entry"
                    class="flex items-center justify-center py-12"
                >
                    <Loader2
                        class="size-5 animate-spin text-muted-foreground"
                    />
                </div>

                <LocaleTabs
                    v-else
                    v-model="activeLocale"
                    :languages="languages"
                >
                    <template #default="{ code }">
                        <div class="pt-4">
                            <component
                                :is="entry.editor"
                                v-model:settings="draft.settings"
                                :data="dataFor(code)"
                                @update:data="updateData(code, $event)"
                                :lang="code"
                            />
                        </div>
                    </template>
                </LocaleTabs>

                <!-- Display rules: per-instance visibility per breakpoint plus
                     an optional custom CSS class. These live outside the
                     LocaleTabs because they're shared across languages. -->
                <div
                    v-if="draft"
                    class="mt-6 space-y-4 rounded-md border bg-muted/30 p-4"
                >
                    <div>
                        <div class="text-sm font-semibold">Display</div>
                        <p class="text-xs text-muted-foreground">
                            Toggle which screen sizes show this widget, and
                            optionally add a custom class.
                        </p>
                    </div>

                    <div class="grid gap-3 sm:grid-cols-3">
                        <label
                            class="flex items-center justify-between gap-3 rounded-md border bg-background px-3 py-2"
                        >
                            <span class="flex items-center gap-2 text-sm">
                                <Smartphone
                                    class="size-4 text-muted-foreground"
                                />
                                Mobile
                            </span>
                            <Switch
                                :model-value="draft.visibility.mobile"
                                @update:model-value="
                                    (v) => setVisibility('mobile', v)
                                "
                            />
                        </label>
                        <label
                            class="flex items-center justify-between gap-3 rounded-md border bg-background px-3 py-2"
                        >
                            <span class="flex items-center gap-2 text-sm">
                                <Tablet class="size-4 text-muted-foreground" />
                                Tablet
                            </span>
                            <Switch
                                :model-value="draft.visibility.tablet"
                                @update:model-value="
                                    (v) => setVisibility('tablet', v)
                                "
                            />
                        </label>
                        <label
                            class="flex items-center justify-between gap-3 rounded-md border bg-background px-3 py-2"
                        >
                            <span class="flex items-center gap-2 text-sm">
                                <Monitor class="size-4 text-muted-foreground" />
                                Desktop
                            </span>
                            <Switch
                                :model-value="draft.visibility.desktop"
                                @update:model-value="
                                    (v) => setVisibility('desktop', v)
                                "
                            />
                        </label>
                    </div>

                    <div class="grid gap-2">
                        <Label for="widget-css-class">
                            Custom CSS class (optional)
                        </Label>
                        <Input
                            id="widget-css-class"
                            :model-value="draft.css_class"
                            placeholder="my-fancy-widget"
                            @update:model-value="(v) => setCssClass(String(v))"
                        />
                        <p class="text-xs text-muted-foreground">
                            Space-separated classes applied to the section on
                            the public page — e.g.
                            <code>pt-0 pb-0</code> to remove its padding.
                            Tailwind spacing utilities (p/px/py/pt/pr/pb/pl and
                            mt/mb/my) and your own classes both work.
                        </p>
                    </div>
                </div>
            </div>

            <SheetFooter class="border-t bg-muted/30 px-6 py-4">
                <Button type="button" variant="outline" @click="open = false">
                    Cancel
                </Button>
                <Button type="button" :disabled="!draft" @click="save">
                    Apply
                </Button>
            </SheetFooter>
        </SheetContent>
    </Sheet>
</template>
