<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Models\Language;
use App\Models\Menu;
use App\Models\Page;
use App\Models\SiteSetting;
use App\Models\User;
use App\Services\MenuResolver;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cache;
use Inertia\Middleware;

final class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that's loaded on the first page visit.
     *
     * @see https://inertiajs.com/server-side-setup#root-template
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determines the current asset version.
     *
     * @see https://inertiajs.com/asset-versioning
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        return [
            ...parent::share($request),
            'name' => config('app.name'),
            'auth' => [
                'user' => $this->presentAuthUser($request->user()),
            ],
            'locale' => App::getLocale(),
            'defaultLocale' => $this->defaultLocaleCode(),
            'sidebarOpen' => ! $request->hasCookie('sidebar_state') || $request->cookie('sidebar_state') === 'true',
            'headerMenu' => fn (): array => app(MenuResolver::class)
                ->resolve(Menu::HEADER, App::getLocale()),
            'footerMenu' => fn (): array => app(MenuResolver::class)
                ->resolve(Menu::FOOTER, App::getLocale()),
            'siteSettings' => fn (): array => $this->presentSiteSettings(),
            'siteLayout' => fn (): array => $this->presentLayout(),
            'siteLegal' => fn (): array => $this->presentLegalLinks(),
            'flash' => fn (): array => [
                'success' => $request->session()->get('success'),
                'error' => $request->session()->get('error'),
                'test_mail_success' => $request->session()->get('test_mail_success'),
                'test_mail_error' => $request->session()->get('test_mail_error'),
                'toast' => $request->session()->get('toast'),
                'importResult' => $request->session()->get('importResult'),
            ],
            // Active languages list — drives the admin header
            // language switcher. Cached for 5 minutes since languages
            // change rarely. Only resolved on admin / dashboard
            // requests to keep public payloads small.
            'adminLanguages' => fn (): array => $this->adminLanguages(),
            'adminLocale' => $request->user()?->admin_locale,
            // Admin UI translation dict — only resolved on admin /
            // dashboard requests so the public payload stays small.
            // Re-resolved on every request (cheap — Laravel caches
            // the loaded lang file in memory) so a locale change
            // is reflected after the next navigation.
            'translations' => fn (): array => $this->isAdminRoute($request)
                ? (array) trans('admin')
                : [],
        ];
    }

    private function isAdminRoute(Request $request): bool
    {
        return $request->is('admin/*')
            || $request->is('admin')
            || $request->is('dashboard')
            || $request->is('settings')
            || $request->is('settings/*');
    }

    /**
     * @return array<int, array{code: string, native_name: string, flag: ?string}>
     */
    private function adminLanguages(): array
    {
        return Cache::remember(
            'admin.languages.switcher',
            300,
            fn (): array => Language::query()
                ->where('status', true)
                ->orderBy('sort_order')
                ->get(['code', 'native_name', 'flag'])
                ->map(fn (Language $l): array => [
                    'code' => (string) $l->code,
                    'native_name' => (string) $l->native_name,
                    'flag' => $l->flag,
                ])
                ->all(),
        );
    }

    /**
     * @return array<string, ?string>
     */
    private function presentSiteSettings(): array
    {
        $s = SiteSetting::current();
        $tr = $s->translation(App::getLocale());

        return [
            'site_name' => $s->site_name,
            'site_tagline' => $tr?->site_tagline,
            'logo_light_url' => $this->storageUrl($s->logo_light_path),
            'logo_dark_url' => $this->storageUrl($s->logo_dark_path),
            'theme_color' => $s->theme_color,
            'about_text' => $tr?->about_text,
            'address' => $s->address,
            'phone' => $s->phone,
            'email' => $s->email,
            'whatsapp' => $s->whatsapp,
            'facebook_url' => $s->facebook_url,
            'twitter_url' => $s->twitter_url,
            'linkedin_url' => $s->linkedin_url,
            'instagram_url' => $s->instagram_url,
        ];
    }

    private function storageUrl(?string $path): ?string
    {
        if ($path === null || $path === '') {
            return null;
        }

        return '/storage/'.mb_ltrim($path, '/');
    }

    /**
     * Layout toggles consumed by SiteHeader / SiteFooter / FrontendLayout.
     * Defaults match the historic public-site behaviour so an upgrade
     * doesn't suddenly hide UI elements.
     *
     * @return array<string, bool>
     */
    private function presentLayout(): array
    {
        $s = SiteSetting::current();

        return [
            'header_sticky' => (bool) ($s->header_sticky ?? true),
            'show_language_switcher' => (bool) ($s->show_language_switcher ?? true),
            'show_back_to_top' => (bool) ($s->show_back_to_top ?? true),
            'footer_copyright_auto_year' => (bool) ($s->footer_copyright_auto_year ?? true),
            'cookie_consent_required' => (bool) ($s->cookie_consent_required ?? false),
        ];
    }

    /**
     * Resolve the privacy / terms / imprint page picks into per-locale URLs
     * the footer can link to. Returns null entries when the admin hasn't
     * chosen a page yet so the frontend can hide / show the link as
     * appropriate.
     *
     * @return array<string, ?string>
     */
    private function presentLegalLinks(): array
    {
        $s = SiteSetting::current();
        $locale = App::getLocale();
        $defaultLocale = $this->defaultLocaleCode();

        return [
            'privacy_url' => $this->pagePermalinkUrl($s->privacy_page_id, $locale, $defaultLocale),
            'terms_url' => $this->pagePermalinkUrl($s->terms_page_id, $locale, $defaultLocale),
            'imprint_url' => $this->pagePermalinkUrl($s->imprint_page_id, $locale, $defaultLocale),
        ];
    }

    /**
     * Build the public URL for the given page id. Per-locale permalink wins;
     * falls back to the default-locale permalink (then the page's canonical
     * permalink) so a half-translated page still has somewhere to link.
     */
    private function pagePermalinkUrl(?int $pageId, string $locale, string $defaultLocale): ?string
    {
        if ($pageId === null) {
            return null;
        }

        $page = Page::query()
            ->with('translations:id,page_id,lang,permalink')
            ->find($pageId, ['id', 'permalink']);

        if ($page === null) {
            return null;
        }

        $translation = $page->translations->firstWhere('lang', $locale)
            ?? $page->translations->firstWhere('lang', $defaultLocale);
        $permalink = $translation?->permalink ?? $page->permalink;

        if ($permalink === null || $permalink === '') {
            return null;
        }

        $prefix = $locale === $defaultLocale ? '' : "/{$locale}";

        return $prefix.'/'.mb_ltrim($permalink, '/');
    }

    /**
     * Shape the auth user for Inertia. We trim the raw User model to the
     * fields the frontend actually consumes:
     *
     *   - avatar          → AppHeader avatar fallback
     *   - avatar_url      → UserInfo (bottom sidebar widget) avatar src
     *   - role_display_name → UserInfo role line (e.g. "Super Admin")
     *   - email_verified_at → Profile.vue "verify your email" banner
     *
     * @return array<string, mixed>|null
     */
    private function presentAuthUser(?User $user): ?array
    {
        if ($user === null) {
            return null;
        }

        $user->loadMissing('roles:id,name,display_name');
        $role = $user->roles->first();

        return [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'email_verified_at' => $user->email_verified_at?->toIso8601String(),
            'avatar' => $user->avatar,
            'avatar_url' => $user->avatar !== null ? '/storage/'.mb_ltrim($user->avatar, '/') : null,
            'role_display_name' => $role?->display_name ?? $role?->name,
        ];
    }

    private function defaultLocaleCode(): string
    {
        return Cache::remember(
            'locales.default.code',
            300,
            fn () => Language::query()
                ->where('lang_is_default', true)
                ->where('status', true)
                ->value('code') ?? config('app.locale', 'de'),
        );
    }
}
