import { onMounted, onUnmounted, readonly, ref, type Ref } from 'vue';
import type { WidgetInstance } from '@/widgets/types';

/**
 * Browser-clipboard helper for the page widget builder. Stores a single
 * widget snapshot in `localStorage` so admins can copy from one page and
 * paste onto another (same browser).
 */

const STORAGE_KEY = 'moovato.widgetClipboard';
const ENVELOPE_VERSION = 1;

type Envelope = {
    version: number;
    copiedAt: string;
    widget: WidgetInstance;
};

function stripIdentityFields(widget: WidgetInstance): WidgetInstance {
    return {
        type: widget.type,
        position: 0,
        is_active: widget.is_active ?? true,
        settings: { ...(widget.settings ?? {}) },
        translations: { ...(widget.translations ?? {}) },
        visibility: {
            desktop: widget.visibility?.desktop ?? true,
            tablet: widget.visibility?.tablet ?? true,
            mobile: widget.visibility?.mobile ?? true,
        },
        css_class: widget.css_class ?? '',
        id: null,
    };
}

function parseEnvelope(raw: string | null): WidgetInstance | null {
    if (raw === null || raw === '') {
        return null;
    }
    try {
        const parsed = JSON.parse(raw) as Partial<Envelope>;
        if (
            parsed.version !== ENVELOPE_VERSION ||
            typeof parsed.widget !== 'object' ||
            parsed.widget === null ||
            typeof (parsed.widget as WidgetInstance).type !== 'string'
        ) {
            return null;
        }
        return parsed.widget as WidgetInstance;
    } catch {
        // Corrupted JSON in localStorage — treat as empty clipboard rather
        // than crashing the canvas. The next copy will overwrite it.
        return null;
    }
}

/**
 * Reactive widget clipboard. The `contents` ref auto-updates when other
 * tabs write to the same localStorage key, and when the current tab calls
 * `copy()` or `clear()`.
 */
export function useWidgetClipboard(): {
    contents: Readonly<Ref<WidgetInstance | null>>;
    copy: (widget: WidgetInstance) => void;
    clear: () => void;
    /** Current contents — `null` if the clipboard is empty. */
    read: () => WidgetInstance | null;
} {
    const contents = ref<WidgetInstance | null>(null);

    function refresh(): void {
        if (typeof window === 'undefined') {
            return;
        }
        contents.value = parseEnvelope(window.localStorage.getItem(STORAGE_KEY));
    }

    function copy(widget: WidgetInstance): void {
        if (typeof window === 'undefined') {
            return;
        }
        const envelope: Envelope = {
            version: ENVELOPE_VERSION,
            copiedAt: new Date().toISOString(),
            widget: stripIdentityFields(widget),
        };
        window.localStorage.setItem(STORAGE_KEY, JSON.stringify(envelope));
        // localStorage's `storage` event only fires in OTHER tabs, so we
        // refresh the local ref ourselves to keep this tab's UI in sync.
        refresh();
    }

    function clear(): void {
        if (typeof window === 'undefined') {
            return;
        }
        window.localStorage.removeItem(STORAGE_KEY);
        refresh();
    }

    function read(): WidgetInstance | null {
        if (typeof window === 'undefined') {
            return null;
        }
        return parseEnvelope(window.localStorage.getItem(STORAGE_KEY));
    }

    function onStorage(event: StorageEvent): void {
        if (event.key !== STORAGE_KEY) {
            return;
        }
        refresh();
    }

    onMounted(() => {
        refresh();
        window.addEventListener('storage', onStorage);
    });

    onUnmounted(() => {
        if (typeof window !== 'undefined') {
            window.removeEventListener('storage', onStorage);
        }
    });

    return {
        contents: readonly(contents),
        copy,
        clear,
        read,
    };
}
