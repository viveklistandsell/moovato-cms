<script setup lang="ts">
import { Loader2 } from 'lucide-vue-next';
import { computed, ref, watch } from 'vue';
import LocaleTabs, { type LocaleOption } from '@/components/common/LocaleTabs.vue';
import { Button } from '@/components/ui/button';
import {
    Sheet,
    SheetContent,
    SheetDescription,
    SheetFooter,
    SheetHeader,
    SheetTitle,
} from '@/components/ui/sheet';
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
// re-emits the draft; cancelling discards local edits.
watch(
    [() => props.widget, open],
    ([w, isOpen]) => {
        if (w && isOpen) {
            draft.value = JSON.parse(JSON.stringify(w));
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

function save(): void {
    if (draft.value) {
        emit('save', draft.value);
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
                <div v-if="!draft || !entry" class="flex items-center justify-center py-12">
                    <Loader2 class="size-5 animate-spin text-muted-foreground" />
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
