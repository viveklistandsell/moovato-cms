<?php

declare(strict_types=1);

namespace App\Http\Controllers\Partner;

use App\Enums\PlanTier;
use App\Http\Controllers\Controller;
use App\Http\Requests\Partner\Reviews\StorePartnerReplyRequest;
use App\Models\Company;
use App\Models\CompanyReview;
use App\Models\CompanyUser;
use App\Policies\CompanyPlanPolicy;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Partner-side review moderation.
 *
 * A partner can act on any review that belongs to THEIR company —
 * the ownership guard `assertOwns()` runs on every endpoint before
 * touching a row. Reply creation is additionally cap-gated by the
 * `reply_reviews` plan cap: Basic tier gets 10 lifetime replies,
 * Premium and Gold get unlimited.
 */
final class ReviewController extends Controller
{
    public function reply(StorePartnerReplyRequest $request, CompanyReview $review): RedirectResponse
    {
        $partner = $this->partner($request);
        $company = $this->assertOwns($partner, $review);

        $isNewReply = $review->reply_body === null;
        if ($isNewReply && ! (new CompanyPlanPolicy())->replyReviews($partner, $company)) {
            throw new AuthorizationException(
                __('partner.reviews.errors.reply_cap_reached'),
            );
        }

        $review->update([
            'reply_body' => (string) $request->validated('reply_body'),
            'replied_at' => now(),
            'reply_by_user_id' => null,
        ]);

        return back()->with('flash', [
            'success' => __('partner.reviews.flash.reply_saved'),
        ]);
    }

    public function destroyReply(Request $request, CompanyReview $review): RedirectResponse
    {
        $partner = $this->partner($request);
        $this->assertOwns($partner, $review);

        $review->update([
            'reply_body' => null,
            'replied_at' => null,
            'reply_by_user_id' => null,
        ]);

        return back()->with('flash', [
            'success' => __('partner.reviews.flash.reply_deleted'),
        ]);
    }

    public function hide(Request $request, CompanyReview $review): RedirectResponse
    {
        return $this->setStatus($request, $review, 'hidden', 'partner.reviews.flash.hidden');
    }

    public function unhide(Request $request, CompanyReview $review): RedirectResponse
    {
        return $this->setStatus($request, $review, 'published', 'partner.reviews.flash.unhidden');
    }

    public function markSpam(Request $request, CompanyReview $review): RedirectResponse
    {
        return $this->setStatus($request, $review, 'spam', 'partner.reviews.flash.spam_marked');
    }

    public function unmarkSpam(Request $request, CompanyReview $review): RedirectResponse
    {
        return $this->setStatus($request, $review, 'published', 'partner.reviews.flash.spam_unmarked');
    }

    public function destroy(Request $request, CompanyReview $review): RedirectResponse
    {
        $partner = $this->partner($request);
        $this->assertOwns($partner, $review);

        $review->delete();

        return back()->with('flash', [
            'success' => __('partner.reviews.flash.review_deleted'),
        ]);
    }

    /* ---------------------------------------------- helpers */

    private function setStatus(Request $request, CompanyReview $review, string $status, string $flashKey): RedirectResponse
    {
        $partner = $this->partner($request);
        $this->assertOwns($partner, $review);

        $review->update(['status' => $status]);

        return back()->with('flash', ['success' => __($flashKey)]);
    }

    private function partner(Request $request): CompanyUser
    {
        /** @var CompanyUser|null $user */
        $user = $request->user('company');
        if ($user === null) {
            throw new NotFoundHttpException;
        }

        return $user;
    }

    private function assertOwns(CompanyUser $partner, CompanyReview $review): Company
    {
        if ($partner->company_id === null || (int) $review->company_id !== (int) $partner->company_id) {
            throw new NotFoundHttpException;
        }

        $company = $review->company;
        if ($company === null) {
            throw new NotFoundHttpException;
        }

        if (! ($company->plan_tier instanceof PlanTier)) {
            $company->plan_tier = PlanTier::tryFrom((string) $company->plan_tier) ?? PlanTier::Basic;
        }

        return $company;
    }
}
