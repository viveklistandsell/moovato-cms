<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'name', 'icon',
    'is_featured', 'is_popular', 'status', 'sort_order',
])]
final class ServiceParentCategory extends Model
{
    protected $table = 'service_parent_categories';

    public static function nextSortOrder(): int
    {
        return ((int) self::query()->max('sort_order')) + 1;
    }
    public static function compactSiblings(): void
    {
        $siblings = self::query()
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
    public function reorderToCurrentPosition(): void
    {
        $siblings = self::query()
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

    public function categories(): HasMany
    {
        return $this->hasMany(ServiceCategory::class, 'parent_category_id')
            ->orderBy('sort_order');
    }

    public function translations(): HasMany
    {
        return $this->hasMany(ServiceParentCategoryTranslation::class, 'parent_category_id');
    }

    public function translation(?string $lang = null): ?ServiceParentCategoryTranslation
    {
        $lang ??= app()->getLocale();

        return $this->translations->firstWhere('lang', $lang)
            ?? $this->translations->first();
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
