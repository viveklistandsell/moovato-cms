<?php

declare(strict_types=1);

namespace App\Actions\Admin\Directory\District;

use App\Models\District;
use Illuminate\Database\Eloquent\Builder;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Streams the districts list as a CSV download. Supports the same
 * country / state / city / popular / search filters the Index page uses
 * so admins can narrow the export before clicking Export.
 *
 * Streams row-by-row via `fputcsv` so memory stays constant even on
 * large tables. UTF-8 BOM prepended so Excel opens the file with
 * correct German characters (Neukölln, Schöneberg, …).
 *
 * Column order matches {@see ImportDistrictsFromCsv} exactly:
 *   country_iso, state_code, city_permalink, name, code,
 *   permalink, postal_code_prefix, is_popular, status, sort_order
 *
 * Portable identifiers — `(country_iso, state_code, city_permalink)`
 * uniquely locates the parent city, so exports from staging import
 * into production without ID remapping.
 */
final readonly class ExportDistrictsToCsv
{
    /** @var list<string> */
    private const HEADERS = [
        'country_iso', 'state_code', 'city_permalink',
        'name', 'code', 'permalink', 'postal_code_prefix',
        'is_popular', 'status', 'sort_order',
    ];

    /**
     * @param  array{country_id?: ?int, state_id?: ?int, city_id?: ?int, popular?: ?string, q?: ?string}  $filters
     */
    public function handle(array $filters = []): StreamedResponse
    {
        $countryId = isset($filters['country_id']) ? (int) $filters['country_id'] : null;
        $stateId = isset($filters['state_id']) ? (int) $filters['state_id'] : null;
        $cityId = isset($filters['city_id']) ? (int) $filters['city_id'] : null;
        $popular = $filters['popular'] ?? null;
        $search = isset($filters['q']) ? mb_trim((string) $filters['q']) : '';

        $filename = 'districts-export-'.date('Y-m-d').'.csv';

        return new StreamedResponse(function () use ($countryId, $stateId, $cityId, $popular, $search): void {
            $out = fopen('php://output', 'w');
            if ($out === false) {
                return;
            }
            fwrite($out, "\xEF\xBB\xBF");
            fputcsv($out, self::HEADERS);

            $query = District::query()
                ->with('city:id,state_id,permalink', 'city.state:id,country_id,code', 'city.state.country:id,iso_code')
                ->orderBy('city_id')
                ->orderBy('sort_order');

            if ($cityId !== null) {
                $query->where('city_id', $cityId);
            } elseif ($stateId !== null) {
                $query->whereHas('city', fn (Builder $q) => $q->where('state_id', $stateId));
            } elseif ($countryId !== null) {
                $query->whereHas('city.state', fn (Builder $q) => $q->where('country_id', $countryId));
            }

            if ($popular === '1') {
                $query->where('is_popular', true);
            } elseif ($popular === '0') {
                $query->where('is_popular', false);
            }

            if ($search !== '') {
                $query->where(function (Builder $q) use ($search): void {
                    $like = "%{$search}%";
                    $q->where('name', 'like', $like)
                        ->orWhere('permalink', 'like', $like)
                        ->orWhere('code', 'like', $like)
                        ->orWhere('postal_code_prefix', 'like', $like);
                });
            }

            $query->chunk(500, function ($rows) use ($out): void {
                foreach ($rows as $district) {
                    fputcsv($out, [
                        $district->city?->state?->country?->iso_code ?? '',
                        $district->city?->state?->code ?? '',
                        $district->city?->permalink ?? '',
                        $district->name,
                        $district->code ?? '',
                        $district->permalink,
                        $district->postal_code_prefix ?? '',
                        $district->is_popular ? 'yes' : 'no',
                        $district->status,
                        (int) $district->sort_order,
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
     * Static template with the correct headers + a handful of realistic
     * example rows using Berlin's actual Bezirke. Mixed filled / empty
     * optional cells demonstrate what's required vs. auto-derived.
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

            fputcsv($out, ['DE', 'BE', 'berlin', 'Mitte', 'MI', 'mitte', '10115', 'yes', 'published', '1']);
            fputcsv($out, ['DE', 'BE', 'berlin', 'Friedrichshain-Kreuzberg', 'FK', 'friedrichshain-kreuzberg', '10243', 'yes', 'published', '2']);
            fputcsv($out, ['DE', 'BE', 'berlin', 'Neukölln', 'NK', '', '12043', 'yes', 'published', '']);
            fputcsv($out, ['DE', 'BE', 'berlin', 'Spandau', '', '', '', '', '', '']);
            fputcsv($out, ['DE', 'BY', 'muenchen', 'Altstadt-Lehel', 'ALT', 'altstadt-lehel', '80331', 'yes', 'published', '1']);

            fclose($out);
        }, 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="districts-sample.csv"',
        ]);
    }
}
