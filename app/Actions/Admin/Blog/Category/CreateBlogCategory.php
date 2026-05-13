<?php

declare(strict_types=1);

namespace App\Actions\Admin\Blog\Category;

use App\Models\BlogCategory;
use App\Models\BlogCategoryTranslation;
use App\Models\Language;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

final readonly class CreateBlogCategory
{
    /**
     * @param  array{
     *   parent_id?: int|null,
     *   icon?: string|null,
     *   status?: string,
     *   is_featured?: bool,
     *   is_default?: bool,
     *   sort_order?: int,
     *   translations: array<string, array{name?: ?string, permalink?: ?string, short_description?: ?string}>
     * }  $data
     */
    public function handle(array $data): BlogCategory
    {
        return DB::transaction(function () use ($data): BlogCategory {
            $defaultCode = Language::query()
                ->where('lang_is_default', true)
                ->where('status', true)
                ->value('code') ?? array_key_first($data['translations']);

            $primary = $data['translations'][$defaultCode] ?? null;

            if ($primary === null || empty($primary['name']) || empty($primary['permalink'])) {
                throw new InvalidArgumentException('Default language translation is required.');
            }

            // If setting this as default, unset any existing default first.
            if ($data['is_default'] ?? false) {
                BlogCategory::query()->where('is_default', true)->update(['is_default' => false]);
            }

            $parentId = $data['parent_id'] ?? null;
            $sortOrder = $data['sort_order'] ?? BlogCategory::nextSortOrder($parentId);

            $category = BlogCategory::query()->create([
                'name' => $primary['name'],
                'permalink' => $primary['permalink'],
                'parent_id' => $parentId,
                'icon' => $data['icon'] ?? null,
                'short_description' => $primary['short_description'] ?? null,
                'status' => $data['status'] ?? 'published',
                'is_featured' => $data['is_featured'] ?? false,
                'is_default' => $data['is_default'] ?? false,
                'sort_order' => $sortOrder,
            ]);

            $category->reorderToCurrentPosition();

            foreach ($data['translations'] as $lang => $translation) {
                if (empty($translation['name']) || empty($translation['permalink'])) {
                    continue;
                }

                BlogCategoryTranslation::query()->create([
                    'category_id' => $category->id,
                    'lang' => $lang,
                    'name' => $translation['name'],
                    'permalink' => $translation['permalink'],
                    'short_description' => $translation['short_description'] ?? null,
                ]);
            }

            return $category->load('translations');
        });
    }
}
