<script setup lang="ts">
import { Image as ImageIcon, Upload, X } from 'lucide-vue-next';
import { ref } from 'vue';
import MediaPicker from '@/components/common/MediaPicker.vue';
import { Button } from '@/components/ui/button';

const props = defineProps<{
    path: string | null;
    url: string | null;
    label?: string;
    aspectClass?: string;
}>();

const emit = defineEmits<{
    (e: 'update', value: { path: string | null; url: string | null }): void;
}>();

const open = ref(false);

function onPick(file: { path: string; url: string; name: string }): void {
    emit('update', { path: file.path, url: file.url });
}

function clear(): void {
    emit('update', { path: null, url: null });
}
</script>

<template>
    <div class="space-y-2">
        <label v-if="label" class="text-sm font-medium">{{ label }}</label>
        <div
            class="flex items-center justify-center overflow-hidden rounded-md border border-dashed bg-muted"
            :class="aspectClass ?? 'aspect-video w-full'"
        >
            <img
                v-if="url"
                :src="url"
                alt=""
                class="size-full object-cover"
            />
            <ImageIcon v-else class="size-8 text-muted-foreground/40" />
        </div>
        <div class="flex items-center gap-2">
            <Button
                type="button"
                variant="outline"
                size="sm"
                class="flex-1"
                @click="open = true"
            >
                <Upload class="size-4" />
                {{ url ? 'Replace' : 'Pick image' }}
            </Button>
            <Button
                v-if="url"
                type="button"
                variant="ghost"
                size="sm"
                @click="clear"
            >
                <X class="size-4" />
            </Button>
        </div>
        <MediaPicker v-model:open="open" @pick="onPick" />
    </div>
</template>
