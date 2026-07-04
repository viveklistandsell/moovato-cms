<?php

declare(strict_types=1);

namespace App\Actions\Admin\Service\ParentCategory;

use App\Models\Language;
use App\Models\ServiceParentCategory;
use App\Models\ServiceParentCategoryTranslation;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;
use RuntimeException;

final readonly class UpdateServiceParentCategory
{
    /**
     * @param  array{
     *   icon?: string|null,
     *   status?: string,
     *   is_featured?: bool,
     *   is_popular?: bool,
     *   sort_order?: int,
     *   translations: array<string, array{name?: ?string, permalink?: ?string, short_description?: ?string}>
     * }  $data
     */
    public function handle(ServiceParentCategory $parent, array $data): ServiceParentCategory
    {
        return DB::transaction(function () use ($parent, $data): ServiceParentCategory {
            $defaultCode = Language::query()
                ->where('lang_is_default', true)
                ->where('status', true)
                ->value('code') ?? array_key_first($data['translations']);

            $primary = $data['translations'][$defaultCode] ?? null;

            if ($primary === null || empty($primary['name']) || empty($primary['permalink'])) {
                throw new InvalidArgumentException('Default language translation is required.');
            }

            $parent->update([
                'name' => $primary['name'],
                'icon' => $data['icon'] ?? $parent->icon,
                'status' => $data['status'] ?? $parent->status,
                'is_featured' => $data['is_featured'] ?? $parent->is_featured,
                'is_popular' => $data['is_popular'] ?? $parent->is_popular,
                'sort_order' => $data['sort_order'] ?? $parent->sort_order,
            ]);

            foreach ($data['translations'] as $lang => $translation) {
                if (empty($translation['name']) || empty($translation['permalink'])) {
                    ServiceParentCategoryTranslation::query()
                        ->where('parent_category_id', $parent->id)
                        ->where('lang', $lang)
                        ->delete();

                    continue;
                }

                ServiceParentCategoryTranslation::query()->updateOrCreate(
                    ['parent_category_id' => $parent->id, 'lang' => $lang],
                    [
                        'name' => $translation['name'],
                        'permalink' => $translation['permalink'],
                        'short_description' => $translation['short_description'] ?? null,
                    ],
                );
            }

            return $parent->fresh(['translations'])
                ?? throw new RuntimeException('Failed to reload parent category after update.');
        });
    }
}
