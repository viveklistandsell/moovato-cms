<script setup lang="ts">
import { useDebounceFn } from '@vueuse/core';
import { Loader2, Search, X } from 'lucide-vue-next';
import { computed, ref, watch } from 'vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { useT } from '@/composables/useT';

const props = withDefaults(
    defineProps<{
        modelValue: string;
        placeholder?: string;
        debounceMs?: number;
        loading?: boolean;
    }>(),
    {
        placeholder: '',
        debounceMs: 300,
        loading: false,
    },
);

const emit = defineEmits<{
    (e: 'update:modelValue', value: string): void;
    (e: 'search', value: string): void;
}>();

const t = useT();
const resolvedPlaceholder = computed<string>(
    () => props.placeholder || t('table.search_default'),
);

const local = ref(props.modelValue);

watch(
    () => props.modelValue,
    (v) => {
        if (v !== local.value) {
            local.value = v;
        }
    },
);

const emitDebounced = useDebounceFn((value: string) => {
    emit('update:modelValue', value);
    emit('search', value);
}, props.debounceMs);

function onInput(event: Event): void {
    const value = (event.target as HTMLInputElement).value;
    local.value = value;
    emitDebounced(value);
}

function clear(): void {
    local.value = '';
    emit('update:modelValue', '');
    emit('search', '');
}
</script>

<template>
    <div class="relative w-full max-w-xs">
        <Loader2
            v-if="loading"
            class="pointer-events-none absolute top-1/2 left-2.5 size-4 -translate-y-1/2 animate-spin text-muted-foreground"
        />
        <Search
            v-else
            class="pointer-events-none absolute top-1/2 left-2.5 size-4 -translate-y-1/2 text-muted-foreground"
        />
        <Input
            :model-value="local"
            :placeholder="resolvedPlaceholder"
            class="h-9 pr-8 pl-8"
            @input="onInput"
        />
        <Button
            v-if="local.length > 0"
            variant="ghost"
            size="sm"
            class="absolute top-1/2 right-1 size-7 -translate-y-1/2 px-0"
            @click="clear"
        >
            <X class="size-3.5" />
        </Button>
    </div>
</template>
