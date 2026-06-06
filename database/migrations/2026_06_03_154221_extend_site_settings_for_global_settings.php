<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Extend the single-row site_settings (and its translation table) with the
 * full Basic / Global Settings field set: identity, branding, SEO, legal,
 * analytics, maintenance, layout, and security. Per-locale fields land on
 * site_setting_translation; everything else lives on the parent row.
 *
 * Naming convention: snake_case columns that mirror the form field names so
 * the admin form, FormRequest, and DB stay in sync without translation.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('site_settings', function (Blueprint $table): void {
            // --- Card 1 · Site identity --------------------------------
            $table->string('site_name')->nullable()->after('id');
            $table->string('timezone', 64)->nullable()->after('site_name');
            $table->string('date_format', 32)->nullable()->after('timezone');
            // --- Card 2 · Branding -------------------------------------
            $table->string('logo_light_path')->nullable();
            $table->string('logo_dark_path')->nullable();
            $table->string('favicon_path')->nullable();
            $table->string('theme_color', 7)->nullable();
            // --- Card 3 · SEO defaults ---------------------------------
            $table->string('default_meta_title_template')->nullable();
            $table->string('default_og_image_path')->nullable();
            $table->boolean('robots_index')->default(true);
            // --- Card 4 · Legal pages ----------------------------------
            $table->foreignId('privacy_page_id')->nullable()->constrained('pages')->nullOnDelete();
            $table->foreignId('terms_page_id')->nullable()->constrained('pages')->nullOnDelete();
            $table->foreignId('imprint_page_id')->nullable()->constrained('pages')->nullOnDelete();
            // --- Card 5 · Analytics & tracking -------------------------
            $table->string('google_analytics_id', 64)->nullable();
            $table->string('google_tag_manager_id', 64)->nullable();
            $table->string('meta_pixel_id', 64)->nullable();
            $table->text('custom_head_code')->nullable();
            $table->boolean('cookie_consent_required')->default(false);
            // --- Card 8 · Maintenance ---------------------------------
            $table->boolean('maintenance_enabled')->default(false);
            $table->string('maintenance_bypass_ips', 500)->nullable();
            // --- Card 9 · Layout toggles ------------------------------
            $table->boolean('header_sticky')->default(true);
            $table->boolean('show_language_switcher')->default(true);
            $table->boolean('show_back_to_top')->default(true);
            $table->boolean('footer_copyright_auto_year')->default(true);
            // --- Card 10 · Security -----------------------------------
            $table->boolean('require_2fa_for_admins')->default(false);
            // Unsigned smallint covers up to 65 535 minutes (~45 days).
            $table->unsignedSmallInteger('session_lifetime_minutes')->nullable();
            $table->unsignedSmallInteger('login_throttle_attempts')->nullable();
        });

        Schema::table('site_setting_translation', function (Blueprint $table): void {
            $table->string('site_tagline')->nullable();
            $table->string('default_meta_description', 500)->nullable();
            $table->string('maintenance_heading')->nullable();
            $table->text('maintenance_message')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('site_setting_translation', function (Blueprint $table): void {
            $table->dropColumn([
                'site_tagline',
                'default_meta_description',
                'maintenance_heading',
                'maintenance_message',
            ]);
        });

        Schema::table('site_settings', function (Blueprint $table): void {
            // Drop FKs by column name so SQLite/MySQL parsers agree.
            $table->dropForeign(['privacy_page_id']);
            $table->dropForeign(['terms_page_id']);
            $table->dropForeign(['imprint_page_id']);

            $table->dropColumn([
                'site_name', 'timezone', 'date_format',
                'logo_light_path', 'logo_dark_path', 'favicon_path', 'theme_color',
                'default_meta_title_template', 'default_og_image_path', 'robots_index',
                'privacy_page_id', 'terms_page_id', 'imprint_page_id',
                'google_analytics_id', 'google_tag_manager_id', 'meta_pixel_id',
                'custom_head_code', 'cookie_consent_required',
                'maintenance_enabled', 'maintenance_bypass_ips',
                'header_sticky', 'show_language_switcher', 'show_back_to_top', 'footer_copyright_auto_year',
                'require_2fa_for_admins', 'session_lifetime_minutes',
                'login_throttle_attempts',
            ]);
        });
    }
};
