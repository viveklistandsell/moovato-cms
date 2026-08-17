<?php

declare(strict_types=1);

namespace App\Http\Controllers\Partner\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Partner\Auth\ResetPasswordRequest;
use App\Models\CompanyUser;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

/**
 * Reset / set-initial-password flow.
 *
 * Same endpoint powers both:
 *   1. First-time "set your password" link from PartnerAccepted mail
 *      (approved application, password is NULL in the DB).
 *   2. Self-service password reset from ForgotPasswordController.
 *
 * A single Vue page renders for both flows — copy adapts based on
 * whether the account already has a password set.
 */
final class ResetPasswordController extends Controller
{
    public function create(Request $request, string $token): Response
    {
        $email = (string) $request->query('email', '');
        $user = $email !== ''
            ? CompanyUser::query()->where('email', $email)->first()
            : null;
        $isSetup = $user !== null && $user->password === null;

        return Inertia::render('partner/auth/ResetPassword', [
            'locale' => App::getLocale(),
            'token' => $token,
            'email' => $email,
            'isSetup' => $isSetup,
        ]);
    }

    public function store(ResetPasswordRequest $request): RedirectResponse
    {
        $status = Password::broker('company_users')->reset(
            $request->validated(),
            function (CompanyUser $user, string $password): void {
                $user->forceFill([
                    'password' => Hash::make($password),
                    'remember_token' => Str::random(60),
                    'email_verified_at' => $user->email_verified_at ?? now(),
                ])->save();

                event(new PasswordReset($user));
            }
        );

        if ($status !== Password::PasswordReset) {
            throw ValidationException::withMessages([
                'email' => [$this->messageForStatus($status)],
            ]);
        }

        $user = CompanyUser::query()->where('email', $request->validated('email'))->first();
        if ($user !== null && $user->status === CompanyUser::STATUS_APPROVED) {
            Auth::guard('company')->login($user);
            $request->session()->regenerate();

            return redirect()->route('partner.dashboard');
        }

        return redirect()
            ->route('partner.login')
            ->with('flash', ['success' => __('partner.auth.password_updated')]);
    }

    private function messageForStatus(string $status): string
    {
        return match ($status) {
            Password::InvalidToken => __('partner.auth.token_invalid'),
            Password::InvalidUser => __('partner.auth.token_invalid'),
            Password::Throttled => __('partner.auth.reset_throttled'),
            default => __('partner.auth.reset_failed'),
        };
    }
}
