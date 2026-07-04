<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('service_parent_category_translation', function (Blueprint $table) {
            $table->id();
            $table->foreignId('parent_category_id')
                ->constrained('service_parent_categories')
                ->cascadeOnDelete();
            $table->string('lang', 10)->index();
            $table->string('name');
            $table->string('permalink');
            $table->text('short_description')->nullable();
            $table->timestamps();
            $table->unique(['parent_category_id', 'lang'], 'spct_parent_lang_unique');
            $table->unique(['lang', 'permalink'], 'spct_lang_permalink_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('service_parent_category_translation');
    }
};
