<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Country;
use Illuminate\Database\Seeder;

/**
 * Seeds the single supported country (Germany). Idempotent — re-running
 * leaves existing rows alone. Add additional rows via the admin UI later
 * if you expand the directory beyond DE.
 */
final class CountrySeeder extends Seeder
{
    public function run(): void
    {
        Country::query()->updateOrCreate(
            ['iso_code' => 'DE'],
            [
                'name' => 'Deutschland',
                'phone_code' => '+49',
                'status' => 'published',
                'sort_order' => 1,
            ],
        );
    }
}
