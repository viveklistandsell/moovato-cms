<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'title', 'permalink', 'parent_id', 'is_default', 'status', 'sort_order',
])]
final class PageCategory extends Model
{
    protected $table = 'page_categories';

    public static function compactAll(): void
    {
        $cats = self::query()->orderBy('sort_order')->orderBy('id')->get();

        foreach ($cats as $index => $cat) {
            $newOrder = $index + 1;
            if ((int) $cat->sort_order !== $newOrder) {
                self::query()->where('id', $cat->id)->update(['sort_order' => $newOrder]);
            }
        }
    }

    public static function nextSortOrder(): int
    {
        return ((int) self::query()->max('sort_order')) + 1;
    }

    /**
     * Ensure exactly one default: clear is_default on every other category.
     */
    public static function clearOtherDefaults(int $exceptId): void
    {
        self::query()
            ->where('id', '!=', $exceptId)
            ->where('is_default', true)
            ->update(['is_default' => false]);
    }

    /**
     * Place this category at its current sort_order (1-based) and renumber
     * the rest of the list 1..N with no gaps.
     */
    public function reorderToCurrentPosition(): void
    {
        $rest = self::query()
            ->where('id', '!=', $this->id)
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();

        $targetIndex = max(0, min($rest->count(), $this->sort_order - 1));
        $rest->splice($targetIndex, 0, [$this]);

        foreach ($rest as $index => $cat) {
            $newOrder = $index + 1;
            if ((int) $cat->sort_order !== $newOrder) {
                self::query()->where('id', $cat->id)->update(['sort_order' => $newOrder]);
            }
        }
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id');
    }

    public function pages(): BelongsToMany
    {
        return $this->belongsToMany(Page::class, 'page_category', 'category_id', 'page_id')
            ->withPivot('is_primary')
            ->withTimestamps();
    }

    public function translations(): HasMany
    {
        return $this->hasMany(PageCategoryTranslation::class, 'category_id');
    }

    public function translation(?string $lang = null): ?PageCategoryTranslation
    {
        $lang ??= app()->getLocale();

        return $this->translations->firstWhere('lang', $lang)
            ?? $this->translations->first();
    }

    protected function casts(): array
    {
        return [
            'is_default' => 'boolean',
            'sort_order' => 'integer',
        ];
    }
}
