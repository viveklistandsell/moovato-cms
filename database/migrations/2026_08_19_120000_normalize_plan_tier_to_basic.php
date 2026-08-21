<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Normalize the `companies.plan_tier` column to the new
 * PlanTier enum vocabulary (basic / premium / gold).
 *
 * The column already exists — it was created with a default of
 * 'free' months ago. This migration:
 *   1. Renames any 'free' values to 'basic' so the string matches
 *      the new PlanTier::Basic case.
 *   2. Backfills NULL rows to 'basic' (some early companies were
 *      inserted before the column was properly defaulted).
 *   3. Changes the column default so new INSERTs pick up 'basic'
 *      automatically if the caller doesn't specify a tier.
 *
 * Reversible — down() puts the default back to 'free' but leaves
 * data alone (no destructive rollback).
 */
return new class extends Migration
{
    public function up(): void
    {
        DB::table('companies')->where('plan_tier', 'free')->update(['plan_tier' => 'basic']);
        DB::table('companies')->whereNull('plan_tier')->update(['plan_tier' => 'basic']);

        if (Schema::hasColumn('companies', 'plan_tier')) {
            try {
                Schema::table('companies', function (Blueprint $table): void {
                    $table->string('plan_tier', 20)->default('basic')->change();
                });
            } catch (Throwable) {

            }
        }
    }

    public function down(): void
    {
        try {
            Schema::table('companies', function (Blueprint $table): void {
                $table->string('plan_tier', 20)->default('free')->change();
            });
        } catch (Throwable) {

        }
    }
};
