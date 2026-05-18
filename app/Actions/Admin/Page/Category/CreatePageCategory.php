<?php

declare(strict_types=1);

namespace App\Actions\Admin\Page\Category;

use App\Models\Language;
use App\Models\PageCategory;
use App\Models\PageCategoryTranslation;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

final readonly class CreatePageCategory
{
    /**
     * @param  array{
     *   parent_id?: int|null,
     *   is_default?: bool,
     *   status?: string,
     *   sort_order?: int,
     *   translations: array<string, array{title?: ?string, permalink?: ?string}>
     * }  $data
     */
    public function handle(array $data): PageCategory
    {
        return DB::transaction(function () use ($data): PageCategory {
            $defaultCode = Language::query()
                ->where('lang_is_default', true)
                ->where('status', true)
                ->value('code') ?? array_key_first($data['translations']);

            $primary = $data['translations'][$defaultCode] ?? null;

            if ($primary === null || empty($primary['title']) || empty($primary['permalink'])) {
                throw new InvalidArgumentException('Default language translation is required.');
            }

            $sortOrder = $data['sort_order'] ?? PageCategory::nextSortOrder();

            $category = PageCategory::query()->create([
                'title' => $primary['title'],
                'permalink' => $primary['permalink'],
                'parent_id' => $data['parent_id'] ?? null,
                'is_default' => $data['is_default'] ?? false,
                'status' => $data['status'] ?? 'published',
                'sort_order' => $sortOrder,
            ]);

            if (($data['is_default'] ?? false) === true) {
                PageCategory::clearOtherDefaults($category->id);
            }

            foreach ($data['translations'] as $lang => $translation) {
                if (empty($translation['title']) || empty($translation['permalink'])) {
                    continue;
                }

                PageCategoryTranslation::query()->create([
                    'category_id' => $category->id,
                    'lang' => $lang,
                    'title' => $translation['title'],
                    'permalink' => $translation['permalink'],
                ]);
            }

            $category->reorderToCurrentPosition();

            return $category->load('translations');
        });
    }
}
