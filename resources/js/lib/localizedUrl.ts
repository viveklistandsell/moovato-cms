// The default locale lives at the URL root (no /<locale>/ prefix).
// Non-default locales keep their /<locale>/ prefix.
//
// Server already shares the active default via Inertia (`page.props.defaultLocale`),
// but we cache it as a constant so the helper can be called from pure functions
// without Vue context. Override via setDefaultLocale() during app bootstrap if
// the project ever changes its default.
let cachedDefault = 'de';

export function setDefaultLocale(code: string): void {
    cachedDefault = code;
}

export function getDefaultLocale(): string {
    return cachedDefault;
}

/**
 * Build a locale-aware URL.
 *  localizedUrl('de', '/blog')             → '/blog'
 *  localizedUrl('en', '/blog')             → '/en/blog'
 *  localizedUrl('de', '/blog/foo?bar=1')   → '/blog/foo?bar=1'
 *  localizedUrl('de', '/')                 → '/'
 */
export function localizedUrl(locale: string, path = '/'): string {
    const normalized = path.startsWith('/') ? path : `/${path}`;
    if (locale === cachedDefault) {
        return normalized;
    }
    if (normalized === '/') {
        return `/${locale}`;
    }
    return `/${locale}${normalized}`;
}
