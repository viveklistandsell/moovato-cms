import { ref } from 'vue';

export type ReorderableItem = {
    id: number;
    parent_id?: number | null;
    sort_order: number;
};

export type ReorderPayload = {
    parent_id: number | null;
    ordered_ids: number[];
};

type UseDragReorderOptions<T extends ReorderableItem> = {
    /** Returns the current full list of items. Called on each drop. */
    getItems: () => T[];
    /**
     * Persist the new order. Should return a Promise that resolves when the
     * server round-trip completes — `isReordering` stays true until then.
     */
    onReorder: (payload: ReorderPayload) => Promise<void> | void;
};

/**
 * HTML5 drag-and-drop reordering for sibling lists keyed by `parent_id`.
 * Items can only be dropped onto siblings sharing the same parent_id;
 * cross-parent drops are blocked at the UI layer (visual cue + dropEffect=none).
 */
export function useDragReorder<T extends ReorderableItem>(
    options: UseDragReorderOptions<T>,
) {
    const draggedId = ref<number | null>(null);
    const draggedParentId = ref<number | null | undefined>(undefined);
    const dropTargetId = ref<number | null>(null);
    const isReordering = ref(false);

    function parentOf(node: T): number | null {
        return node.parent_id ?? null;
    }

    function isDragging(node: T): boolean {
        return draggedId.value === node.id;
    }

    function isDropTarget(node: T): boolean {
        return (
            dropTargetId.value === node.id &&
            draggedId.value !== null &&
            draggedId.value !== node.id &&
            parentOf(node) === draggedParentId.value
        );
    }

    function isInvalidDrop(node: T): boolean {
        return (
            draggedId.value !== null &&
            draggedId.value !== node.id &&
            parentOf(node) !== draggedParentId.value
        );
    }

    function reset(): void {
        draggedId.value = null;
        draggedParentId.value = undefined;
        dropTargetId.value = null;
    }

    function onDragStart(e: DragEvent, node: T): void {
        draggedId.value = node.id;
        draggedParentId.value = parentOf(node);
        if (e.dataTransfer) {
            e.dataTransfer.effectAllowed = 'move';
            e.dataTransfer.setData('text/plain', String(node.id));
        }
    }

    function onDragOver(e: DragEvent, node: T): void {
        if (draggedId.value === null) {
            return;
        }
        if (parentOf(node) !== draggedParentId.value) {
            if (e.dataTransfer) {
                e.dataTransfer.dropEffect = 'none';
            }
            return;
        }
        e.preventDefault();
        if (e.dataTransfer) {
            e.dataTransfer.dropEffect = 'move';
        }
        dropTargetId.value = node.id;
    }

    function onDragLeave(node: T): void {
        if (dropTargetId.value === node.id) {
            dropTargetId.value = null;
        }
    }

    function onDragEnd(): void {
        reset();
    }

    async function onDrop(e: DragEvent, target: T): Promise<void> {
        e.preventDefault();
        const sourceId = draggedId.value;
        const targetParentId = parentOf(target);

        if (
            sourceId === null ||
            sourceId === target.id ||
            targetParentId !== draggedParentId.value
        ) {
            reset();
            return;
        }

        const siblings = options
            .getItems()
            .filter((c) => parentOf(c) === targetParentId)
            .sort((a, b) => a.sort_order - b.sort_order);

        const sourceIdx = siblings.findIndex((c) => c.id === sourceId);
        const targetIdx = siblings.findIndex((c) => c.id === target.id);

        if (sourceIdx === -1 || targetIdx === -1) {
            reset();
            return;
        }

        const [moved] = siblings.splice(sourceIdx, 1);
        siblings.splice(targetIdx, 0, moved);

        const orderedIds = siblings.map((c) => c.id);
        isReordering.value = true;

        try {
            await options.onReorder({
                parent_id: targetParentId,
                ordered_ids: orderedIds,
            });
        } finally {
            reset();
            isReordering.value = false;
        }
    }

    return {
        draggedId,
        dropTargetId,
        isReordering,
        isDragging,
        isDropTarget,
        isInvalidDrop,
        onDragStart,
        onDragOver,
        onDragLeave,
        onDragEnd,
        onDrop,
        reset,
    };
}
