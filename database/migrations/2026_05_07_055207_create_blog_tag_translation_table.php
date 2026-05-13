<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('blog_tag_translation', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tag_id')->constrained('blog_tags')->cascadeOnDelete();
            $table->string('lang', 10)->index();
            $table->string('name');
            $table->string('permalink');
            $table->text('short_description')->nullable();
            $table->timestamps();

            $table->unique(['tag_id', 'lang']);
            $table->unique(['lang', 'permalink']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('blog_tag_translation');
    }
};
