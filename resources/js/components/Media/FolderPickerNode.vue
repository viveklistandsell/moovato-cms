<script setup lang="ts">
import { ref } from 'vue';
import { ChevronRight, Folder } from 'lucide-vue-next';
import type { FolderNode } from '@/components/Media/FolderTreeNode.vue';

const props = defineProps<{
    node: FolderNode;
    selectedId: number | null;
    excludedIds: Set<number>;
    currentFolderId: number | null;
    depth: number;
}>();

const emit = defineEmits<{
    (e: 'select', id: number): void;
}>();

const open = ref<boolean>(true);
const isExcluded = props.excludedIds.has(props.node.id);
</script>

<template>
    <div>
        <div
            class="flex items-center gap-1 border-b border-border/50 text-sm"
            :style="{ paddingLeft: `${depth * 12 + 8}px` }"
        >
            <button
                v-if="node.children.length > 0"
                type="button"
                class="flex h-7 w-5 items-center justify-center text-muted-foreground transition-transform"
                :class="{ 'rotate-90': open }"
                @click="open = !open"
            >
                <ChevronRight class="h-3.5 w-3.5" />
            </button>
            <span v-else class="inline-block h-7 w-5"></span>

            <button
                type="button"
                class="flex flex-1 items-center gap-2 truncate py-2 pr-3 text-left"
                :class="{
                    'bg-primary/10 font-medium': selectedId === node.id,
                    'text-muted-foreground hover:bg-transparent':
                        isExcluded || currentFolderId === node.id,
                    'hover:bg-accent':
                        !isExcluded && currentFolderId !== node.id,
                }"
                :disabled="isExcluded || currentFolderId === node.id"
                @click="emit('select', node.id)"
            >
                <Folder class="h-4 w-4 shrink-0 text-amber-500" />
                <span class="truncate">{{ node.name }}</span>
                <span
                    v-if="currentFolderId === node.id"
                    class="text-[11px] text-muted-foreground"
                >
                    (current)
                </span>
                <span
                    v-else-if="isExcluded"
                    class="text-[11px] text-muted-foreground"
                >
                    (own subtree)
                </span>
            </button>
        </div>

        <div v-if="open && node.children.length > 0">
            <FolderPickerNode
                v-for="child in node.children"
                :key="child.id"
                :node="child"
                :selected-id="selectedId"
                :excluded-ids="excludedIds"
                :current-folder-id="currentFolderId"
                :depth="depth + 1"
                @select="(id) => emit('select', id)"
            />
        </div>
    </div>
</template>
