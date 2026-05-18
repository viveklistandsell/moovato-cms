<?php

declare(strict_types=1);

namespace App\Actions\Admin\Page\Category;

use App\Models\PageCategory;
use App\Models\PageCategoryTranslation;
use Illuminate\Support\Facades\DB;

final readonly class UpdatePageCategory
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
    public function handle(PageCategory $category, array $data): PageCategory
    {
        return DB::transaction(function () use ($category, $data): PageCategory {
            $defaultPrimary = null;
            foreach ($data['translations'] as $lang => $translation) {
                if (! empty($translation['title']) && ! empty($translation['permalink'])) {
                    $defaultPrimary = ['lang' => $lang, ...$translation];
                    break;
                }
            }

            $category->fill([
                'title' => $defaultPrimary['title'] ?? $category->title,
                'permalink' => $defaultPrimary['permalink'] ?? $category->permalink,
                'parent_id' => $data['parent_id'] ?? null,
                'is_default' => $data['is_default'] ?? false,
                'status' => $data['status'] ?? $category->status,
                'sort_order' => $data['sort_order'] ?? $category->sort_order,
            ]);

            $category->save();

            if (($data['is_default'] ?? false) === true) {
                PageCategory::clearOtherDefaults($category->id);
            }

            $category->translations()->delete();
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
