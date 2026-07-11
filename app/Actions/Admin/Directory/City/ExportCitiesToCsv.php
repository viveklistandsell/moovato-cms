<?php

declare(strict_types=1);

namespace App\Actions\Admin\Directory\City;

use App\Models\City;
use Illuminate\Database\Eloquent\Builder;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Streams the cities list as a CSV download. Supports the same
 * `country_id`, `state_id`, and search filters the Index page uses so
 * an admin can filter to (say) Bayern, click Export, and get only
 * cities from Bayern.
 * Column order matches {@see ImportCitiesFromCsv} exactly:
 */
final readonly class ExportCitiesToCsv
{
    /**
     * @param  array{country_id?: ?int, state_id?: ?int, q?: ?string, popular?: ?string}  $filters
     */
    public function handle(array $filters = []): StreamedResponse
    {
        $countryId = isset($filters['country_id']) ? (int) $filters['country_id'] : null;
        $stateId = isset($filters['state_id']) ? (int) $filters['state_id'] : null;
        $search = isset($filters['q']) ? mb_trim((string) $filters['q']) : '';
        $popular = $filters['popular'] ?? null;

        $filename = 'cities-export-'.date('Y-m-d').'.csv';

        return new StreamedResponse(function () use ($countryId, $stateId, $search, $popular): void {
            $out = fopen('php://output', 'w');
            if ($out === false) {
                return;
            }
            fwrite($out, "\xEF\xBB\xBF");

            fputcsv($out, [
                'country_iso', 'state_code', 'name', 'permalink',
                'postal_code', 'is_popular', 'status', 'sort_order',
            ]);

            $query = City::query()
                ->with('state:id,country_id,code', 'state.country:id,iso_code')
                ->orderBy('state_id')
                ->orderBy('sort_order');

            if ($stateId !== null) {
                $query->where('state_id', $stateId);
            } elseif ($countryId !== null) {
                $query->whereHas('state', fn (Builder $q) => $q->where('country_id', $countryId));
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
                        ->orWhere('postal_code', 'like', $like);
                });
            }

            $query->chunk(500, function ($rows) use ($out): void {
                foreach ($rows as $city) {
                    fputcsv($out, [
                        $city->state?->country?->iso_code ?? '',
                        $city->state?->code ?? '',
                        $city->name,
                        $city->permalink,
                        $city->postal_code ?? '',
                        $city->is_popular ? 'yes' : 'no',
                        $city->status,
                        (int) $city->sort_order,
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

    public function sample(): StreamedResponse
    {
        return new StreamedResponse(function (): void {
            $out = fopen('php://output', 'w');
            if ($out === false) {
                return;
            }
            fwrite($out, "\xEF\xBB\xBF");
            fputcsv($out, [
                'country_iso', 'state_code', 'name', 'permalink',
                'postal_code', 'is_popular', 'status', 'sort_order',
            ]);
            fputcsv($out, ['IN', 'BR', 'Bihar', 'Chapra', '841301', 'yes', 'published', '1']);
            fputcsv($out, ['IN', 'CH', 'Chandigarh', 'chandigarh', '160001', 'yes', 'published', '1']);
            fputcsv($out, ['IN', 'MH', 'Mumbai', 'mumbai', '400001', 'yes', 'published', '1']);
            fputcsv($out, ['DE', 'BE', 'Berlin', 'berlin', '10115', 'yes', 'published', '1']);
            fputcsv($out, ['DE', 'BY', 'München', 'muenchen', '80331', 'yes', 'published', '2']);
            fputcsv($out, ['DE', 'BY', 'Nürnberg', '', '90402', 'no', 'published', '']);
            fputcsv($out, ['DE', 'BW', 'Stuttgart', '', '', '', '', '']);

            fclose($out);
        }, 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="cities-sample.csv"',
        ]);
    }
}
