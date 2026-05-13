<script setup lang="ts">
import { Check, ChevronsUpDown, X } from 'lucide-vue-next';
import { computed, ref } from 'vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import {
    Popover,
    PopoverContent,
    PopoverTrigger,
} from '@/components/ui/popover';

type Option = { id: number; name: string };

const props = withDefaults(
    defineProps<{
        modelValue: number[];
        options: Option[];
        placeholder?: string;
        searchPlaceholder?: string;
        emptyText?: string;
    }>(),
    {
        placeholder: 'Select…',
        searchPlaceholder: 'Search…',
        emptyText: 'No options',
    },
);

const emit = defineEmits<{
    (e: 'update:modelValue', value: number[]): void;
}>();

const open = ref(false);
const search = ref('');

const filtered = computed(() => {
    const q = search.value.trim().toLowerCase();
    if (q === '') {
        return props.options;
    }
    return props.options.filter((o) => o.name.toLowerCase().includes(q));
});

const selectedOptions = computed(() =>
    props.options.filter((o) => props.modelValue.includes(o.id)),
);

function toggle(id: number): void {
    const set = new Set(props.modelValue);
    if (set.has(id)) {
        set.delete(id);
    } else {
        set.add(id);
    }
    emit('update:modelValue', Array.from(set));
}

function remove(id: number): void {
    emit(
        'update:modelValue',
        props.modelValue.filter((x) => x !== id),
    );
}

function clearAll(): void {
    emit('update:modelValue', []);
}

function isSelected(id: number): boolean {
    return props.modelValue.includes(id);
}
</script>

<template>
    <div class="space-y-2">
        <Popover v-model:open="open">
            <PopoverTrigger as-child>
                <Button
                    type="button"
                    variant="outline"
                    role="combobox"
                    :aria-expanded="open"
                    class="h-auto min-h-9 w-full justify-between py-1.5 text-left font-normal"
                >
                    <span
                        v-if="selectedOptions.length === 0"
                        class="text-muted-foreground"
                    >
                        {{ placeholder }}
                    </span>
                    <span v-else class="text-sm">
                        {{ selectedOptions.length }} selected
                    </span>
                    <ChevronsUpDown class="size-4 shrink-0 opacity-50" />
                </Button>
            </PopoverTrigger>
            <PopoverContent class="w-[--reka-popover-trigger-width] p-0" align="start">
                <div class="border-b p-2">
                    <input
                        v-model="search"
                        type="search"
                        :placeholder="searchPlaceholder"
                        class="h-8 w-full rounded border border-input bg-transparent px-2 text-sm outline-none focus-visible:ring-1 focus-visible:ring-ring"
                    />
                </div>
                <div class="max-h-64 overflow-y-auto py-1">
                    <p
                        v-if="filtered.length === 0"
                        class="px-3 py-4 text-center text-sm text-muted-foreground"
                    >
                        {{ emptyText }}
                    </p>
                    <button
                        v-for="opt in filtered"
                        :key="opt.id"
                        type="button"
                        class="flex w-full cursor-pointer items-center gap-2 px-3 py-1.5 text-left text-sm hover:bg-accent hover:text-accent-foreground"
                        @click="toggle(opt.id)"
                    >
                        <span
                            class="flex size-4 items-center justify-center rounded-sm border border-primary"
                            :class="
                                isSelected(opt.id)
                                    ? 'bg-primary text-primary-foreground'
                                    : 'opacity-50'
                            "
                        >
                            <Check
                                v-if="isSelected(opt.id)"
                                class="size-3"
                            />
                        </span>
                        <span>{{ opt.name }}</span>
                    </button>
                </div>
                <div
                    v-if="modelValue.length > 0"
                    class="flex justify-between border-t p-2 text-xs"
                >
                    <span class="text-muted-foreground">
                        {{ modelValue.length }} selected
                    </span>
                    <button
                        type="button"
                        class="text-destructive hover:underline"
                        @click="clearAll"
                    >
                        Clear all
                    </button>
                </div>
            </PopoverContent>
        </Popover>

        <div
            v-if="selectedOptions.length > 0"
            class="flex flex-wrap gap-1"
        >
            <Badge
                v-for="opt in selectedOptions"
                :key="opt.id"
                variant="secondary"
                class="gap-1 pr-1"
            >
                <span>{{ opt.name }}</span>
                <button
                    type="button"
                    class="rounded-full p-0.5 hover:bg-muted-foreground/20"
                    @click="remove(opt.id)"
                >
                    <X class="size-3" />
                </button>
            </Badge>
        </div>
    </div>
</template>
