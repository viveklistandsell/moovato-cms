<?php

declare(strict_types=1);

use App\Models\City;
use App\Models\Country;
use App\Models\District;
use App\Models\State;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('suggest returns cities matching the query', function (): void {
    $country = Country::factory()->create(['name' => 'Germany']);
    $state = State::factory()->create(['country_id' => $country->id, 'name' => 'Berlin']);
    City::factory()->create(['state_id' => $state->id, 'name' => 'Berlin']);
    City::factory()->create(['state_id' => $state->id, 'name' => 'Hamburg']);

    $response = $this->getJson('/location-search/suggest?q=berl');

    $response->assertOk();
    $results = $response->json('results');

    $cities = collect($results)->where('kind', 'city');
    expect($cities)->not->toBeEmpty()
        ->and($cities->pluck('name')->all())->toContain('Berlin');

    $city = $cities->firstWhere('name', 'Berlin');
    expect($city['url'])->toBe('/companies?city='.$city['id']);
});

test('suggest also finds districts, states, and countries in one call', function (): void {
    $country = Country::factory()->create(['name' => 'Deutschland']);
    $state = State::factory()->create(['country_id' => $country->id, 'name' => 'Bayern']);
    $city = City::factory()->create(['state_id' => $state->id, 'name' => 'München']);
    District::factory()->create(['city_id' => $city->id, 'name' => 'Schwabing']);

    $results = $this->getJson('/location-search/suggest?q=n')->json('results');
    $kinds = collect($results)->pluck('kind')->unique()->values();

    expect($kinds->all())->toContain('country')
        ->and($kinds->all())->toContain('state')
        ->and($kinds->all())->toContain('city')
        ->and($kinds->all())->toContain('district');
});

test('suggest excludes rows that are not published', function (): void {
    Country::factory()->create(['name' => 'PublishedCountry']);
    State::factory()->create(['name' => 'DraftState', 'status' => 'inactive']);
    City::factory()->create(['name' => 'DraftCity', 'status' => 'inactive']);

    $results = $this->getJson('/location-search/suggest?q=Draft')->json('results');

    expect(collect($results)->pluck('kind')->all())
        ->not->toContain('state')
        ->and(collect($results)->pluck('kind')->all())->not->toContain('city');
});

test('suggest requires q of at least 1 char and rejects empty input', function (): void {
    $this->getJson('/location-search/suggest')->assertStatus(422);
    $this->getJson('/location-search/suggest?q=')->assertStatus(422);
});

test('resolve redirects to /companies with the correct scoping query param', function (): void {
    $country = Country::factory()->create(['name' => 'AT']);
    $state = State::factory()->create(['country_id' => $country->id, 'name' => 'Wien', 'status' => 'published']);

    $response = $this->get('/location-search/resolve?kind=state&id='.$state->id);
    $response->assertRedirect('/companies?state='.$state->id);
});

test('resolve returns 404 when the location kind or id is unknown', function (): void {
    $response = $this->get('/location-search/resolve?kind=city&id=999999');
    $response->assertNotFound();
});

test('go redirects to the exact-match city when the query hits one', function (): void {
    $country = Country::factory()->create(['name' => 'Germany']);
    $state = State::factory()->create(['country_id' => $country->id, 'name' => 'Berlin']);
    $berlin = City::factory()->create(['state_id' => $state->id, 'name' => 'Berlin']);
    City::factory()->create(['state_id' => $state->id, 'name' => 'Berlinchen']);

    $response = $this->get('/location-search/go?q=Berlin');
    $response->assertRedirect('/companies?city='.$berlin->id);
});

test('go falls back to /companies?q= when nothing matches', function (): void {
    $response = $this->get('/location-search/go?q=xyzzy');
    $response->assertRedirect('/companies?q=xyzzy');
});

test('go redirects to a matching state when the query is a state name', function (): void {
    $country = Country::factory()->create(['name' => 'Germany']);
    $state = State::factory()->create(['country_id' => $country->id, 'name' => 'Bayern']);

    $response = $this->get('/location-search/go?q=Bayern');
    $response->assertRedirect('/companies?state='.$state->id);
});

test('go rejects an empty q with a validation error', function (): void {
    $this->getJson('/location-search/go')->assertStatus(422);
});

test('suggest returns a two-character prefix match (autosuggest threshold)', function (): void {
    $country = Country::factory()->create(['name' => 'Germany']);
    $state = State::factory()->create(['country_id' => $country->id, 'name' => 'Berlin']);
    City::factory()->create(['state_id' => $state->id, 'name' => 'Berlin']);
    City::factory()->create(['state_id' => $state->id, 'name' => 'Bern']);

    $results = $this->getJson('/location-search/suggest?q=Be')->json('results');
    $names = collect($results)->pluck('name')->all();

    expect($names)->toContain('Berlin')
        ->and($names)->toContain('Bern');

    foreach ($results as $r) {
        expect($r)->toHaveKey('url')
            ->and($r['url'])->toStartWith('/companies?');
    }
});

test('suggest results carry the redirect url that clicking navigates to', function (): void {
    $country = Country::factory()->create(['name' => 'Germany']);
    $state = State::factory()->create(['country_id' => $country->id, 'name' => 'BW']);
    $city = City::factory()->create(['state_id' => $state->id, 'name' => 'Mannheim']);
    $district = District::factory()->create(['city_id' => $city->id, 'name' => 'Neckarstadt']);

    $results = $this->getJson('/location-search/suggest?q=Neckar')->json('results');
    $neckar = collect($results)->firstWhere('name', 'Neckarstadt');

    expect($neckar)->not->toBeNull()
        ->and($neckar['kind'])->toBe('district')
        ->and($neckar['url'])->toBe('/companies?district='.$district->id);
});
