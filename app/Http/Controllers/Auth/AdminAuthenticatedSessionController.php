<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use Laravel\Fortify\Contracts\LogoutResponse;
use Laravel\Fortify\Http\Controllers\AuthenticatedSessionController as FortifyAuthenticatedSessionController;

/**
 * Admin logout — replaces Fortify's built-in destroy() so we don't
 * torch the parallel `company` guard session when an admin signs
 * out.
 *
 * Fortify's default flow:
 *   1. Auth::guard('web')->logout()
 *   2. $request->session()->invalidate()   ← nukes ALL session data
 *   3. $request->session()->regenerateToken()
 *
 * Both guards (`web` for admin, `company` for partners) share the
 * same underlying session store — each just stores its user id
 * under a guard-specific key (`login_web_…` vs. `login_company_…`).
 * Calling `invalidate()` blows away every key, so a signed-in
 * partner is silently logged out when the admin clicks "log out".
 *
 * We only need to remove the web guard's user + regenerate the
 * CSRF token; the company guard's session key is left intact.
 * PartnerLogoutController already mirrors this behavior for the
 * inverse direction.
 */
final class AdminAuthenticatedSessionController extends FortifyAuthenticatedSessionController
{
    public function destroy(Request $request): LogoutResponse
    {
        $this->guard->logout();

        if ($request->hasSession()) {
            $request->session()->regenerateToken();
        }

        return app(LogoutResponse::class);
    }
}
