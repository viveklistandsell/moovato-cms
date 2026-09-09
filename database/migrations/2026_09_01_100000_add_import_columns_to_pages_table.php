<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Adds columns needed by the CSV page importer:
 *   - nav_label:  short "header_menu" label from the import CSV
 *                 (e.g. "home"), kept alongside title so it isn't lost.
 *   - qa_score:   optional QA score from the import CSV (free-form).
 *   - brand_size: optional brand-size label from the import CSV.
 *
 * All three are nullable and unused by the current admin UI — they exist
 * so a re-export of an imported page round-trips without losing metadata.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pages', function (Blueprint $table): void {
            $table->string('nav_label', 100)->nullable()->after('title');
            $table->string('qa_score', 50)->nullable()->after('status');
            $table->string('brand_size', 50)->nullable()->after('qa_score');
        });
    }

    public function down(): void
    {
        Schema::table('pages', function (Blueprint $table): void {
            $table->dropColumn(['nav_label', 'qa_score', 'brand_size']);
        });
    }
};
