<script setup lang="ts">
import { computed, onMounted, ref } from 'vue';
import { router } from '@inertiajs/vue3';
import { Upload, X } from 'lucide-vue-next';
import { Button } from '@/components/ui/button';
import { useChunkedUpload } from '@/composables/useChunkedUpload';

const props = defineProps<{
    folderId: number | null;
}>();

const dropzone = ref<HTMLElement | null>(null);
const browseTrigger = ref<HTMLElement | null>(null);
const dragOver = ref(false);

const { files, uploading, assignBrowse, assignDrop, clearCompleted } =
    useChunkedUpload({
        target: '/admin/media/files',
        testTarget: '/admin/media/files/upload',
        chunkSize: 5 * 1024 * 1024,
        folderId: props.folderId,
        onSuccess: () => {
            router.reload({ only: ['files', 'folders'] });
        },
    });

onMounted(() => {
    assignBrowse(browseTrigger.value);
    assignDrop(dropzone.value);
});

const totalProgress = computed(() => {
    if (files.value.length === 0) {
        return 0;
    }
    const sum = files.value.reduce((acc, f) => acc + f.progress, 0);
    return Math.round(sum / files.value.length);
});

function statusLabel(
    status: 'queued' | 'uploading' | 'done' | 'error',
): string {
    switch (status) {
        case 'queued':
            return 'Queued';
        case 'uploading':
            return 'Uploading';
        case 'done':
            return 'Done';
        case 'error':
            return 'Error';
    }
}
</script>

<template>
    <div class="flex flex-col gap-3">
        <div
            ref="dropzone"
            class="relative flex flex-col items-center justify-center gap-3 rounded-lg border-2 border-dashed border-border bg-muted/30 p-8 text-center transition-colors"
            :class="{ 'border-primary bg-primary/5': dragOver }"
            @dragenter.prevent="dragOver = true"
            @dragleave.prevent="dragOver = false"
            @drop.prevent="dragOver = false"
            @dragover.prevent
        >
            <Upload class="h-8 w-8 text-muted-foreground" />
            <div class="flex flex-col gap-1">
                <p class="text-sm font-medium">
                    Drop files here, or
                    <button
                        ref="browseTrigger"
                        type="button"
                        class="text-primary underline hover:no-underline"
                    >
                        browse
                    </button>
                </p>
                <p class="text-xs text-muted-foreground">
                    Any file type — images, PDF, Excel, Word, text, video,
                    audio, zip, etc. Up to 500&nbsp;MB per file. Chunked upload
                    — resumable on disconnect.
                </p>
            </div>
        </div>

        <div
            v-if="files.length > 0"
            class="flex flex-col gap-2 rounded-lg border border-border bg-card p-3"
        >
            <div class="flex items-center justify-between">
                <p class="text-xs font-medium">
                    {{ files.length }} file(s) — {{ totalProgress }}%
                </p>
                <Button
                    v-if="!uploading"
                    type="button"
                    size="sm"
                    variant="ghost"
                    @click="clearCompleted"
                >
                    Clear done
                </Button>
            </div>

            <div class="flex max-h-48 flex-col gap-1.5 overflow-y-auto">
                <div
                    v-for="file in files"
                    :key="file.id"
                    class="flex flex-col gap-1 rounded border border-border/60 p-2"
                >
                    <div
                        class="flex items-center justify-between gap-2 text-xs"
                    >
                        <span class="flex-1 truncate">{{ file.name }}</span>
                        <span
                            class="shrink-0 text-muted-foreground"
                            :class="{
                                'text-emerald-600': file.status === 'done',
                                'text-destructive': file.status === 'error',
                            }"
                        >
                            {{ statusLabel(file.status) }} —
                            {{ file.progress }}%
                        </span>
                    </div>
                    <div class="h-1 w-full overflow-hidden rounded bg-muted">
                        <div
                            class="h-full bg-primary transition-all"
                            :class="{
                                'bg-emerald-500': file.status === 'done',
                                'bg-destructive': file.status === 'error',
                            }"
                            :style="{ width: `${file.progress}%` }"
                        />
                    </div>
                    <p
                        v-if="file.error"
                        class="flex items-center gap-1 text-[11px] text-destructive"
                    >
                        <X class="h-3 w-3" /> {{ file.error }}
                    </p>
                </div>
            </div>
        </div>
    </div>
</template>
