import { usePage } from '@inertiajs/vue3';
import { computed, type ComputedRef } from 'vue';

/**
 * Map the saved admin locale (e.g. `de`, `en`, `de_AT`) to a BCP-47 tag
 * suitable for `Intl` APIs. `de_AT` → `de-AT`. Falls back to the bare code.
 */
function toBcp47(locale: string): string {
    return locale.replace('_', '-');
}

/**
 * Returns the raw base language code (`'de'`, `'en'`, …) of the admin's
 * active locale — the segment before any region suffix. Use this to key
 * into `translations[code]` maps returned by admin controllers.
 *
 * Falls back to `'de'` (the Moovato default) when no locale is set.
 */
export function useAdminLanguage(): ComputedRef<string> {
    const page = usePage();
    return computed<string>(() => {
        const raw =
            (page.props as { adminLocale?: string | null }).adminLocale ??
            (page.props as { locale?: string | null }).locale ??
            'de';
        if (typeof raw !== 'string' || raw === '') {
            return 'de';
        }
        return raw.split(/[-_]/)[0].toLowerCase();
    });
}

/**
 * Returns a reactive BCP-47 locale tag matching the user's active admin
 * locale. Used to localize `Intl.DateTimeFormat` / `toLocaleString` output
 * so dates render in the same language as the rest of the admin UI.
 */
export function useAdminLocale(): ComputedRef<string> {
    const page = usePage();
    return computed<string>(() => {
        const fromUser = (page.props as { adminLocale?: string | null })
            .adminLocale;
        if (typeof fromUser === 'string' && fromUser !== '') {
            return toBcp47(fromUser);
        }
        const fromShared = (page.props as { locale?: string }).locale;
        return typeof fromShared === 'string' && fromShared !== ''
            ? toBcp47(fromShared)
            : 'de';
    });
}

/**
 * Format an ISO date string using the current admin locale. Returns
 * `'—'` for null/empty input so callers don't need to guard.
 */
export function useFormatDate(): (iso: string | null) => string {
    const locale = useAdminLocale();
    return (iso: string | null): string => {
        if (!iso) {
            return '—';
        }
        return new Date(iso).toLocaleDateString(locale.value, {
            year: 'numeric',
            month: 'short',
            day: 'numeric',
        });
    };
}

/**
 * Same as `useFormatDate` but includes hours + minutes. Used by activity
 * logs and email-log tables where time matters.
 */
export function useFormatDateTime(): (iso: string | null) => string {
    const locale = useAdminLocale();
    return (iso: string | null): string => {
        if (!iso) {
            return '—';
        }
        return new Date(iso).toLocaleString(locale.value, {
            day: '2-digit',
            month: 'short',
            year: 'numeric',
            hour: '2-digit',
            minute: '2-digit',
        });
    };
}
