<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Country;
use App\Models\State;
use Illuminate\Database\Seeder;

/**
 * Seeds the 16 German Bundesländer attached to the Germany country row
 * created by {@see CountrySeeder}. Idempotent — re-running updates existing
 * rows by (country_id, code) without duplicating.
 */
final class StateSeeder extends Seeder
{
    public function run(): void
    {
        $germany = Country::query()->where('iso_code', 'DE')->first();

        if ($germany === null) {
            $this->command?->warn('CountrySeeder must run before StateSeeder — Germany row missing.');

            return;
        }

        $states = [
            ['code' => 'BW', 'name' => 'Baden-Württemberg',     'permalink' => 'baden-wuerttemberg'],
            ['code' => 'BY', 'name' => 'Bayern',                'permalink' => 'bayern'],
            ['code' => 'BE', 'name' => 'Berlin',                'permalink' => 'berlin'],
            ['code' => 'BB', 'name' => 'Brandenburg',           'permalink' => 'brandenburg'],
            ['code' => 'HB', 'name' => 'Bremen',                'permalink' => 'bremen'],
            ['code' => 'HH', 'name' => 'Hamburg',               'permalink' => 'hamburg'],
            ['code' => 'HE', 'name' => 'Hessen',                'permalink' => 'hessen'],
            ['code' => 'MV', 'name' => 'Mecklenburg-Vorpommern', 'permalink' => 'mecklenburg-vorpommern'],
            ['code' => 'NI', 'name' => 'Niedersachsen',         'permalink' => 'niedersachsen'],
            ['code' => 'NW', 'name' => 'Nordrhein-Westfalen',   'permalink' => 'nordrhein-westfalen'],
            ['code' => 'RP', 'name' => 'Rheinland-Pfalz',       'permalink' => 'rheinland-pfalz'],
            ['code' => 'SL', 'name' => 'Saarland',              'permalink' => 'saarland'],
            ['code' => 'SN', 'name' => 'Sachsen',               'permalink' => 'sachsen'],
            ['code' => 'ST', 'name' => 'Sachsen-Anhalt',        'permalink' => 'sachsen-anhalt'],
            ['code' => 'SH', 'name' => 'Schleswig-Holstein',    'permalink' => 'schleswig-holstein'],
            ['code' => 'TH', 'name' => 'Thüringen',             'permalink' => 'thueringen'],
        ];

        foreach ($states as $index => $row) {
            State::query()->updateOrCreate(
                ['country_id' => $germany->id, 'code' => $row['code']],
                [
                    'name' => $row['name'],
                    'permalink' => $row['permalink'],
                    'status' => 'published',
                    'sort_order' => $index + 1,
                ],
            );
        }
    }
}
