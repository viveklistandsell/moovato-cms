<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Models\User;
use App\Services\ActivityLogger;
use Illuminate\Auth\Events\Login;
use Illuminate\Support\Facades\Request;

/**
 * Fires on every successful Fortify / Auth login. Bumps the user's login
 * telemetry (last_login_at, last_login_ip, login_count) and drops an
 * `user.logged-in` activity log row.
 *
 * Wired up in App\Providers\AppServiceProvider::boot().
 */
final readonly class RecordSuccessfulLogin
{
    public function __construct(private ActivityLogger $logger) {}

    public function handle(Login $event): void
    {
        $user = $event->user;
        if (! $user instanceof User) {
            return;
        }

        $user->forceFill([
            'last_login_at' => now(),
            'last_login_ip' => Request::ip(),
            'login_count' => $user->login_count + 1,
        ])->saveQuietly();

        $this->logger->log(
            'user.logged-in',
            "{$user->name} logged in",
            $user,
        );
    }
}
