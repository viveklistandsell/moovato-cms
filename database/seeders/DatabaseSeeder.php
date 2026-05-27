<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;

final class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            LanguageSeeder::class,
            // Order matters: permissions must exist before roles attach to them,
            // and roles must exist before legacy is_admin users are promoted.
            PermissionSeeder::class,
            RoleSeeder::class,
        ]);
    }
}
