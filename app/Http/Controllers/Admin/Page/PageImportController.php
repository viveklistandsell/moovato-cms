<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin\Page;

use App\Actions\Admin\Page\Import\ImportPagesFromCsv;
use App\Actions\Admin\Page\Import\WidgetAliasMap;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Page\ImportPagesRequest;
use App\Models\MediaFile;
use App\Widgets\Registry\WidgetRegistry;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Inertia\Inertia;
use Inertia\Response;
use RuntimeException;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Admin CSV importer for Pages + widget sections.
 *
 * Endpoints:
 *   - show()      GET  /admin/pages/import                       → renders the upload UI
 *   - import()    POST /admin/pages/import                       → processes an upload and flashes the result
 *   - template()  GET  /admin/pages/import/template?lang=&format= → streams a starter CSV
 *
 * Template variants:
 *   - `format=csv`  (default) — flat column layout: title, description, image, …
 *   - `format=json`           — one `section_json` column carrying the whole widget config as JSON.
 *                               Useful when the widget has custom fields (columns, service_items, …)
 *                               that don't fit the flat schema.
 *
 * `lang=de` (default) or `lang=en`. The EN variant adds a `german_page_slug`
 * column so translation imports can bind to the existing German page.
 */
final class PageImportController extends Controller
{
    public function show(WidgetRegistry $registry): Response
    {
        return Inertia::render('admin/page/pages/Import', [
            'aliases' => (new WidgetAliasMap)->csvSlugs(),
            'nativeWidgets' => $registry->types(),
        ]);
    }

    public function import(ImportPagesRequest $request, ImportPagesFromCsv $action): RedirectResponse
    {
        /** @var UploadedFile $file */
        $file = $request->file('file');
        $lang = (string) $request->input('lang', 'de');
        $chunkSize = (int) ($request->input('chunk_size') ?: 50);

        try {
            $result = $action->handle($file, $lang, $chunkSize);
        } catch (RuntimeException $e) {
            return back()->with('toast', ['type' => 'error', 'message' => $e->getMessage()]);
        }

        $summary = (string) __('admin.pages.import_toast_success', [
            'created' => $result->created,
            'updated' => $result->updated,
            'widgets' => $result->widgetsAdded,
            'skipped' => $result->skipped,
        ]);
        $type = $result->hasProblems() ? 'warning' : 'success';

        return redirect()
            ->route('admin.pages.import.show')
            ->with('toast', ['type' => $type, 'message' => $summary])
            ->with('importResult', $result->toArray());
    }

    public function template(Request $request): StreamedResponse
    {
        $lang = mb_strtolower((string) $request->query('lang', 'de')) === 'en' ? 'en' : 'de';
        $format = mb_strtolower((string) $request->query('format', 'csv')) === 'json' ? 'json' : 'csv';

        $headers = $this->templateHeaders($lang, $format);
        $rows = $this->templateRows($lang, $format);
        $filename = sprintf(
            'pages-import-template-%s-%s.csv',
            $format,
            $lang,
        );

        return new StreamedResponse(function () use ($headers, $rows): void {
            $out = fopen('php://output', 'w');
            if ($out === false) {
                return;
            }
            // UTF-8 BOM so Excel opens special chars (ß, ü) correctly on Windows.
            fwrite($out, "\xEF\xBB\xBF");
            fputcsv($out, $headers, ',', '"', '');
            foreach ($rows as $row) {
                fputcsv($out, $row, ',', '"', '');
            }
            fclose($out);
        }, 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="'.$filename.'"',
        ]);
    }

    /**
     * @return list<string>
     */
    private function templateHeaders(string $lang, string $format): array
    {
        if ($format === 'json') {
            $base = ['type', 'page_title', 'page_slug', 'template', 'sections', 'section_json', 'header_menu', 'meta_title', 'meta_desc', 'meta_image', 'qa_score', 'brand_size'];
            if ($lang === 'en') {
                array_splice($base, 3, 0, 'german_page_slug');
            }

            return $base;
        }

        $base = ['type', 'page_title', 'page_slug', 'template', 'sections', 'title', 'description', 'image', 'image_alt', 'image_title', 'header_menu', 'meta_title', 'meta_desc', 'meta_image', 'qa_score', 'brand_size'];
        if ($lang === 'en') {
            array_splice($base, 3, 0, 'german_page_slug');
        }

        return $base;
    }

    /**
     * @return list<list<string>>
     */
    private function templateRows(string $lang, string $format): array
    {
        if ($format === 'json') {
            return $this->jsonTemplateRows($lang);
        }

        [$heroImage, $contentImage, $metaImage] = $this->sampleMediaFilenames();

        if ($lang === 'en') {
            return [
                ['1', 'My Sample Page', 'my-sample-page', 'my-sample-seite', 'import', 'service_hero', 'Sample hero title', '<p>Sample hero copy.</p>', $heroImage, 'Hero image alt text', 'Hero image title', 'home', 'Sample meta title', 'Sample meta description', $metaImage, '', ''],
                ['0', '', '', '', '', 'content_image', 'A content section', '<p>Section body.</p>', $contentImage, 'Content image alt text', 'Content image title', '', '', '', '', '', ''],
                ['0', '', '', '', '', 'faq', 'Q1 || Q2', '<p>Answer 1</p> || <p>Answer 2</p>', '', '', '', '', '', '', '', '', ''],
            ];
        }

        return [
            ['1', 'Beispielseite', 'beispielseite', 'import', 'service_hero', 'Beispiel Hero-Titel', '<p>Kurze Einleitung zum Hero.</p>', $heroImage, 'Alt-Text des Hero-Bildes', 'Titel des Hero-Bildes', 'home', 'Beispiel Meta-Titel', 'Beispiel Meta-Beschreibung', $metaImage, '', ''],
            ['0', '', '', '', 'content_image', 'Ein Content-Abschnitt', '<p>Fließtext des Abschnitts.</p>', $contentImage, 'Alt-Text des Bildes', 'Titel des Bildes', '', '', '', '', '', ''],
            ['0', '', '', '', 'faq', 'Frage 1 || Frage 2', '<p>Antwort 1</p> || <p>Antwort 2</p>', '', '', '', '', '', '', '', '', ''],
        ];
    }

    /**
     * Pick three sample filenames from the media library for the template.
     * Prefers photos/illustrations over ULID-named uploads and dedupes the
     * list so a hero/content/meta triple is always distinct when possible.
     *
     * @return array{0: string, 1: string, 2: string}
     */
    private function sampleMediaFilenames(): array
    {
        $candidates = MediaFile::query()
            ->whereIn('extension', ['png', 'jpg', 'jpeg', 'webp', 'svg'])
            ->whereRaw('LOWER(name) NOT REGEXP ?', ['^01[a-z0-9]{24}\\.'])
            ->orderBy('name')
            ->limit(50)
            ->pluck('name')
            ->unique()
            ->values();

        $fallback = 'logo.svg';
        $hero = (string) ($candidates[0] ?? $fallback);
        $content = (string) ($candidates[1] ?? $candidates[0] ?? $fallback);
        $meta = (string) ($candidates[2] ?? $candidates[0] ?? $fallback);

        return [$hero, $content, $meta];
    }

    /**
     * @return list<list<string>>
     */
    private function jsonTemplateRows(string $lang): array
    {
        [$heroImage, $contentImage, $metaImage] = $this->sampleMediaFilenames();

        $heroJson = json_encode([
            'widget_type' => 'hero',
            'title' => $lang === 'en' ? 'Sample hero title' : 'Beispiel Hero-Titel',
            'description' => $lang === 'en' ? '<p>Sample hero copy.</p>' : '<p>Kurze Einleitung zum Hero.</p>',
            'image' => $heroImage,
            'image_alt' => $lang === 'en' ? 'Hero image alt text' : 'Alt-Text des Hero-Bildes',
        ], JSON_UNESCAPED_UNICODE) ?: '';

        $splitJson = json_encode([
            'widget_type' => 'split_media',
            'title' => $lang === 'en' ? 'A content section' : 'Ein Content-Abschnitt',
            'description' => $lang === 'en'
                ? '<p>Body copy.</p><ul><li>Point one</li><li>Point two</li></ul>'
                : '<p>Fließtext.</p><ul><li>Punkt eins</li><li>Punkt zwei</li></ul>',
            'image' => $contentImage,
            'image_alt' => $lang === 'en' ? 'Content image alt' : 'Alt-Text des Bildes',
        ], JSON_UNESCAPED_UNICODE) ?: '';

        $faqJson = json_encode([
            'widget_type' => 'faq',
            'items' => $lang === 'en'
                ? [
                    ['question' => 'Q1', 'answer' => 'A1'],
                    ['question' => 'Q2', 'answer' => 'A2'],
                ]
                : [
                    ['question' => 'Frage 1', 'answer' => 'Antwort 1'],
                    ['question' => 'Frage 2', 'answer' => 'Antwort 2'],
                ],
        ], JSON_UNESCAPED_UNICODE) ?: '';

        if ($lang === 'en') {
            return [
                ['1', 'My Sample Page', 'my-sample-page', 'my-sample-seite', 'import', '', $heroJson, 'home', 'Sample meta title', 'Sample meta description', $metaImage, '', ''],
                ['0', '', '', '', '', '', $splitJson, '', '', '', '', '', ''],
                ['0', '', '', '', '', '', $faqJson, '', '', '', '', '', ''],
            ];
        }

        return [
            ['1', 'Beispielseite', 'beispielseite', 'import', '', $heroJson, 'home', 'Beispiel Meta-Titel', 'Beispiel Meta-Beschreibung', $metaImage, '', ''],
            ['0', '', '', '', '', $splitJson, '', '', '', '', '', ''],
            ['0', '', '', '', '', $faqJson, '', '', '', '', '', ''],
        ];
    }
}
