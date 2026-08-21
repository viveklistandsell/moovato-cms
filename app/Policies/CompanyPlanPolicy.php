<?php

declare(strict_types=1);

namespace App\Policies;

use App\Enums\PlanTier;
use App\Models\Company;
use App\Models\CompanyReview;
use App\Models\CompanyUser;

/**
 * Plan-tier authorization for portal edit endpoints.
 *
 * Each `can<Feature>()` method returns true when the company's
 * plan_tier includes that feature. Called from controllers via
 * `Gate::authorize('portal.<feature>', $company)` — Laravel maps
 * the ability name to the matching method here automatically.
 *
 * The methods deliberately ignore the CompanyUser argument (all we
 * gate on is the company's plan) but Laravel's authorization
 * pipeline always passes it as the first arg, so we accept it and
 * discard it.
 *
 * Ability naming convention: `portal.<feature-slug>` where
 * `<feature-slug>` matches the strings in `config('plans.features')`.
 * That way a controller call reads like:
 *     Gate::authorize('portal.about', $company);
 *
 * If we ever want to override on a per-CompanyUser basis (e.g.
 * revoke edit rights for a specific rep) the signatures already
 * accept the user — just add the check inside.
 */
final class CompanyPlanPolicy
{
    public static function countReplies(Company $company): int
    {
        return CompanyReview::query()
            ->where('company_id', $company->id)
            ->whereNotNull('reply_body')
            ->count();
    }
    /* ---------------------------------------------- on/off features */

    public function shortDescription(?CompanyUser $user, Company $company): bool
    {
        return $this->has($company, 'short_description');
    }

    public function about(?CompanyUser $user, Company $company): bool
    {
        return $this->has($company, 'about');
    }

    public function founded(?CompanyUser $user, Company $company): bool
    {
        return $this->has($company, 'founded');
    }

    public function employees(?CompanyUser $user, Company $company): bool
    {
        return $this->has($company, 'employees');
    }

    public function faqs(?CompanyUser $user, Company $company): bool
    {
        return $this->has($company, 'faqs');
    }

    public function cover(?CompanyUser $user, Company $company): bool
    {
        return $this->has($company, 'cover');
    }

    public function trust(?CompanyUser $user, Company $company): bool
    {
        return $this->has($company, 'trust');
    }

    public function google(?CompanyUser $user, Company $company): bool
    {
        return $this->has($company, 'google');
    }

    /**
     * Cap-aware: partner can reply while the number of reviews on
     * their company that already carry a reply is BELOW the cap.
     * Once equal-or-above, no more replies until they upgrade OR
     * the admin loosens the cap.
     *
     * cap = null → unlimited (always true)
     * cap = 0    → replies feature is fully off (always false)
     * cap = N    → true while used < N
     *
     * `EDITING` an existing reply doesn't consume a new slot — call
     * this only for NEW replies. Deleting a reply frees a slot back.
     */
    public function replyReviews(?CompanyUser $user, Company $company): bool
    {
        $tier = $this->tierOf($company);
        $cap = $tier->cap('reply_reviews');

        if ($cap === null) {
            return true;
        }
        if ($cap <= 0) {
            return false;
        }

        return self::countReplies($company) < $cap;
    }

    private function has(Company $company, string $feature): bool
    {
        $tier = $this->tierOf($company);

        return $tier->hasFeature($feature);
    }

    private function tierOf(Company $company): PlanTier
    {
        $raw = $company->plan_tier;
        if ($raw instanceof PlanTier) {
            return $raw;
        }

        return PlanTier::tryFrom((string) $raw) ?? PlanTier::Basic;
    }
}
