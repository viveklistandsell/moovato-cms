<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Per-user admin UI language preference. Two-character locale code
 * (de, en, …) nullable so existing users keep the system default
 * until they pick a language from the header switcher.
 *
 * Deliberately separate from the frontend locale (which lives in
 * the URL prefix for /en/* public pages). An admin can prefer the
 * German UI while previewing English content — these don't fight.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->string('admin_locale', 5)->nullable()->after('email');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->dropColumn('admin_locale');
        });
    }
};
