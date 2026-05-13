<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { ChevronRight, Home } from 'lucide-vue-next';

defineProps<{
    items: Array<{ id: number; name: string }>;
}>();
</script>

<template>
    <nav class="flex items-center gap-1 text-sm text-muted-foreground">
        <Link
            href="/admin/media"
            class="flex items-center gap-1 rounded px-2 py-1 hover:bg-accent"
        >
            <Home class="h-3.5 w-3.5" />
            <span>Media</span>
        </Link>

        <template v-for="(item, idx) in items" :key="item.id">
            <ChevronRight class="h-3.5 w-3.5" />
            <Link
                :href="`/admin/media?folder=${item.id}`"
                class="rounded px-2 py-1 hover:bg-accent"
                :class="{
                    'font-medium text-foreground': idx === items.length - 1,
                }"
            >
                {{ item.name }}
            </Link>
        </template>
    </nav>
</template>
