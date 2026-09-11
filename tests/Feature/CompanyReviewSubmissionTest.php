<?php

declare(strict_types=1);

use App\Models\City;
use App\Models\Company;
use App\Models\CompanyReview;
use App\Models\CompanyTranslation;
use App\Models\Country;
use App\Models\State;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

/**
 * Minimal published company + DE translation permalink. City/State/Country
 * lack HasFactory in this app (see CompaniesIndexPageTest), so rows are
 * created explicitly.
 */
function seedReviewCompany(string $slug = 'testfirma-berlin'): Company
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
    $city = City::query()->create([
        'state_id' => $state->id,
        'name' => 'Berlin',
        'permalink' => 'berlin',
        'postal_code' => '10115',
        'is_popular' => true,
        'status' => 'published',
        'sort_order' => 0,
    ]);

    $company = Company::query()->create([
        'primary_city_id' => $city->id,
        'primary_district_id' => null,
        'street' => 'Teststraße 1',
        'postal_code' => '10115',
        'status' => 'published',
        'sort_order' => 1,
    ]);
    CompanyTranslation::query()->create([
        'company_id' => $company->id,
        'lang' => 'de',
        'name' => 'Testfirma Berlin',
        'permalink' => $slug,
    ]);

    return $company;
}

test('the write-a-review form renders for a published company', function (): void {
    seedReviewCompany();

    $this->get('/company/testfirma-berlin/review')
        ->assertOk();
});

test('a valid submission creates a published review immediately', function (): void {
    $company = seedReviewCompany();

    $response = $this->post('/company/testfirma-berlin/review', [
        'rating' => 5,
        'body' => 'Ein wirklich sehr guter Umzug — pünktlich, freundlich und keine Beschädigungen. Kann ich absolut weiterempfehlen!',
        'advantages' => ['friendly', 'professional'],
        'disadvantages' => [],
        'source' => 'google',
        'author_name' => 'Max Mustermann',
        'author_email' => 'max@example.com',
        'is_anonymous' => false,
        'website_url' => '',
    ]);

    $response->assertRedirect('/company/testfirma-berlin/review/thanks');

    $review = CompanyReview::query()->where('company_id', $company->id)->first();
    expect($review)->not->toBeNull()
        ->and($review->status)->toBe('published')
        ->and($review->rating)->toBe(5)
        ->and($review->author_initials)->toBe('M.M.')
        ->and($review->advantages)->toBe(['friendly', 'professional'])
        ->and($review->source)->toBe('google')
        ->and($review->published_at)->not->toBeNull();
});

test('the honeypot field silently rejects bot submissions', function (): void {
    $company = seedReviewCompany();

    $response = $this->post('/company/testfirma-berlin/review', [
        'rating' => 5,
        'body' => 'Ein sehr guter Umzug, kann ich absolut weiterempfehlen!',
        'author_name' => 'Bot',
        'author_email' => 'bot@example.com',
        'website_url' => 'https://spammy.example.com', // honeypot tripped
    ]);

    $response->assertSessionHasErrors('website_url');
    expect(CompanyReview::query()->where('company_id', $company->id)->count())->toBe(0);
});

test('rating outside 1-5 fails validation', function (): void {
    seedReviewCompany();

    $this->post('/company/testfirma-berlin/review', [
        'rating' => 6,
        'body' => 'Ein sehr guter Umzug, kann ich absolut weiterempfehlen!',
        'author_name' => 'X',
        'author_email' => 'x@example.com',
    ])->assertSessionHasErrors('rating');
});

test('body shorter than 20 chars fails validation', function (): void {
    seedReviewCompany();

    $this->post('/company/testfirma-berlin/review', [
        'rating' => 5,
        'body' => 'zu kurz',
        'author_name' => 'X',
        'author_email' => 'x@example.com',
    ])->assertSessionHasErrors('body');
});

test('the thanks page renders after a successful submission', function (): void {
    seedReviewCompany();

    $this->get('/company/testfirma-berlin/review/thanks')
        ->assertOk();
});
