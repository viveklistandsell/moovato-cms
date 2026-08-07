<?php

declare(strict_types=1);

use App\Models\City;
use App\Models\Company;
use App\Models\CompanyReview;
use App\Models\CompanyTranslation;
use App\Models\Country;
use App\Models\State;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    $role = Role::findOrCreate(User::SUPER_ADMIN_ROLE, 'web');
    $this->admin = User::factory()->create();
    $this->admin->assignRole($role);
    $this->actingAs($this->admin);
});

function seedAdminReviewCompany(): Company
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
        'permalink' => 'testfirma-berlin',
    ]);

    return $company;
}

function makeAdminReview(int $companyId, int $rating = 5, string $status = 'published'): CompanyReview
{
    return CompanyReview::query()->create([
        'company_id' => $companyId,
        'author_name' => 'Test User',
        'author_email' => 'test.'.uniqid().'@example.com',
        'author_initials' => 'T.U.',
        'rating' => $rating,
        'body' => 'Ein ausreichend langer Testtext fuer die Admin-Tests der Bewertungsverwaltung.',
        'status' => $status,
        'published_at' => $status === 'published' ? now() : null,
    ]);
}

test('the reviews index renders for a super-admin', function (): void {
    $company = seedAdminReviewCompany();
    makeAdminReview($company->id, 5);
    makeAdminReview($company->id, 2, 'hidden');

    $this->get('/admin/reviews')
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('admin/reviews/Index')
            ->has('reviews', 2)
            ->has('stats')
            ->where('stats.total', 2)
            ->where('stats.published', 1)
            ->where('stats.hidden', 1),
        );
});

test('rating filter narrows the list', function (): void {
    $company = seedAdminReviewCompany();
    makeAdminReview($company->id, 5);
    makeAdminReview($company->id, 5);
    makeAdminReview($company->id, 3);

    $this->get('/admin/reviews?rating=5')
        ->assertOk()
        ->assertInertia(fn ($page) => $page->has('reviews', 2));
});

test('has_reply=no returns only reviews without a company reply', function (): void {
    $company = seedAdminReviewCompany();
    $withoutReply = makeAdminReview($company->id, 5);
    $withReply = makeAdminReview($company->id, 5);
    $withReply->update([
        'reply_body' => 'Danke!',
        'replied_at' => now(),
    ]);

    $this->get('/admin/reviews?has_reply=no')
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->has('reviews', 1)
            ->where('reviews.0.id', $withoutReply->id),
        );
});

test('an admin can post a reply on a review', function (): void {
    $company = seedAdminReviewCompany();
    $review = makeAdminReview($company->id, 4);

    $this->post("/admin/reviews/{$review->id}/reply", [
        'reply_body' => 'Vielen Dank fuer Ihre Bewertung, wir freuen uns sehr!',
    ])->assertRedirect();

    $review->refresh();
    expect($review->reply_body)->toBe('Vielen Dank fuer Ihre Bewertung, wir freuen uns sehr!')
        ->and($review->replied_at)->not->toBeNull()
        ->and($review->reply_by_user_id)->toBe($this->admin->id);
});

test('reply validation rejects empty or oversized bodies', function (): void {
    $company = seedAdminReviewCompany();
    $review = makeAdminReview($company->id, 4);

    $this->post("/admin/reviews/{$review->id}/reply", ['reply_body' => ''])
        ->assertSessionHasErrors('reply_body');
    $this->post("/admin/reviews/{$review->id}/reply", ['reply_body' => str_repeat('x', 2001)])
        ->assertSessionHasErrors('reply_body');
});

test('destroying a reply clears the inline fields', function (): void {
    $company = seedAdminReviewCompany();
    $review = makeAdminReview($company->id, 4);
    $review->update([
        'reply_body' => 'Danke!',
        'replied_at' => now(),
        'reply_by_user_id' => $this->admin->id,
    ]);

    $this->delete("/admin/reviews/{$review->id}/reply")
        ->assertRedirect();

    $review->refresh();
    expect($review->reply_body)->toBeNull()
        ->and($review->replied_at)->toBeNull()
        ->and($review->reply_by_user_id)->toBeNull();
});

test('hiding a review drops it out of the company cache', function (): void {
    $company = seedAdminReviewCompany();
    makeAdminReview($company->id, 5);
    $second = makeAdminReview($company->id, 1);

    $company->refresh();
    expect($company->review_count)->toBe(2)
        ->and((float) $company->rating_avg)->toBe(3.0);

    $this->post("/admin/reviews/{$second->id}/status", ['status' => 'hidden'])
        ->assertRedirect();

    $company->refresh();
    expect($company->review_count)->toBe(1)
        ->and((float) $company->rating_avg)->toBe(5.0);
});

test('an invalid status value is rejected', function (): void {
    $company = seedAdminReviewCompany();
    $review = makeAdminReview($company->id, 3);

    $this->post("/admin/reviews/{$review->id}/status", ['status' => 'bogus'])
        ->assertSessionHasErrors('status');
});

test('destroying a review deletes it and recomputes the cache', function (): void {
    $company = seedAdminReviewCompany();
    $review = makeAdminReview($company->id, 5);

    $company->refresh();
    expect($company->review_count)->toBe(1);

    $this->delete("/admin/reviews/{$review->id}")
        ->assertRedirect();

    expect(CompanyReview::query()->find($review->id))->toBeNull();
    $company->refresh();
    expect($company->review_count)->toBe(0);
});

test('a non-admin user cannot access the reviews page', function (): void {
    auth()->logout();
    $plain = User::factory()->create();
    $this->actingAs($plain)
        ->get('/admin/reviews')
        ->assertForbidden();
});
