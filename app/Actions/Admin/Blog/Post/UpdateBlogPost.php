<?php

declare(strict_types=1);

namespace App\Actions\Admin\Blog\Post;

use App\Models\Blog;
use App\Models\BlogTranslation;
use App\Models\Language;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use InvalidArgumentException;
use RuntimeException;

final readonly class UpdateBlogPost
{
    /**
     * @param  array{
     *   user_id?: int|null,
     *   category_ids?: array<int, int>|null,
     *   tag_ids?: array<int, int>|null,
     *   image?: UploadedFile|string|null,
     *   remove_image?: bool,
     *   status?: string,
     *   sort_order?: int,
     *   reading_time?: int,
     *   is_sticky?: bool,
     *   is_featured?: bool,
     *   translations: array<string, array{name?: ?string, permalink?: ?string, short_description?: ?string, content?: ?string, meta_title?: ?string, meta_description?: ?string}>
     * }  $data
     */
    public function handle(Blog $post, array $data): Blog
    {
        return DB::transaction(function () use ($post, $data): Blog {
            $defaultCode = Language::query()
                ->where('lang_is_default', true)
                ->where('status', true)
                ->value('code') ?? array_key_first($data['translations']);

            $primary = $data['translations'][$defaultCode] ?? null;

            if ($primary === null || empty($primary['name']) || empty($primary['permalink'])) {
                throw new InvalidArgumentException('Default language translation is required.');
            }

            $imagePath = $post->image;
            if (isset($data['image']) && $data['image'] instanceof UploadedFile) {
                if ($imagePath !== null) {
                    Storage::disk('public')->delete($imagePath);
                }
                $imagePath = $data['image']->store('blog/posts', 'public');
            } elseif (! empty($data['remove_image']) && $imagePath !== null) {
                Storage::disk('public')->delete($imagePath);
                $imagePath = null;
            }

            $post->update([
                'name' => $primary['name'],
                'permalink' => $primary['permalink'],
                'short_description' => $primary['short_description'] ?? null,
                'content' => $primary['content'] ?? null,
                'image' => $imagePath,
                'user_id' => $data['user_id'] ?? $post->user_id,
                'status' => $data['status'] ?? $post->status,
                'sort_order' => $data['sort_order'] ?? $post->sort_order,
                'reading_time' => $data['reading_time'] ?? $post->reading_time,
                'is_sticky' => $data['is_sticky'] ?? $post->is_sticky,
                'is_featured' => $data['is_featured'] ?? $post->is_featured,
            ]);

            $sync = [];
            foreach ($data['category_ids'] ?? [] as $i => $id) {
                $sync[$id] = ['is_primary' => $i === 0];
            }
            $post->categories()->sync($sync);

            $post->tags()->sync($data['tag_ids'] ?? []);

            foreach ($data['translations'] as $lang => $translation) {
                if (empty($translation['name']) || empty($translation['permalink'])) {
                    BlogTranslation::query()
                        ->where('blog_id', $post->id)
                        ->where('lang', $lang)
                        ->delete();

                    continue;
                }

                BlogTranslation::query()->updateOrCreate(
                    ['blog_id' => $post->id, 'lang' => $lang],
                    [
                        'name' => $translation['name'],
                        'permalink' => $translation['permalink'],
                        'short_description' => $translation['short_description'] ?? null,
                        'content' => $translation['content'] ?? null,
                    ],
                );
            }

            $post->reorderToCurrentPosition();

            return $post->fresh(['translations', 'categories', 'tags'])
                ?? throw new RuntimeException('Failed to reload post after update.');
        });
    }
}
