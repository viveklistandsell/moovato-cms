<?php

declare(strict_types=1);

namespace App\Actions\Admin\Blog\Post;

use App\Models\Blog;
use App\Models\BlogTranslation;
use App\Models\Language;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

final readonly class CreateBlogPost
{
    /**
     * @param  array{
     *   user_id?: int|null,
     *   category_ids?: array<int, int>|null,
     *   tag_ids?: array<int, int>|null,
     *   image?: UploadedFile|string|null,
     *   status?: string,
     *   sort_order?: int,
     *   reading_time?: int,
     *   is_sticky?: bool,
     *   is_featured?: bool,
     *   translations: array<string, array{name?: ?string, permalink?: ?string, short_description?: ?string, content?: ?string, meta_title?: ?string, meta_description?: ?string, schema?: ?string, meta_image?: ?string}>
     * }  $data
     */
    public function handle(array $data): Blog
    {
        return DB::transaction(function () use ($data): Blog {
            $defaultCode = Language::query()
                ->where('lang_is_default', true)
                ->where('status', true)
                ->value('code') ?? array_key_first($data['translations']);

            $primary = $data['translations'][$defaultCode] ?? null;

            if ($primary === null || empty($primary['name']) || empty($primary['permalink'])) {
                throw new InvalidArgumentException('Default language translation is required.');
            }

            $imagePath = null;
            if (isset($data['image']) && $data['image'] instanceof UploadedFile) {
                $imagePath = $data['image']->store('blog/posts', 'public');
            } elseif (! empty($data['image_path'])) {
                $imagePath = $data['image_path'];
            }

            $sortOrder = $data['sort_order'] ?? Blog::nextSortOrder();

            $post = Blog::query()->create([
                'name' => $primary['name'],
                'permalink' => $primary['permalink'],
                'short_description' => $primary['short_description'] ?? null,
                'content' => $primary['content'] ?? null,
                'image' => $imagePath,
                'user_id' => $data['user_id'] ?? null,
                'status' => $data['status'] ?? 'published',
                'sort_order' => $sortOrder,
                'reading_time' => $data['reading_time'] ?? 0,
                'is_sticky' => $data['is_sticky'] ?? false,
                'is_featured' => $data['is_featured'] ?? false,
            ]);

            $categoryIds = $data['category_ids'] ?? [];
            if (count($categoryIds) > 0) {
                $sync = [];
                foreach ($categoryIds as $i => $id) {
                    $sync[$id] = ['is_primary' => $i === 0];
                }
                $post->categories()->sync($sync);
            }

            $tagIds = $data['tag_ids'] ?? [];
            if (count($tagIds) > 0) {
                $post->tags()->sync($tagIds);
            }

            foreach ($data['translations'] as $lang => $translation) {
                if (empty($translation['name']) || empty($translation['permalink'])) {
                    continue;
                }

                BlogTranslation::query()->create([
                    'blog_id' => $post->id,
                    'lang' => $lang,
                    'name' => $translation['name'],
                    'permalink' => $translation['permalink'],
                    'short_description' => $translation['short_description'] ?? null,
                    'content' => $translation['content'] ?? null,
                    'meta_title' => $translation['meta_title'] ?? null,
                    'meta_description' => $translation['meta_description'] ?? null,
                    'schema' => $this->normalizeSchema($translation['schema'] ?? null),
                    'meta_image' => $translation['meta_image'] ?? null,
                ]);
            }

            $post->reorderToCurrentPosition();

            return $post->load(['translations', 'categories', 'tags']);
        });
    }

    private function normalizeSchema(mixed $raw): ?array
    {
        if ($raw === null || $raw === '' || $raw === []) {
            return null;
        }
        if (is_array($raw)) {
            return $raw;
        }

        $decoded = json_decode((string) $raw, true);

        return is_array($decoded) ? $decoded : null;
    }
}
