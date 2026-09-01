<?php

declare(strict_types=1);

namespace App\Actions\Admin\Page\Import;

use App\Actions\Admin\Page\Import\Support\MojibakeFixer;
use App\Actions\Admin\Page\Import\Support\ResolveMedia;
use App\Actions\Admin\Page\Page\CreatePage;
use App\Actions\Admin\Page\Page\UpdatePage;
use App\Actions\Admin\Page\Widget\SyncPageWidgets;
use App\Models\Page;
use App\Widgets\Registry\WidgetRegistry;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use RuntimeException;

/**
 * Streams a CSV of pages + widget sections and materialises them.
 *
 * Row grouping semantics:
 *   - `type=1` starts a new page (or overwrites an existing page with
 *     the same slug). The importer collects following `type=0` rows
 *     into that page's widget stack until the next `type=1` row.
 *   - `type=0` on its own (no preceding `type=1` in the file) is skipped
 *     with a fatal error.
 *
 * Two lang modes:
 *   - lang=de → default flow. Every `type=1` row creates or overwrites
 *     a page whose primary language is German.
 *   - lang=en → translation flow. Every `type=1` row must carry a
 *     `german_page_slug` cell. The importer looks up the existing DE
 *     page, and each following section row overlays an EN translation
 *     onto the matching widget by position (row 1 = widget 1, etc.).
 *
 * Chunk semantics:
 *   - Rows are processed one PAGE GROUP at a time inside its own DB
 *     transaction. `chunkSize` caps how many page groups get committed
 *     between transactions; this keeps memory bounded and lets partial
 *     runs commit before a later failure aborts the rest.
 */
final readonly class ImportPagesFromCsv
{
    private const REQUIRED_HEADERS_DE = ['type', 'sections'];

    private const REQUIRED_HEADERS_EN = ['type', 'sections', 'german_page_slug'];

    public function __construct(
        private WidgetAliasMap $aliases,
        private WidgetRegistry $registry,
        private CreatePage $createPage,
        private UpdatePage $updatePage,
        private SyncPageWidgets $syncWidgets,
    ) {}

    public function handle(UploadedFile $file, string $lang, int $chunkSize = 50): ImportPagesResult
    {
        $result = new ImportPagesResult;
        $lang = mb_strtolower($lang) === 'en' ? 'en' : 'de';
        $chunkSize = max(1, min(500, $chunkSize));

        $path = $file->getRealPath();
        if ($path === false) {
            throw new RuntimeException('Uploaded CSV is not accessible.');
        }
        $handle = fopen($path, 'r');
        if ($handle === false) {
            throw new RuntimeException('Could not open the uploaded CSV.');
        }

        try {
            $rawHeader = fgetcsv($handle, 0, ',', '"', '');
            if ($rawHeader === false || $rawHeader === null) {
                throw new RuntimeException('The uploaded CSV appears to be empty.');
            }
            $header = $this->normaliseHeader($rawHeader);
            $required = $lang === 'en' ? self::REQUIRED_HEADERS_EN : self::REQUIRED_HEADERS_DE;
            $missing = array_diff($required, $header);
            if ($missing !== []) {
                throw new RuntimeException('Missing required column(s): '.implode(', ', $missing));
            }

            $rowNumber = 1;
            $currentGroup = null;
            $pendingGroups = 0;

            $flush = function (?array $group) use (&$pendingGroups, $lang, $result): void {
                if ($group === null) {
                    return;
                }
                DB::transaction(function () use ($group, $lang, $result): void {
                    $this->commitGroup($group, $lang, $result);
                });
                $pendingGroups++;
            };

            while (($rawRow = fgetcsv($handle, 0, ',', '"', '')) !== false) {
                $rowNumber++;

                if ($this->isBlankRow($rawRow)) {
                    continue;
                }

                $row = $this->associate($header, $rawRow);
                $type = mb_trim($row['type'] ?? '');

                if ($type === '1') {
                    $flush($currentGroup);
                    $currentGroup = ['header_row' => $rowNumber, 'header' => $row, 'sections' => []];
                    if (mb_trim($row['sections'] ?? '') !== '' || mb_trim($row['section_json'] ?? '') !== '') {
                        $currentGroup['sections'][] = ['row' => $rowNumber, 'data' => $row];
                    }
                    if ($pendingGroups >= $chunkSize) {
                        $pendingGroups = 0;
                    }

                    continue;
                }

                if ($type === '0') {
                    if ($currentGroup === null) {
                        $result->skipped++;
                        $result->errors[] = [
                            'row' => $rowNumber,
                            'errors' => ['type' => ['A type=0 section row appeared before any type=1 page row.']],
                            'values' => $row,
                        ];

                        continue;
                    }
                    $currentGroup['sections'][] = ['row' => $rowNumber, 'data' => $row];

                    continue;
                }

                $result->skipped++;
                $result->errors[] = [
                    'row' => $rowNumber,
                    'errors' => ['type' => ['Unknown value "'.$type.'" in the `type` column — expected 0 or 1.']],
                    'values' => $row,
                ];
            }

            $flush($currentGroup);
        } finally {
            fclose($handle);
        }

        return $result;
    }

    /**
     * @param  array{header_row: int, header: array<string, string>, sections: list<array{row: int, data: array<string, string>}>}  $group
     */
    private function commitGroup(array $group, string $lang, ImportPagesResult $result): void
    {
        $headerRow = $group['header_row'];
        $header = $group['header'];

        if ($lang === 'en') {
            $this->commitEnGroup($headerRow, $header, $group['sections'], $result);

            return;
        }

        $this->commitDeGroup($headerRow, $header, $group['sections'], $result);
    }

    /**
     * @param  array<string, string>  $header
     * @param  list<array{row: int, data: array<string, string>}>  $sections
     */
    private function commitDeGroup(int $headerRow, array $header, array $sections, ImportPagesResult $result): void
    {
        $title = mb_trim($header['page_title'] ?? '');
        $slug = mb_trim($header['page_slug'] ?? '');
        if ($slug === '' && $title !== '') {
            $slug = Str::slug($title);
        }

        if ($title === '' || $slug === '') {
            $result->skipped++;
            $result->errors[] = [
                'row' => $headerRow,
                'errors' => ['page_title' => ['`page_title` and `page_slug` are both required on type=1 rows.']],
                'values' => $header,
            ];

            return;
        }

        $existing = Page::query()->where('permalink', $slug)->first();

        $widgets = $this->buildWidgetStack($sections, 'de', $result);

        $payload = [
            'user_id' => null,
            'template' => mb_trim($header['template'] ?? '') !== '' ? mb_trim($header['template'] ?? '') : 'default',
            'is_home' => false,
            'status' => 'published',
            'translations' => [
                'de' => [
                    'title' => $title,
                    'permalink' => $slug,
                    'meta_title' => mb_trim($header['meta_title'] ?? '') !== '' ? mb_trim($header['meta_title'] ?? '') : null,
                    'meta_description' => mb_trim($header['meta_desc'] ?? '') !== '' ? mb_trim($header['meta_desc'] ?? '') : null,
                    'meta_image' => $this->resolvedMetaImagePath($header),
                    'schema' => null,
                ],
            ],
            'widgets' => $widgets,
        ];

        if ($existing === null) {
            $page = $this->createPage->handle($payload);
            $result->created++;
        } else {
            $page = $this->updatePage->handle($existing, $payload);
            $result->updated++;
        }

        $navLabel = mb_trim($header['header_menu'] ?? '');
        $qaScore = mb_trim($header['qa_score'] ?? '');
        $brandSize = mb_trim($header['brand_size'] ?? '');
        if ($navLabel !== '' || $qaScore !== '' || $brandSize !== '') {
            $page->forceFill([
                'nav_label' => $navLabel !== '' ? $navLabel : $page->nav_label,
                'qa_score' => $qaScore !== '' ? $qaScore : $page->qa_score,
                'brand_size' => $brandSize !== '' ? $brandSize : $page->brand_size,
            ])->save();
        }

        $result->widgetsAdded += count($widgets);
    }

    /**
     * @param  array<string, string>  $header
     * @param  list<array{row: int, data: array<string, string>}>  $sections
     */
    private function commitEnGroup(int $headerRow, array $header, array $sections, ImportPagesResult $result): void
    {
        $germanSlug = mb_trim($header['german_page_slug'] ?? '');
        if ($germanSlug === '') {
            $result->skipped++;
            $result->errors[] = [
                'row' => $headerRow,
                'errors' => ['german_page_slug' => ['EN import requires `german_page_slug` on every type=1 row.']],
                'values' => $header,
            ];

            return;
        }

        $page = Page::query()->where('permalink', $germanSlug)->with('widgets')->first();
        if ($page === null) {
            $result->skipped++;
            $result->errors[] = [
                'row' => $headerRow,
                'errors' => ['german_page_slug' => ['No German page found with permalink "'.$germanSlug.'".']],
                'values' => $header,
            ];

            return;
        }

        $enTitle = mb_trim($header['page_title'] ?? '');
        $enSlug = mb_trim($header['page_slug'] ?? '');
        if ($enSlug === '' && $enTitle !== '') {
            $enSlug = Str::slug($enTitle);
        }

        $deTranslation = $page->translations()->where('lang', 'de')->first();
        $usedFallback = false;
        if ($enTitle === '') {
            $enTitle = (string) ($deTranslation->title ?? $page->title ?? $germanSlug);
            $usedFallback = true;
        }
        if ($enSlug === '') {
            $enSlug = (string) ($deTranslation->permalink ?? $page->permalink ?? Str::slug($enTitle));
            $usedFallback = true;
        }

        $page->translations()->updateOrCreate(
            ['lang' => 'en'],
            [
                'title' => $enTitle,
                'permalink' => $enSlug,
                'meta_title' => mb_trim($header['meta_title'] ?? '') !== '' ? mb_trim($header['meta_title'] ?? '') : null,
                'meta_description' => mb_trim($header['meta_desc'] ?? '') !== '' ? mb_trim($header['meta_desc'] ?? '') : null,
                'meta_image' => $this->resolvedMetaImagePath($header),
                'schema' => null,
            ],
        );
        $result->updated++;

        if ($usedFallback) {
            $result->warnings[] = [
                'row' => $headerRow,
                'kind' => 'en_fallback_used',
                'message' => 'EN page_title/page_slug was empty — filled from the German page ("'.$enTitle.'" / "'.$enSlug.'") so the EN URL still resolves.',
                'values' => $header,
            ];
        }

        // Overlay EN translations onto existing widgets by position.
        $existingWidgets = $page->widgets()->orderBy('position')->get();
        $lastError = null;
        foreach ($sections as $offset => $section) {
            if (! isset($existingWidgets[$offset])) {
                $result->widgetsSkipped++;
                $result->warnings[] = [
                    'row' => $section['row'],
                    'kind' => 'en_no_matching_widget',
                    'message' => 'No German widget at position #'.($offset + 1).' — skipping EN row.',
                    'values' => $section['data'],
                ];

                continue;
            }

            $built = app(BuildWidgetFromRow::class)->build($section['data'], 'en');
            if ($built === null) {
                $result->widgetsSkipped++;
                $result->warnings[] = [
                    'row' => $section['row'],
                    'kind' => 'unknown_widget_slug',
                    'message' => 'Unknown widget slug "'.($section['data']['sections'] ?? '').'".',
                    'values' => $section['data'],
                ];

                continue;
            }

            $existing = $existingWidgets[$offset];
            $existing->translations()->updateOrCreate(
                ['lang' => 'en'],
                ['data' => $built['translations']['en']],
            );
            $result->widgetsAdded++;
        }

        // Report any missing-image warnings for EN section rows too.
        $this->collectImageWarnings($sections, $result);

        unset($lastError);
    }

    /**
     * @param  list<array{row: int, data: array<string, string>}>  $sections
     * @return list<array{type: string, settings: array<string, mixed>, translations: array<string, array<string, mixed>>}>
     */
    private function buildWidgetStack(array $sections, string $lang, ImportPagesResult $result): array
    {
        /** @var BuildWidgetFromRow $builder */
        $builder = app(BuildWidgetFromRow::class);
        $out = [];

        foreach ($sections as $section) {
            $built = $builder->build($section['data'], $lang);
            if ($built === null) {
                $result->widgetsSkipped++;
                $result->warnings[] = [
                    'row' => $section['row'],
                    'kind' => 'unknown_widget_slug',
                    'message' => 'Unknown widget slug "'.($section['data']['sections'] ?? '').'".',
                    'values' => $section['data'],
                ];

                continue;
            }
            $out[] = $built;
        }

        $this->collectImageWarnings($sections, $result);

        return $out;
    }

    /**
     * @param  list<array{row: int, data: array<string, string>}>  $sections
     */
    private function collectImageWarnings(array $sections, ImportPagesResult $result): void
    {
        /** @var ResolveMedia $media */
        $media = app(ResolveMedia::class);
        foreach ($sections as $section) {
            $filename = mb_trim($section['data']['image'] ?? '');
            if ($filename === '') {
                continue;
            }
            if ($media->pathFor($filename) === null) {
                $result->warnings[] = [
                    'row' => $section['row'],
                    'kind' => 'image_not_found',
                    'message' => 'Image "'.$filename.'" not found in the media library — the section was created without an image.',
                    'values' => $section['data'],
                ];
            }
        }
    }

    /**
     * @param  array<string, string>  $header
     */
    private function resolvedMetaImagePath(array $header): ?string
    {
        $filename = mb_trim($header['meta_image'] ?? '');
        if ($filename === '') {
            return null;
        }

        return app(ResolveMedia::class)->pathFor($filename);
    }

    /**
     * Lowercase header cells, strip UTF-8 BOM, and repair mojibake so a
     * Windows Excel export lines up with our canonical column names.
     *
     * @param  array<int, string|null>  $rawHeader
     * @return list<string>
     */
    private function normaliseHeader(array $rawHeader): array
    {
        return array_values(array_map(
            static function ($h): string {
                $s = (string) $h;
                if (str_starts_with($s, "\xEF\xBB\xBF")) {
                    $s = mb_substr($s, 1);
                }

                return mb_strtolower(mb_trim(MojibakeFixer::fix($s)));
            },
            $rawHeader,
        ));
    }

    /**
     * Position-index a raw CSV row against the normalised header and
     * mojibake-fix every value in one pass so downstream code never
     * has to touch encoding again.
     *
     * @param  list<string>  $header
     * @param  array<int, string|null>  $row
     * @return array<string, string>
     */
    private function associate(array $header, array $row): array
    {
        $out = [];
        foreach ($header as $index => $key) {
            $out[$key] = MojibakeFixer::fix((string) ($row[$index] ?? ''));
        }

        return $out;
    }

    /**
     * @param  array<int, string|null>  $row
     */
    private function isBlankRow(array $row): bool
    {
        foreach ($row as $cell) {
            if (mb_trim((string) $cell) !== '') {
                return false;
            }
        }

        return true;
    }
}
