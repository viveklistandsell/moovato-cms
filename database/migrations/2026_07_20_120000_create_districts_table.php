<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Districts (Bezirke / Stadtbezirke) — the geo tier below City.
 * Universal: any city can have districts. Berlin ships with its 12
 * Bezirke seeded on install, other cities are populated by admins.
 *
 * Design mirrors the existing States and Cities tables exactly so the
 * admin CRUD, drag-reorder, bulk actions and CSV import/export patterns
 * all transfer 1:1 without new mental model.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('districts', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('city_id')
                ->constrained('cities')
                ->cascadeOnDelete();
            $table->string('name', 120);
            $table->string('code', 8)->nullable();
            $table->string('permalink', 160);
            $table->string('postal_code_prefix', 5)->nullable();
            $table->boolean('is_popular')->default(false)->index();
            $table->string('status', 20)->default('published')->index();
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();
            $table->unique(['city_id', 'permalink'], 'districts_city_permalink_unique');
            $table->index(['city_id', 'code']);
            $table->index(['city_id', 'sort_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('districts');
    }
};
