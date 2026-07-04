<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'parent_category_id', 'name', 'icon',
    'is_featured', 'is_popular', 'status', 'sort_order',
])]
final class ServiceCategory extends Model
{
    protected $table = 'service_categories';

    /**
     * Next sort_order for a new sibling within the given parent-category
     * scope. Categories are now flat under a parent (no self-hierarchy),
     * so sort_order only needs to be unique per `parent_category_id`.
     */
    public static function nextSortOrder(?int $parentCategoryId = null): int
    {
        $query = self::query();
        if ($parentCategoryId !== null) {
            $query->where('parent_category_id', $parentCategoryId);
        }

        return ((int) $query->max('sort_order')) + 1;
    }

    /**
     * Renumber siblings under the given parent so sort_order is 1..N.
     * Called after inserts/updates that shift a row into or out of a
     * parent bucket.
     */
    public static function compactSiblings(int $parentCategoryId): void
    {
        $siblings = self::query()
            ->where('parent_category_id', $parentCategoryId)
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();

        foreach ($siblings as $index => $sibling) {
            $newOrder = $index + 1;
            if ((int) $sibling->sort_order !== $newOrder) {
                self::query()->where('id', $sibling->id)->update(['sort_order' => $newOrder]);
            }
        }
    }

    public function parentCategory(): BelongsTo
    {
        return $this->belongsTo(ServiceParentCategory::class, 'parent_category_id');
    }

    public function translations(): HasMany
    {
        return $this->hasMany(ServiceCategoryTranslation::class, 'category_id');
    }

    public function translation(?string $lang = null): ?ServiceCategoryTranslation
    {
        $lang ??= app()->getLocale();

        return $this->translations->firstWhere('lang', $lang)
            ?? $this->translations->first();
    }

    /**
     * Move this category into its `sort_order` position within its parent
     * scope and renumber siblings sequentially.
     */
    public function reorderToCurrentPosition(): void
    {
        $siblings = self::query()
            ->where('parent_category_id', $this->parent_category_id)
            ->where('id', '!=', $this->id)
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();

        $targetIndex = max(0, min($siblings->count(), $this->sort_order - 1));
        $siblings->splice($targetIndex, 0, [$this]);

        foreach ($siblings as $index => $sibling) {
            $newOrder = $index + 1;
            if ((int) $sibling->sort_order !== $newOrder) {
                self::query()->where('id', $sibling->id)->update(['sort_order' => $newOrder]);
            }
        }
    }

    protected function casts(): array
    {
        return [
            'is_featured' => 'boolean',
            'is_popular' => 'boolean',
            'sort_order' => 'integer',
        ];
    }
}
