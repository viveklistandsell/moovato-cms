<?php

declare(strict_types=1);

namespace App\Actions\Admin\Directory\District;

/**
 * Counter + error-log DTO returned by {@see ImportDistrictsFromCsv}.
 * Lives across the redirect via Inertia flash so the client can render
 * a "3 created, 2 updated, 1 skipped" toast plus a detailed error panel.
 *
 * Row numbers in `errors` are 1-based and INCLUDE the header row — so a
 * failure on the third data row shows as `row: 4`, matching what the
 * admin sees when they open the CSV in Excel.
 */
final class ImportDistrictsResult
{
    public int $created = 0;

    public int $updated = 0;

    public int $skipped = 0;

    /** @var list<array{row: int, errors: array<string, list<string>>, values: array<string, mixed>}> */
    public array $errors = [];

    public function total(): int
    {
        return $this->created + $this->updated + $this->skipped;
    }

    public function hasErrors(): bool
    {
        return $this->skipped > 0;
    }

    /**
     * @return array{created: int, updated: int, skipped: int, total: int, errors: list<array<string, mixed>>}
     */
    public function toArray(): array
    {
        return [
            'created' => $this->created,
            'updated' => $this->updated,
            'skipped' => $this->skipped,
            'total' => $this->total(),
            'errors' => $this->errors,
        ];
    }
}
