<script setup lang="ts">
import { router } from '@inertiajs/vue3';
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';
import { Button } from '@/components/ui/button';
import { ref } from 'vue';

const props = defineProps<{
    open: boolean;
    target: { id: number; name: string } | null;
    type: 'folder' | 'file';
    forceDelete?: boolean;
}>();

const emit = defineEmits<{
    (e: 'update:open', value: boolean): void;
    (e: 'deleted'): void;
}>();

const processing = ref(false);

function urlFor(): string {
    if (!props.target) {
        return '';
    }
    if (props.forceDelete) {
        return props.type === 'folder'
            ? `/admin/media/trash/folders/${props.target.id}`
            : `/admin/media/trash/files/${props.target.id}`;
    }
    return props.type === 'folder'
        ? `/admin/media/folders/${props.target.id}`
        : `/admin/media/files/${props.target.id}`;
}

function submit(): void {
    if (!props.target) {
        return;
    }
    processing.value = true;
    router.delete(urlFor(), {
        preserveScroll: true,
        onSuccess: () => {
            emit('update:open', false);
            emit('deleted');
        },
        onFinish: () => {
            processing.value = false;
        },
    });
}
</script>

<template>
    <Dialog :open="open" @update:open="emit('update:open', $event)">
        <DialogContent>
            <DialogHeader>
                <DialogTitle>
                    {{ forceDelete ? 'Permanently delete' : 'Delete' }}
                    {{ type === 'folder' ? 'folder' : 'file' }}?
                </DialogTitle>
                <DialogDescription>
                    <span v-if="forceDelete">
                        <strong>{{ target?.name }}</strong> will be removed
                        permanently. This cannot be undone.
                    </span>
                    <span v-else>
                        <strong>{{ target?.name }}</strong> will be moved to
                        trash. You can restore it later.
                    </span>
                </DialogDescription>
            </DialogHeader>

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
                    variant="destructive"
                    :disabled="processing"
                    @click="submit"
                >
                    {{ forceDelete ? 'Delete forever' : 'Move to trash' }}
                </Button>
            </DialogFooter>
        </DialogContent>
    </Dialog>
</template>
