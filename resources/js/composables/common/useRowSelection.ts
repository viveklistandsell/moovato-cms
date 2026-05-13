import { computed, ref } from 'vue';

/**
 * Manages a Set of selected row IDs for bulk actions in tables.
 * Reactive — assigning to selectedIds.value with a fresh Set triggers updates.
 */
export function useRowSelection() {
    const selectedIds = ref<Set<number>>(new Set());

    function isSelected(id: number): boolean {
        return selectedIds.value.has(id);
    }

    function toggle(id: number): void {
        const next = new Set(selectedIds.value);
        if (next.has(id)) {
            next.delete(id);
        } else {
            next.add(id);
        }
        selectedIds.value = next;
    }

    function set(ids: number[]): void {
        selectedIds.value = new Set(ids);
    }

    function clear(): void {
        selectedIds.value = new Set();
    }

    /**
     * If every id in `ids` is already selected → deselect them all.
     * Otherwise → add them all to the selection. Useful for "select all"
     * checkboxes that scope to the current page.
     */
    function toggleAll(ids: number[]): void {
        const next = new Set(selectedIds.value);
        const allSelected = ids.length > 0 && ids.every((id) => next.has(id));
        if (allSelected) {
            ids.forEach((id) => next.delete(id));
        } else {
            ids.forEach((id) => next.add(id));
        }
        selectedIds.value = next;
    }

    function areAllSelected(ids: number[]): boolean {
        return ids.length > 0 && ids.every((id) => selectedIds.value.has(id));
    }

    function someSelected(ids: number[]): boolean {
        return ids.some((id) => selectedIds.value.has(id));
    }

    const count = computed(() => selectedIds.value.size);
    const isEmpty = computed(() => count.value === 0);
    const ids = computed(() => Array.from(selectedIds.value));

    return {
        selectedIds,
        ids,
        count,
        isEmpty,
        isSelected,
        toggle,
        toggleAll,
        set,
        clear,
        areAllSelected,
        someSelected,
    };
}
