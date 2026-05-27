<?php

declare(strict_types=1);

namespace App\Actions\Admin\User;

use App\Models\User;
use App\Services\ActivityLogger;
use RuntimeException;

/**
 * Soft-deletes a user with two guards:
 *  - cannot delete yourself (avoids locking the actor out)
 *  - cannot delete the last Super Admin (prevents bricking the system)
 *
 * Caller passes the actor explicitly so this stays unit-testable.
 */
final readonly class DeleteUser
{
    public function __construct(private ActivityLogger $logger) {}

    public function handle(User $user, User $actor): void
    {
        if ($user->getKey() === $actor->getKey()) {
            throw new RuntimeException('You cannot delete your own account.');
        }

        if ($user->hasRole(User::SUPER_ADMIN_ROLE) && $this->lastSuperAdmin($user)) {
            throw new RuntimeException('Cannot delete the last Super Admin.');
        }

        $email = $user->email;
        $user->delete();

        $this->logger->deleted(
            $user,
            "Deleted user {$email}",
        );
    }

    private function lastSuperAdmin(User $user): bool
    {
        return User::query()
            ->role(User::SUPER_ADMIN_ROLE)
            ->whereKeyNot($user->getKey())
            ->doesntExist();
    }
}
