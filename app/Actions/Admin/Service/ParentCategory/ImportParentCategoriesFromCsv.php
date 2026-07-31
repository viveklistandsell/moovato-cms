<?php

declare(strict_types=1);

namespace App\Actions\Admin\Service\ParentCategory;

use App\Http\Requests\Admin\Service\ParentCategory\StoreServiceParentCategoryRequest;
use App\Models\ServiceParentCategory;
use App\Models\ServiceParentCategoryTranslation;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use RuntimeException;

/**
 * Streams a CSV upload, validates each row against the same rules
 * {@see StoreServiceParentCategoryRequest} uses, and creates-or-updates
 * each valid row keyed on `permalink_de` — the DB-level unique field on
 * `service_parent_category_translation` for the default locale.
 *
 * Bilingual layout in a single row: DE + EN columns side-by-side. The
 * DE translation is REQUIRED (that's the dedup key); the EN translation
 * is optional and only written when both `name_en` AND `permalink_en`
 * (or a derivable slug) are provided.
 *
 * Design decisions:
 *   - PARTIAL SUCCESS: good rows commit, bad rows are reported. Faster
 *     fix-and-retry loop than all-or-nothing.
 *   - TRANSACTION: wraps the whole batch so a fatal error rolls back.
 *   - AUTO-DERIVATION: missing permalink_de derives from name_de via
 *     Str::slug. Missing permalink_en derives from name_en. Missing
 *     status defaults to `published`; missing sort_order gets next
 *     available.
 *   - PRESERVE-ON-UPDATE: empty CSV cells for optional fields (image,
 *     is_featured, is_popular, status, sort_order, short_description_*)
 *     mean "keep existing value" on update — NOT "wipe it." Prevents
 *     partial re-imports from silently clearing data. Same lesson as
 *     the postal_code bug in ImportCitiesFromCsv.
 *   - NON-DESTRUCTIVE TRANSLATIONS: empty EN cells on update keep the
 *     existing EN translation row untouched, they don't delete it.
 *   - BOOLEAN PARSING: `is_featured` / `is_popular` accept yes/no,
 *     true/false, 1/0, y/n (case-insensitive) — matches how humans
 *     fill spreadsheets.
 */
final readonly class ImportParentCategoriesFromCsv
{
    /**
     * Only `name_de` is strictly required — `permalink_de` is derived
     * via Str::slug when absent. Extra columns are ignored.
     */
    private const REQUIRED_HEADERS = ['name_de'];

    public function handle(UploadedFile $file): ImportParentCategoriesResult
    {
        $result = new ImportParentCategoriesResult;

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
             * Tracks `permalink_de` values already processed in THIS
             * file, mapping the permalink to the 1-based row number of
             * its first occurrence. Rejects intra-file duplicates —
             * without this, two rows with the same permalink would
             * silently overwrite each other via updateOrCreate.
             *
             * @var array<string, int> $seenKeys
             */
            $seenKeys = [];

            DB::transaction(function () use ($handle, $header, $result, &$seenKeys): void {
                $rowNumber = 1;
                while (($rawRow = fgetcsv($handle)) !== false) {
                    $rowNumber++;

                    if ($this->isBlankRow($rawRow)) {
                        continue;
                    }

                    $row = $this->associate($header, $rawRow);

                    $this->processRow($rowNumber, $row, $seenKeys, $result);
                }
            });
        } finally {
            fclose($handle);
        }

        return $result;
    }

    /**
     * @param  array<string, string|null>  $row
     * @param  array<string, int>  $seenKeys  1-based row number of first occurrence for each permalink_de
     */
    private function processRow(int $rowNumber, array $row, array &$seenKeys, ImportParentCategoriesResult $result): void
    {
        $nameDe = mb_trim((string) ($row['name_de'] ?? ''));
        $permalinkDe = mb_trim((string) ($row['permalink_de'] ?? ''));
        $shortDe = mb_trim((string) ($row['short_description_de'] ?? ''));
        $nameEn = mb_trim((string) ($row['name_en'] ?? ''));
        $permalinkEn = mb_trim((string) ($row['permalink_en'] ?? ''));
        $shortEn = mb_trim((string) ($row['short_description_en'] ?? ''));
        $imageRaw = mb_trim((string) ($row['image'] ?? ''));
        $isFeaturedRaw = mb_trim((string) ($row['is_featured'] ?? ''));
        $isPopularRaw = mb_trim((string) ($row['is_popular'] ?? ''));
        $statusRaw = mb_strtolower(mb_trim((string) ($row['status'] ?? '')));
        $sortOrderRaw = mb_trim((string) ($row['sort_order'] ?? ''));

        // Derive DE permalink from name if missing — the dedup key IS
        // the permalink, so this happens BEFORE the seenKeys check.
        if ($permalinkDe === '' && $nameDe !== '') {
            $permalinkDe = Str::slug($nameDe);
        }
        if ($permalinkEn === '' && $nameEn !== '') {
            $permalinkEn = Str::slug($nameEn);
        }

        // Reject intra-file duplicates BEFORE hitting the DB.
        if ($permalinkDe !== '') {
            if (isset($seenKeys[$permalinkDe])) {
                $firstRow = $seenKeys[$permalinkDe];
                $result->skipped++;
                $result->errors[] = [
                    'row' => $rowNumber,
                    'errors' => [
                        'permalink_de' => [(string) __(
                            'admin.locations.import_error_duplicate_parent',
                            ['first_row' => $firstRow, 'permalink' => $permalinkDe],
                        )],
                    ],
                    'values' => $row,
                ];

                return;
            }
            $seenKeys[$permalinkDe] = $rowNumber;
        }

        // Fetch existing parent (if any) BEFORE building the write-side
        // data so we can preserve untouched fields on partial re-imports.
        // Lookup goes via the DE translation table since permalink is
        // unique-per-lang there, not on the base table.
        $existingParent = null;
        if ($permalinkDe !== '') {
            $existingTranslation = ServiceParentCategoryTranslation::query()
                ->where('lang', 'de')
                ->where('permalink', $permalinkDe)
                ->first();
            if ($existingTranslation !== null) {
                $existingParent = ServiceParentCategory::query()->find($existingTranslation->parent_category_id);
            }
        }
        $existingParentId = $existingParent?->id;

        // Resolve optional fields with "preserve on update" semantics.
        // Same lesson as the postal_code bug: empty CSV cells for optional
        // fields should never wipe data on update.
        $statusValue = $statusRaw !== ''
            ? $statusRaw
            : ((string) ($existingParent?->status ?? 'published'));

        $imageValue = $imageRaw !== ''
            ? $imageRaw
            : ($existingParent?->image);

        $isFeaturedValue = $isFeaturedRaw !== ''
            ? $this->parseBool($isFeaturedRaw)
            : ((bool) ($existingParent?->is_featured ?? false));

        $isPopularValue = $isPopularRaw !== ''
            ? $this->parseBool($isPopularRaw)
            : ((bool) ($existingParent?->is_popular ?? false));

        $sortOrderValue = $sortOrderRaw !== ''
            ? (int) $sortOrderRaw
            : ($existingParent?->sort_order);

        $data = [
            'name' => $nameDe,
            'image' => $imageValue,
            'is_featured' => $isFeaturedValue,
            'is_popular' => $isPopularValue,
            'status' => $statusValue,
            'sort_order' => $sortOrderValue,
            'permalink_de' => $permalinkDe,
        ];

        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'image' => ['nullable', 'string', 'max:255'],
            'is_featured' => ['boolean'],
            'is_popular' => ['boolean'],
            'status' => ['required', Rule::in(['published', 'draft', 'inactive'])],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:65535'],
            'permalink_de' => [
                'required', 'string', 'max:255',
                'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/',
                Rule::unique('service_parent_category_translation', 'permalink')
                    ->where('lang', 'de')
                    ->ignore($this->translationIdFor($existingParentId, 'de')),
            ],
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

        // Auto-assign sort_order on create if still null. Uses the model
        // helper so newly created rows always land at the end.
        if ($validated['sort_order'] === null) {
            $validated['sort_order'] = ServiceParentCategory::nextSortOrder();
        }

        // Persist parent row first — we need its id for the translation
        // upserts below. updateOrCreate keyed by the base row's `name`
        // ONLY on create; on update we go via $existingParent so the
        // key is really the id (avoids race conditions on rename).
        if ($existingParent !== null) {
            $existingParent->update([
                'name' => $validated['name'],
                'image' => $validated['image'],
                'is_featured' => $validated['is_featured'],
                'is_popular' => $validated['is_popular'],
                'status' => $validated['status'],
                'sort_order' => $validated['sort_order'],
            ]);
            $parent = $existingParent->fresh() ?? $existingParent;
            $result->updated++;
        } else {
            $parent = ServiceParentCategory::query()->create([
                'name' => $validated['name'],
                'image' => $validated['image'],
                'is_featured' => $validated['is_featured'],
                'is_popular' => $validated['is_popular'],
                'status' => $validated['status'],
                'sort_order' => $validated['sort_order'],
            ]);
            $result->created++;
        }

        // Upsert DE translation — always present since permalink_de is
        // required. Empty short_description_de on update keeps existing.
        $existingDe = ServiceParentCategoryTranslation::query()
            ->where('parent_category_id', $parent->id)
            ->where('lang', 'de')
            ->first();
        ServiceParentCategoryTranslation::query()->updateOrCreate(
            ['parent_category_id' => $parent->id, 'lang' => 'de'],
            [
                'name' => $nameDe,
                'permalink' => $permalinkDe,
                'short_description' => $shortDe !== ''
                    ? $shortDe
                    : ($existingDe?->short_description),
            ],
        );

        // Upsert EN translation — only if the row provides EN data OR
        // an EN translation already exists (partial update). Empty EN
        // cells on update preserve the existing EN row (non-destructive).
        $existingEn = ServiceParentCategoryTranslation::query()
            ->where('parent_category_id', $parent->id)
            ->where('lang', 'en')
            ->first();

        if ($nameEn !== '' && $permalinkEn !== '') {
            ServiceParentCategoryTranslation::query()->updateOrCreate(
                ['parent_category_id' => $parent->id, 'lang' => 'en'],
                [
                    'name' => $nameEn,
                    'permalink' => $permalinkEn,
                    'short_description' => $shortEn !== ''
                        ? $shortEn
                        : ($existingEn?->short_description),
                ],
            );
        }
        // else: no EN data supplied → leave existing EN row (if any) alone.

        $parent->reorderToCurrentPosition();
    }

    private function translationIdFor(?int $parentId, string $lang): ?int
    {
        if ($parentId === null) {
            return null;
        }

        $id = ServiceParentCategoryTranslation::query()
            ->where('parent_category_id', $parentId)
            ->where('lang', $lang)
            ->value('id');

        return $id !== null ? (int) $id : null;
    }

    /**
     * Accepts yes/no, true/false, 1/0, y/n (case-insensitive). Falls
     * back to `false` for unrecognised values so a typo doesn't silently
     * mark a category as featured when the admin didn't mean to.
     */
    private function parseBool(string $raw): bool
    {
        $lower = mb_strtolower($raw);

        return in_array($lower, ['1', 'yes', 'y', 'true', 't'], true);
    }

    /**
     * Lowercase the header cells so `Name_DE`, `name_de`, and ` Name DE `
     * all resolve to the same key. Also trims whitespace that spreadsheet
     * apps sometimes add.
     *
     * IMPORTANT: strips the UTF-8 BOM if present on the first cell — our
     * exporter writes a BOM for Excel compatibility, but fgetcsv reads it
     * as part of the first cell. Same fix as in ImportStatesFromCsv /
     * ImportCitiesFromCsv (bug learned once, applied everywhere).
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
     * Turn a positional row into an associative one keyed by header.
     * Handles rows shorter than the header by filling with empty strings.
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
