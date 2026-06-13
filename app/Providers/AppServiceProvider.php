<?php

declare(strict_types=1);

namespace App\Providers;

use App\Listeners\RecordSuccessfulLogin;
use App\Models\SiteSetting;
use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Auth\Events\Login;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;
use Throwable;

final class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->overrideSessionLifetime();
    }
    private function overrideSessionLifetime(): void
    {
        try {
            $configured = SiteSetting::current()->session_lifetime_minutes ?? null;
        } catch (Throwable) {
            return;
        }

        $value = (int) $configured;

        // 5 minutes minimum (anything shorter logs admins out mid-task);
        // 1 week ceiling (going beyond invites security audit findings on
        // "session lifetime exceeds policy"). Out-of-range → ignore.
        if ($value < 5 || $value > 10_080) {
            return;
        }

        config(['session.lifetime' => $value]);
    }

    public function boot(): void
    {
        $this->configureDefaults();
        $this->configureAuthorization();
        $this->registerEventListeners();
    }

    /**
     * Configure default behaviors for production-ready applications.
     */
    private function configureDefaults(): void
    {
        Date::use(CarbonImmutable::class);

        DB::prohibitDestructiveCommands(
            app()->isProduction(),
        );

        Password::defaults(fn (): ?Password => app()->isProduction()
            ? Password::min(12)
                ->mixedCase()
                ->letters()
                ->numbers()
                ->symbols()
                ->uncompromised()
            : null,
        );
    }

    /**
     * Grant Super Admin role a free pass on every Gate / can() check. This
     * keeps fine-grained permission checks in controllers and policies clean
     * — we never have to special-case the super admin downstream.
     */
    private function configureAuthorization(): void
    {
        Gate::before(static function (User $user, string $ability): ?bool {
            return $user->isSuperAdmin() ? true : null;
        });
    }

    private function registerEventListeners(): void
    {
        Event::listen(Login::class, RecordSuccessfulLogin::class);
    }
}
