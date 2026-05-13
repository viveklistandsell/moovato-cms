<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('media_files', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('folder_id')->nullable()->constrained('media_folders')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('name');
            $table->string('original_name');
            $table->string('mime_type');
            $table->string('extension', 32);
            $table->unsignedBigInteger('size');
            $table->string('disk')->default('public');
            $table->string('path');
            $table->string('thumb_path')->nullable();
            $table->string('medium_path')->nullable();
            $table->json('metadata')->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->index(['folder_id', 'name']);
            $table->index('mime_type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('media_files');
    }
};
