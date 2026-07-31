import { usePage } from '@inertiajs/vue3';

type Dict = Record<string, unknown>;

/**
 * Admin translation helper. Returns a `t(key, replacements?)` function
 * that looks up dot-notation keys (e.g. `'sidebar.dashboard'`) in the
 * `translations` shared prop populated by HandleInertiaRequests.
 *
 * Unknown keys fall back to the key itself so missing translations
 * are visible at a glance during development without throwing.
 *
 * Placeholders use Laravel's `:name` syntax to match the lang files
 * exactly — `t('dashboard.by_author', { author: 'Vivek' })`.
 *
 * Usage:
 *   const t = useT();
 *   t('sidebar.dashboard');                            // → 'Übersicht'
 *   t('dashboard.by_author', { author: 'Vivek' });     // → 'von Vivek'
 */
export function useT(): (
    key: string,
    replacements?: Record<string, string | number>,
) => string {
    const page = usePage();

    return (key: string, replacements: Record<string, string | number> = {}): string => {
        const dict = (page.props as { translations?: Dict }).translations ?? {};
        const value = resolve(dict, key);
        if (typeof value !== 'string') {
            return key;
        }
        // Laravel-style pluralization: "singular|plural" picks based on the
        // `count` replacement. 1 → singular, anything else → plural.
        const picked = pickPlural(value, replacements.count);
        return Object.entries(replacements).reduce<string>(
            (acc, [name, val]) => acc.replaceAll(`:${name}`, String(val)),
            picked,
        );
    };
}

function pickPlural(value: string, count: string | number | undefined): string {
    if (!value.includes('|')) {
        return value;
    }
    const [singular, plural] = value.split('|', 2);
    const n = typeof count === 'string' ? Number(count) : count;
    return n === 1 ? singular : (plural ?? singular);
}

function resolve(dict: Dict, dotKey: string): unknown {
    const parts = dotKey.split('.');
    let cursor: unknown = dict;
    for (const part of parts) {
        if (cursor !== null && typeof cursor === 'object' && part in (cursor as Dict)) {
            cursor = (cursor as Dict)[part];
        } else {
            return undefined;
        }
    }
    return cursor;
}
