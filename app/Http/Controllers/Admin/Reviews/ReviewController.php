<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin\Reviews;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Reviews\StoreReplyRequest;
use App\Http\Requests\Admin\Reviews\UpdateReviewStatusRequest;
use App\Models\CompanyReview;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Admin actions on customer reviews.
 *
 * The listing UI now lives inline on the company edit page (see
 * Admin\Companies\CompanyController::edit and the
 * CompanyReviewsSection Vue component). This controller only owns
 * the action endpoints:
 *
 *   POST   /admin/reviews/{review}/reply   → reply (upsert)
 *   DELETE /admin/reviews/{review}/reply   → destroyReply (clear reply)
 *   POST   /admin/reviews/{review}/status  → setStatus (published/hidden/spam)
 *   DELETE /admin/reviews/{review}         → destroy (hard delete)
 *   GET    /admin/reviews/{review}/proof   → downloadProof (streamed)
 *
 * Reply mutations only touch reply_body / replied_at / reply_by_user_id
 * — the CompanyReviewObserver's `wasChanged` guard means the cache
 * doesn't churn on every reply.
 */
final class ReviewController extends Controller
{
    private const ALLOWED_SORTS = ['newest', 'oldest', 'rating_high', 'rating_low'];

    /**
     * Paginate one company's reviews for the inline section on the
     * admin company edit page.
     *
     * Query params (all namespaced under `reviews_` so a future filter
     * on the edit URL doesn't collide):
     *   reviews_sort → newest (default) | oldest | rating_high | rating_low
     *   reviews_page → 1..N
     *   reviews_per_page → 1..100 (default 15)
     *
     * Static + public so CompanyController::edit() can call it inside
     * an Inertia lazy prop closure without holding an instance.
     *
     * @return array<string, mixed>
     */
    public static function paginateForCompany(Request $request, int $companyId): array
    {
        $sort = (string) $request->query('reviews_sort', 'newest');
        if (! in_array($sort, self::ALLOWED_SORTS, true)) {
            $sort = 'newest';
        }

        $perPage = max(1, min(100, (int) $request->query('reviews_per_page', '15')));

        $query = CompanyReview::query()
            ->where('company_id', $companyId)
            ->with(['replyAuthor:id,name']);
        self::applySort($query, $sort);

        $paginated = $query->paginate($perPage, ['*'], 'reviews_page')->withQueryString();

        return [
            'sort' => $sort,
            'data' => $paginated->getCollection()
                ->map(fn (CompanyReview $r): array => self::presentReview($r))
                ->values()
                ->all(),
            'current_page' => $paginated->currentPage(),
            'last_page' => $paginated->lastPage(),
            'per_page' => $paginated->perPage(),
            'total' => $paginated->total(),
            'has_more' => $paginated->currentPage() < $paginated->lastPage(),
        ];
    }

    public function reply(StoreReplyRequest $request, CompanyReview $review): RedirectResponse
    {
        $review->update([
            'reply_body' => (string) $request->validated('reply_body'),
            'replied_at' => now(),
            'reply_by_user_id' => $request->user()?->id,
        ]);

        return back()->with('flash', ['success' => __('admin.reviews.flash.reply_saved')]);
    }

    public function destroyReply(CompanyReview $review): RedirectResponse
    {
        $review->update([
            'reply_body' => null,
            'replied_at' => null,
            'reply_by_user_id' => null,
        ]);

        return back()->with('flash', ['success' => __('admin.reviews.flash.reply_deleted')]);
    }

    public function setStatus(UpdateReviewStatusRequest $request, CompanyReview $review): RedirectResponse
    {
        $review->update(['status' => (string) $request->validated('status')]);

        return back()->with('flash', ['success' => __('admin.reviews.flash.status_updated')]);
    }

    public function destroy(CompanyReview $review): RedirectResponse
    {
        $review->delete();

        return back()->with('flash', ['success' => __('admin.reviews.flash.review_deleted')]);
    }

    public function downloadProof(CompanyReview $review): StreamedResponse
    {
        $path = (string) $review->proof_document;
        if ($path === '' || ! Storage::disk('local')->exists($path)) {
            throw new NotFoundHttpException('Proof document not found.');
        }

        return Storage::disk('local')->response(
            $path,
            basename($path),
            ['Content-Type' => Storage::disk('local')->mimeType($path) ?: 'application/octet-stream'],
        );
    }

    /* ---------------------------------------------- helpers */
    private static function proofUrlFor(CompanyReview $review): ?string
    {
        $path = (string) ($review->proof_document ?? '');
        if ($path === '' || ! Storage::disk('local')->exists($path)) {
            return null;
        }

        return route('admin.reviews.proof', $review->id);
    }

    /**
     * Apply the sort direction to the review query. Secondary ordering
     * on `id DESC` for stable pagination — without it, two reviews
     * created in the same second could shuffle between pages.
     *
     * @param  Builder<CompanyReview>  $query
     */
    private static function applySort(Builder $query, string $sort): void
    {
        match ($sort) {
            'oldest' => $query->orderBy('created_at')->orderBy('id'),
            'rating_high' => $query->orderByDesc('rating')->orderByDesc('id'),
            'rating_low' => $query->orderBy('rating')->orderByDesc('id'),
            default => $query->latest('created_at')->orderByDesc('id'),
        };
    }

    /**
     * @return array<string, mixed>
     */
    private static function presentReview(CompanyReview $review): array
    {
        return [
            'id' => (int) $review->id,
            'author_name' => $review->author_name,
            'author_email' => $review->author_email,
            'author_initials' => $review->author_initials,
            'is_anonymous' => (bool) $review->is_anonymous,
            'public_name' => $review->publicName(),
            'rating' => (int) $review->rating,
            'body' => $review->body,
            'advantages' => $review->advantages ?? [],
            'disadvantages' => $review->disadvantages ?? [],
            'source' => $review->source,
            'status' => $review->status,
            'proof_url' => self::proofUrlFor($review),
            'proof_filename' => $review->proof_document !== null
                ? basename((string) $review->proof_document)
                : null,
            'reply_body' => $review->reply_body,
            'replied_at' => $review->replied_at?->toIso8601String(),
            'reply_author_name' => $review->replyAuthor?->name,
            'helpful_count' => (int) $review->helpful_count,
            'ip_address' => $review->ip_address,
            'created_at' => $review->created_at?->toIso8601String(),
            'published_at' => $review->published_at?->toIso8601String(),
        ];
    }
}
