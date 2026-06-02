<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Models\Language;
use App\Models\Menu;
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
        ];
    }

    /**
     * @return array<string, ?string>
     */
    private function presentSiteSettings(): array
    {
        $s = SiteSetting::current();
        $tr = $s->translation(App::getLocale());

        return [
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
