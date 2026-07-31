<?php

declare(strict_types=1);

namespace App\Actions\Admin\Service\Category;

use App\Models\Language;
use App\Models\ServiceCategory;
use App\Models\ServiceCategoryTranslation;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

final readonly class CreateServiceCategory
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
    public function handle(array $data): ServiceCategory
    {
        return DB::transaction(function () use ($data): ServiceCategory {
            $defaultCode = Language::query()
                ->where('lang_is_default', true)
                ->where('status', true)
                ->value('code') ?? array_key_first($data['translations']);

            $primary = $data['translations'][$defaultCode] ?? null;

            if ($primary === null || empty($primary['name']) || empty($primary['permalink'])) {
                throw new InvalidArgumentException('Default language translation is required.');
            }

            $parentCategoryId = (int) $data['parent_category_id'];
            $sortOrder = $data['sort_order'] ?? ServiceCategory::nextSortOrder($parentCategoryId);

            /** @var ServiceCategory $category */
            $category = ServiceCategory::query()->create([
                'name' => $primary['name'],
                'parent_category_id' => $parentCategoryId,
                'image' => $data['image'] ?? null,
                'status' => $data['status'] ?? 'published',
                'is_featured' => $data['is_featured'] ?? false,
                'is_popular' => $data['is_popular'] ?? false,
                'sort_order' => $sortOrder,
            ]);

            $category->reorderToCurrentPosition();

            foreach ($data['translations'] as $lang => $translation) {
                if (empty($translation['name']) || empty($translation['permalink'])) {
                    continue;
                }

                ServiceCategoryTranslation::query()->create([
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
