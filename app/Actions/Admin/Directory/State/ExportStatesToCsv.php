<?php

declare(strict_types=1);

namespace App\Actions\Admin\Directory\State;

use App\Models\State;
use Illuminate\Database\Eloquent\Builder;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Streams the states list as a CSV download. Supports the same
 * `country_id` and search filters the Index page uses so an admin can
 * filter to (say) Germany, click Export, and get only German states.
 *
 * Streams row-by-row via `fputcsv` so memory usage stays constant even
 * if the states table grows large. UTF-8 BOM is written at the start so
 * Excel opens the file with correct German characters (ü, ß, …).
 *
 * Column order matches {@see ImportStatesFromCsv} exactly:
 *   country_iso, name, code, permalink, status, sort_order
 * Using `country_iso` (portable) instead of `country_id` (env-specific)
 * so an export from staging can be imported into production without
 * remapping numeric IDs.
 */
final readonly class ExportStatesToCsv
{
    /**
     * @param  array{country_id?: ?int, q?: ?string}  $filters
     */
    public function handle(array $filters = []): StreamedResponse
    {
        $countryId = isset($filters['country_id']) ? (int) $filters['country_id'] : null;
        $search = isset($filters['q']) ? mb_trim((string) $filters['q']) : '';

        $filename = 'states-export-'.date('Y-m-d').'.csv';

        return new StreamedResponse(function () use ($countryId, $search): void {
            $out = fopen('php://output', 'w');
            if ($out === false) {
                return;
            }
            fwrite($out, "\xEF\xBB\xBF");

            fputcsv($out, ['country_iso', 'name', 'code', 'permalink', 'status', 'sort_order']);

            $query = State::query()
                ->with('country:id,iso_code')
                ->orderBy('country_id')
                ->orderBy('sort_order');

            if ($countryId !== null) {
                $query->where('country_id', $countryId);
            }

            if ($search !== '') {
                $query->where(function (Builder $q) use ($search): void {
                    $like = "%{$search}%";
                    $q->where('name', 'like', $like)
                        ->orWhere('code', 'like', $like)
                        ->orWhere('permalink', 'like', $like);
                });
            }

            $query->chunk(500, function ($rows) use ($out): void {
                foreach ($rows as $state) {
                    fputcsv($out, [
                        $state->country?->iso_code ?? '',
                        $state->name,
                        $state->code,
                        $state->permalink,
                        $state->status,
                        (int) $state->sort_order,
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
     * A tiny CSV file with the correct headers and 3 realistic example
     * rows. Handed out via the Index page's "Download sample" button so
     * admins know the expected shape before their first import.
     */
    public function sample(): StreamedResponse
    {
        return new StreamedResponse(function (): void {
            $out = fopen('php://output', 'w');
            if ($out === false) {
                return;
            }
            fwrite($out, "\xEF\xBB\xBF");
            fputcsv($out, ['country_iso', 'name', 'code', 'permalink', 'status', 'sort_order']);
            fputcsv($out, ['IN', 'Bihar', 'BR', 'bihar', 'published', '1']);
            fputcsv($out, ['IN', 'Maharashtra', 'MH', 'maharashtra', 'published', '2']);
            fputcsv($out, ['DE', 'Bayern', 'BY', 'bayern', 'published', '3']);
            fputcsv($out, ['DE', 'Berlin', 'BE', '', 'published', '']);
            fputcsv($out, ['DE', 'Baden-Württemberg', 'BW', '', '', '']);

            fclose($out);
        }, 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="states-sample.csv"',
        ]);
    }
}
