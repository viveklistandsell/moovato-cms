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
