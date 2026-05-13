<script setup lang="ts">
import { computed, ref, watch } from 'vue';
import { router } from '@inertiajs/vue3';
import { Home } from 'lucide-vue-next';
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';
import { Button } from '@/components/ui/button';
import FolderPickerNode from '@/components/Media/FolderPickerNode.vue';
import type { FolderNode } from '@/components/Media/FolderTreeNode.vue';

type Target =
    | { type: 'file'; id: number; name: string; currentFolderId: number | null }
    | { type: 'folder'; id: number; name: string; currentFolderId: number | null }
    | {
          type: 'bulk-files';
          ids: number[];
          name: string;
          currentFolderId: number | null;
      };

const props = defineProps<{
    open: boolean;
    target: Target | null;
    tree: FolderNode[];
}>();

const emit = defineEmits<{
    (e: 'update:open', value: boolean): void;
    (e: 'moved'): void;
}>();

const selectedFolderId = ref<number | null>(null);
const processing = ref(false);

const excludedIds = computed<Set<number>>(() => {
    const set = new Set<number>();

    if (props.target?.type === 'folder') {
        const collectDescendants = (
            nodes: FolderNode[],
            findId: number,
            inside: boolean,
        ): void => {
            for (const node of nodes) {
                const isMatch = node.id === findId;
                if (inside || isMatch) {
                    set.add(node.id);
                }
                collectDescendants(node.children, findId, inside || isMatch);
            }
        };
        collectDescendants(props.tree, props.target.id, false);
    }

    return set;
});

watch(
    () => props.open,
    (open) => {
        if (open) {
            selectedFolderId.value = null;
        }
    },
);

function submit(): void {
    if (!props.target) return;
    processing.value = true;

    const folderId = selectedFolderId.value;

    const onFinish = () => {
        processing.value = false;
    };

    const onSuccess = () => {
        emit('update:open', false);
        emit('moved');
    };

    if (props.target.type === 'file') {
        router.patch(
            `/admin/media/files/${props.target.id}`,
            { folder_id: folderId },
            { preserveScroll: true, onSuccess, onFinish },
        );
        return;
    }

    if (props.target.type === 'folder') {
        router.patch(
            `/admin/media/folders/${props.target.id}`,
            { parent_id: folderId },
            { preserveScroll: true, onSuccess, onFinish },
        );
        return;
    }

    if (props.target.type === 'bulk-files') {
        router.post(
            '/admin/media/files/bulk-move',
            { ids: props.target.ids, folder_id: folderId },
            { preserveScroll: true, onSuccess, onFinish },
        );
    }
}
</script>

<template>
    <Dialog :open="open" @update:open="emit('update:open', $event)">
        <DialogContent class="max-w-md">
            <DialogHeader>
                <DialogTitle>Move {{ target?.name }}</DialogTitle>
                <DialogDescription>
                    Pick a destination folder.
                </DialogDescription>
            </DialogHeader>

            <div class="max-h-80 overflow-auto rounded border border-border">
                <button
                    type="button"
                    class="flex w-full items-center gap-2 border-b border-border px-3 py-2 text-left text-sm hover:bg-accent"
                    :class="{
                        'bg-primary/10 font-medium':
                            selectedFolderId === null,
                    }"
                    :disabled="target?.currentFolderId === null"
                    @click="selectedFolderId = null"
                >
                    <Home class="h-4 w-4" />
                    <span>All media (root)</span>
                </button>

                <FolderPickerNode
                    v-for="node in tree"
                    :key="node.id"
                    :node="node"
                    :selected-id="selectedFolderId"
                    :excluded-ids="excludedIds"
                    :current-folder-id="target?.currentFolderId ?? null"
                    :depth="0"
                    @select="selectedFolderId = $event"
                />

                <p
                    v-if="tree.length === 0"
                    class="px-3 py-4 text-center text-xs text-muted-foreground"
                >
                    No folders yet — pick "All media" to leave files at the root.
                </p>
            </div>

            <DialogFooter>
                <Button
                    type="button"
                    variant="outline"
                    @click="emit('update:open', false)"
                >
                    Cancel
                </Button>
                <Button
                    type="button"
                    :disabled="
                        processing ||
                        selectedFolderId === target?.currentFolderId
                    "
                    @click="submit"
                >
                    Move here
                </Button>
            </DialogFooter>
        </DialogContent>
    </Dialog>
</template>
