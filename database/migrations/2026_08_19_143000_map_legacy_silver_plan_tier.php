<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Map 'silver' → 'premium' as the cardinal equivalent (both sit
 * between the free tier and the top tier in their respective
 * vocabularies) and pick up any other legacy values just in case.
 *
 * Reversible-ish — down() would need to know which rows were
 * originally silver vs. always-premium, and we don't have that
 * info, so down() is a no-op. Rolling back this migration keeps
 * the current values.
 */
return new class extends Migration
{
    public function up(): void
    {
        DB::table('companies')->where('plan_tier', 'silver')->update(['plan_tier' => 'premium']);
        DB::table('companies')
            ->whereNotIn('plan_tier', ['basic', 'premium', 'gold'])
            ->update(['plan_tier' => 'basic']);
    }

    public function down(): void {}
};
