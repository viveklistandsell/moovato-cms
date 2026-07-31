<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Coarse "is this a staff user?" gate for the admin area as a whole.
 *
 * After the move to Spatie roles + permissions, "admin" means "has at least
 * one assigned role" (Super Admin / Admin / Editor / Author). Specific
 * actions inside the admin area must layer additional `permission:*`
 * middleware on top of this — e.g. `permission:users.create` on the user
 * store route.
 *
 * Super Admin always passes thanks to the `Gate::before` registered in
 * AppServiceProvider, but we still check it explicitly here so the middleware
 * doesn't depend on the implicit Gate hook to function.
 */
final class EnsureUserIsAdmin
{
    /**
     * @param  Closure(Request): Response  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user === null) {
            abort(403);
        }

        if ($user->isSuperAdmin() || $user->roles()->exists()) {
            return $next($request);
        }

        abort(403);
    }
}
