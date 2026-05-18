<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('page_categories', function (Blueprint $table): void {
            $table->id();
            $table->string('title');
            $table->string('permalink')->unique();
            $table->foreignId('parent_id')->nullable()->constrained('page_categories')->nullOnDelete();
            $table->boolean('is_default')->default(false)->index();
            $table->string('status', 20)->default('published')->index();
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();

            $table->index(['parent_id', 'sort_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('page_categories');
    }
};
