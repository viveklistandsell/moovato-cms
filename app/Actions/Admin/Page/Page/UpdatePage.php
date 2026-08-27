<?php

declare(strict_types=1);

namespace App\Actions\Admin\Page\Page;

use App\Actions\Admin\Page\Widget\SyncPageWidgets;
use App\Models\Page;
use App\Models\PageTranslation;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

use function is_array;

final readonly class UpdatePage
{
    public function __construct(private SyncPageWidgets $syncWidgets) {}

    /**
     * @param  array{
     *   user_id?: int|null,
     *   category_ids?: array<int, int>|null,
     *   image?: UploadedFile|string|null,
     *   template?: string,
     *   is_home?: bool,
     *   status?: string,
     *   translations: array<string, array{title?: ?string, permalink?: ?string, meta_title?: ?string, meta_description?: ?string, schema?: ?string, meta_image?: ?string}>,
     *   widgets?: array<int, array<string, mixed>>|null
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

            // Form data arrives as "1"/"true" strings, not real PHP bools, so
            // a strict `=== true` check below silently skipped the cleanup —
            // that's why the home icon was sticking on multiple pages.
            $isHome = filter_var($data['is_home'] ?? false, FILTER_VALIDATE_BOOLEAN);

            $page->fill([
                'title' => $defaultPrimary['title'] ?? $page->title,
                'permalink' => $defaultPrimary['permalink'] ?? $page->permalink,
                'template' => $data['template'] ?? $page->template,
                'is_home' => $isHome,
                'status' => $data['status'] ?? $page->status,
            ]);

            $page->save();

            if ($isHome) {
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
                    'meta_title' => $translation['meta_title'] ?? null,
                    'meta_description' => $translation['meta_description'] ?? null,
                    'schema' => $this->normalizeSchema($translation['schema'] ?? null),
                    'meta_image' => $translation['meta_image'] ?? null,
                ]);
            }

            if (array_key_exists('widgets', $data) && is_array($data['widgets'])) {
                $this->syncWidgets->handle($page, $data['widgets']);
            }

            return $page->load(['translations', 'categories', 'widgets.translations']);
        });
    }

    /**
     * Raw JSON-LD string → array (JSON column). Empty / invalid → null.
     */
    private function normalizeSchema(mixed $raw): ?array
    {
        if ($raw === null || $raw === '' || $raw === []) {
            return null;
        }
        if (is_array($raw)) {
            return $raw;
        }

        $decoded = json_decode((string) $raw, true);

        return is_array($decoded) ? $decoded : null;
    }
}
