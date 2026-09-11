<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Nullable JSON column to store a company's weekly opening hours,
 * one entry per weekday: `{mon: {closed, open, close}, …, sun: …}`.
 * Availability is gated by the `opening_hours` plan feature — when
 * the tier does not carry the feature the field is stripped by the
 * Company observer just like `cover`, `google_rating`, etc.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('companies', function (Blueprint $t): void {
            $t->json('opening_hours')->nullable()->after('employee_count');
        });
    }

    public function down(): void
    {
        Schema::table('companies', function (Blueprint $t): void {
            $t->dropColumn('opening_hours');
        });
    }
};
