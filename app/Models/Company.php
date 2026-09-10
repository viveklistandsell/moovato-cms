<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\PlanTier;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

#[Fillable([
    'primary_city_id', 'primary_district_id', 'street', 'postal_code',
    'logo', 'cover', 'verified', 'is_top_rated', 'plan_tier',
    'rating_avg', 'review_count', 'recommend_pct', 'rating_breakdown',
    'google_rating', 'google_review_count',
    'founded_year', 'employee_count', 'status', 'sort_order',
])]
final class Company extends Model
{
    use HasFactory;

    protected $table = 'companies';

    protected $casts = [
        'verified' => 'boolean',
        'is_top_rated' => 'boolean',
        'rating_avg' => 'decimal:1',
        'google_rating' => 'decimal:1',
        'rating_breakdown' => 'array',
        'plan_tier' => PlanTier::class,
    ];

    public static function nextSortOrder(): int
    {
        return ((int) self::query()->max('sort_order')) + 1;
    }

    /**
     * Renumber every company so sort_order is 1..N. Called after a row
     * is inserted or its sort_order shifts (so gaps never grow).
     */
    public static function compactSiblings(): void
    {
        $siblings = self::query()->orderBy('sort_order')->orderBy('id')->get();
        foreach ($siblings as $index => $sibling) {
            $newOrder = $index + 1;
            if ((int) $sibling->sort_order !== $newOrder) {
                self::query()->where('id', $sibling->id)->update(['sort_order' => $newOrder]);
            }
        }
    }

    /**
     * Recompute the four denormalized review cache columns for one
     * company in a single aggregate query. Called by
     * CompanyReviewObserver on create/update/delete and by the
     * `reviews:recompute-stats` backfill command.
     *
     * A review with rating >= 4 counts as a "recommend".
     *
     * When a company has no published reviews left, all four fields
     * reset to 0 / null so the frontend doesn't render stale numbers.
     */
    public static function recomputeReviewStats(int $companyId): void
    {
        $row = DB::table('company_reviews')
            ->selectRaw('
                COUNT(*)                                              AS review_count,
                AVG(rating)                                           AS rating_avg,
                SUM(CASE WHEN rating >= 4 THEN 1 ELSE 0 END)          AS recommends,
                SUM(CASE WHEN rating = 5 THEN 1 ELSE 0 END)           AS r5,
                SUM(CASE WHEN rating = 4 THEN 1 ELSE 0 END)           AS r4,
                SUM(CASE WHEN rating = 3 THEN 1 ELSE 0 END)           AS r3,
                SUM(CASE WHEN rating = 2 THEN 1 ELSE 0 END)           AS r2,
                SUM(CASE WHEN rating = 1 THEN 1 ELSE 0 END)           AS r1
            ')
            ->where('company_id', $companyId)
            ->where('status', 'published')
            ->first();

        $count = (int) ($row->review_count ?? 0);
        $avg = $count > 0 ? round((float) $row->rating_avg, 1) : 0;
        $pct = $count > 0
            ? (int) round(100 * ((int) $row->recommends) / $count)
            : 0;
        $breakdown = [
            '5' => (int) ($row->r5 ?? 0),
            '4' => (int) ($row->r4 ?? 0),
            '3' => (int) ($row->r3 ?? 0),
            '2' => (int) ($row->r2 ?? 0),
            '1' => (int) ($row->r1 ?? 0),
        ];

        self::query()->where('id', $companyId)->update([
            'review_count' => $count,
            'rating_avg' => $avg,
            'recommend_pct' => $pct,
            'rating_breakdown' => json_encode($breakdown, JSON_THROW_ON_ERROR),
        ]);
    }

    public function reorderToCurrentPosition(): void
    {
        $siblings = self::query()
            ->where('id', '!=', $this->id)
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get(['id', 'sort_order']);

        $target = (int) $this->sort_order;
        $order = 1;
        $placed = false;

        foreach ($siblings as $sibling) {
            if (! $placed && $order === $target) {
                self::query()->where('id', $this->id)->update(['sort_order' => $order]);
                $order++;
                $placed = true;
            }
            self::query()->where('id', $sibling->id)->update(['sort_order' => $order]);
            $order++;
        }
        if (! $placed) {
            self::query()->where('id', $this->id)->update(['sort_order' => $order]);
        }
    }

    public function primaryCity(): BelongsTo
    {
        return $this->belongsTo(City::class, 'primary_city_id');
    }

    public function primaryDistrict(): BelongsTo
    {
        return $this->belongsTo(District::class, 'primary_district_id');
    }

    public function translations(): HasMany
    {
        return $this->hasMany(CompanyTranslation::class);
    }

    public function translation(?string $lang = null): ?CompanyTranslation
    {
        $lang ??= app()->getLocale();

        return $this->translations->firstWhere('lang', $lang)
            ?? $this->translations->first();
    }

    public function contacts(): HasMany
    {
        return $this->hasMany(CompanyContact::class);
    }

    public function media(): HasMany
    {
        return $this->hasMany(CompanyMedia::class);
    }

    public function services(): BelongsToMany
    {
        return $this->belongsToMany(ServiceCategory::class, 'company_services', 'company_id', 'service_category_id')
            ->withPivot(['is_primary', 'price_from', 'price_unit'])
            ->withTimestamps();
    }

    public function serviceAreas(): BelongsToMany
    {
        return $this->belongsToMany(District::class, 'company_service_areas', 'company_id', 'district_id')
            ->withPivot(['is_home_base', 'response_hours'])
            ->withTimestamps();
    }

    public function faqs(): HasMany
    {
        return $this->hasMany(CompanyFaq::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(CompanyReview::class);
    }

    /* ---------------------------------------------- plan helpers */
    public function tier(): PlanTier
    {
        $raw = $this->plan_tier;
        if ($raw instanceof PlanTier) {
            return $raw;
        }

        return PlanTier::tryFrom((string) $raw) ?? PlanTier::Basic;
    }

    public function enforceLimits(): void
    {
        $tier = $this->tier();

        DB::transaction(function () use ($tier): void {
            $this->pruneCollection('media', $tier->photoLimit(), 'kind', 'gallery');
            $this->pruneContacts($tier->contactLimit());
            $this->pruneServices($tier->serviceLimit());
            $this->pruneAreas($tier->areaLimit());
            $this->pruneFaqs($tier->faqLimit());
            $this->stripLockedFeatures($tier);
        });
    }

    private function pruneCollection(
        string $relation,
        ?int $cap,
        ?string $filterCol = null,
        mixed $filterVal = null,
    ): void {
        if ($cap === null) {
            return;
        }

        $query = $this->{$relation}();
        if ($filterCol !== null) {
            $query = $query->where($filterCol, $filterVal);
        }
        $count = $query->count();
        if ($count <= $cap) {
            return;
        }

        $excess = $count - $cap;
        $victims = (clone $query)->orderBy('sort_order')->orderBy('id')->limit($excess)->get();
        foreach ($victims as $victim) {
            $victim->delete();
        }
    }

    private function pruneContacts(?int $cap): void
    {
        if ($cap === null) {
            return;
        }
        $count = $this->contacts()->count();
        if ($count <= $cap) {
            return;
        }
        $victims = $this->contacts()
            ->orderBy('is_primary')
            ->orderByDesc('id')
            ->limit($count - $cap)
            ->get();
        foreach ($victims as $victim) {
            $victim->delete();
        }
    }

    private function pruneServices(?int $cap): void
    {
        if ($cap === null) {
            return;
        }
        $ids = $this->services()
            ->orderByDesc('company_services.created_at')
            ->pluck('service_categories.id')
            ->all();
        $keep = array_slice($ids, -1 * $cap);
        $drop = array_diff($ids, $keep);
        if ($drop !== []) {
            $this->services()->detach($drop);
        }
    }

    private function pruneAreas(?int $cap): void
    {
        if ($cap === null) {
            return;
        }
        $ids = $this->serviceAreas()
            ->orderByDesc('company_service_areas.created_at')
            ->pluck('districts.id')
            ->all();
        $keep = array_slice($ids, -1 * $cap);
        $drop = array_diff($ids, $keep);
        if ($drop !== []) {
            $this->serviceAreas()->detach($drop);
        }
    }

    private function pruneFaqs(?int $cap): void
    {
        if ($cap === null) {
            return;
        }
        $count = $this->faqs()->count();
        if ($count <= $cap) {
            return;
        }
        $victims = $this->faqs()
            ->orderByDesc('sort_order')
            ->orderByDesc('id')
            ->limit($count - $cap)
            ->get();
        foreach ($victims as $victim) {
            $victim->delete();
        }
    }

    private function stripLockedFeatures(PlanTier $tier): void
    {
        $updates = [];

        if (! $tier->hasFeature('founded')) {
            $updates['founded_year'] = null;
        }
        if (! $tier->hasFeature('employees')) {
            $updates['employee_count'] = null;
        }
        if (! $tier->hasFeature('google')) {
            $updates['google_rating'] = null;
            $updates['google_review_count'] = 0;
        }
        if (! $tier->hasFeature('trust')) {
            $updates['verified'] = false;
            $updates['is_top_rated'] = false;
        }
        if (! $tier->hasFeature('cover') && $this->cover !== null) {
            if (! str_starts_with((string) $this->cover, 'http')) {
                Storage::disk('public')->delete((string) $this->cover);
            }
            $updates['cover'] = null;
        }

        if ($updates !== []) {
            $this->forceFill($updates)->save();
        }
        $translationUpdates = [];
        if (! $tier->hasFeature('short_description')) {
            $translationUpdates['short_description'] = null;
        }
        if (! $tier->hasFeature('about')) {
            $translationUpdates['about'] = null;
        }
        if ($translationUpdates !== []) {
            $this->translations()->update($translationUpdates);
        }
    }
}
