<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\ServiceCategory;
use App\Models\ServiceCategoryTranslation;
use App\Models\ServiceParentCategory;
use Illuminate\Database\Seeder;

final class ServiceCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            // ────────── Under Umzüge ──────────
            [
                'parent_key' => 'Umzüge',
                'icon' => 'Home',
                'is_featured' => false,
                'is_popular' => true,
                'de' => [
                    'name' => 'Privatumzug',
                    'permalink' => 'privatumzug',
                    'short_description' => 'Sicherer Umzug für private Haushalte in Berlin und ganz Deutschland.',
                ],
                'en' => [
                    'name' => 'Private Move',
                    'permalink' => 'private-move',
                    'short_description' => 'Reliable moves for private households in Berlin and across Germany.',
                ],
            ],
            [
                'parent_key' => 'Umzüge',
                'icon' => 'Building2',
                'is_featured' => false,
                'is_popular' => true,
                'de' => [
                    'name' => 'Firmenumzug',
                    'permalink' => 'firmenumzug',
                    'short_description' => 'Büro- und Gewerbeumzüge — minimaler Ausfall, professionelle Abwicklung.',
                ],
                'en' => [
                    'name' => 'Business Move',
                    'permalink' => 'business-move',
                    'short_description' => 'Office and commercial moves with minimal downtime.',
                ],
            ],
            [
                'parent_key' => 'Umzüge',
                'icon' => 'Truck',
                'is_featured' => false,
                'is_popular' => true,
                'de' => [
                    'name' => 'Fernumzug',
                    'permalink' => 'fernumzug',
                    'short_description' => 'Deutschlandweite und europäische Umzüge mit fester Ankunftszeit.',
                ],
                'en' => [
                    'name' => 'Long-distance Move',
                    'permalink' => 'long-distance-move',
                    'short_description' => 'Nationwide and European moves with guaranteed arrival times.',
                ],
            ],
            [
                'parent_key' => 'Umzüge',
                'icon' => 'Users',
                'is_featured' => false,
                'is_popular' => false,
                'de' => [
                    'name' => 'Umzugshelfer',
                    'permalink' => 'umzugshelfer',
                    'short_description' => 'Stundenweise buchbare Umzugshelfer — Muskelkraft für deinen Umzugstag.',
                ],
                'en' => [
                    'name' => 'Moving Helpers',
                    'permalink' => 'moving-helpers',
                    'short_description' => 'Movers you can book by the hour — extra hands for your moving day.',
                ],
            ],
            [
                'parent_key' => 'Umzüge',
                'icon' => 'PackageOpen',
                'is_featured' => false,
                'is_popular' => false,
                'de' => [
                    'name' => 'Verpackungsservice',
                    'permalink' => 'verpackungsservice',
                    'short_description' => 'Fachgerechtes Verpacken durch geschulte Umzugshelfer inklusive Material.',
                ],
                'en' => [
                    'name' => 'Packing Service',
                    'permalink' => 'packing-service',
                    'short_description' => 'Professional packing by trained movers, materials included.',
                ],
            ],
            [
                'parent_key' => 'Umzüge',
                'icon' => 'Building',
                'is_featured' => false,
                'is_popular' => false,
                'de' => [
                    'name' => 'Wohnungsumzug',
                    'permalink' => 'wohnungsumzug',
                    'short_description' => 'Sicherer Umzug für Mietwohnungen — Etage zu Etage.',
                ],
                'en' => [
                    'name' => 'Apartment Move',
                    'permalink' => 'apartment-move',
                    'short_description' => 'Safe moving for rental apartments — floor to floor.',
                ],
            ],
            [
                'parent_key' => 'Umzüge',
                'icon' => 'HousePlus',
                'is_featured' => false,
                'is_popular' => false,
                'de' => [
                    'name' => 'Hausumzug',
                    'permalink' => 'hausumzug',
                    'short_description' => 'Umzug eines ganzen Hauses inklusive Garten und Keller.',
                ],
                'en' => [
                    'name' => 'House Move',
                    'permalink' => 'house-move',
                    'short_description' => 'Moving an entire house including garden and cellar.',
                ],
            ],
            [
                'parent_key' => 'Umzüge',
                'icon' => 'Briefcase',
                'is_featured' => false,
                'is_popular' => false,
                'de' => [
                    'name' => 'Büroumzug',
                    'permalink' => 'bueroumzug',
                    'short_description' => 'Büroumzug mit minimalem Ausfall — Aktenordnung inklusive.',
                ],
                'en' => [
                    'name' => 'Office Move',
                    'permalink' => 'office-move',
                    'short_description' => 'Office moves with minimal downtime — filing preserved.',
                ],
            ],
            [
                'parent_key' => 'Umzüge',
                'icon' => 'Stethoscope',
                'is_featured' => false,
                'is_popular' => false,
                'de' => [
                    'name' => 'Praxisumzug',
                    'permalink' => 'praxisumzug',
                    'short_description' => 'Umzug für Arztpraxen, Kanzleien und therapeutische Einrichtungen.',
                ],
                'en' => [
                    'name' => 'Practice Move',
                    'permalink' => 'practice-move',
                    'short_description' => 'Moves for medical practices, law firms and therapy clinics.',
                ],
            ],

            // ────────── Under Spezialtransport ──────────
            [
                'parent_key' => 'Spezialtransport',
                'icon' => 'Music',
                'is_featured' => false,
                'is_popular' => true,
                'de' => [
                    'name' => 'Klaviertransport',
                    'permalink' => 'klaviertransport',
                    'short_description' => 'Klavier- und Flügeltransport durch spezialisierte Teams.',
                ],
                'en' => [
                    'name' => 'Piano Transport',
                    'permalink' => 'piano-transport',
                    'short_description' => 'Upright and grand piano transport by specialist teams.',
                ],
            ],
            [
                'parent_key' => 'Spezialtransport',
                'icon' => 'Palette',
                'is_featured' => false,
                'is_popular' => false,
                'de' => [
                    'name' => 'Kunsttransport',
                    'permalink' => 'kunsttransport',
                    'short_description' => 'Sicherer Transport von Kunstwerken, Antiquitäten und Ausstellungsstücken.',
                ],
                'en' => [
                    'name' => 'Art Transport',
                    'permalink' => 'art-transport',
                    'short_description' => 'Safe transport of artworks, antiques and exhibition pieces.',
                ],
            ],
        ];

        // Track next sort_order per parent so siblings get 1..N ordering.
        /** @var array<int, int> $sortByParent */
        $sortByParent = [];

        foreach ($categories as $data) {
            $parentId = ServiceParentCategory::query()
                ->where('name', $data['parent_key'])
                ->value('id');

            if ($parentId === null) {
                $this->command?->warn(
                    "Skipping '{$data['de']['name']}' — parent '{$data['parent_key']}' not found. Run ServiceParentCategorySeeder first."
                );

                continue;
            }

            $sortByParent[$parentId] = ($sortByParent[$parentId] ?? 0) + 1;

            /** @var ServiceCategory $category */
            $category = ServiceCategory::query()->updateOrCreate(
                ['name' => $data['de']['name']],
                [
                    'parent_category_id' => $parentId,
                    'icon' => $data['icon'],
                    'is_featured' => $data['is_featured'],
                    'is_popular' => $data['is_popular'],
                    'status' => 'published',
                    'sort_order' => $sortByParent[$parentId],
                ],
            );

            foreach (['de', 'en'] as $lang) {
                ServiceCategoryTranslation::query()->updateOrCreate(
                    ['category_id' => $category->id, 'lang' => $lang],
                    [
                        'name' => $data[$lang]['name'],
                        'permalink' => $data[$lang]['permalink'],
                        'short_description' => $data[$lang]['short_description'],
                    ],
                );
            }
        }
    }
}
