<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Single-row settings table. The row is created by SiteSettingSeeder and
     * the admin form only ever updates it — no extra rows are created. The
     * `SiteSetting::current()` accessor enforces this contract.
     */
    public function up(): void
    {
        Schema::create('site_settings', function (Blueprint $table): void {
            $table->id();

            // About Us column (footer left)
            $table->text('about_text')->nullable();

            // Contact Info column (footer right)
            $table->string('address')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            // WhatsApp digits only (no '+' / spaces) so we can build a wa.me
            // link cleanly. The admin form strips formatting on save.
            $table->string('whatsapp')->nullable();

            // Social URLs — bottom of About Us column.
            $table->string('facebook_url')->nullable();
            $table->string('twitter_url')->nullable();
            $table->string('linkedin_url')->nullable();
            $table->string('instagram_url')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('site_settings');
    }
};
