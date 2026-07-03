<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\City;
use App\Models\State;
use Illuminate\Database\Seeder;

/**
 * Seeds the 25 largest German cities, grouped by Bundesland, with the
 * `is_popular` flag set so the public homepage's "Beliebte Städte" widget
 * can highlight them. Idempotent — re-running updates by (state_id, permalink).
 *
 * Add more cities later via the admin UI (Bulk CSV import in Phase 1b).
 */
final class CitySeeder extends Seeder
{
    public function run(): void
    {
        $states = State::query()
            ->whereHas('country', fn ($q) => $q->where('iso_code', 'DE'))
            ->get()
            ->keyBy('code');

        if ($states->isEmpty()) {
            $this->command?->warn('StateSeeder must run before CitySeeder — no German states found.');

            return;
        }

        // Top German cities by population, grouped by state code.
        // is_popular = true for the 12 largest; rest are shown only when
        // filtering by their Bundesland.
        $cities = [
            // Berlin (city-state)
            ['state' => 'BE', 'name' => 'Berlin',         'permalink' => 'berlin',         'postal_code' => '10115', 'is_popular' => true],
            // Hamburg (city-state)
            ['state' => 'HH', 'name' => 'Hamburg',        'permalink' => 'hamburg',        'postal_code' => '20095', 'is_popular' => true],
            // Bayern
            ['state' => 'BY', 'name' => 'München',        'permalink' => 'muenchen',       'postal_code' => '80331', 'is_popular' => true],
            ['state' => 'BY', 'name' => 'Nürnberg',       'permalink' => 'nuernberg',      'postal_code' => '90402', 'is_popular' => true],
            ['state' => 'BY', 'name' => 'Augsburg',       'permalink' => 'augsburg',       'postal_code' => '86150', 'is_popular' => false],
            // Baden-Württemberg
            ['state' => 'BW', 'name' => 'Stuttgart',      'permalink' => 'stuttgart',      'postal_code' => '70173', 'is_popular' => true],
            ['state' => 'BW', 'name' => 'Mannheim',       'permalink' => 'mannheim',       'postal_code' => '68159', 'is_popular' => false],
            ['state' => 'BW', 'name' => 'Karlsruhe',      'permalink' => 'karlsruhe',      'postal_code' => '76131', 'is_popular' => false],
            ['state' => 'BW', 'name' => 'Freiburg',       'permalink' => 'freiburg',       'postal_code' => '79098', 'is_popular' => false],
            // Nordrhein-Westfalen
            ['state' => 'NW', 'name' => 'Köln',           'permalink' => 'koeln',          'postal_code' => '50667', 'is_popular' => true],
            ['state' => 'NW', 'name' => 'Düsseldorf',     'permalink' => 'duesseldorf',    'postal_code' => '40213', 'is_popular' => true],
            ['state' => 'NW', 'name' => 'Dortmund',       'permalink' => 'dortmund',       'postal_code' => '44135', 'is_popular' => true],
            ['state' => 'NW', 'name' => 'Essen',          'permalink' => 'essen',          'postal_code' => '45127', 'is_popular' => true],
            ['state' => 'NW', 'name' => 'Bochum',         'permalink' => 'bochum',         'postal_code' => '44787', 'is_popular' => false],
            ['state' => 'NW', 'name' => 'Bonn',           'permalink' => 'bonn',           'postal_code' => '53111', 'is_popular' => false],
            // Hessen
            ['state' => 'HE', 'name' => 'Frankfurt am Main', 'permalink' => 'frankfurt-am-main', 'postal_code' => '60311', 'is_popular' => true],
            ['state' => 'HE', 'name' => 'Wiesbaden',      'permalink' => 'wiesbaden',      'postal_code' => '65183', 'is_popular' => false],
            // Sachsen
            ['state' => 'SN', 'name' => 'Dresden',        'permalink' => 'dresden',        'postal_code' => '01067', 'is_popular' => true],
            ['state' => 'SN', 'name' => 'Leipzig',        'permalink' => 'leipzig',        'postal_code' => '04109', 'is_popular' => true],
            // Niedersachsen
            ['state' => 'NI', 'name' => 'Hannover',       'permalink' => 'hannover',       'postal_code' => '30159', 'is_popular' => true],
            ['state' => 'NI', 'name' => 'Braunschweig',   'permalink' => 'braunschweig',   'postal_code' => '38100', 'is_popular' => false],
            // Bremen (city-state)
            ['state' => 'HB', 'name' => 'Bremen',         'permalink' => 'bremen-stadt',   'postal_code' => '28195', 'is_popular' => false],
            // Schleswig-Holstein
            ['state' => 'SH', 'name' => 'Kiel',           'permalink' => 'kiel',           'postal_code' => '24103', 'is_popular' => false],
            ['state' => 'SH', 'name' => 'Lübeck',         'permalink' => 'luebeck',        'postal_code' => '23552', 'is_popular' => false],
            // Rheinland-Pfalz
            ['state' => 'RP', 'name' => 'Mainz',          'permalink' => 'mainz',          'postal_code' => '55116', 'is_popular' => false],
        ];

        $position = 0;
        foreach ($cities as $row) {
            $state = $states->get($row['state']);
            if ($state === null) {
                continue;
            }

            $position++;
            City::query()->updateOrCreate(
                ['state_id' => $state->id, 'permalink' => $row['permalink']],
                [
                    'name' => $row['name'],
                    'postal_code' => $row['postal_code'],
                    'is_popular' => $row['is_popular'],
                    'status' => 'published',
                    'sort_order' => $position,
                ],
            );
        }
    }
}
