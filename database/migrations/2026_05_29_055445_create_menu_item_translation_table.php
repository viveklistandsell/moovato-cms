<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('menu_item_translation', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('menu_item_id')
                ->constrained('menu_items')->cascadeOnDelete();
            $table->string('lang', 10);
            $table->string('label', 255);
            $table->string('link_url', 500)->nullable();
            $table->timestamps();
            $table->unique(['menu_item_id', 'lang'], 'menu_item_translation_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('menu_item_translation');
    }
};
