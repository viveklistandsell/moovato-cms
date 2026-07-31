<?php

declare(strict_types=1);

namespace App\Actions\Admin\Service\ParentCategory;

use App\Models\ServiceParentCategory;
use Illuminate\Database\Eloquent\Builder;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Streams the parent service categories list as a CSV download. Supports
 * the same search/status filters the Index page uses so an admin can
 * narrow the export before clicking Export.
 *
 * Streams row-by-row via `fputcsv` so memory stays constant even with a
 * large table. UTF-8 BOM is written at the start so Excel opens the file
 * with correct German characters (Umzüge, Entrümpelung, …).
 *
 * Column order matches {@see ImportParentCategoriesFromCsv} exactly.
 * Bilingual layout: DE + EN columns side-by-side (`name_de`, `name_en`,
 * `permalink_de`, `permalink_en`, `short_description_de`,
 * `short_description_en`) so one CSV row = one parent — matches the
 * spreadsheet mental model admins already have.
 *
 * The `image` column stores the storage path (e.g.
 * `media/2026/07/foo.webp`) — same convention the admin UI uses.
 */
final readonly class ExportParentCategoriesToCsv
{
    /** @var list<string> */
    private const HEADERS = [
        'permalink_de', 'name_de', 'short_description_de',
        'permalink_en', 'name_en', 'short_description_en',
        'image', 'is_featured', 'is_popular', 'status', 'sort_order',
    ];

    /**
     * @param  array{q?: ?string, status?: ?string}  $filters
     */
    public function handle(array $filters = []): StreamedResponse
    {
        $search = isset($filters['q']) ? mb_trim((string) $filters['q']) : '';
        $status = isset($filters['status']) ? mb_trim((string) $filters['status']) : '';

        $filename = 'parent-categories-export-'.date('Y-m-d').'.csv';

        return new StreamedResponse(function () use ($search, $status): void {
            $out = fopen('php://output', 'w');
            if ($out === false) {
                return;
            }
            fwrite($out, "\xEF\xBB\xBF");
            fputcsv($out, self::HEADERS);

            $query = ServiceParentCategory::query()
                ->with('translations')
                ->orderBy('sort_order');

            if ($status !== '') {
                $query->where('status', $status);
            }

            if ($search !== '') {
                $query->where(function (Builder $q) use ($search): void {
                    $like = "%{$search}%";
                    $q->where('name', 'like', $like)
                        ->orWhereHas('translations', function (Builder $t) use ($like): void {
                            $t->where('name', 'like', $like)->orWhere('permalink', 'like', $like);
                        });
                });
            }

            $query->chunk(200, function ($rows) use ($out): void {
                foreach ($rows as $parent) {
                    $de = $parent->translations->firstWhere('lang', 'de');
                    $en = $parent->translations->firstWhere('lang', 'en');
                    fputcsv($out, [
                        $de?->permalink ?? '',
                        $de?->name ?? $parent->name,
                        $de?->short_description ?? '',
                        $en?->permalink ?? '',
                        $en?->name ?? '',
                        $en?->short_description ?? '',
                        $parent->image ?? '',
                        $parent->is_featured ? 'yes' : 'no',
                        $parent->is_popular ? 'yes' : 'no',
                        $parent->status,
                        (int) $parent->sort_order,
                    ]);
                }
            });

            fclose($out);
        }, 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="'.$filename.'"',
            'Cache-Control' => 'no-store, no-cache, must-revalidate, max-age=0',
            'Pragma' => 'no-cache',
        ]);
    }

    /**
     * A tiny CSV file with the correct headers and 5 realistic example
     * rows using the actual Moovato service umbrellas. Mix of filled-in
     * and empty optional columns demonstrates what's required vs.
     * auto-derived server-side.
     */
    public function sample(): StreamedResponse
    {
        return new StreamedResponse(function (): void {
            $out = fopen('php://output', 'w');
            if ($out === false) {
                return;
            }
            fwrite($out, "\xEF\xBB\xBF");
            fputcsv($out, self::HEADERS);

            fputcsv($out, [
                'umzuege', 'Umzüge', 'Alle Umzugsdienstleistungen — privat, gewerblich und deutschlandweit.',
                'moves', 'Moves', 'All moving services — private, commercial and nationwide.',
                '', 'yes', 'no', 'published', '1',
            ]);
            fputcsv($out, [
                'spezialtransport', 'Spezialtransport', 'Klaviere, Tresore, Kunstwerke und andere Spezialgüter.',
                'special-transport', 'Special Transport', 'Pianos, safes, artwork and other specialty goods.',
                'media/2026/07/special-transport.webp', 'yes', 'no', 'published', '2',
            ]);
            fputcsv($out, [
                'moebelmontage', 'Möbelmontage', '',
                'furniture-assembly', 'Furniture Assembly', '',
                '', 'yes', 'yes', 'published', '3',
            ]);
            fputcsv($out, [
                'entruempelung', 'Entrümpelung', '',
                '', '', '',
                '', '', '', '', '',
            ]);
            fputcsv($out, [
                '', 'Einlagerung', '',
                '', '', '',
                '', 'no', 'no', '', '',
            ]);

            fclose($out);
        }, 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="parent-categories-sample.csv"',
        ]);
    }
}
