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
                // Identity
                'site_name' => config('app.name', 'Moovato'),
                'timezone' => 'Europe/Berlin',
                'date_format' => 'd.m.Y',
                // Footer contact / socials
                'address' => '123, Berlin, Germany',
                'phone' => '+49 123 456 7890',
                'email' => 'info@example.com',
                'whatsapp' => '491234567890',
                'facebook_url' => 'https://facebook.com/',
                'twitter_url' => 'https://twitter.com/',
                'linkedin_url' => 'https://linkedin.com/',
                'instagram_url' => 'https://instagram.com/',
                // SEO defaults
                'default_meta_title_template' => '%page% — %site%',
                'robots_index' => true,
                'header_sticky' => true,
                'show_language_switcher' => true,
                'show_back_to_top' => true,
                'footer_copyright_auto_year' => true,
                // Security / privacy off by default; admin opts in.
                'cookie_consent_required' => false,
                'maintenance_enabled' => false,
                'require_2fa_for_admins' => false,
            ],
        );

        $aboutByLocale = [
            'en' => 'We deliver creative solutions to help your business grow faster and achieve success.',
            'de' => 'Wir liefern kreative Lösungen, mit denen Ihr Unternehmen schneller wächst und erfolgreich ist.',
        ];

        $taglineByLocale = [
            'en' => 'Modern web solutions',
            'de' => 'Moderne Weblösungen',
        ];

        $maintenanceHeadingByLocale = [
            'en' => "We'll be right back",
            'de' => 'Wir sind gleich zurück',
        ];

        $maintenanceMessageByLocale = [
            'en' => 'The site is briefly down for scheduled maintenance. Please check again in a few minutes.',
            'de' => 'Die Seite ist kurz aufgrund von Wartungsarbeiten offline. Bitte versuchen Sie es in wenigen Minuten erneut.',
        ];

        $activeCodes = Language::query()
            ->where('status', true)
            ->pluck('code')
            ->all();

        foreach ($activeCodes as $code) {
            SiteSettingTranslation::query()->firstOrCreate(
                ['site_setting_id' => $settings->id, 'lang' => $code],
                [
                    'about_text' => $aboutByLocale[$code] ?? $aboutByLocale['en'],
                    'site_tagline' => $taglineByLocale[$code] ?? $taglineByLocale['en'],
                    'maintenance_heading' => $maintenanceHeadingByLocale[$code] ?? $maintenanceHeadingByLocale['en'],
                    'maintenance_message' => $maintenanceMessageByLocale[$code] ?? $maintenanceMessageByLocale['en'],
                ],
            );
        }
    }
}
