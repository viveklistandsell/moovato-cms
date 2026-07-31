<?php

declare(strict_types=1);

namespace App\Actions\Admin\Service\ParentCategory;

use App\Models\Language;
use App\Models\ServiceParentCategory;
use App\Models\ServiceParentCategoryTranslation;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

final readonly class CreateServiceParentCategory
{
    /**
     * @param  array{
     *   image?: string|null,
     *   status?: string,
     *   is_featured?: bool,
     *   is_popular?: bool,
     *   sort_order?: int,
     *   translations: array<string, array{name?: ?string, permalink?: ?string, short_description?: ?string}>
     * }  $data
     */
    public function handle(array $data): ServiceParentCategory
    {
        return DB::transaction(function () use ($data): ServiceParentCategory {
            $defaultCode = Language::query()
                ->where('lang_is_default', true)
                ->where('status', true)
                ->value('code') ?? array_key_first($data['translations']);

            $primary = $data['translations'][$defaultCode] ?? null;

            if ($primary === null || empty($primary['name']) || empty($primary['permalink'])) {
                throw new InvalidArgumentException('Default language translation is required.');
            }

            $sortOrder = $data['sort_order'] ?? ServiceParentCategory::nextSortOrder();

            /** @var ServiceParentCategory $parent */
            $parent = ServiceParentCategory::query()->create([
                'name' => $primary['name'],
                'image' => $data['image'] ?? null,
                'status' => $data['status'] ?? 'published',
                'is_featured' => $data['is_featured'] ?? false,
                'is_popular' => $data['is_popular'] ?? false,
                'sort_order' => $sortOrder,
            ]);

            foreach ($data['translations'] as $lang => $translation) {
                if (empty($translation['name']) || empty($translation['permalink'])) {
                    continue;
                }

                ServiceParentCategoryTranslation::query()->create([
                    'parent_category_id' => $parent->id,
                    'lang' => $lang,
                    'name' => $translation['name'],
                    'permalink' => $translation['permalink'],
                    'short_description' => $translation['short_description'] ?? null,
                ]);
            }

            return $parent->load('translations');
        });
    }
}
