<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('company_service_areas', function (Blueprint $t) {
            $t->foreignId('company_id')->constrained('companies')->cascadeOnDelete();
            $t->foreignId('district_id')->constrained('districts')->cascadeOnDelete();
            $t->boolean('is_home_base')->default(false);
            $t->unsignedSmallInteger('response_hours')->nullable();
            $t->timestamps();

            $t->primary(['company_id', 'district_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('company_service_areas');
    }
};
