<?php

declare(strict_types=1);

namespace App\Actions\Admin\Role;

use App\Models\Role;
use App\Services\ActivityLogger;
use RuntimeException;

/**
 * Soft guards: system roles are immutable, and a role can only be deleted
 * when no user is still assigned to it (otherwise those users would silently
 * lose every permission).
 */
final readonly class DeleteRole
{
    public function __construct(private ActivityLogger $logger) {}

    public function handle(Role $role): void
    {
        if ($role->is_system === true) {
            throw new RuntimeException('System roles cannot be deleted.');
        }

        $userCount = $role->users()->count();
        if ($userCount > 0) {
            throw new RuntimeException(
                "Cannot delete role: {$userCount} user(s) are still assigned to it.",
            );
        }

        $name = $role->name;
        $role->delete();

        $this->logger->deleted(
            $role,
            "Deleted role {$name}",
        );
    }
}
