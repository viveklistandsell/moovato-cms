<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Models\Language;
use App\Models\SiteSetting;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

/**
 * Site-Settings-driven holding-page gate. When `maintenance_enabled` is on
 * the public frontend renders a localised holding view; admin routes and
 * authenticated admins always pass through so the toggle can be flipped
 * without locking the operator out.
 */
final class MaintenanceMode
{
    public function handle(Request $request, Closure $next): Response
    {
        $settings = SiteSetting::current();

        if (! ($settings->maintenance_enabled ?? false)) {
            return $next($request);
        }

        // Admin shell, settings save, login + Fortify endpoints stay
        // reachable so the admin can flip the toggle off and so anyone
        // who needs to log in can still do so.
        $path = $request->path();
        if (
            $path === 'admin'
            || str_starts_with($path, 'admin/')
            || $path === 'login'
            || $path === 'logout'
            || str_starts_with($path, 'two-factor-challenge')
            || str_starts_with($path, 'settings/')
            || str_starts_with($path, '_boost')
            || str_starts_with($path, 'storage')
            || str_starts_with($path, 'build')
            || $path === 'sitemap.xml'
            || $path === 'robots.txt'
        ) {
            return $next($request);
        }

        // Admins always see the live site so they can verify content,
        // navigate as a normal visitor, and flip the toggle off from any
        // page. "Admin" mirrors EnsureUserIsAdmin: Super Admin OR any user
        // with at least one assigned role. Everyone else — anonymous OR
        // authenticated but role-less — sees the holding page.
        $user = $request->user();
        if ($user !== null && ($user->isSuperAdmin() || $user->roles()->exists())) {
            return $next($request);
        }

        if ($this->ipIsAllowed($request, (string) ($settings->maintenance_bypass_ips ?? ''))) {
            return $next($request);
        }

        // Resolve locale ourselves — this middleware runs in the global web
        // stack BEFORE the per-route `locale` middleware, so app()->getLocale()
        // would still hold the default config locale at this point. We mirror
        // the SetLocale logic: first URL segment vs. active language codes,
        // fall back to the configured default.
        $locale = $this->detectLocale($request);
        App::setLocale($locale);
        $translation = $settings->translation($locale);

        $logoUrl = $settings->logo_light_path
            ? '/storage/'.mb_ltrim($settings->logo_light_path, '/')
            : null;

        return response()->view('maintenance', [
            'heading' => $translation?->maintenance_heading ?: ($locale === 'de' ? 'Wir sind gleich zurück' : 'We will be right back'),
            'message' => $translation?->maintenance_message ?: ($locale === 'de' ? 'Die Seite ist kurz aufgrund von Wartungsarbeiten offline.' : 'The site is briefly down for scheduled maintenance.'),
            'siteName' => $settings->site_name ?: config('app.name', 'Site'),
            'siteTagline' => $translation?->site_tagline,
            'locale' => $locale,
            'logoUrl' => $logoUrl,
            'themeColor' => $settings->theme_color ?: '#0f172a',
        ], 503);
    }

    private function detectLocale(Request $request): string
    {
        $defaultCode = Cache::remember(
            'locales.default.code',
            300,
            fn () => Language::query()
                ->where('lang_is_default', true)
                ->where('status', true)
                ->value('code') ?? (string) config('app.locale', 'de'),
        );

        $activeCodes = Cache::remember(
            'locales.active.codes',
            300,
            fn () => Language::query()
                ->where('status', true)
                ->orderBy('sort_order')
                ->pluck('code')
                ->all(),
        );

        $firstSegment = explode('/', mb_trim($request->path(), '/'))[0] ?? '';

        if ($firstSegment !== '' && in_array($firstSegment, $activeCodes, true)) {
            return $firstSegment;
        }

        return (string) $defaultCode;
    }

    /**
     * Comma-separated allow-list, whitespace tolerant. Empty / unset
     * matches nothing.
     */
    private function ipIsAllowed(Request $request, string $csv): bool
    {
        if ($csv === '') {
            return false;
        }

        $ip = $request->ip();
        if ($ip === null) {
            return false;
        }

        foreach (explode(',', $csv) as $allowed) {
            if (mb_trim($allowed) === $ip) {
                return true;
            }
        }

        return false;
    }
}
