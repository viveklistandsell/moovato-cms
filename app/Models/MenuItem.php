<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'menu_id', 'parent_id', 'sort_order', 'link_type', 'link_id',
    'open_in_new_tab', 'css_class', 'is_active',
])]
final class MenuItem extends Model
{
    public const TYPE_HOME = 'home';

    public const TYPE_PAGE = 'page';

    public const TYPE_CATEGORY = 'category';

    public const TYPE_URL = 'url';

    /** @return array<int, string> */
    public static function linkTypes(): array
    {
        return [self::TYPE_HOME, self::TYPE_PAGE, self::TYPE_CATEGORY, self::TYPE_URL];
    }

    public function menu(): BelongsTo
    {
        return $this->belongsTo(Menu::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id')
            ->orderBy('sort_order')
            ->orderBy('id');
    }

    public function translations(): HasMany
    {
        return $this->hasMany(MenuItemTranslation::class);
    }

    public function translation(?string $lang = null): ?MenuItemTranslation
    {
        $lang ??= app()->getLocale();

        return $this->translations->firstWhere('lang', $lang)
            ?? $this->translations->first();
    }

    /**
     * For 'page' items — resolves to the linked Page so the caller can look
     * up its translation and build a per-locale URL.
     */
    public function linkedPage(): BelongsTo
    {
        return $this->belongsTo(Page::class, 'link_id');
    }

    /**
     * For 'category' items — linked PageCategory for label fallback (the
     * dropdown header still needs a name even if the admin omits the
     * translation override).
     */
    public function linkedCategory(): BelongsTo
    {
        return $this->belongsTo(PageCategory::class, 'link_id');
    }

    protected function casts(): array
    {
        return [
            'open_in_new_tab' => 'boolean',
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ];
    }
}
