<?php

declare(strict_types=1);

namespace App\Http\Controllers\Partner\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Partner\Auth\ForgotPasswordRequest;
use App\Mail\PartnerPasswordReset;
use App\Models\CompanyUser;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;
use Inertia\Inertia;
use Inertia\Response;
use Throwable;

/**
 * Self-service "I forgot my password" flow.
 *
 * Only APPROVED partners can trigger a reset link — pending /
 * rejected / inactive rows silently succeed (same "we sent you an
 * email" screen) so we don't disclose their status to a stranger.
 */
final class ForgotPasswordController extends Controller
{
    public function create(): Response
    {
        return Inertia::render('partner/auth/ForgotPassword', [
            'locale' => App::getLocale(),
        ]);
    }

    public function store(ForgotPasswordRequest $request): RedirectResponse
    {
        $email = (string) $request->validated('email');

        $user = CompanyUser::query()->where('email', $email)->first();

        if ($user !== null && $user->status === CompanyUser::STATUS_APPROVED) {
            $token = Password::broker('company_users')->createToken($user);
            $resetUrl = url('/partner/reset-password/'.$token.'?email='.urlencode($user->email));

            try {
                Mail::to($user->email)->send(new PartnerPasswordReset($user, $resetUrl));
            } catch (Throwable $e) {
                report($e);
            }
        }

        return redirect()
            ->route('partner.password.request')
            ->with('flash', ['success' => __('partner.auth.forgot_sent')]);
    }
}
