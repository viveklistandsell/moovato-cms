<?php

declare(strict_types=1);

namespace App\Actions\Admin\Directory\City;

use App\Http\Requests\Admin\Directory\City\StoreCityRequest;
use App\Models\City;
use App\Models\State;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use RuntimeException;

/**
 * Streams a CSV upload, validates each row against the same rules
 * {@see StoreCityRequest} uses, and creates-or-updates each valid row
 * keyed on `(state_id, permalink)` — the DB-level unique constraint on
 * `cities`.
 */
final readonly class ImportCitiesFromCsv
{
    private const REQUIRED_HEADERS = ['country_iso', 'state_code', 'name'];

    public function handle(UploadedFile $file): ImportCitiesResult
    {
        $result = new ImportCitiesResult;

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
             * @var array<string, int> $stateLookup
             */
            $stateLookup = State::query()
                ->with('country:id,iso_code')
                ->get(['id', 'country_id', 'code'])
                ->reduce(function (array $carry, State $s): array {
                    $iso = mb_strtoupper((string) ($s->country?->iso_code ?? ''));
                    $code = mb_strtoupper((string) $s->code);
                    if ($iso !== '' && $code !== '') {
                        $carry[$iso.'|'.$code] = (int) $s->id;
                    }

                    return $carry;
                }, []);

            /**
             * @var array<string, int> $seenKeys
             */
            $seenKeys = [];

            DB::transaction(function () use ($handle, $header, $stateLookup, $result, &$seenKeys): void {
                $rowNumber = 1;
                while (($rawRow = fgetcsv($handle)) !== false) {
                    $rowNumber++;

                    if ($this->isBlankRow($rawRow)) {
                        continue;
                    }

                    $row = $this->associate($header, $rawRow);

                    $this->processRow($rowNumber, $row, $stateLookup, $seenKeys, $result);
                }
            });
        } finally {
            fclose($handle);
        }

        return $result;
    }

    /**
     * @param  array<string, string|null>  $row
     * @param  array<string, int>  $stateLookup  "ISO|CODE" → state_id
     * @param  array<string, int>  $seenKeys  1-based row number of first occurrence for each (state_id, permalink)
     */
    private function processRow(int $rowNumber, array $row, array $stateLookup, array &$seenKeys, ImportCitiesResult $result): void
    {
        $iso = mb_strtoupper(mb_trim((string) ($row['country_iso'] ?? '')));
        $stateCode = mb_strtoupper(mb_trim((string) ($row['state_code'] ?? '')));
        $name = mb_trim((string) ($row['name'] ?? ''));
        $permalink = mb_trim((string) ($row['permalink'] ?? ''));
        $postalCode = mb_trim((string) ($row['postal_code'] ?? ''));
        $isPopularRaw = mb_trim((string) ($row['is_popular'] ?? ''));
        $status = mb_strtolower(mb_trim((string) ($row['status'] ?? '')));
        $sortOrderRaw = mb_trim((string) ($row['sort_order'] ?? ''));

        $lookupKey = $iso.'|'.$stateCode;
        if ($iso === '' || $stateCode === '' || ! isset($stateLookup[$lookupKey])) {
            $result->skipped++;
            $result->errors[] = [
                'row' => $rowNumber,
                'errors' => ['state_code' => [(string) __(
                    'admin.locations.import_error_unknown_state',
                    ['iso' => $iso, 'code' => $stateCode],
                )]],
                'values' => $row,
            ];

            return;
        }
        $stateId = $stateLookup[$lookupKey];

        if ($permalink === '' && $name !== '') {
            $permalink = Str::slug($name);
        }

        if ($permalink !== '') {
            $key = $stateId.'|'.$permalink;
            if (isset($seenKeys[$key])) {
                $firstRow = $seenKeys[$key];
                $result->skipped++;
                $result->errors[] = [
                    'row' => $rowNumber,
                    'errors' => [
                        'permalink' => [(string) __(
                            'admin.locations.import_error_duplicate_city',
                            ['first_row' => $firstRow, 'permalink' => $permalink],
                        )],
                    ],
                    'values' => $row,
                ];

                return;
            }
            $seenKeys[$key] = $rowNumber;
        }

        $existingCity = City::query()
            ->where('state_id', $stateId)
            ->where('permalink', $permalink)
            ->first();
        $existingId = $existingCity?->id;

        $statusValue = $status !== ''
            ? $status
            : ((string) ($existingCity?->status ?? 'published'));

        $postalCodeValue = $postalCode !== ''
            ? $postalCode
            : ($existingCity?->postal_code);

        $isPopularValue = $isPopularRaw !== ''
            ? $this->parseBool($isPopularRaw)
            : ((bool) ($existingCity?->is_popular ?? false));

        $sortOrderValue = $sortOrderRaw !== ''
            ? (int) $sortOrderRaw
            : ($existingCity?->sort_order);

        $data = [
            'state_id' => $stateId,
            'name' => $name,
            'permalink' => $permalink,
            'postal_code' => $postalCodeValue,
            'is_popular' => $isPopularValue,
            'status' => $statusValue,
            'sort_order' => $sortOrderValue,
        ];

        $rules = [
            'state_id' => ['required', 'integer', 'exists:states,id'],
            'name' => ['required', 'string', 'max:255'],
            'permalink' => [
                'required', 'string', 'max:255',
                'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/',
                Rule::unique('cities', 'permalink')->ignore($existingId)->where('state_id', $stateId),
            ],
            'postal_code' => ['nullable', 'string', 'max:16'],
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
            $validated['sort_order'] = City::nextSortOrder($stateId);
        }

        $city = City::query()->updateOrCreate(
            ['state_id' => $stateId, 'permalink' => $permalink],
            $validated,
        );

        if ($city->wasRecentlyCreated) {
            $result->created++;
        } else {
            $result->updated++;
        }

        $city->reorderToCurrentPosition();
    }

    private function parseBool(string $raw): bool
    {
        $lower = mb_strtolower($raw);

        return in_array($lower, ['1', 'yes', 'y', 'true', 't'], true);
    }

    /**
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
     * Turn a positional row (`['DE', 'BE', 'Berlin']`) into an associative
     * one keyed by header (`['country_iso' => 'DE', ...]`). Handles rows
     * shorter than the header by filling missing cells with empty strings.
     *
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
