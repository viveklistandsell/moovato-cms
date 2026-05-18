import { ref, type Ref } from 'vue';
import Resumable from 'resumablejs';

export type UploaderFile = {
    id: string;
    name: string;
    size: number;
    progress: number;
    status: 'queued' | 'uploading' | 'done' | 'error';
    error?: string;
};

export type UseChunkedUploadOptions = {
    target: string;
    testTarget?: string;
    chunkSize?: number;
    /**
     * Folder id to attach uploaded files to. Accepts a static value OR a
     * getter — when a getter is supplied, Resumable reads the current value
     * on every chunk POST, so navigating folders mid-session targets the
     * folder currently visible to the user.
     */
    folderId?: number | null | (() => number | null);
    onSuccess?: () => void;
};

export function useChunkedUpload(options: UseChunkedUploadOptions) {
    const files: Ref<UploaderFile[]> = ref([]);
    const uploading = ref(false);

    const csrf =
        document.querySelector<HTMLMetaElement>('meta[name="csrf-token"]')
            ?.content ?? '';

    const currentFolderId = (): number | null => {
        if (typeof options.folderId === 'function') {
            return options.folderId();
        }
        return options.folderId ?? null;
    };

    const resumable = new Resumable({
        target: options.target,
        testTarget: options.testTarget ?? options.target,
        chunkSize: options.chunkSize ?? 5 * 1024 * 1024,
        simultaneousUploads: 1,
        testChunks: true,
        throttleProgressCallbacks: 0.5,
        headers: {
            'X-CSRF-TOKEN': csrf,
            Accept: 'application/json',
        },
        query: () => ({
            folder_id: currentFolderId() ?? '',
        }),
    });

    function findOrCreateFileEntry(rFile: Resumable.ResumableFile): UploaderFile {
        let entry = files.value.find((f) => f.id === rFile.uniqueIdentifier);
        if (!entry) {
            entry = {
                id: rFile.uniqueIdentifier,
                name: rFile.fileName,
                size: rFile.size,
                progress: 0,
                status: 'queued',
            };
            files.value.push(entry);
        }
        return entry;
    }

    resumable.on('fileAdded', (rFile: Resumable.ResumableFile) => {
        findOrCreateFileEntry(rFile);
        if (!uploading.value) {
            uploading.value = true;
            resumable.upload();
        }
    });

    resumable.on('fileProgress', (rFile: Resumable.ResumableFile) => {
        const entry = findOrCreateFileEntry(rFile);
        entry.status = 'uploading';
        entry.progress = Math.round(rFile.progress(false) * 100);
    });

    resumable.on('fileSuccess', (rFile: Resumable.ResumableFile) => {
        const entry = findOrCreateFileEntry(rFile);
        entry.progress = 100;
        entry.status = 'done';
    });

    resumable.on(
        'fileError',
        (rFile: Resumable.ResumableFile, message: string) => {
            const entry = findOrCreateFileEntry(rFile);
            entry.status = 'error';
            entry.error = message || 'Upload failed';
        },
    );

    resumable.on('complete', () => {
        uploading.value = false;
        options.onSuccess?.();
    });

    function assignBrowse(el: HTMLElement | null | undefined) {
        if (el) {
            resumable.assignBrowse(el, false);
        }
    }

    function assignDrop(el: HTMLElement | null | undefined) {
        if (el) {
            resumable.assignDrop(el);
        }
    }

    function clearCompleted() {
        files.value = files.value.filter((f) => f.status !== 'done');
    }

    function cancelAll() {
        resumable.cancel();
        files.value = [];
        uploading.value = false;
    }

    return {
        files,
        uploading,
        assignBrowse,
        assignDrop,
        clearCompleted,
        cancelAll,
    };
}
