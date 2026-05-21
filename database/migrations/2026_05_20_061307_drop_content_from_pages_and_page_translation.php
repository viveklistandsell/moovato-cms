<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pages', function (Blueprint $table): void {
            $table->dropColumn('content');
        });

        Schema::table('page_translation', function (Blueprint $table): void {
            $table->dropColumn('content');
        });
    }

    public function down(): void
    {
        Schema::table('pages', function (Blueprint $table): void {
            $table->longText('content')->nullable()->after('image');
        });

        Schema::table('page_translation', function (Blueprint $table): void {
            $table->longText('content')->nullable()->after('permalink');
        });
    }
};
