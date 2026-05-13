<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('languages', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('native_name');
            $table->string('code', 10)->unique();
            $table->string('flag')->nullable();
            $table->boolean('status')->default(true)->index();
            $table->boolean('is_rtl')->default(false);
            $table->string('lang_locale', 20)->nullable();
            $table->boolean('lang_is_default')->default(false)->index();
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('languages');
    }
};
