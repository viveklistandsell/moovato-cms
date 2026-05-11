<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { Home } from 'lucide-vue-next';
import FolderTreeNode, { type FolderNode } from './FolderTreeNode.vue';

defineProps<{
    tree: FolderNode[];
    currentFolderId: number | null;
}>();
</script>

<template>
    <div class="flex flex-col gap-1">
        <Link
            href="/admin/media"
            class="flex items-center gap-2 rounded-md px-2 py-1 text-sm hover:bg-sidebar-accent"
            :class="{
                'bg-sidebar-accent font-medium': currentFolderId === null,
            }"
        >
            <Home class="h-4 w-4 shrink-0" />
            <span>All media</span>
        </Link>

        <FolderTreeNode
            v-for="node in tree"
            :key="node.id"
            :node="node"
            :current-folder-id="currentFolderId"
        />

        <p
            v-if="tree.length === 0"
            class="px-2 py-1 text-xs text-muted-foreground"
        >
            No folders yet.
        </p>
    </div>
</template>
