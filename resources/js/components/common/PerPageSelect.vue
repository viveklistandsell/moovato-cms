<script setup lang="ts">
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';

withDefaults(
    defineProps<{
        modelValue: number;
        options?: number[];
        label?: string;
    }>(),
    {
        options: () => [10, 25, 50, 100],
        label: 'Per page',
    },
);

defineEmits<{
    (e: 'update:modelValue', value: number): void;
}>();
</script>

<template>
    <div class="flex items-center gap-2 text-xs text-muted-foreground">
        <span class="whitespace-nowrap">{{ label }}</span>
        <Select
            :model-value="String(modelValue)"
            @update:model-value="
                (v) => $emit('update:modelValue', Number(v))
            "
        >
            <SelectTrigger class="h-9 w-[72px]">
                <SelectValue />
            </SelectTrigger>
            <SelectContent>
                <SelectItem
                    v-for="opt in options"
                    :key="opt"
                    :value="String(opt)"
                >
                    {{ opt }}
                </SelectItem>
            </SelectContent>
        </Select>
    </div>
</template>
