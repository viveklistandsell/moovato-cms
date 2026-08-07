<?php

declare(strict_types=1);

namespace App\Observers;

use App\Models\Company;
use App\Models\CompanyReview;

/**
 * Keeps the denormalized rating cache columns on `companies`
 * (rating_avg, review_count, recommend_pct, rating_breakdown) in
 * sync with the underlying `company_reviews` rows.
 *
 * Registered via #[ObservedBy(CompanyReviewObserver::class)] on the
 * CompanyReview model — no ServiceProvider wiring needed.
 *
 * The rows the frontend reads are derived, not authoritative — the
 * observer treats the reviews table as the source of truth.
 */
final class CompanyReviewObserver
{
    public function created(CompanyReview $review): void
    {
        Company::recomputeReviewStats((int) $review->company_id);
    }

    public function updated(CompanyReview $review): void
    {
        $touched = $review->wasChanged(['status', 'rating', 'company_id']);
        if (! $touched) {
            return;
        }

        Company::recomputeReviewStats((int) $review->company_id);

        $originalCompanyId = (int) ($review->getOriginal('company_id') ?? 0);
        if ($originalCompanyId !== 0 && $originalCompanyId !== (int) $review->company_id) {
            Company::recomputeReviewStats($originalCompanyId);
        }
    }

    public function deleted(CompanyReview $review): void
    {
        Company::recomputeReviewStats((int) $review->company_id);
    }
}
