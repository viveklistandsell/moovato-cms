<?php

declare(strict_types=1);

namespace App\Http\Controllers\Partner\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Partner\Auth\PartnerLoginRequest;
use App\Models\CompanyUser;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

/**
 * Partner login for the `company` guard.
 *
 * Uses the standard Auth::guard('company')->attempt() flow — every
 * failure is coerced into a ValidationException so the Inertia form
 * gets a single "email" error message and we never leak whether the
 * email exists (timing side-channel aside).
 */
final class LoginController extends Controller
{
    private const RATE_LIMIT_MAX_ATTEMPTS = 5;

    private const RATE_LIMIT_DECAY_SECONDS = 60;

    public function create(): Response
    {
        return Inertia::render('partner/auth/Login', [
            'locale' => App::getLocale(),
        ]);
    }

    public function store(PartnerLoginRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $throttleKey = $this->throttleKey($request, (string) $data['email']);

        if (RateLimiter::tooManyAttempts($throttleKey, self::RATE_LIMIT_MAX_ATTEMPTS)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            throw ValidationException::withMessages([
                'email' => __('partner.auth.throttle', ['seconds' => $seconds]),
            ]);
        }

        $user = CompanyUser::query()
            ->where('email', $data['email'])
            ->first();

        if (
            $user === null
            || $user->password === null
            || ! Hash::check((string) $data['password'], (string) $user->password)
        ) {
            RateLimiter::hit($throttleKey, self::RATE_LIMIT_DECAY_SECONDS);
            throw ValidationException::withMessages([
                'email' => __('partner.auth.invalid_credentials'),
            ]);
        }

        $this->assertCanLogin($user);

        RateLimiter::clear($throttleKey);
        Auth::guard('company')->login($user, (bool) ($data['remember'] ?? false));
        $request->session()->regenerate();

        return redirect()->route('partner.dashboard');
    }
    private function assertCanLogin(CompanyUser $user): void
    {
        if ($user->status === CompanyUser::STATUS_PENDING) {
            throw ValidationException::withMessages([
                'email' => __('partner.auth.status_pending'),
            ]);
        }
        if ($user->status === CompanyUser::STATUS_REJECTED) {
            throw ValidationException::withMessages([
                'email' => __('partner.auth.status_rejected'),
            ]);
        }
        if ($user->status === CompanyUser::STATUS_INACTIVE) {
            throw ValidationException::withMessages([
                'email' => __('partner.auth.status_inactive'),
            ]);
        }
    }

    private function throttleKey(Request $request, string $email): string
    {
        return 'partner-login:'.Str::lower($email).'|'.$request->ip();
    }
}
