<script setup lang="ts">
import { watch } from 'vue';
import { useForm } from '@inertiajs/vue3';
import {
    Dialog,
    DialogContent,
    DialogFooter,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Button } from '@/components/ui/button';
import InputError from '@/components/InputError.vue';

const props = defineProps<{
    open: boolean;
    target: { id: number; name: string } | null;
    type: 'folder' | 'file';
}>();

const emit = defineEmits<{
    (e: 'update:open', value: boolean): void;
}>();

const form = useForm({
    name: '',
});

watch(
    () => props.open,
    (isOpen) => {
        if (isOpen && props.target) {
            form.reset();
            form.name = props.target.name;
        }
    },
);

function submit(): void {
    if (!props.target) {
        return;
    }
    const url =
        props.type === 'folder'
            ? `/admin/media/folders/${props.target.id}`
            : `/admin/media/files/${props.target.id}`;

    form.patch(url, {
        preserveScroll: true,
        onSuccess: () => emit('update:open', false),
    });
}
</script>

<template>
    <Dialog :open="open" @update:open="emit('update:open', $event)">
        <DialogContent>
            <DialogHeader>
                <DialogTitle>
                    Rename {{ type === 'folder' ? 'folder' : 'file' }}
                </DialogTitle>
            </DialogHeader>

            <form class="flex flex-col gap-4" @submit.prevent="submit">
                <div class="flex flex-col gap-2">
                    <Label for="rename-name">Name</Label>
                    <Input id="rename-name" v-model="form.name" autofocus />
                    <InputError :message="form.errors.name" />
                </div>

                <DialogFooter>
                    <Button
                        type="button"
                        variant="outline"
                        @click="emit('update:open', false)"
                    >
                        Cancel
                    </Button>
                    <Button type="submit" :disabled="form.processing">
                        Save
                    </Button>
                </DialogFooter>
            </form>
        </DialogContent>
    </Dialog>
</template>
