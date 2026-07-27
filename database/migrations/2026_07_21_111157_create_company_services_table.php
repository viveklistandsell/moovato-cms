<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('company_services', function (Blueprint $t) {
            $t->foreignId('company_id')->constrained('companies')->cascadeOnDelete();
            $t->foreignId('service_category_id')->constrained('service_categories')->cascadeOnDelete();
            $t->boolean('is_primary')->default(false);
            $t->decimal('price_from', 10, 2)->nullable();
            $t->string('price_unit', 40)->nullable();
            $t->timestamps();

            $t->primary(['company_id', 'service_category_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('company_services');
    }
};
