<script setup lang="ts">
import { ref } from 'vue';
import { Link } from '@inertiajs/vue3';
import { ChevronRight, Folder, FolderOpen } from 'lucide-vue-next';

export type FolderNode = {
    id: number;
    name: string;
    children: FolderNode[];
};

const props = defineProps<{
    node: FolderNode;
    currentFolderId: number | null;
    depth?: number;
}>();

const open = ref<boolean>(props.depth === 0);
const depth = props.depth ?? 0;

function toggle(): void {
    open.value = !open.value;
}

function isActive(id: number): boolean {
    return props.currentFolderId === id;
}
</script>

<template>
    <div>
        <div
            class="group flex items-center gap-1 rounded-md px-2 py-1 text-sm hover:bg-sidebar-accent"
            :class="{
                'bg-sidebar-accent font-medium': isActive(node.id),
            }"
            :style="{ paddingLeft: `${depth * 12 + 8}px` }"
        >
            <button
                v-if="node.children.length > 0"
                type="button"
                class="flex h-4 w-4 items-center justify-center rounded text-muted-foreground transition-transform"
                :class="{ 'rotate-90': open }"
                @click="toggle"
            >
                <ChevronRight class="h-3.5 w-3.5" />
            </button>
            <span v-else class="inline-block w-4"></span>

            <Link
                :href="`/admin/media?folder=${node.id}`"
                class="flex flex-1 items-center gap-2 truncate"
            >
                <FolderOpen
                    v-if="open && node.children.length > 0"
                    class="h-4 w-4 shrink-0 text-amber-500"
                />
                <Folder v-else class="h-4 w-4 shrink-0 text-amber-500" />
                <span class="truncate">{{ node.name }}</span>
            </Link>
        </div>

        <div v-if="open && node.children.length > 0">
            <FolderTreeNode
                v-for="child in node.children"
                :key="child.id"
                :node="child"
                :current-folder-id="currentFolderId"
                :depth="depth + 1"
            />
        </div>
    </div>
</template>
