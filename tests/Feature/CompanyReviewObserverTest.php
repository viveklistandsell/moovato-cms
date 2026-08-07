<?php

declare(strict_types=1);

use App\Models\City;
use App\Models\Company;
use App\Models\CompanyReview;
use App\Models\Country;
use App\Models\State;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

/**
 * Seed one published Berlin company. Reused by every test in this
 * file — the observer only cares that a company row exists.
 */
function seedObservedCompany(): Company
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

    return Company::query()->create([
        'primary_city_id' => $city->id,
        'primary_district_id' => null,
        'street' => 'Teststraße 1',
        'postal_code' => '10115',
        'status' => 'published',
        'sort_order' => 1,
    ]);
}

function makeReview(int $companyId, int $rating, string $status = 'published'): CompanyReview
{
    return CompanyReview::query()->create([
        'company_id' => $companyId,
        'author_name' => 'Test User',
        'author_email' => 'test.'.uniqid().'@example.com',
        'author_initials' => 'T.U.',
        'rating' => $rating,
        'body' => 'Ein ausreichend langer Testtext fuer die Validierung des Observers.',
        'status' => $status,
        'published_at' => $status === 'published' ? now() : null,
    ]);
}

test('creating a review updates the parent company cache', function (): void {
    $company = seedObservedCompany();

    makeReview($company->id, 5);
    makeReview($company->id, 4);
    makeReview($company->id, 3);

    $company->refresh();

    expect($company->review_count)->toBe(3)
        ->and((float) $company->rating_avg)->toBe(4.0)
        ->and($company->recommend_pct)->toBe(67) // 2 of 3 rated >= 4
        ->and($company->rating_breakdown)->toBe([
            '5' => 1, '4' => 1, '3' => 1, '2' => 0, '1' => 0,
        ]);
});

test('changing a review rating triggers a recompute', function (): void {
    $company = seedObservedCompany();
    $review = makeReview($company->id, 2);

    $company->refresh();
    expect((float) $company->rating_avg)->toBe(2.0)
        ->and($company->recommend_pct)->toBe(0);

    $review->update(['rating' => 5]);

    $company->refresh();
    expect((float) $company->rating_avg)->toBe(5.0)
        ->and($company->recommend_pct)->toBe(100)
        ->and($company->rating_breakdown['5'])->toBe(1)
        ->and($company->rating_breakdown['2'])->toBe(0);
});

test('hidden reviews are excluded from the aggregates', function (): void {
    $company = seedObservedCompany();
    makeReview($company->id, 5);
    $review = makeReview($company->id, 1);

    $company->refresh();
    expect($company->review_count)->toBe(2)
        ->and((float) $company->rating_avg)->toBe(3.0);

    $review->update(['status' => 'hidden']);

    $company->refresh();
    expect($company->review_count)->toBe(1)
        ->and((float) $company->rating_avg)->toBe(5.0);
});

test('editing only the reply_body does NOT change the cache', function (): void {
    $company = seedObservedCompany();
    $review = makeReview($company->id, 4);

    $company->refresh();
    $snapshotAvg = (string) $company->rating_avg;
    $snapshotCount = $company->review_count;

    $review->update([
        'reply_body' => 'Danke fuer Ihr Feedback!',
        'replied_at' => now(),
    ]);

    $company->refresh();
    expect((string) $company->rating_avg)->toBe($snapshotAvg)
        ->and($company->review_count)->toBe($snapshotCount);
});

test('deleting a review recomputes the cache', function (): void {
    $company = seedObservedCompany();
    makeReview($company->id, 5);
    $review = makeReview($company->id, 1);

    $company->refresh();
    expect($company->review_count)->toBe(2);

    $review->delete();

    $company->refresh();
    expect($company->review_count)->toBe(1)
        ->and((float) $company->rating_avg)->toBe(5.0);
});

test('when the last review is deleted the cache resets to zero', function (): void {
    $company = seedObservedCompany();
    $review = makeReview($company->id, 5);

    $review->delete();

    $company->refresh();
    expect($company->review_count)->toBe(0)
        ->and((int) $company->rating_avg)->toBe(0)
        ->and($company->recommend_pct)->toBe(0)
        ->and($company->rating_breakdown)->toBe([
            '5' => 0, '4' => 0, '3' => 0, '2' => 0, '1' => 0,
        ]);
});
