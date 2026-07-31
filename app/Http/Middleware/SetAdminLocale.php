<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Models\Language;
use Carbon\Carbon;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

/**
 * Applies the authenticated admin's preferred UI locale.
 *
 * - Reads `users.admin_locale` (set via the header language switcher).
 * - Validates against the active `Language` table so a stale value
 *   on the user row (e.g. a language that's since been deactivated)
 *   falls back gracefully to the system default.
 * - Calls both `app()->setLocale()` (used by translation lookups,
 *   `__()`, and Inertia's shared `locale` prop) and
 *   `Carbon::setLocale()` (so date helpers like `diffForHumans()`
 *   render in the chosen language).
 * - Silent when there's no user / no preference — leaves the locale
 *   set by `SetLocale` (URL-driven, frontend) untouched.
 */
final class SetAdminLocale
{
    /**
     * @param  Closure(Request): Response  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user !== null && $user->admin_locale !== null && $user->admin_locale !== '') {
            $activeCodes = Cache::remember(
                'admin.active_locale_codes',
                300,
                fn (): array => Language::query()
                    ->where('status', true)
                    ->pluck('code')
                    ->all(),
            );

            if (in_array($user->admin_locale, $activeCodes, true)) {
                app()->setLocale($user->admin_locale);
                Carbon::setLocale($user->admin_locale);
            }
        }

        return $next($request);
    }
}
