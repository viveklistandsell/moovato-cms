<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('page_category_translation', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('category_id')->constrained('page_categories')->cascadeOnDelete();
            $table->string('lang', 10)->index();
            $table->string('title');
            $table->string('permalink');
            $table->timestamps();

            $table->unique(['category_id', 'lang']);
            $table->unique(['lang', 'permalink']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('page_category_translation');
    }
};
