<script setup lang="ts">
import { Search, X } from 'lucide-vue-next';
import { computed, nextTick, ref, watch } from 'vue';
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';
import WidgetIcon from '@/widgets/shared/WidgetIcon.vue';
import type { WidgetMeta } from '@/widgets/types';

const props = defineProps<{
    widgets: WidgetMeta[];
}>();

const open = defineModel<boolean>('open', { default: false });

const emit = defineEmits<{
    (e: 'pick', widget: WidgetMeta): void;
}>();

const search = ref('');
const searchInput = ref<InstanceType<typeof Input> | null>(null);

watch(open, (isOpen) => {
    search.value = '';

    if (isOpen) {
        void nextTick(() => {
            (searchInput.value?.$el as HTMLInputElement | undefined)?.focus();
        });
    }
});

const filtered = computed<WidgetMeta[]>(() => {
    const needle = search.value.trim().toLowerCase();

    if (needle === '') {
        return props.widgets;
    }

    return props.widgets.filter((w) =>
        [w.label, w.type, w.category ?? '']
            .join(' ')
            .toLowerCase()
            .includes(needle),
    );
});

const grouped = computed<Record<string, WidgetMeta[]>>(() => {
    const out: Record<string, WidgetMeta[]> = {};
    for (const w of filtered.value) {
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

            <div class="relative">
                <Search
                    class="pointer-events-none absolute top-1/2 left-3 size-4 -translate-y-1/2 text-muted-foreground"
                />
                <Input
                    ref="searchInput"
                    v-model="search"
                    type="text"
                    placeholder="Search widgets by name or type…"
                    class="pl-9"
                    @keydown.esc.stop="search = ''"
                />
                <button
                    v-if="search !== ''"
                    type="button"
                    class="absolute top-1/2 right-2 -translate-y-1/2 rounded-sm p-1 text-muted-foreground transition hover:text-foreground"
                    @click="search = ''"
                >
                    <span class="sr-only">Clear search</span>
                    <X class="size-4" />
                </button>
            </div>

            <div class="max-h-[60vh] space-y-6 overflow-y-auto pr-1">
                <p
                    v-if="filtered.length === 0"
                    class="py-10 text-center text-sm text-muted-foreground"
                >
                    No widgets match “{{ search }}”.
                </p>

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
