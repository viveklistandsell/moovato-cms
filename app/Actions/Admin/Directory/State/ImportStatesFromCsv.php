<?php

declare(strict_types=1);

namespace App\Actions\Admin\Directory\State;

use App\Http\Requests\Admin\Directory\State\StoreStateRequest;
use App\Models\Country;
use App\Models\State;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use RuntimeException;

/**
 * Streams a CSV upload, validates each row against the same rules
 * {@see StoreStateRequest} uses,
 * and creates-or-updates each valid row keyed on `(country_id, code)`.
 *
 * Design decisions:
 *   - PARTIAL SUCCESS: good rows commit, bad rows are reported. Faster
 *     fix-and-retry loop for the admin than all-or-nothing.
 *   - TRANSACTION: wraps the whole batch so a fatal error (DB gone, disk
 *     full) rolls back cleanly. Row-level validation failures do NOT
 *     abort — they're skipped and reported.
 *   - COUNTRY LOOKUP: CSV carries `country_iso` (portable). The action
 *     resolves it to a `country_id` per row. Rows for unknown ISO codes
 *     are skipped with a clear error.
 *   - AUTO-DERIVATION: missing permalink is derived from name via
 *     Str::slug; missing status defaults to `published`; missing
 *     sort_order gets the next available position within the country.
 */
final readonly class ImportStatesFromCsv
{
    /**
     * Required first-row headers, in any order. Extra columns are
     * ignored — makes the CSV forgiving if an admin adds notes columns.
     * Optional columns (`permalink`, `status`, `sort_order`) are
     * auto-derived when absent; see `processRow`.
     */
    private const REQUIRED_HEADERS = ['country_iso', 'name', 'code'];

    public function handle(UploadedFile $file): ImportStatesResult
    {
        $result = new ImportStatesResult;

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

            /** @var array<string, int> $isoToId */
            $isoToId = Country::query()
                ->pluck('id', 'iso_code')
                ->map(fn ($id): int => (int) $id)
                ->all();
            $seenKeys = [];

            DB::transaction(function () use ($handle, $header, $isoToId, $result, &$seenKeys): void {
                $rowNumber = 1;
                while (($rawRow = fgetcsv($handle)) !== false) {
                    $rowNumber++;

                    if ($this->isBlankRow($rawRow)) {
                        continue;
                    }

                    $row = $this->associate($header, $rawRow);

                    $this->processRow($rowNumber, $row, $isoToId, $seenKeys, $result);
                }
            });
        } finally {
            fclose($handle);
        }

        return $result;
    }

    /**
     * @param  array<string, string|null>  $row
     * @param  array<string, int>  $isoToId
     * @param  array<string, int>  $seenKeys  1-based row number of first occurrence for each (iso, code)
     */
    private function processRow(int $rowNumber, array $row, array $isoToId, array &$seenKeys, ImportStatesResult $result): void
    {
        $iso = mb_strtoupper(mb_trim((string) ($row['country_iso'] ?? '')));
        $name = mb_trim((string) ($row['name'] ?? ''));
        $code = mb_strtoupper(mb_trim((string) ($row['code'] ?? '')));
        $permalink = mb_trim((string) ($row['permalink'] ?? ''));
        $status = mb_strtolower(mb_trim((string) ($row['status'] ?? '')));
        $sortOrderRaw = mb_trim((string) ($row['sort_order'] ?? ''));

        if ($iso === '' || ! isset($isoToId[$iso])) {
            $result->skipped++;
            $result->errors[] = [
                'row' => $rowNumber,
                'errors' => ['country_iso' => [(string) __(
                    'admin.locations.import_error_unknown_iso',
                    ['iso' => $iso],
                )]],
                'values' => $row,
            ];

            return;
        }
        $countryId = $isoToId[$iso];

        if ($code !== '') {
            $key = $iso.'|'.$code;
            if (isset($seenKeys[$key])) {
                $firstRow = $seenKeys[$key];
                $result->skipped++;
                $result->errors[] = [
                    'row' => $rowNumber,
                    'errors' => [
                        'code' => [(string) __(
                            'admin.locations.import_error_duplicate_row',
                            ['first_row' => $firstRow, 'iso' => $iso, 'code' => $code],
                        )],
                    ],
                    'values' => $row,
                ];

                return;
            }
            $seenKeys[$key] = $rowNumber;
        }

        if ($permalink === '' && $name !== '') {
            $permalink = Str::slug($name);
        }
        if ($status === '') {
            $status = 'published';
        }
        $sortOrder = $sortOrderRaw === '' ? null : (int) $sortOrderRaw;

        $data = [
            'country_id' => $countryId,
            'name' => $name,
            'code' => $code,
            'permalink' => $permalink,
            'status' => $status,
            'sort_order' => $sortOrder,
        ];

        $existingId = State::query()
            ->where('country_id', $countryId)
            ->where('code', $code)
            ->value('id');

        $rules = [
            'country_id' => ['required', 'integer', 'exists:countries,id'],
            'name' => ['required', 'string', 'max:255'],
            'code' => [
                'required', 'string', 'max:8',
                Rule::unique('states', 'code')->ignore($existingId)->where('country_id', $countryId),
            ],
            'permalink' => [
                'required', 'string', 'max:255',
                'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/',
                Rule::unique('states', 'permalink')->ignore($existingId)->where('country_id', $countryId),
            ],
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
            $validated['sort_order'] = State::nextSortOrder($countryId);
        }

        $state = State::query()->updateOrCreate(
            ['country_id' => $countryId, 'code' => $code],
            $validated,
        );

        if ($state->wasRecentlyCreated) {
            $result->created++;
        } else {
            $result->updated++;
        }

        $state->reorderToCurrentPosition();
    }

    /**
     * Lowercase the header cells so `Country_ISO`, `country_iso`, and
     * ` Country ISO ` all resolve to the same key. Also trims whitespace
     * that spreadsheet apps sometimes add.
     *
     * IMPORTANT: strips the UTF-8 BOM (`\xEF\xBB\xBF`) if present on the
     * first cell. Our exporter writes a BOM at the start of every CSV so
     * Excel displays German characters (Baden-Württemberg) correctly on
     * Windows — but `fgetcsv` reads that BOM as part of the first header
     * cell. Without this strip, a re-imported export file fails header
     * validation because "country_iso" is actually "\xEF\xBB\xBFcountry_iso".
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
     * Turn a positional row (`['DE', 'Bayern', 'BY']`) into an associative
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
