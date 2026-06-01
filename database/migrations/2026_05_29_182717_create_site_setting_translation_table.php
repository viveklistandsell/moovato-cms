<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Per-language translations for the (single) site_settings row. Only
     * fields whose value differs across languages live here — address /
     * phone / email / WhatsApp / social URLs are language-neutral and stay
     * on the parent row. Today that means just `about_text`; add more
     * columns here if future settings need per-locale variants.
     *
     * Singular table name matches the project convention
     * (page_translation, menu_item_translation, etc.).
     */
    public function up(): void
    {
        Schema::create('site_setting_translation', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('site_setting_id')
                ->constrained('site_settings')->cascadeOnDelete();
            $table->string('lang', 10);
            $table->text('about_text')->nullable();
            $table->timestamps();

            $table->unique(['site_setting_id', 'lang'], 'site_setting_translation_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('site_setting_translation');
    }
};
