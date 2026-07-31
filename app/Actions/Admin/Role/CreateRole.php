<?php

declare(strict_types=1);

namespace App\Actions\Admin\Role;

use App\Models\Permission;
use App\Models\Role;
use App\Services\ActivityLogger;
use Illuminate\Support\Facades\DB;

final readonly class CreateRole
{
    public function __construct(private ActivityLogger $logger) {}

    /**
     * @param  array{
     *   name: string,
     *   display_name?: ?string,
     *   description?: ?string,
     *   color: string,
     *   permissions?: array<int, int>|null,
     * }  $data
     */
    public function handle(array $data): Role
    {
        return DB::transaction(function () use ($data): Role {
            /** @var Role $role */
            $role = Role::query()->create([
                'name' => $data['name'],
                'guard_name' => 'web',
                'display_name' => $data['display_name'] ?? null,
                'description' => $data['description'] ?? null,
                'color' => $data['color'],
                'is_system' => false,
            ]);

            $permissionIds = $data['permissions'] ?? [];
            if ($permissionIds !== []) {
                $permissions = Permission::query()->whereIn('id', $permissionIds)->get();
                $role->syncPermissions($permissions);
            }

            $this->logger->created(
                $role,
                "Created role {$role->name}",
                ['permissions' => count($permissionIds)],
            );

            return $role;
        });
    }
}
