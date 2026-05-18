<?php

declare(strict_types=1);

namespace App\Actions\Admin\Page\Page;

use App\Models\Page;
use App\Models\PageTranslation;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

final readonly class UpdatePage
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
    public function handle(Page $page, array $data): Page
    {
        return DB::transaction(function () use ($page, $data): Page {
            $defaultPrimary = null;
            foreach ($data['translations'] as $lang => $translation) {
                if (! empty($translation['title']) && ! empty($translation['permalink'])) {
                    $defaultPrimary = ['lang' => $lang, ...$translation];
                    break;
                }
            }

            if (isset($data['image']) && $data['image'] instanceof UploadedFile) {
                if ($page->image !== null) {
                    Storage::disk('public')->delete($page->image);
                }
                $page->image = $data['image']->store('pages', 'public');
            } elseif (! empty($data['image_path']) && $data['image_path'] !== $page->image) {
                // Picked from media library — adopt path. Don't delete the
                // previous file: it might be a media-library asset shared
                // with other entities.
                $page->image = $data['image_path'];
            }

            $page->fill([
                'title' => $defaultPrimary['title'] ?? $page->title,
                'permalink' => $defaultPrimary['permalink'] ?? $page->permalink,
                'content' => $defaultPrimary['content'] ?? $page->content,
                'template' => $data['template'] ?? $page->template,
                'is_home' => $data['is_home'] ?? false,
                'status' => $data['status'] ?? $page->status,
            ]);

            $page->save();

            if (($data['is_home'] ?? false) === true) {
                Page::clearOtherHomes($page->id);
            }

            $categoryIds = $data['category_ids'] ?? [];
            $sync = [];
            foreach ($categoryIds as $i => $id) {
                $sync[$id] = ['is_primary' => $i === 0];
            }
            $page->categories()->sync($sync);

            $page->translations()->delete();
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
