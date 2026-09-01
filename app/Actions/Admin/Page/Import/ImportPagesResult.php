<?php

declare(strict_types=1);

namespace App\Actions\Admin\Page\Import;

/**
 * Aggregated outcome of a single CSV import run, flashed to the next
 * request so the Vue Import page can render the summary and error panel.
 *
 * Row numbers in `errors` are 1-based and INCLUDE the header row — so a
 * failure on the third data row shows as `row: 4`, matching what the
 * admin sees when they open the CSV in Excel.
 */
final class ImportPagesResult
{
    /** Pages created from scratch. */
    public int $created = 0;

    /** Pages that already existed and were overwritten. */
    public int $updated = 0;

    /** Rows the importer refused (page-level failures). */
    public int $skipped = 0;

    /** Widget sections added to a page. */
    public int $widgetsAdded = 0;

    /** Widget rows the importer refused (unknown slug, unresolved parent, …). */
    public int $widgetsSkipped = 0;

    /**
     * Non-fatal warnings (missing image, unknown widget slug on a section
     * row). The page is still created / updated — just missing that piece.
     *
     * @var list<array{row: int, kind: string, message: string, values: array<string, mixed>}>
     */
    public array $warnings = [];

    /**
     * Fatal per-row errors that caused a whole page group to be skipped.
     *
     * @var list<array{row: int, errors: array<string, list<string>>, values: array<string, mixed>}>
     */
    public array $errors = [];

    public function hasProblems(): bool
    {
        return $this->skipped > 0 || $this->warnings !== [] || $this->widgetsSkipped > 0;
    }

    /**
     * @return array{created: int, updated: int, skipped: int, widgets_added: int, widgets_skipped: int, warnings: list<array<string, mixed>>, errors: list<array<string, mixed>>}
     */
    public function toArray(): array
    {
        return [
            'created' => $this->created,
            'updated' => $this->updated,
            'skipped' => $this->skipped,
            'widgets_added' => $this->widgetsAdded,
            'widgets_skipped' => $this->widgetsSkipped,
            'warnings' => $this->warnings,
            'errors' => $this->errors,
        ];
    }
}
