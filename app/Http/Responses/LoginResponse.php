<?php

declare(strict_types=1);

namespace App\Http\Responses;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Laravel\Fortify\Contracts\LoginResponse as LoginResponseContract;

/**
 * Always redirects authenticated users to the dashboard after login, ignoring
 * any intended URL stored by the auth middleware. The admin's first stop
 * post-login is the dashboard regardless of which protected page they were
 * trying to reach.
 */
final class LoginResponse implements LoginResponseContract
{
    public function toResponse($request): JsonResponse|RedirectResponse
    {
        /** @var Request $request */
        if ($request->wantsJson()) {
            return new JsonResponse('', 204);
        }

        return redirect('/dashboard');
    }
}
