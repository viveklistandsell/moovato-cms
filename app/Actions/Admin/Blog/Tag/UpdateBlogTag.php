<?php

declare(strict_types=1);

namespace App\Actions\Admin\Blog\Tag;

use App\Models\BlogTag;
use App\Models\BlogTagTranslation;
use App\Models\Language;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;
use RuntimeException;

final readonly class UpdateBlogTag
{
    /**
     * @param  array{
     *   status?: string,
     *   sort_order?: int,
     *   translations: array<string, array{name?: ?string, permalink?: ?string, short_description?: ?string}>
     * }  $data
     */
    public function handle(BlogTag $tag, array $data): BlogTag
    {
        return DB::transaction(function () use ($tag, $data): BlogTag {
            $defaultCode = Language::query()
                ->where('lang_is_default', true)
                ->where('status', true)
                ->value('code') ?? array_key_first($data['translations']);

            $primary = $data['translations'][$defaultCode] ?? null;

            if ($primary === null || empty($primary['name']) || empty($primary['permalink'])) {
                throw new InvalidArgumentException('Default language translation is required.');
            }

            $tag->update([
                'name' => $primary['name'],
                'permalink' => $primary['permalink'],
                'short_description' => $primary['short_description'] ?? null,
                'status' => $data['status'] ?? $tag->status,
                'sort_order' => $data['sort_order'] ?? $tag->sort_order,
            ]);

            foreach ($data['translations'] as $lang => $translation) {
                if (empty($translation['name']) || empty($translation['permalink'])) {
                    BlogTagTranslation::query()
                        ->where('tag_id', $tag->id)
                        ->where('lang', $lang)
                        ->delete();

                    continue;
                }

                BlogTagTranslation::query()->updateOrCreate(
                    ['tag_id' => $tag->id, 'lang' => $lang],
                    [
                        'name' => $translation['name'],
                        'permalink' => $translation['permalink'],
                        'short_description' => $translation['short_description'] ?? null,
                    ],
                );
            }

            $tag->reorderToCurrentPosition();

            return $tag->fresh(['translations'])
                ?? throw new RuntimeException('Failed to reload tag after update.');
        });
    }
}
