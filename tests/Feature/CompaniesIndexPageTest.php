<?php

declare(strict_types=1);

use App\Models\City;
use App\Models\Country;
use App\Models\ServiceCategory;
use App\Models\ServiceCategoryTranslation;
use App\Models\State;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Inertia\Testing\AssertableInertia;

uses(RefreshDatabase::class);

/**
 * The City/State/Country models do not use HasFactory, so their factories
 * cannot be resolved — the rows are seeded explicitly instead.
 */
function seedBerlinCities(): void
{
    $country = Country::query()->create([
        'name' => 'Deutschland',
        'iso_code' => 'DE',
        'phone_code' => '+49',
        'status' => 'published',
        'sort_order' => 0,
    ]);

    $state = State::query()->create([
        'country_id' => $country->id,
        'name' => 'Berlin',
        'code' => 'BE',
        'permalink' => 'berlin',
        'status' => 'published',
        'sort_order' => 0,
    ]);

    foreach (['Berlin', 'Potsdam'] as $index => $name) {
        City::query()->create([
            'state_id' => $state->id,
            'name' => $name,
            'permalink' => mb_strtolower($name),
            'postal_code' => '1000'.$index,
            'is_popular' => false,
            'status' => 'published',
            'sort_order' => $index,
        ]);
    }
}

test('companies index renders the frontend page with the filter rail props', function (): void {
    seedBerlinCities();

    $parentCategoryId = DB::table('service_parent_categories')->insertGetId([
        'name' => 'Umzug',
        'status' => 'published',
        'sort_order' => 0,
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $service = ServiceCategory::query()->create([
        'parent_category_id' => $parentCategoryId,
        'name' => 'Privatumzug',
        'status' => 'published',
        'sort_order' => 0,
    ]);
    ServiceCategoryTranslation::query()->create([
        'category_id' => $service->id,
        'lang' => 'de',
        'name' => 'Privatumzug',
        'permalink' => 'privatumzug',
    ]);

    $this->get('/companies?sort=rating')
        ->assertOk()
        ->assertInertia(fn (AssertableInertia $page) => $page
            ->component('frontend/companies/Index')
            ->where('sort', 'rating')
            ->where('companies', [])
            ->where('pagination.total', 0)
            ->has('cities', 2)
            ->has('services', 1)
            ->where('services.0.name', 'Privatumzug')
        );
});

test('companies index keeps the sort segment values the page offers', function (string $sort): void {
    $this->get("/companies?sort={$sort}")
        ->assertOk()
        ->assertInertia(fn (AssertableInertia $page) => $page->where('sort', $sort));
})->with(['rating', 'newest']);

test('companies index reflects the rating and verified filters in its props', function (): void {
    $this->get('/companies?sort=rating&min_rating=8.5&verified=1')
        ->assertOk()
        ->assertInertia(fn (AssertableInertia $page) => $page
            ->where('minRating', 8.5)
            ->where('verifiedOnly', true)
        );
});
