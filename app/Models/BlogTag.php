<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'name', 'permalink', 'short_description', 'status', 'sort_order',
])]
final class BlogTag extends Model
{
    protected $table = 'blog_tags';

    /**
     * Renumber all tags so sort_order is 1..N with no gaps.
     */
    public static function compactAll(): void
    {
        $tags = self::query()->orderBy('sort_order')->orderBy('id')->get();

        foreach ($tags as $index => $tag) {
            $newOrder = $index + 1;
            if ((int) $tag->sort_order !== $newOrder) {
                self::query()->where('id', $tag->id)->update(['sort_order' => $newOrder]);
            }
        }
    }

    /**
     * Next available sort_order for a new tag.
     */
    public static function nextSortOrder(): int
    {
        return ((int) self::query()->max('sort_order')) + 1;
    }

    /**
     * Place this tag at its current sort_order (1-based) and renumber the rest
     * of the list 1..N with no gaps.
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

        foreach ($rest as $index => $tag) {
            $newOrder = $index + 1;
            if ((int) $tag->sort_order !== $newOrder) {
                self::query()->where('id', $tag->id)->update(['sort_order' => $newOrder]);
            }
        }
    }

    public function translations(): HasMany
    {
        return $this->hasMany(BlogTagTranslation::class, 'tag_id');
    }

    public function translation(?string $lang = null): ?BlogTagTranslation
    {
        $lang ??= app()->getLocale();

        return $this->translations->firstWhere('lang', $lang)
            ?? $this->translations->first();
    }

    public function blogs(): BelongsToMany
    {
        return $this->belongsToMany(Blog::class, 'blog_tag', 'tag_id', 'blog_id')
            ->withTimestamps();
    }

    protected function casts(): array
    {
        return [
            'sort_order' => 'integer',
        ];
    }
}
