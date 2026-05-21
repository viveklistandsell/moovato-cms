<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'title', 'permalink', 'image', 'template',
    'is_home', 'user_id', 'status',
])]
final class Page extends Model
{
    /**
     * Ensure exactly one homepage: clear is_home on every other page.
     */
    public static function clearOtherHomes(int $exceptId): void
    {
        self::query()
            ->where('id', '!=', $exceptId)
            ->where('is_home', true)
            ->update(['is_home' => false]);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(PageCategory::class, 'page_category', 'page_id', 'category_id')
            ->withPivot('is_primary')
            ->withTimestamps();
    }

    public function translations(): HasMany
    {
        return $this->hasMany(PageTranslation::class);
    }

    public function widgets(): HasMany
    {
        return $this->hasMany(PageWidget::class)->orderBy('position');
    }

    public function translation(?string $lang = null): ?PageTranslation
    {
        $lang ??= app()->getLocale();

        return $this->translations->firstWhere('lang', $lang)
            ?? $this->translations->first();
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', 'published');
    }

    protected function casts(): array
    {
        return [
            'is_home' => 'boolean',
        ];
    }
}
