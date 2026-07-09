<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { ChevronRight, Home } from 'lucide-vue-next';

defineProps<{
    items: Array<{ id: number; name: string }>;
    isDragging?: boolean;
    dropTargetFolderId?: number | null | undefined;
}>();

const emit = defineEmits<{
    (e: 'dragover', folderId: number | null, event: DragEvent): void;
    (e: 'dragleave', folderId: number | null): void;
    (e: 'drop', folderId: number | null, event: DragEvent): void;
}>();

function onDragOver(folderId: number | null, event: DragEvent): void {
    emit('dragover', folderId, event);
}
function onDragLeave(folderId: number | null): void {
    emit('dragleave', folderId);
}
function onDrop(folderId: number | null, event: DragEvent): void {
    emit('drop', folderId, event);
}
</script>

<template>
    <nav class="flex items-center gap-1 text-sm text-muted-foreground">
        <Link
            href="/admin/media"
            class="flex items-center gap-1 rounded px-2 py-1 transition-all"
            :class="[
                isDragging
                    ? 'ring-1 ring-dashed ring-primary/40 hover:ring-primary'
                    : 'hover:bg-accent',
                dropTargetFolderId === null && isDragging
                    ? '!ring-2 !ring-emerald-500 bg-emerald-50 dark:bg-emerald-950/40'
                    : '',
            ]"
            @dragover.prevent="onDragOver(null, $event)"
            @dragleave="onDragLeave(null)"
            @drop.prevent="onDrop(null, $event)"
        >
            <Home class="h-3.5 w-3.5" />
            <span>Media</span>
        </Link>

        <template v-for="(item, idx) in items" :key="item.id">
            <ChevronRight class="h-3.5 w-3.5" />
            <Link
                :href="`/admin/media?folder=${item.id}`"
                class="rounded px-2 py-1 transition-all"
                :class="[
                    idx === items.length - 1
                        ? 'font-medium text-foreground'
                        : '',
                    isDragging && idx !== items.length - 1
                        ? 'ring-1 ring-dashed ring-primary/40 hover:ring-primary'
                        : 'hover:bg-accent',
                    dropTargetFolderId === item.id && isDragging
                        ? '!ring-2 !ring-emerald-500 bg-emerald-50 dark:bg-emerald-950/40'
                        : '',
                ]"
                @dragover.prevent="onDragOver(item.id, $event)"
                @dragleave="onDragLeave(item.id)"
                @drop.prevent="onDrop(item.id, $event)"
            >
                {{ item.name }}
            </Link>
        </template>
    </nav>
</template>
