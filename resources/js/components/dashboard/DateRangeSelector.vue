<script setup lang="ts">
import { router } from '@inertiajs/vue3';
import { Calendar, ChevronDown } from 'lucide-vue-next';
import { ref } from 'vue';

const props = defineProps<{ value: string }>();

type Option = { key: string; label: string };
const options: Option[] = [
    { key: '7d', label: 'Last 7 days' },
    { key: '30d', label: 'Last 30 days' },
    { key: '90d', label: 'Last 90 days' },
    { key: 'year', label: 'This year' },
];

const open = ref<boolean>(false);

function currentLabel(): string {
    return options.find((o) => o.key === props.value)?.label ?? 'Last 30 days';
}

function pick(key: string): void {
    open.value = false;
    if (key === props.value) return;
    // Partial reload only re-fetches the data props that depend on the
    // range; the welcome strip / permissions etc. stay cached.
    router.get(
        '/dashboard',
        { range: key },
        {
            preserveScroll: true,
            preserveState: true,
            replace: true,
        },
    );
}
</script>

<template>
    <div class="relative">
        <button
            type="button"
            class="inline-flex items-center gap-2 rounded-xl border border-white/40 bg-white/70 px-3 py-1.5 text-xs font-semibold text-[var(--midnight)] backdrop-blur-xl transition-all hover:-translate-y-0.5 hover:border-[var(--orange)]/40 hover:shadow-lg hover:shadow-[var(--orange)]/15 dark:border-white/5 dark:bg-[var(--midnight)]/60 dark:text-[var(--linen)]"
            @click="open = !open"
        >
            <Calendar class="size-3.5 text-[var(--orange)]" />
            <span>{{ currentLabel() }}</span>
            <ChevronDown
                class="size-3.5 transition-transform"
                :class="open ? 'rotate-180' : ''"
            />
        </button>
        <div
            v-if="open"
            class="animate-in fade-in slide-in-from-top-1 absolute right-0 z-50 mt-1 w-44 overflow-hidden rounded-xl border border-white/40 bg-white/95 shadow-2xl backdrop-blur-xl duration-150 dark:border-white/5 dark:bg-[var(--midnight)]/95"
        >
            <button
                v-for="o in options"
                :key="o.key"
                type="button"
                class="flex w-full items-center justify-between px-3 py-2 text-xs font-medium text-left transition-colors hover:bg-[var(--orange)]/10"
                :class="
                    o.key === value
                        ? 'bg-[var(--orange)]/15 text-[var(--orange)]'
                        : 'text-[var(--midnight)] dark:text-[var(--linen)]'
                "
                @click="pick(o.key)"
            >
                {{ o.label }}
                <span
                    v-if="o.key === value"
                    class="size-1.5 rounded-full bg-[var(--orange)]"
                />
            </button>
        </div>
    </div>
</template>
