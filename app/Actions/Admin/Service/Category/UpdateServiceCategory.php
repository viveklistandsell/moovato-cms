<?php

declare(strict_types=1);

namespace App\Actions\Admin\Service\Category;

use App\Models\Language;
use App\Models\ServiceCategory;
use App\Models\ServiceCategoryTranslation;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;
use RuntimeException;

final readonly class UpdateServiceCategory
{
    /**
     * @param  array{
     *   parent_category_id: int,
     *   image?: string|null,
     *   status?: string,
     *   is_featured?: bool,
     *   is_popular?: bool,
     *   sort_order?: int,
     *   translations: array<string, array{name?: ?string, permalink?: ?string, short_description?: ?string}>
     * }  $data
     */
    public function handle(ServiceCategory $category, array $data): ServiceCategory
    {
        return DB::transaction(function () use ($category, $data): ServiceCategory {
            $defaultCode = Language::query()
                ->where('lang_is_default', true)
                ->where('status', true)
                ->value('code') ?? array_key_first($data['translations']);

            $primary = $data['translations'][$defaultCode] ?? null;

            if ($primary === null || empty($primary['name']) || empty($primary['permalink'])) {
                throw new InvalidArgumentException('Default language translation is required.');
            }

            $oldParentCategoryId = $category->parent_category_id;
            $newParentCategoryId = (int) $data['parent_category_id'];

            $category->update([
                'name' => $primary['name'],
                'parent_category_id' => $newParentCategoryId,
                'image' => $data['image'] ?? $category->image,
                'status' => $data['status'] ?? $category->status,
                'is_featured' => $data['is_featured'] ?? $category->is_featured,
                'is_popular' => $data['is_popular'] ?? $category->is_popular,
                'sort_order' => $data['sort_order'] ?? $category->sort_order,
            ]);

            $category->reorderToCurrentPosition();

            if ($oldParentCategoryId !== null && $oldParentCategoryId !== $newParentCategoryId) {
                // Old parent's siblings may now have a gap — renumber them.
                ServiceCategory::compactSiblings((int) $oldParentCategoryId);
            }

            foreach ($data['translations'] as $lang => $translation) {
                if (empty($translation['name']) || empty($translation['permalink'])) {
                    ServiceCategoryTranslation::query()
                        ->where('category_id', $category->id)
                        ->where('lang', $lang)
                        ->delete();

                    continue;
                }

                ServiceCategoryTranslation::query()->updateOrCreate(
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
