<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Database\Seeder;
use Spatie\Permission\PermissionRegistrar;

/**
 * Idempotent seed of every permission the admin UI cares about. Permissions
 * are bucketed by `group` so the role form can display them as collapsible
 * sections (Users / Roles / Pages / Blog / Media / Settings).
 *
 * Naming convention: `<resource>.<verb>` e.g. `users.create`. New verbs that
 * the app uses must be added here AND seeded (running the seeder again is
 * safe — updateOrCreate keeps existing rows in sync).
 */
final class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        foreach (self::definitions() as $definition) {
            Permission::query()->updateOrCreate(
                ['name' => $definition['name'], 'guard_name' => 'web'],
                [
                    'display_name' => $definition['display_name'],
                    'group' => $definition['group'],
                ],
            );
        }

        // Bust the permission cache so freshly-seeded rows are visible without
        // an artisan cache:clear.
        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }

    /**
     * Full registry of permissions, grouped by module. Edit this list to add
     * or rename permissions — `php artisan db:seed --class=PermissionSeeder`
     * applies the diff (no destructive deletes; remove rows manually if needed).
     *
     * @return list<array{name: string, display_name: string, group: string}>
     */
    private static function definitions(): array
    {
        return [
            // Users
            ['name' => 'users.view', 'display_name' => 'View users', 'group' => 'Users'],
            ['name' => 'users.create', 'display_name' => 'Create users', 'group' => 'Users'],
            ['name' => 'users.update', 'display_name' => 'Update users', 'group' => 'Users'],
            ['name' => 'users.delete', 'display_name' => 'Delete users', 'group' => 'Users'],
            ['name' => 'users.ban', 'display_name' => 'Ban / unban users', 'group' => 'Users'],
            ['name' => 'users.reset-password', 'display_name' => 'Reset user passwords', 'group' => 'Users'],

            // Roles + permissions
            ['name' => 'roles.view', 'display_name' => 'View roles', 'group' => 'Roles'],
            ['name' => 'roles.create', 'display_name' => 'Create roles', 'group' => 'Roles'],
            ['name' => 'roles.update', 'display_name' => 'Update roles', 'group' => 'Roles'],
            ['name' => 'roles.delete', 'display_name' => 'Delete roles', 'group' => 'Roles'],
            ['name' => 'roles.assign', 'display_name' => 'Assign roles to users', 'group' => 'Roles'],

            // Pages
            ['name' => 'pages.view', 'display_name' => 'View pages', 'group' => 'Pages'],
            ['name' => 'pages.create', 'display_name' => 'Create pages', 'group' => 'Pages'],
            ['name' => 'pages.update', 'display_name' => 'Update pages', 'group' => 'Pages'],
            ['name' => 'pages.delete', 'display_name' => 'Delete pages', 'group' => 'Pages'],
            ['name' => 'pages.publish', 'display_name' => 'Publish / unpublish pages', 'group' => 'Pages'],

            // Blog
            ['name' => 'blog.view', 'display_name' => 'View blog posts', 'group' => 'Blog'],
            ['name' => 'blog.create', 'display_name' => 'Create blog posts', 'group' => 'Blog'],
            ['name' => 'blog.update', 'display_name' => 'Update blog posts', 'group' => 'Blog'],
            ['name' => 'blog.delete', 'display_name' => 'Delete blog posts', 'group' => 'Blog'],
            ['name' => 'blog.publish', 'display_name' => 'Publish / unpublish posts', 'group' => 'Blog'],

            // Media library
            ['name' => 'media.view', 'display_name' => 'Browse media', 'group' => 'Media'],
            ['name' => 'media.upload', 'display_name' => 'Upload files', 'group' => 'Media'],
            ['name' => 'media.delete', 'display_name' => 'Delete media', 'group' => 'Media'],

            // Navigation / menus
            ['name' => 'menus.view', 'display_name' => 'View menus', 'group' => 'Menus'],
            ['name' => 'menus.create', 'display_name' => 'Create menu items', 'group' => 'Menus'],
            ['name' => 'menus.update', 'display_name' => 'Update menu items', 'group' => 'Menus'],
            ['name' => 'menus.delete', 'display_name' => 'Delete menu items', 'group' => 'Menus'],

            // Directory / Locations (countries, states, cities)
            ['name' => 'locations.view', 'display_name' => 'View countries / states / cities', 'group' => 'Directory'],
            ['name' => 'locations.create', 'display_name' => 'Create countries / states / cities', 'group' => 'Directory'],
            ['name' => 'locations.update', 'display_name' => 'Update countries / states / cities', 'group' => 'Directory'],
            ['name' => 'locations.delete', 'display_name' => 'Delete countries / states / cities', 'group' => 'Directory'],

            // Services — service categories (and later listings / enquiries)
            ['name' => 'service_categories.view', 'display_name' => 'View service categories', 'group' => 'Services'],
            ['name' => 'service_categories.create', 'display_name' => 'Create service categories', 'group' => 'Services'],
            ['name' => 'service_categories.update', 'display_name' => 'Update service categories', 'group' => 'Services'],
            ['name' => 'service_categories.delete', 'display_name' => 'Delete service categories', 'group' => 'Services'],

            // Languages + global settings
            ['name' => 'settings.languages', 'display_name' => 'Manage languages', 'group' => 'Settings'],
            ['name' => 'settings.activity', 'display_name' => 'View activity log', 'group' => 'Settings'],
            ['name' => 'settings.site', 'display_name' => 'Manage site settings (footer, contact)', 'group' => 'Settings'],
            ['name' => 'settings.email', 'display_name' => 'Manage email / SMTP configuration', 'group' => 'Settings'],

            // System (cache, queue, logs)
            ['name' => 'system.cache', 'display_name' => 'Clear application caches', 'group' => 'System'],
        ];
    }
}
