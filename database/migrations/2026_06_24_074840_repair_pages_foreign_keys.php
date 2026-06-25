<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Repair four foreign keys that point at a phantom `pages1` table instead
 * of `pages`. The originating migrations declare the FKs against `pages`
 * (e.g. `->constrained('pages')->cascadeOnDelete()`), but the live database
 * on at least one dev environment ended up with the wrong REFERENCED_TABLE
 * — likely because the `pages` table was renamed mid-development and the
 * FK wasn't re-cut.
 *
 * Broken FKs (all reference the non-existent `pages1` table):
 *   - page_category.page_id_foreign            (cascadeOnDelete)
 *   - site_settings.privacy_page_id_foreign    (nullOnDelete)
 *   - site_settings.terms_page_id_foreign      (nullOnDelete)
 *   - site_settings.imprint_page_id_foreign    (nullOnDelete)
 *
 * Symptom: editing/saving a Page triggers
 *   SQLSTATE[23000]: Cannot add or update a child row: a foreign key
 *   constraint fails (`...`.`page_category`, CONSTRAINT
 *   `page_category_page_id_foreign` FOREIGN KEY (`page_id`)
 *   REFERENCES `pages1` (`id`) ON DELETE CASCADE)
 *
 * This migration drops each broken FK by name and re-creates it against
 * `pages.id`. Idempotent — only acts when the constraint exists and is
 * pointing at the wrong table.
 */
return new class extends Migration
{
    public function up(): void
    {
        // Step 1 — clean up orphaned rows. Because the FKs were pointing at
        // a non-existent table, MySQL was silently allowing inserts that
        // referenced deleted pages. Those orphans have to be reconciled
        // before a real FK can be put back on.
        //
        // For `page_category` (cascadeOnDelete in spec) the cascade equivalent
        // is to delete the pivot row entirely — same outcome the FK would
        // have produced when the referenced page was deleted.
        DB::statement('
            DELETE pc
            FROM page_category pc
            LEFT JOIN pages p ON p.id = pc.page_id
            WHERE p.id IS NULL
        ');

        // For the three site_settings page-id columns (nullOnDelete in spec)
        // the nullification equivalent is to set the column to NULL when the
        // referenced page no longer exists.
        foreach (['privacy_page_id', 'terms_page_id', 'imprint_page_id'] as $col) {
            DB::statement("
                UPDATE site_settings ss
                LEFT JOIN pages p ON p.id = ss.{$col}
                SET ss.{$col} = NULL
                WHERE ss.{$col} IS NOT NULL AND p.id IS NULL
            ");
        }

        // Step 2 — drop each broken FK and re-create it against `pages`.
        // Idempotent: if the constraint is already correct (or already gone
        // from a previous partial run), we skip / adapt.
        $broken = [
            ['table' => 'page_category', 'fk' => 'page_category_page_id_foreign', 'column' => 'page_id', 'onDelete' => 'CASCADE'],
            ['table' => 'site_settings', 'fk' => 'site_settings_privacy_page_id_foreign', 'column' => 'privacy_page_id', 'onDelete' => 'SET NULL'],
            ['table' => 'site_settings', 'fk' => 'site_settings_terms_page_id_foreign', 'column' => 'terms_page_id', 'onDelete' => 'SET NULL'],
            ['table' => 'site_settings', 'fk' => 'site_settings_imprint_page_id_foreign', 'column' => 'imprint_page_id', 'onDelete' => 'SET NULL'],
        ];

        foreach ($broken as $row) {
            $current = DB::selectOne(
                'SELECT REFERENCED_TABLE_NAME AS ref
                 FROM information_schema.KEY_COLUMN_USAGE
                 WHERE TABLE_SCHEMA = DATABASE()
                   AND TABLE_NAME = ?
                   AND CONSTRAINT_NAME = ?
                   AND REFERENCED_TABLE_NAME IS NOT NULL
                 LIMIT 1',
                [$row['table'], $row['fk']],
            );

            // Already pointing at the right table — nothing to do.
            if ($current !== null && $current->ref === 'pages') {
                continue;
            }

            // FK exists but points at the wrong table — drop it.
            if ($current !== null) {
                DB::statement(sprintf(
                    'ALTER TABLE `%s` DROP FOREIGN KEY `%s`',
                    $row['table'],
                    $row['fk'],
                ));
            }

            // FK is now absent — add the correct one.
            DB::statement(sprintf(
                'ALTER TABLE `%s` ADD CONSTRAINT `%s` FOREIGN KEY (`%s`) REFERENCES `pages` (`id`) ON DELETE %s',
                $row['table'],
                $row['fk'],
                $row['column'],
                $row['onDelete'],
            ));
        }
    }

    /**
     * Reverting this migration is intentionally a no-op — there is no
     * meaningful "down" state to roll back to. The previous state was
     * a broken FK pointing at a table that doesn't exist, which we don't
     * want to recreate.
     */
    public function down(): void
    {
        //
    }
};
