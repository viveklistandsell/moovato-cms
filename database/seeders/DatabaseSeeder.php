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
            PermissionSeeder::class,
            RoleSeeder::class,
            MenuSeeder::class,
            SiteSettingSeeder::class,
            EmailSettingSeeder::class,
            CountrySeeder::class,
            StateSeeder::class,
            CitySeeder::class,
            ServiceParentCategorySeeder::class,
            ServiceCategorySeeder::class,
        ]);
    }
}
