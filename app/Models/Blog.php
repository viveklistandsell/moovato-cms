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
    'name', 'permalink', 'image', 'short_description', 'content',
    'is_sticky', 'is_featured', 'sort_order', 'user_id',
    'status', 'view_count', 'reading_time',
])]
final class Blog extends Model
{
    public static function compactAll(): void
    {
        $posts = self::query()->orderBy('sort_order')->orderBy('id')->get();

        foreach ($posts as $index => $post) {
            $newOrder = $index + 1;
            if ((int) $post->sort_order !== $newOrder) {
                self::query()->where('id', $post->id)->update(['sort_order' => $newOrder]);
            }
        }
    }

    public static function nextSortOrder(): int
    {
        return ((int) self::query()->max('sort_order')) + 1;
    }

    /**
     * Place this post at its current sort_order (1-based) and renumber
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

        foreach ($rest as $index => $post) {
            $newOrder = $index + 1;
            if ((int) $post->sort_order !== $newOrder) {
                self::query()->where('id', $post->id)->update(['sort_order' => $newOrder]);
            }
        }
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(BlogCategory::class, 'blog_category', 'blog_id', 'category_id')
            ->withPivot('is_primary')
            ->withTimestamps();
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(BlogTag::class, 'blog_tag', 'blog_id', 'tag_id')
            ->withTimestamps();
    }

    public function translations(): HasMany
    {
        return $this->hasMany(BlogTranslation::class);
    }

    public function translation(?string $lang = null): ?BlogTranslation
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
            'is_sticky' => 'boolean',
            'is_featured' => 'boolean',
            'sort_order' => 'integer',
            'view_count' => 'integer',
            'reading_time' => 'integer',
        ];
    }
}
