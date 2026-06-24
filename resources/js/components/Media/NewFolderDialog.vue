<script setup lang="ts">
import { ref, watch } from 'vue';
import { useForm } from '@inertiajs/vue3';
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Button } from '@/components/ui/button';
import InputError from '@/components/InputError.vue';
import { useT } from '@/composables/useT';

const t = useT();

const props = defineProps<{
    open: boolean;
    parentId: number | null;
}>();

const emit = defineEmits<{
    (e: 'update:open', value: boolean): void;
}>();

const form = useForm({
    name: '',
    parent_id: props.parentId,
});

watch(
    () => props.open,
    (isOpen) => {
        if (isOpen) {
            form.reset();
            form.parent_id = props.parentId;
        }
    },
);

function submit(): void {
    form.post('/admin/media/folders', {
        preserveScroll: true,
        onSuccess: () => emit('update:open', false),
    });
}
</script>

<template>
    <Dialog :open="open" @update:open="emit('update:open', $event)">
        <DialogContent>
            <DialogHeader>
                <DialogTitle>{{ t('media.new_folder_title') }}</DialogTitle>
                <DialogDescription>
                    {{
                        parentId
                            ? t('media.new_folder_in_current')
                            : t('media.new_folder_at_root')
                    }}
                </DialogDescription>
            </DialogHeader>

            <form class="flex flex-col gap-4" @submit.prevent="submit">
                <div class="flex flex-col gap-2">
                    <Label for="folder-name">{{ t('media.name_label') }}</Label>
                    <Input
                        id="folder-name"
                        v-model="form.name"
                        autofocus
                        :placeholder="t('media.name_placeholder')"
                    />
                    <InputError :message="form.errors.name" />
                </div>

                <DialogFooter>
                    <Button
                        type="button"
                        variant="outline"
                        @click="emit('update:open', false)"
                    >
                        {{ t('media.cancel') }}
                    </Button>
                    <Button type="submit" :disabled="form.processing">
                        {{ t('media.create_button') }}
                    </Button>
                </DialogFooter>
            </form>
        </DialogContent>
    </Dialog>
</template>
