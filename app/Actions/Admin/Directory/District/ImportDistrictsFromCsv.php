<?php

declare(strict_types=1);

namespace App\Actions\Admin\Directory\District;

use App\Http\Requests\Admin\Directory\District\StoreDistrictRequest;
use App\Models\City;
use App\Models\District;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use RuntimeException;

/**
 * Streams a CSV upload, validates each row against the same rules
 * {@see StoreDistrictRequest} uses, and creates-or-updates each valid
 * row keyed on `(city_id, permalink)` — the DB-level unique constraint
 * on `districts`.
 *
 * Design decisions (copied from ImportCitiesFromCsv — one pattern, four
 * geo tiers):
 *   - PARTIAL SUCCESS: good rows commit, bad rows are reported.
 *   - TRANSACTION: wraps the whole batch so a fatal error rolls back.
 *   - CITY LOOKUP: CSV carries `(country_iso, state_code, city_permalink)`
 *     — all portable identifiers. The action resolves the tuple to a
 *     `city_id` per row. Rows for unknown combinations are skipped
 *     with a clear error.
 *   - AUTO-DERIVATION: missing permalink derives from name via
 *     Str::slug; missing status defaults to `published`; missing
 *     sort_order gets next available; missing is_popular defaults to
 *     false.
 *   - PRESERVE-ON-UPDATE: empty CSV cells for optional fields
 *     (code, postal_code_prefix, is_popular, sort_order, status) mean
 *     "keep existing value" on update — NOT "wipe it." Same lesson as
 *     the postal_code bug.
 *   - BOOLEAN PARSING: `is_popular` accepts yes/no, true/false, 1/0,
 *     y/n (case-insensitive).
 */
final readonly class ImportDistrictsFromCsv
{
    /**
     * Required headers. Optional (permalink, code, postal_code_prefix,
     * is_popular, status, sort_order) are auto-derived when absent.
     */
    private const REQUIRED_HEADERS = ['country_iso', 'state_code', 'city_permalink', 'name'];

    public function handle(UploadedFile $file): ImportDistrictsResult
    {
        $result = new ImportDistrictsResult;

        $path = $file->getRealPath();
        if ($path === false) {
            throw new RuntimeException((string) __('admin.locations.import_error_file_not_accessible'));
        }

        $handle = fopen($path, 'r');
        if ($handle === false) {
            throw new RuntimeException((string) __('admin.locations.import_error_file_open_failed'));
        }

        try {
            $rawHeader = fgetcsv($handle);
            if ($rawHeader === false || $rawHeader === null) {
                throw new RuntimeException((string) __('admin.locations.import_error_file_empty'));
            }
            $header = $this->normaliseHeader($rawHeader);
            $missing = array_diff(self::REQUIRED_HEADERS, $header);
            if ($missing !== []) {
                throw new RuntimeException((string) __(
                    'admin.locations.import_error_missing_columns',
                    ['columns' => implode(', ', $missing)],
                ));
            }

            /**
             * Precompute (country_iso, state_code, city_permalink) →
             * city_id map so we don't hit the DB per row. Key format
             * "ISO|CODE|PERMALINK" (all uppercase for iso/code, lower
             * for permalink) — matches how CSV values get normalised.
             *
             * @var array<string, int> $cityLookup
             */
            $cityLookup = City::query()
                ->with('state:id,country_id,code', 'state.country:id,iso_code')
                ->get(['id', 'state_id', 'permalink'])
                ->reduce(function (array $carry, City $c): array {
                    $iso = mb_strtoupper((string) ($c->state?->country?->iso_code ?? ''));
                    $stateCode = mb_strtoupper((string) ($c->state?->code ?? ''));
                    $permalink = mb_strtolower((string) $c->permalink);
                    if ($iso !== '' && $stateCode !== '' && $permalink !== '') {
                        $carry[$iso.'|'.$stateCode.'|'.$permalink] = (int) $c->id;
                    }

                    return $carry;
                }, []);

            /**
             * Tracks (city_id, permalink) pairs already processed in
             * THIS file, mapping to the 1-based row number of the first
             * occurrence. Rejects intra-file duplicates.
             *
             * @var array<string, int> $seenKeys
             */
            $seenKeys = [];

            DB::transaction(function () use ($handle, $header, $cityLookup, $result, &$seenKeys): void {
                $rowNumber = 1;
                while (($rawRow = fgetcsv($handle)) !== false) {
                    $rowNumber++;

                    if ($this->isBlankRow($rawRow)) {
                        continue;
                    }

                    $row = $this->associate($header, $rawRow);
                    $this->processRow($rowNumber, $row, $cityLookup, $seenKeys, $result);
                }
            });
        } finally {
            fclose($handle);
        }

        return $result;
    }

    /**
     * @param  array<string, string|null>  $row
     * @param  array<string, int>  $cityLookup  "ISO|CODE|PERMALINK" → city_id
     * @param  array<string, int>  $seenKeys  1-based row number of first occurrence for each (city_id, permalink)
     */
    private function processRow(int $rowNumber, array $row, array $cityLookup, array &$seenKeys, ImportDistrictsResult $result): void
    {
        $iso = mb_strtoupper(mb_trim((string) ($row['country_iso'] ?? '')));
        $stateCode = mb_strtoupper(mb_trim((string) ($row['state_code'] ?? '')));
        $cityPermalink = mb_strtolower(mb_trim((string) ($row['city_permalink'] ?? '')));
        $name = mb_trim((string) ($row['name'] ?? ''));
        $code = mb_trim((string) ($row['code'] ?? ''));
        $permalink = mb_trim((string) ($row['permalink'] ?? ''));
        $postalPrefix = mb_trim((string) ($row['postal_code_prefix'] ?? ''));
        $isPopularRaw = mb_trim((string) ($row['is_popular'] ?? ''));
        $status = mb_strtolower(mb_trim((string) ($row['status'] ?? '')));
        $sortOrderRaw = mb_trim((string) ($row['sort_order'] ?? ''));

        // City resolution BEFORE validation so the city_id is available
        // for the unique rules below.
        $lookupKey = $iso.'|'.$stateCode.'|'.$cityPermalink;
        if ($iso === '' || $stateCode === '' || $cityPermalink === '' || ! isset($cityLookup[$lookupKey])) {
            $result->skipped++;
            $result->errors[] = [
                'row' => $rowNumber,
                'errors' => ['city_permalink' => [(string) __(
                    'admin.locations.import_error_unknown_city',
                    ['iso' => $iso, 'state' => $stateCode, 'city' => $cityPermalink],
                )]],
                'values' => $row,
            ];

            return;
        }
        $cityId = $cityLookup[$lookupKey];

        // Derive permalink BEFORE dedup — dedup key IS the permalink.
        if ($permalink === '' && $name !== '') {
            $permalink = Str::slug($name);
        }

        // Reject intra-file duplicates BEFORE hitting the DB.
        if ($permalink !== '') {
            $key = $cityId.'|'.$permalink;
            if (isset($seenKeys[$key])) {
                $firstRow = $seenKeys[$key];
                $result->skipped++;
                $result->errors[] = [
                    'row' => $rowNumber,
                    'errors' => [
                        'permalink' => [(string) __(
                            'admin.locations.import_error_duplicate_district',
                            ['first_row' => $firstRow, 'permalink' => $permalink],
                        )],
                    ],
                    'values' => $row,
                ];

                return;
            }
            $seenKeys[$key] = $rowNumber;
        }

        // Fetch existing district (if any) BEFORE building the data so
        // we can preserve untouched fields on partial re-imports.
        $existingDistrict = District::query()
            ->where('city_id', $cityId)
            ->where('permalink', $permalink)
            ->first();
        $existingId = $existingDistrict?->id;

        // Preserve-on-update semantics for optional fields — empty CSV
        // cells never wipe data (postal_code bug lesson).
        $codeValue = $code !== '' ? mb_strtoupper($code) : ($existingDistrict?->code);
        $postalPrefixValue = $postalPrefix !== '' ? $postalPrefix : ($existingDistrict?->postal_code_prefix);
        $statusValue = $status !== ''
            ? $status
            : ((string) ($existingDistrict?->status ?? 'published'));
        $isPopularValue = $isPopularRaw !== ''
            ? $this->parseBool($isPopularRaw)
            : ((bool) ($existingDistrict?->is_popular ?? false));
        $sortOrderValue = $sortOrderRaw !== ''
            ? (int) $sortOrderRaw
            : ($existingDistrict?->sort_order);

        $data = [
            'city_id' => $cityId,
            'name' => $name,
            'code' => $codeValue,
            'permalink' => $permalink,
            'postal_code_prefix' => $postalPrefixValue,
            'is_popular' => $isPopularValue,
            'status' => $statusValue,
            'sort_order' => $sortOrderValue,
        ];

        $rules = [
            'city_id' => ['required', 'integer', 'exists:cities,id'],
            'name' => ['required', 'string', 'max:120'],
            'code' => [
                'nullable', 'string', 'max:8',
                Rule::unique('districts', 'code')->ignore($existingId)->where('city_id', $cityId),
            ],
            'permalink' => [
                'required', 'string', 'max:160',
                'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/',
                Rule::unique('districts', 'permalink')->ignore($existingId)->where('city_id', $cityId),
            ],
            'postal_code_prefix' => ['nullable', 'string', 'max:5'],
            'is_popular' => ['boolean'],
            'status' => ['required', Rule::in(['published', 'draft', 'inactive'])],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:65535'],
        ];

        $validator = Validator::make($data, $rules);
        if ($validator->fails()) {
            $result->skipped++;
            $result->errors[] = [
                'row' => $rowNumber,
                'errors' => $validator->errors()->toArray(),
                'values' => $row,
            ];

            return;
        }

        $validated = $validator->validated();
        if ($validated['sort_order'] === null) {
            $validated['sort_order'] = District::nextSortOrder($cityId);
        }

        $district = District::query()->updateOrCreate(
            ['city_id' => $cityId, 'permalink' => $permalink],
            $validated,
        );

        if ($district->wasRecentlyCreated) {
            $result->created++;
        } else {
            $result->updated++;
        }

        $district->reorderToCurrentPosition();
    }

    /**
     * Accepts yes/no, true/false, 1/0, y/n (case-insensitive). Anything
     * else falls back to false so a typo doesn't accidentally flag a
     * district as popular.
     */
    private function parseBool(string $raw): bool
    {
        return in_array(mb_strtolower($raw), ['1', 'yes', 'y', 'true', 't'], true);
    }

    /**
     * Lowercase headers so `Country_ISO` / `country_iso` / ` COUNTRY ISO `
     * all resolve to the same key. Strips the UTF-8 BOM from the first
     * cell (Excel round-trip fix — same as States/Cities importers).
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

                return mb_strtolower(mb_trim($s));
            },
            $rawHeader,
        ));
    }

    /**
     * @param  list<string>  $header
     * @param  array<int, string|null>  $row
     * @return array<string, string>
     */
    private function associate(array $header, array $row): array
    {
        $out = [];
        foreach ($header as $index => $key) {
            $out[$key] = (string) ($row[$index] ?? '');
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
