<?php

declare(strict_types=1);

namespace App\Actions\Admin\User;

use App\Enums\UserStatus;
use App\Models\User;
use App\Services\ActivityLogger;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use RuntimeException;

/**
 * Bulk activate / deactivate / ban / delete for the user list. Same guards
 * as the single-user actions:
 *  - the actor's own id is silently dropped from the target set
 *  - the last Super Admin can never be deleted (throws)
 *
 * Returns the number of rows that were actually mutated, for the toast.
 */
final readonly class BulkUserAction
{
    public function __construct(private ActivityLogger $logger) {}

    /**
     * @param  list<int>  $ids
     */
    public function handle(string $action, array $ids, User $actor): int
    {
        $targetIds = array_values(array_filter(
            $ids,
            static fn (int $id): bool => $id !== $actor->getKey(),
        ));

        if ($targetIds === []) {
            return 0;
        }

        return DB::transaction(function () use ($action, $targetIds): int {
            $users = User::query()->whereIn('id', $targetIds)->get();

            return match ($action) {
                'activate' => $this->setStatus($users, UserStatus::Active),
                'deactivate' => $this->setStatus($users, UserStatus::Inactive),
                'ban' => $this->setStatus($users, UserStatus::Banned),
                'delete' => $this->bulkDelete($users),
                default => 0,
            };
        });
    }

    /**
     * @param  Collection<int, User>  $users
     */
    private function setStatus($users, UserStatus $status): int
    {
        $count = $users->count();
        if ($count === 0) {
            return 0;
        }

        User::query()
            ->whereIn('id', $users->modelKeys())
            ->update(['status' => $status->value]);

        $this->logger->log(
            'user.bulk-status',
            "Set status={$status->value} for {$count} user(s)",
            null,
            ['ids' => $users->modelKeys(), 'status' => $status->value],
        );

        return $count;
    }

    /**
     * @param  Collection<int, User>  $users
     */
    private function bulkDelete($users): int
    {
        // Guard the last Super Admin: refuse the whole batch if it would
        // wipe out every remaining super admin.
        $superAdminIdsBeingDeleted = $users
            ->filter(static fn (User $u): bool => $u->hasRole(User::SUPER_ADMIN_ROLE))
            ->modelKeys();

        if ($superAdminIdsBeingDeleted !== [] && $this->wouldDrainSuperAdmins($superAdminIdsBeingDeleted)) {
            throw new RuntimeException('Cannot delete the last Super Admin.');
        }

        $count = $users->count();
        foreach ($users as $user) {
            $user->delete();
        }

        $this->logger->log(
            'user.bulk-delete',
            "Deleted {$count} user(s)",
            null,
            ['ids' => $users->modelKeys()],
        );

        return $count;
    }

    /**
     * @param  list<int>  $idsBeingDeleted
     */
    private function wouldDrainSuperAdmins(array $idsBeingDeleted): bool
    {
        return User::query()
            ->role(User::SUPER_ADMIN_ROLE)
            ->whereNotIn('id', $idsBeingDeleted)
            ->doesntExist();
    }
}
