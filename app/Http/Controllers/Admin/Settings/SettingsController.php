<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin\Settings;

use App\Actions\Admin\SiteSetting\UpdateSiteSetting;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Settings\UpdateAnalyticsRequest;
use App\Http\Requests\Admin\Settings\UpdateBrandingRequest;
use App\Http\Requests\Admin\Settings\UpdateIdentityRequest;
use App\Http\Requests\Admin\Settings\UpdateLayoutRequest;
use App\Http\Requests\Admin\Settings\UpdateLegalRequest;
use App\Http\Requests\Admin\Settings\UpdateMaintenanceRequest;
use App\Http\Requests\Admin\Settings\UpdateSecurityRequest;
use App\Http\Requests\Admin\Settings\UpdateSeoRequest;
use App\Models\Language;
use App\Models\Page;
use App\Models\SiteSetting;
use App\Models\SiteSettingTranslation;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

/**
 * Per-section Global Settings pages. Every section reads from / writes to
 * the same single site_settings row, but each lives at its own URL so the
 * admin sidebar can deep-link straight to the card it cares about.
 *
 * Shared bits (UpdateSiteSetting action, translation upsert, cache flush)
 * are reused — section controllers only carry the prop-shape + the
 * back-redirect after save.
 */
final class SettingsController extends Controller
{
    // ============================================================
    // Card 1 · Site identity
    // ============================================================
    public function identity(): Response
    {
        $s = $this->settings();

        return Inertia::render('admin/settings/Identity', [
            'settings' => [
                'site_name' => $s->site_name,
                'timezone' => $s->timezone,
                'date_format' => $s->date_format,
                'translations' => $this->translationsMap($s, ['site_tagline']),
            ],
            'languages' => $this->presentLanguages(),
            'timezones' => $this->timezones(),
        ]);
    }

    public function updateIdentity(UpdateIdentityRequest $request, UpdateSiteSetting $action): RedirectResponse
    {
        return $this->save($request->validated(), $action, 'Site identity updated.');
    }

    // ============================================================
    // Card 2 · Branding
    // ============================================================
    public function branding(): Response
    {
        $s = $this->settings();

        return Inertia::render('admin/settings/Branding', [
            'settings' => [
                'logo_light_path' => $s->logo_light_path,
                'logo_dark_path' => $s->logo_dark_path,
                'favicon_path' => $s->favicon_path,
                'theme_color' => $s->theme_color,
            ],
        ]);
    }

    public function updateBranding(UpdateBrandingRequest $request, UpdateSiteSetting $action): RedirectResponse
    {
        return $this->save($request->validated(), $action, 'Branding updated.');
    }

    // ============================================================
    // Card 3 · SEO defaults
    // ============================================================
    public function seo(): Response
    {
        $s = $this->settings();

        return Inertia::render('admin/settings/Seo', [
            'settings' => [
                'default_meta_title_template' => $s->default_meta_title_template,
                'default_og_image_path' => $s->default_og_image_path,
                'robots_index' => (bool) ($s->robots_index ?? true),
                'translations' => $this->translationsMap($s, ['default_meta_description']),
            ],
            'languages' => $this->presentLanguages(),
        ]);
    }

    public function updateSeo(UpdateSeoRequest $request, UpdateSiteSetting $action): RedirectResponse
    {
        return $this->save($request->validated(), $action, 'SEO defaults updated.');
    }

    // ============================================================
    // Card 4 · Legal pages
    // ============================================================
    public function legal(): Response
    {
        $s = $this->settings();

        return Inertia::render('admin/settings/Legal', [
            'settings' => [
                'privacy_page_id' => $s->privacy_page_id,
                'terms_page_id' => $s->terms_page_id,
                'imprint_page_id' => $s->imprint_page_id,
            ],
            'pageOptions' => $this->pageOptions(),
        ]);
    }

    public function updateLegal(UpdateLegalRequest $request, UpdateSiteSetting $action): RedirectResponse
    {
        return $this->save($request->validated(), $action, 'Legal pages updated.');
    }

    // ============================================================
    // Card 5 · Analytics & tracking
    // ============================================================
    public function analytics(): Response
    {
        $s = $this->settings();

        return Inertia::render('admin/settings/Analytics', [
            'settings' => [
                'google_analytics_id' => $s->google_analytics_id,
                'google_tag_manager_id' => $s->google_tag_manager_id,
                'meta_pixel_id' => $s->meta_pixel_id,
                'custom_head_code' => $s->custom_head_code,
                'cookie_consent_required' => (bool) ($s->cookie_consent_required ?? false),
            ],
        ]);
    }

    public function updateAnalytics(UpdateAnalyticsRequest $request, UpdateSiteSetting $action): RedirectResponse
    {
        return $this->save($request->validated(), $action, 'Analytics updated.');
    }

    // ============================================================
    // Card 8 · Maintenance
    // ============================================================
    public function maintenance(): Response
    {
        $s = $this->settings();

        return Inertia::render('admin/settings/Maintenance', [
            'settings' => [
                'maintenance_enabled' => (bool) ($s->maintenance_enabled ?? false),
                'maintenance_bypass_ips' => $s->maintenance_bypass_ips,
                'translations' => $this->translationsMap($s, ['maintenance_heading', 'maintenance_message']),
            ],
            'languages' => $this->presentLanguages(),
        ]);
    }

    public function updateMaintenance(UpdateMaintenanceRequest $request, UpdateSiteSetting $action): RedirectResponse
    {
        return $this->save($request->validated(), $action, 'Maintenance settings updated.');
    }

    // ============================================================
    // Card 9 · Layout toggles
    // ============================================================
    public function layout(): Response
    {
        $s = $this->settings();

        return Inertia::render('admin/settings/Layout', [
            'settings' => [
                'header_sticky' => (bool) ($s->header_sticky ?? true),
                'show_language_switcher' => (bool) ($s->show_language_switcher ?? true),
                'show_back_to_top' => (bool) ($s->show_back_to_top ?? true),
                'footer_copyright_auto_year' => (bool) ($s->footer_copyright_auto_year ?? true),
            ],
        ]);
    }

    public function updateLayout(UpdateLayoutRequest $request, UpdateSiteSetting $action): RedirectResponse
    {
        return $this->save($request->validated(), $action, 'Layout toggles updated.');
    }

    // ============================================================
    // Card 10 · Security
    // ============================================================
    public function security(): Response
    {
        $s = $this->settings();

        return Inertia::render('admin/settings/Security', [
            'settings' => [
                'require_2fa_for_admins' => (bool) ($s->require_2fa_for_admins ?? false),
                'session_lifetime_minutes' => $s->session_lifetime_minutes,
                'login_throttle_attempts' => $s->login_throttle_attempts,
            ],
        ]);
    }

    public function updateSecurity(UpdateSecurityRequest $request, UpdateSiteSetting $action): RedirectResponse
    {
        return $this->save($request->validated(), $action, 'Security settings updated.');
    }

    // ============================================================
    // Helpers
    // ============================================================
    private function settings(): SiteSetting
    {
        return SiteSetting::query()
            ->with('translations')
            ->firstOrNew(['id' => 1]);
    }

    /**
     * @param  array<string, mixed>  $data
     */
    private function save(array $data, UpdateSiteSetting $action, string $message): RedirectResponse
    {
        $action->handle($data);

        return back()->with('toast', ['type' => 'success', 'message' => $message]);
    }

    /**
     * Project the translations relation into a `{lang: {field: value, ...}}`
     * map so the Vue form can index by locale code without scanning an
     * array each time.
     *
     * @param  array<int, string>  $fields
     * @return array<string, array<string, mixed>>
     */
    private function translationsMap(SiteSetting $settings, array $fields): array
    {
        if (! $settings->relationLoaded('translations')) {
            return [];
        }

        return $settings->translations
            ->mapWithKeys(function (SiteSettingTranslation $t) use ($fields): array {
                $row = [];
                foreach ($fields as $field) {
                    $row[$field] = $t->{$field};
                }

                return [$t->lang => $row];
            })
            ->all();
    }

    /**
     * @return array<int, array{code: string, name: string, native_name: string, flag: ?string, is_default: bool}>
     */
    private function presentLanguages(): array
    {
        return Language::query()
            ->where('status', true)
            ->orderBy('sort_order')
            ->get()
            ->map(fn (Language $lang): array => [
                'code' => $lang->code,
                'name' => $lang->name,
                'native_name' => $lang->native_name,
                'flag' => $lang->flag,
                'is_default' => $lang->lang_is_default,
            ])
            ->values()
            ->all();
    }

    /**
     * @return array<int, array{id: int, title: string}>
     */
    private function pageOptions(): array
    {
        return Page::query()
            ->where('status', 'published')
            ->orderBy('title')
            ->get(['id', 'title'])
            ->map(fn (Page $p): array => ['id' => $p->id, 'title' => $p->title])
            ->all();
    }

    /**
     * @return array{common: array<int, string>, all: array<int, string>}
     */
    private function timezones(): array
    {
        return [
            'common' => [
                'Europe/Berlin', 'Europe/Vienna', 'Europe/Zurich',
                'Europe/London', 'Europe/Paris', 'Europe/Madrid',
                'UTC', 'America/New_York', 'America/Los_Angeles',
                'Asia/Dubai', 'Asia/Kolkata', 'Asia/Tokyo', 'Australia/Sydney',
            ],
            'all' => timezone_identifiers_list(),
        ];
    }
}
