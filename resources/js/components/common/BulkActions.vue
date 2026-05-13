<script setup lang="ts">
import { ChevronDown } from 'lucide-vue-next';
import { Button } from '@/components/ui/button';
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuSeparator,
    DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';

export type BulkAction = {
    value: string;
    label: string;
    destructive?: boolean;
    confirm?: string;
};

const props = defineProps<{
    actions: BulkAction[];
    count: number;
    label?: string;
}>();

const emit = defineEmits<{
    (e: 'action', value: string): void;
}>();

function trigger(action: BulkAction): void {
    if (props.count === 0) {
        return;
    }
    if (action.confirm && !confirm(action.confirm.replace('{count}', String(props.count)))) {
        return;
    }
    emit('action', action.value);
}
</script>

<template>
    <DropdownMenu>
        <DropdownMenuTrigger as-child>
            <Button variant="outline" size="sm" :disabled="count === 0">
                <span>{{ label ?? 'Bulk action' }}</span>
                <span
                    v-if="count > 0"
                    class="ml-1 inline-flex h-5 min-w-5 items-center justify-center rounded-full bg-primary px-1.5 text-[10px] font-semibold text-primary-foreground"
                >
                    {{ count }}
                </span>
                <ChevronDown class="ml-1 size-3" />
            </Button>
        </DropdownMenuTrigger>
        <DropdownMenuContent align="start">
            <template v-for="(action, idx) in actions" :key="action.value">
                <DropdownMenuSeparator
                    v-if="idx > 0 && action.destructive && !actions[idx - 1].destructive"
                />
                <DropdownMenuItem
                    :class="
                        action.destructive
                            ? 'text-destructive focus:text-destructive'
                            : ''
                    "
                    @click="trigger(action)"
                >
                    {{ action.label }}
                </DropdownMenuItem>
            </template>
        </DropdownMenuContent>
    </DropdownMenu>
</template>
