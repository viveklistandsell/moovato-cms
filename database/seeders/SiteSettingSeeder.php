<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Language;
use App\Models\SiteSetting;
use App\Models\SiteSettingTranslation;
use Illuminate\Database\Seeder;

/**
 * Seeds the single site_settings row + one translation per active locale.
 * Re-running is safe — firstOrCreate on both the parent row and each
 * translation row keeps existing admin content intact.
 */
final class SiteSettingSeeder extends Seeder
{
    public function run(): void
    {
        $settings = SiteSetting::query()->firstOrCreate(
            ['id' => 1],
            [
                'address' => '123, Berlin, Germany',
                'phone' => '+49 123 456 7890',
                'email' => 'info@example.com',
                'whatsapp' => '491234567890',
                'facebook_url' => 'https://facebook.com/',
                'twitter_url' => 'https://twitter.com/',
                'linkedin_url' => 'https://linkedin.com/',
                'instagram_url' => 'https://instagram.com/',
            ],
        );

        // Per-locale About paragraph. New locales added via the Languages
        // admin will fall back to the English copy via SiteSetting::translation()
        // until the admin fills the form.
        $aboutByLocale = [
            'en' => 'We deliver creative solutions to help your business grow faster and achieve success.',
            'de' => 'Wir liefern kreative Lösungen, mit denen Ihr Unternehmen schneller wächst und erfolgreich ist.',
        ];

        $activeCodes = Language::query()
            ->where('status', true)
            ->pluck('code')
            ->all();

        foreach ($activeCodes as $code) {
            SiteSettingTranslation::query()->firstOrCreate(
                ['site_setting_id' => $settings->id, 'lang' => $code],
                ['about_text' => $aboutByLocale[$code] ?? $aboutByLocale['en']],
            );
        }
    }
}
