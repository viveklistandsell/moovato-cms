<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'name', 'permalink', 'parent_id', 'icon', 'short_description',
    'is_featured', 'is_default', 'status', 'sort_order',
])]
final class BlogCategory extends Model
{
    protected $table = 'blog_categories';

    /**
     * Renumber all siblings under the given parent so sort_order is 1..N with no gaps.
     */
    public static function compactSiblings(?int $parentId): void
    {
        $siblings = self::query()
            ->where('parent_id', $parentId)
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

    /**
     * Next available sort_order for a new sibling under the given parent.
     */
    public static function nextSortOrder(?int $parentId = null): int
    {
        return ((int) self::query()->where('parent_id', $parentId)->max('sort_order')) + 1;
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id')->orderBy('sort_order');
    }

    public function translations(): HasMany
    {
        return $this->hasMany(BlogCategoryTranslation::class, 'category_id');
    }

    public function translation(?string $lang = null): ?BlogCategoryTranslation
    {
        $lang ??= app()->getLocale();

        return $this->translations->firstWhere('lang', $lang)
            ?? $this->translations->first();
    }

    public function blogs(): BelongsToMany
    {
        return $this->belongsToMany(Blog::class, 'blog_category', 'category_id', 'blog_id')
            ->withPivot('is_primary')
            ->withTimestamps();
    }

    /**
     * Move this category into the position given by its current sort_order
     * (1-based) within its parent scope, and renumber siblings sequentially.
     */
    public function reorderToCurrentPosition(): void
    {
        $siblings = self::query()
            ->where('parent_id', $this->parent_id)
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
            'is_default' => 'boolean',
            'sort_order' => 'integer',
        ];
    }
}
