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

        [$bypassIps, $redirectUrl] = $this->parseBypassList(
            (string) ($settings->maintenance_bypass_ips ?? '')
        );

        if ($this->ipIsAllowed($request, $bypassIps)) {
            return $next($request);
        }

        if ($redirectUrl !== null) {
            return redirect()->away($redirectUrl, 302);
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

    /**
     *
     * The FIRST URL in the
     * list wins — additional URLs after it are silently ignored, since
     * there's no useful interpretation of "redirect to two places at once".
     *
     * @return array{0: list<string>, 1: ?string}
     */
    private function parseBypassList(string $csv): array
    {
        if ($csv === '') {
            return [[], null];
        }

        $ips = [];
        $redirectUrl = null;

        foreach (explode(',', $csv) as $raw) {
            $entry = mb_trim($raw);
            if ($entry === '') {
                continue;
            }

            // 1) Explicit URL with scheme — pass through as-is.
            if (preg_match('~^https?://~i', $entry) === 1) {
                $redirectUrl ??= $entry;

                continue;
            }

            // 2) Valid IPv4 / IPv6 — add to bypass list.
            if (filter_var($entry, FILTER_VALIDATE_IP) !== false) {
                $ips[] = $entry;

                continue;
            }

            // 3) Bare host like "flipkart.com" — treat as https:// URL.
            //    Heuristic: contains a dot AND has only domain-safe chars
            //    (letters, digits, dots, hyphens). Keeps us from
            //    accidentally promoting partial IPs like "10.0.0" to URLs.
            if (str_contains($entry, '.') && preg_match('~^[a-zA-Z0-9.\-]+(/.*)?$~', $entry) === 1) {
                $redirectUrl ??= 'https://'.$entry;

                continue;
            }

            // 4) Unrecognised — drop silently. The admin will see the
            //    holding page (not a redirect, not a bypass) which is the
            //    safe default.
        }

        return [$ips, $redirectUrl];
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
     * @param  list<string>  $bypassIps  Parsed by parseBypassList().
     */
    private function ipIsAllowed(Request $request, array $bypassIps): bool
    {
        if ($bypassIps === []) {
            return false;
        }

        $ip = $request->ip();
        if ($ip === null) {
            return false;
        }

        return in_array($ip, $bypassIps, true);
    }
}
