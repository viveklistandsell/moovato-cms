<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('companies', function (Blueprint $t) {
            $t->id();

            $t->foreignId('primary_city_id')->constrained('cities')->restrictOnDelete();
            $t->foreignId('primary_district_id')->nullable()->constrained('districts')->nullOnDelete();
            $t->string('street', 255)->nullable();
            $t->string('postal_code', 10)->nullable();

            $t->string('logo', 255)->nullable();
            $t->string('cover', 255)->nullable();

            $t->boolean('verified')->default(false);
            $t->boolean('is_top_rated')->default(false);
            $t->string('plan_tier', 20)->default('free');

            $t->decimal('rating_avg', 3, 1)->default(0);
            $t->unsignedInteger('review_count')->default(0);
            $t->unsignedTinyInteger('recommend_pct')->default(0);
            $t->json('rating_breakdown')->nullable();

            $t->decimal('google_rating', 3, 1)->nullable();
            $t->unsignedInteger('google_review_count')->default(0);

            $t->unsignedSmallInteger('founded_year')->nullable();
            $t->unsignedInteger('employee_count')->nullable();

            $t->string('status', 20)->default('published');
            $t->unsignedInteger('sort_order')->default(0);
            $t->timestamps();

            $t->index(['status', 'primary_city_id']);
            $t->index(['status', 'rating_avg']);
            $t->index('is_top_rated');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('companies');
    }
};
