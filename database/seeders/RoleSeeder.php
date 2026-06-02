<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\PermissionRegistrar;

/**
 * Seeds the four built-in roles and re-syncs their permissions.
 *
 * `Super Admin` is special: the Gate::before binding in AppServiceProvider
 * short-circuits every check for this role, so its explicit permission set
 * is informational only — useful for showing "all permissions" in the role
 * detail view.
 *
 * `is_system = true` rows are locked against deletion in the role admin UI.
 */
final class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $allPermissionNames = Permission::query()->pluck('name')->all();

        foreach (self::definitions() as $def) {
            /** @var Role $role */
            $role = Role::query()->updateOrCreate(
                ['name' => $def['name'], 'guard_name' => 'web'],
                [
                    'display_name' => $def['display_name'],
                    'description' => $def['description'],
                    'color' => $def['color'],
                    'is_system' => $def['is_system'],
                ],
            );

            $names = $def['permissions'] === '*' ? $allPermissionNames : $def['permissions'];
            $permissions = Permission::query()->whereIn('name', $names)->get();
            $role->syncPermissions($permissions);
        }

        // Legacy is_admin → Super Admin promotion is handled by the
        // `drop_is_admin_from_users_table` migration (it runs first and is
        // idempotent), so nothing to do here besides bust the cache.
        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }

    /**
     * @return list<array{name: string, display_name: string, description: string, color: string, is_system: bool, permissions: list<string>|'*'}>
     */
    private static function definitions(): array
    {
        return [
            [
                'name' => User::SUPER_ADMIN_ROLE,
                'display_name' => 'Super Admin',
                'description' => 'Unrestricted access. Bypasses all permission checks.',
                'color' => 'rose',
                'is_system' => true,
                'permissions' => '*',
            ],
            [
                'name' => 'Admin',
                'display_name' => 'Admin',
                'description' => 'Full content + user management, but no settings.',
                'color' => 'amber',
                'is_system' => true,
                'permissions' => [
                    'users.view', 'users.create', 'users.update', 'users.delete', 'users.ban', 'users.reset-password',
                    'roles.view', 'roles.assign',
                    'pages.view', 'pages.create', 'pages.update', 'pages.delete', 'pages.publish',
                    'blog.view', 'blog.create', 'blog.update', 'blog.delete', 'blog.publish',
                    'media.view', 'media.upload', 'media.delete',
                    'menus.view', 'menus.create', 'menus.update', 'menus.delete',
                    'settings.activity',
                ],
            ],
            [
                'name' => 'Editor',
                'display_name' => 'Editor',
                'description' => 'Manage and publish content; cannot change users or roles.',
                'color' => 'sky',
                'is_system' => true,
                'permissions' => [
                    'pages.view', 'pages.create', 'pages.update', 'pages.publish',
                    'blog.view', 'blog.create', 'blog.update', 'blog.publish',
                    'media.view', 'media.upload',
                    'menus.view', 'menus.update',
                ],
            ],
            [
                'name' => 'Author',
                'display_name' => 'Author',
                'description' => 'Draft content; cannot publish or delete.',
                'color' => 'emerald',
                'is_system' => true,
                'permissions' => [
                    'pages.view', 'pages.create', 'pages.update',
                    'blog.view', 'blog.create', 'blog.update',
                    'media.view', 'media.upload',
                ],
            ],
        ];
    }
}
