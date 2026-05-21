<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('page_widget_translation', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('page_widget_id')->constrained('page_widgets')->cascadeOnDelete();
            $table->string('lang', 10)->index();
            $table->json('data')->nullable();
            $table->timestamps();

            $table->unique(['page_widget_id', 'lang']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('page_widget_translation');
    }
};
