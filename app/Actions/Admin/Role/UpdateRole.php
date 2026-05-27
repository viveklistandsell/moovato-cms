<?php

declare(strict_types=1);

namespace App\Actions\Admin\Role;

use App\Models\Permission;
use App\Models\Role;
use App\Services\ActivityLogger;
use Illuminate\Support\Facades\DB;

final readonly class UpdateRole
{
    public function __construct(private ActivityLogger $logger) {}

    /**
     * @param  array{
     *   name?: ?string,
     *   display_name?: ?string,
     *   description?: ?string,
     *   color: string,
     *   permissions?: array<int, int>|null,
     * }  $data
     */
    public function handle(Role $role, array $data): Role
    {
        return DB::transaction(function () use ($role, $data): Role {
            $original = [
                'display_name' => $role->display_name,
                'color' => $role->color,
                'permission_count' => $role->permissions()->count(),
            ];

            // Name is locked on system roles by the form request, so this
            // assignment is a no-op there.
            if (! $role->is_system && ! empty($data['name'])) {
                $role->name = $data['name'];
            }
            $role->display_name = $data['display_name'] ?? null;
            $role->description = $data['description'] ?? null;
            $role->color = $data['color'];
            $role->save();

            $permissionIds = $data['permissions'] ?? [];
            $permissions = Permission::query()->whereIn('id', $permissionIds)->get();
            $role->syncPermissions($permissions);

            $this->logger->updated(
                $role,
                "Updated role {$role->name}",
                [
                    'before' => $original,
                    'after' => [
                        'display_name' => $role->display_name,
                        'color' => $role->color,
                        'permission_count' => count($permissionIds),
                    ],
                ],
            );

            return $role;
        });
    }
}
