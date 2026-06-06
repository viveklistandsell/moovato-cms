<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Cache;

#[Fillable([
    // Footer contact / socials (original set).
    'address', 'phone', 'email', 'whatsapp',
    'facebook_url', 'twitter_url', 'linkedin_url', 'instagram_url',
    // Identity.
    'site_name', 'timezone', 'date_format',
    // Branding.
    'logo_light_path', 'logo_dark_path', 'favicon_path', 'theme_color',
    // SEO defaults.
    'default_meta_title_template', 'default_og_image_path', 'robots_index',
    // Legal page selectors.
    'privacy_page_id', 'terms_page_id', 'imprint_page_id',
    // Analytics & tracking.
    'google_analytics_id', 'google_tag_manager_id', 'meta_pixel_id',
    'custom_head_code', 'cookie_consent_required',
    // Maintenance.
    'maintenance_enabled', 'maintenance_bypass_ips',
    // Layout.
    'header_sticky', 'show_language_switcher', 'show_back_to_top', 'footer_copyright_auto_year',
    // Security.
    'require_2fa_for_admins', 'session_lifetime_minutes',
    'login_throttle_attempts',
])]
final class SiteSetting extends Model
{
    private const CACHE_KEY = 'site_settings:current';

    public static function current(): self
    {
        /** @var array{attrs: array<string, mixed>, translations: array<int, array<string, mixed>>} $payload */
        $payload = Cache::remember(
            self::CACHE_KEY,
            3600,
            fn (): array => self::buildPayload(),
        );

        $model = (new self)->forceFill($payload['attrs']);

        $rel = $model->newCollection(
            array_map(
                fn (array $row): SiteSettingTranslation => (new SiteSettingTranslation)->forceFill($row),
                $payload['translations'],
            ),
        );
        $model->setRelation('translations', $rel);

        return $model;
    }

    public static function flush(): void
    {
        Cache::forget(self::CACHE_KEY);
    }

    public function translations(): HasMany
    {
        return $this->hasMany(SiteSettingTranslation::class);
    }

    public function translation(?string $lang = null): ?SiteSettingTranslation
    {
        $lang ??= app()->getLocale();

        return $this->translations->firstWhere('lang', $lang)
            ?? $this->translations->first();
    }

    /**
     * Eloquent casts so the admin form receives real booleans/ints (instead
     * of "0"/"1" strings) and toggles in the UI bind cleanly.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'robots_index' => 'boolean',
            'cookie_consent_required' => 'boolean',
            'maintenance_enabled' => 'boolean',
            'header_sticky' => 'boolean',
            'show_language_switcher' => 'boolean',
            'show_back_to_top' => 'boolean',
            'footer_copyright_auto_year' => 'boolean',
            'require_2fa_for_admins' => 'boolean',
            'session_lifetime_minutes' => 'integer',
            'login_throttle_attempts' => 'integer',
            'privacy_page_id' => 'integer',
            'terms_page_id' => 'integer',
            'imprint_page_id' => 'integer',
        ];
    }

    /**
     * @return array{attrs: array<string, mixed>, translations: array<int, array<string, mixed>>}
     */
    private static function buildPayload(): array
    {
        $row = self::query()->with('translations')->first();

        if ($row === null) {
            return ['attrs' => [], 'translations' => []];
        }

        return [
            'attrs' => $row->getAttributes(),
            'translations' => $row->translations
                ->map(fn (SiteSettingTranslation $t): array => $t->getAttributes())
                ->all(),
        ];
    }
}
