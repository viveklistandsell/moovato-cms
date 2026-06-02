<script setup lang="ts">
import { computed } from 'vue';
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';
import WidgetIcon from '@/widgets/shared/WidgetIcon.vue';
import type { WidgetMeta } from '@/widgets/types';

const props = defineProps<{
    widgets: WidgetMeta[];
}>();

const open = defineModel<boolean>('open', { default: false });

const emit = defineEmits<{
    (e: 'pick', widget: WidgetMeta): void;
}>();

const grouped = computed<Record<string, WidgetMeta[]>>(() => {
    const out: Record<string, WidgetMeta[]> = {};
    for (const w of props.widgets) {
        const key = w.category || 'other';
        if (!out[key]) out[key] = [];
        out[key].push(w);
    }
    return out;
});

const categoryOrder = ['layout', 'content', 'media', 'marketing', 'other'];

const categoriesSorted = computed(() =>
    Object.keys(grouped.value).sort(
        (a, b) => categoryOrder.indexOf(a) - categoryOrder.indexOf(b),
    ),
);

function categoryLabel(key: string): string {
    return key.charAt(0).toUpperCase() + key.slice(1);
}

function choose(widget: WidgetMeta): void {
    emit('pick', widget);
    open.value = false;
}
</script>

<template>
    <Dialog v-model:open="open">
        <DialogContent class="max-w-3xl">
            <DialogHeader>
                <DialogTitle>Add a widget</DialogTitle>
                <DialogDescription>
                    Pick a block to drop into the page. You can re-order, edit,
                    or delete it afterwards.
                </DialogDescription>
            </DialogHeader>

            <div class="max-h-[60vh] space-y-6 overflow-y-auto pr-1">
                <div v-for="category in categoriesSorted" :key="category">
                    <h3
                        class="mb-3 text-xs font-semibold tracking-wide text-muted-foreground uppercase"
                    >
                        {{ categoryLabel(category) }}
                    </h3>
                    <div class="grid grid-cols-2 gap-3 sm:grid-cols-3">
                        <button
                            v-for="widget in grouped[category]"
                            :key="widget.type"
                            type="button"
                            class="group flex flex-col items-start gap-2 rounded-lg border bg-card p-4 text-left transition hover:border-primary hover:shadow-md"
                            @click="choose(widget)"
                        >
                            <div
                                class="inline-flex size-9 items-center justify-center rounded-md bg-primary/10 text-primary transition group-hover:bg-primary group-hover:text-primary-foreground"
                            >
                                <WidgetIcon
                                    :name="widget.icon"
                                    fallback="Square"
                                    class="size-5"
                                />
                            </div>
                            <div class="text-sm font-semibold">
                                {{ widget.label }}
                            </div>
                            <div class="text-xs text-muted-foreground">
                                {{ widget.type }}
                            </div>
                        </button>
                    </div>
                </div>
            </div>
        </DialogContent>
    </Dialog>
</template>
