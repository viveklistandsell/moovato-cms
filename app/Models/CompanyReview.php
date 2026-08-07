<?php

declare(strict_types=1);

namespace App\Models;

use App\Observers\CompanyReviewObserver;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * A single customer review left on a company listing.
 *
 * Reviews are self-service (no admin approval) — see the "no approval"
 * decision in the review-module spec. `status = published` on insert;
 * admins can flip to `hidden` or `spam` after the fact if a review is
 * abusive. A ReviewObserver (Phase 3) keeps `companies.rating_avg`,
 * `review_count`, `recommend_pct` and `rating_breakdown` in sync.
 *
 * The company reply lives inline on this row (`reply_body`,
 * `replied_at`, `reply_by_user_id`) — one reply per review, matching the
 * Google Reviews UX.
 */
#[Fillable([
    'company_id', 'user_id',
    'author_name', 'author_email', 'author_initials', 'is_anonymous',
    'rating', 'body', 'advantages', 'disadvantages', 'source',
    'proof_document', 'status',
    'reply_body', 'replied_at', 'reply_by_user_id',
    'helpful_count',
    'ip_address', 'user_agent',
    'email_verified_at', 'published_at',
])]
#[ObservedBy([CompanyReviewObserver::class])]
final class CompanyReview extends Model
{
    use HasFactory;

    protected $table = 'company_reviews';

    protected $casts = [
        'is_anonymous' => 'boolean',
        'rating' => 'integer',
        'advantages' => 'array',
        'disadvantages' => 'array',
        'helpful_count' => 'integer',
        'replied_at' => 'datetime',
        'email_verified_at' => 'datetime',
        'published_at' => 'datetime',
    ];

    /**
     * Derive initials from a full name — first letter of first word +
     * first letter of last word, uppercased, joined by a dot. Used at
     * save time to populate `author_initials`, so we don't recompute on
     * every render.
     */
    public static function initialsFor(string $name): string
    {
        $parts = preg_split('/\s+/', mb_trim($name), -1, PREG_SPLIT_NO_EMPTY) ?: [];
        if ($parts === []) {
            return '';
        }
        $first = mb_strtoupper(mb_substr($parts[0], 0, 1));
        $last = count($parts) > 1
            ? mb_strtoupper(mb_substr($parts[count($parts) - 1], 0, 1))
            : '';

        return $last !== '' ? "{$first}.{$last}." : "{$first}.";
    }

    /* -------------------------------------------- Relations */

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * The submitter, if they were logged in when they wrote the review.
     * NULL for anonymous public submissions — use author_name/email in
     * that case.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * The admin (or company owner via the future self-service portal)
     * who wrote the reply.
     */
    public function replyAuthor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reply_by_user_id');
    }

    /* -------------------------------------------- Scopes */

    /**
     * Only reviews visible to the public. `hidden` / `spam` reviews are
     * kept in the DB (soft-hide) so admins can restore them, but they
     * never surface on the frontend or feed into the observer's cached
     * rating aggregates.
     */
    public function scopePublished(Builder $q): Builder
    {
        return $q->where('status', 'published');
    }

    /**
     * Public display name — "Klaus Müller" or "K.M." when the reviewer
     * chose to publish anonymously. Kept as a model method (not a
     * computed attribute) because we always want fresh output; casting
     * as an accessor would cache it across the request.
     */
    public function publicName(): string
    {
        return $this->is_anonymous
            ? ($this->author_initials !== '' ? $this->author_initials : '—')
            : $this->author_name;
    }
}
