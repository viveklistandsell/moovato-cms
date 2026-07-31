<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Replace the string `icon` (Lucide identifier) with a nullable `image`
 * column that stores a media path — matches Blog + Page hero images.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('service_categories', function (Blueprint $table): void {
            $table->dropColumn('icon');
        });

        Schema::table('service_categories', function (Blueprint $table): void {
            $table->string('image')->nullable()->after('name');
        });
    }

    public function down(): void
    {
        Schema::table('service_categories', function (Blueprint $table): void {
            $table->dropColumn('image');
        });

        Schema::table('service_categories', function (Blueprint $table): void {
            $table->string('icon')->nullable()->after('name');
        });
    }
};
