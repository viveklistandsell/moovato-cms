import { router } from '@inertiajs/vue3';
import { toast } from 'vue-sonner';

/**
 * Payload shape produced server-side by `->with('toast', [...])`. `type`
 * maps to a vue-sonner method (`success`/`error`/`info`/`warning`) —
 * anything else falls back to a neutral `toast()` call.
 */
type ToastPayload = {
    type: 'success' | 'error' | 'info' | 'warning';
    message: string;
};

export function initializeFlashToast(): void {
    try {
        const raw = document.getElementById('app')?.dataset.page;
        if (raw) {
            const initial = JSON.parse(raw) as {
                props?: { flash?: { toast?: ToastPayload } };
            };
            emit(initial.props?.flash?.toast);
        }
    } catch {
    }

    router.on('success', (event) => {
        const page = (event as CustomEvent).detail?.page as
            | { props?: { flash?: { toast?: ToastPayload } } }
            | undefined;
        emit(page?.props?.flash?.toast);
    });
}

function emit(data: ToastPayload | undefined): void {
    if (!data || typeof data.message !== 'string' || data.message === '') {
        return;
    }

    const fn = (
        toast as unknown as Record<string, ((msg: string) => void) | undefined>
    )[data.type];

    if (typeof fn === 'function') {
        fn(data.message);
    } else {
        toast(data.message);
    }
}
