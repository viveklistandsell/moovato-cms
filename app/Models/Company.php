<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'primary_city_id', 'primary_district_id', 'street', 'postal_code',
    'logo', 'cover', 'verified', 'is_top_rated', 'plan_tier',
    'rating_avg', 'review_count', 'recommend_pct', 'rating_breakdown',
    'google_rating', 'google_review_count',
    'founded_year', 'employee_count', 'status', 'sort_order',
])]
final class Company extends Model
{
    protected $table = 'companies';

    protected $casts = [
        'verified' => 'boolean',
        'is_top_rated' => 'boolean',
        'rating_avg' => 'decimal:1',
        'google_rating' => 'decimal:1',
        'rating_breakdown' => 'array',
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
}
