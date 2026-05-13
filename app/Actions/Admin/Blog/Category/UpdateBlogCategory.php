<?php

declare(strict_types=1);

namespace App\Actions\Admin\Blog\Category;

use App\Models\BlogCategory;
use App\Models\BlogCategoryTranslation;
use App\Models\Language;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;
use RuntimeException;

final readonly class UpdateBlogCategory
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
    public function handle(BlogCategory $category, array $data): BlogCategory
    {
        return DB::transaction(function () use ($category, $data): BlogCategory {
            $defaultCode = Language::query()
                ->where('lang_is_default', true)
                ->where('status', true)
                ->value('code') ?? array_key_first($data['translations']);

            $primary = $data['translations'][$defaultCode] ?? null;

            if ($primary === null || empty($primary['name']) || empty($primary['permalink'])) {
                throw new InvalidArgumentException('Default language translation is required.');
            }

            if (($data['is_default'] ?? false) && ! $category->is_default) {
                BlogCategory::query()
                    ->where('is_default', true)
                    ->where('id', '!=', $category->id)
                    ->update(['is_default' => false]);
            }

            $oldParentId = $category->parent_id;

            $category->update([
                'name' => $primary['name'],
                'permalink' => $primary['permalink'],
                'parent_id' => $data['parent_id'] ?? null,
                'icon' => $data['icon'] ?? $category->icon,
                'short_description' => $primary['short_description'] ?? null,
                'status' => $data['status'] ?? $category->status,
                'is_featured' => $data['is_featured'] ?? $category->is_featured,
                'is_default' => $data['is_default'] ?? $category->is_default,
                'sort_order' => $data['sort_order'] ?? $category->sort_order,
            ]);

            $category->reorderToCurrentPosition();

            if ($oldParentId !== $category->parent_id) {
                BlogCategory::compactSiblings($oldParentId);
            }

            foreach ($data['translations'] as $lang => $translation) {
                if (empty($translation['name']) || empty($translation['permalink'])) {
                    BlogCategoryTranslation::query()
                        ->where('category_id', $category->id)
                        ->where('lang', $lang)
                        ->delete();

                    continue;
                }

                BlogCategoryTranslation::query()->updateOrCreate(
                    ['category_id' => $category->id, 'lang' => $lang],
                    [
                        'name' => $translation['name'],
                        'permalink' => $translation['permalink'],
                        'short_description' => $translation['short_description'] ?? null,
                    ],
                );
            }

            return $category->fresh(['translations'])
                ?? throw new RuntimeException('Failed to reload category after update.');
        });
    }
}
