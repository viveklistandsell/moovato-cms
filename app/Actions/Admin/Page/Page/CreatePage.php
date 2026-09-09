<?php

declare(strict_types=1);

namespace App\Actions\Admin\Page\Page;

use App\Actions\Admin\Page\Widget\SyncPageWidgets;
use App\Models\Language;
use App\Models\Page;
use App\Models\PageTranslation;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

use function is_array;

final readonly class CreatePage
{
    private SyncPageWidgets $syncWidgets;

    public function __construct(SyncPageWidgets $syncWidgets)
    {
        $this->syncWidgets = $syncWidgets;
    }

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
            // Form data arrives as "1"/"true" strings, not real PHP bools, so
            // a strict `=== true` check below silently skipped the cleanup —
            // that's why the home icon was sticking on multiple pages.
            $isHome = filter_var($data['is_home'] ?? false, FILTER_VALIDATE_BOOLEAN);

            $page = Page::query()->create([
                'title' => $primary['title'],
                'permalink' => $primary['permalink'],
                'image' => $imagePath,
                'template' => $data['template'] ?? 'default',
                'is_home' => $isHome,
                'user_id' => $data['user_id'] ?? null,
                'status' => $data['status'] ?? 'published',
            ]);

            if ($isHome) {
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
                    'meta_title' => $translation['meta_title'] ?? null,
                    'meta_description' => $translation['meta_description'] ?? null,
                    'schema' => $this->normalizeSchema($translation['schema'] ?? null),
                    'meta_image' => $translation['meta_image'] ?? null,
                ]);
            }

            $widgets = $data['widgets'] ?? null;
            if (is_array($widgets) && $widgets !== []) {
                $this->syncWidgets->handle($page, $widgets);
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
