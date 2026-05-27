<?php

declare(strict_types=1);

namespace App\Actions\Admin\User;

use App\Models\User;
use App\Services\ActivityLogger;

final readonly class ResetUserPassword
{
    public function __construct(private ActivityLogger $logger) {}

    public function handle(User $user, string $newPassword): void
    {
        // Hashed via the User model's `password` cast.
        $user->forceFill(['password' => $newPassword])->save();

        $this->logger->log(
            'user.password-reset',
            "Reset password for {$user->email}",
            $user,
        );
    }
}
