<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('company_media', function (Blueprint $t) {
            $t->id();
            $t->foreignId('company_id')->constrained('companies')->cascadeOnDelete();
            $t->foreignId('media_file_id')->constrained('media_files')->cascadeOnDelete();
            $t->string('kind', 20);
            $t->unsignedInteger('sort_order')->default(0);
            $t->timestamps();

            $t->index(['company_id', 'kind', 'sort_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('company_media');
    }
};
