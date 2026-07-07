<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\ServiceParentCategory;
use App\Models\ServiceParentCategoryTranslation;
use Illuminate\Database\Seeder;

/**
 * Seeds the 5 top-level ("parent") Moovato service umbrellas with DE + EN
 * translations. Idempotent — keyed on the German name so re-running only
 * updates existing rows.
 *
 * Featured flags: Umzüge + Spezialtransport anchor the homepage tile grid.
 * Möbelmontage / Entrümpelung / Einlagerung stay published but unfeatured.
 */
final class ServiceParentCategorySeeder extends Seeder
{
    public function run(): void
    {
        $parents = [
            [
                'icon' => 'Boxes',
                'is_featured' => true,
                'is_popular' => false,
                'de' => [
                    'name' => 'Umzüge',
                    'permalink' => 'umzuege',
                    'short_description' => 'Alle Umzugsdienstleistungen — privat, gewerblich und deutschlandweit.',
                ],
                'en' => [
                    'name' => 'Moves',
                    'permalink' => 'moves',
                    'short_description' => 'All moving services — private, commercial and nationwide.',
                ],
            ],
            [
                'icon' => 'Package',
                'is_featured' => true,
                'is_popular' => false,
                'de' => [
                    'name' => 'Spezialtransport',
                    'permalink' => 'spezialtransport',
                    'short_description' => 'Klaviere, Tresore, Kunstwerke und andere Spezialgüter — sicher transportiert.',
                ],
                'en' => [
                    'name' => 'Special Transport',
                    'permalink' => 'special-transport',
                    'short_description' => 'Pianos, safes, artwork and other specialty goods — moved safely.',
                ],
            ],
            [
                'icon' => 'Wrench',
                'is_featured' => true,
                'is_popular' => true,
                'de' => [
                    'name' => 'Möbelmontage',
                    'permalink' => 'moebelmontage',
                    'short_description' => 'Auf- und Abbau von Möbeln durch erfahrene Handwerker.',
                ],
                'en' => [
                    'name' => 'Furniture Assembly',
                    'permalink' => 'furniture-assembly',
                    'short_description' => 'Assembly and disassembly of furniture by experienced craftsmen.',
                ],
            ],
            [
                'icon' => 'Trash2',
                'is_featured' => true,
                'is_popular' => true,
                'de' => [
                    'name' => 'Entrümpelung',
                    'permalink' => 'entruempelung',
                    'short_description' => 'Wohnungen, Keller und Dachböden fachgerecht entrümpelt und entsorgt.',
                ],
                'en' => [
                    'name' => 'House Clearance',
                    'permalink' => 'house-clearance',
                    'short_description' => 'Apartments, cellars and attics cleared and disposed of properly.',
                ],
            ],
            [
                'icon' => 'Warehouse',
                'is_featured' => false,
                'is_popular' => false,
                'de' => [
                    'name' => 'Einlagerung',
                    'permalink' => 'einlagerung',
                    'short_description' => 'Sichere Kurz- und Langzeitlagerung in klimatisierten Lagerräumen.',
                ],
                'en' => [
                    'name' => 'Storage',
                    'permalink' => 'storage',
                    'short_description' => 'Secure short- and long-term storage in climate-controlled facilities.',
                ],
            ],
        ];

        foreach ($parents as $index => $data) {
            /** @var ServiceParentCategory $parent */
            $parent = ServiceParentCategory::query()->updateOrCreate(
                ['name' => $data['de']['name']],
                [
                    'icon' => $data['icon'],
                    'is_featured' => $data['is_featured'],
                    'is_popular' => $data['is_popular'],
                    'status' => 'published',
                    'sort_order' => $index + 1,
                ],
            );

            foreach (['de', 'en'] as $lang) {
                ServiceParentCategoryTranslation::query()->updateOrCreate(
                    ['parent_category_id' => $parent->id, 'lang' => $lang],
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
