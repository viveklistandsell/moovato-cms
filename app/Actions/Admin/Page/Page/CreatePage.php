<?php

declare(strict_types=1);

namespace App\Actions\Admin\Page\Page;

use App\Models\Language;
use App\Models\Page;
use App\Models\PageTranslation;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

final readonly class CreatePage
{
    /**
     * @param  array{
     *   user_id?: int|null,
     *   category_ids?: array<int, int>|null,
     *   image?: UploadedFile|string|null,
     *   template?: string,
     *   is_home?: bool,
     *   status?: string,
     *   translations: array<string, array{title?: ?string, permalink?: ?string, content?: ?string}>
     * }  $data
     */
    public function handle(array $data): Page
    {
        return DB::transaction(function () use ($data): Page {
            $defaultCode = Language::query()
                ->where('lang_is_default', true)
                ->where('status', true)
                ->value('code') ?? array_key_first($data['translations']);

            $primary = $data['translations'][$defaultCode] ?? null;

            if ($primary === null || empty($primary['title']) || empty($primary['permalink'])) {
                throw new InvalidArgumentException('Default language translation is required.');
            }

            $imagePath = null;
            if (isset($data['image']) && $data['image'] instanceof UploadedFile) {
                $imagePath = $data['image']->store('pages', 'public');
            } elseif (! empty($data['image_path'])) {
                // Picked from media library — store path as-is.
                $imagePath = $data['image_path'];
            }

            $page = Page::query()->create([
                'title' => $primary['title'],
                'permalink' => $primary['permalink'],
                'content' => $primary['content'] ?? null,
                'image' => $imagePath,
                'template' => $data['template'] ?? 'default',
                'is_home' => $data['is_home'] ?? false,
                'user_id' => $data['user_id'] ?? null,
                'status' => $data['status'] ?? 'published',
            ]);

            if (($data['is_home'] ?? false) === true) {
                Page::clearOtherHomes($page->id);
            }

            $categoryIds = $data['category_ids'] ?? [];
            if (count($categoryIds) > 0) {
                $sync = [];
                foreach ($categoryIds as $i => $id) {
                    $sync[$id] = ['is_primary' => $i === 0];
                }
                $page->categories()->sync($sync);
            }

            foreach ($data['translations'] as $lang => $translation) {
                if (empty($translation['title']) || empty($translation['permalink'])) {
                    continue;
                }

                PageTranslation::query()->create([
                    'page_id' => $page->id,
                    'lang' => $lang,
                    'title' => $translation['title'],
                    'permalink' => $translation['permalink'],
                    'content' => $translation['content'] ?? null,
                ]);
            }

            return $page->load(['translations', 'categories']);
        });
    }
}
