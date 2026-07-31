<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin\System;

use App\Http\Controllers\Controller;
use App\Models\Menu;
use App\Models\SiteSetting;
use App\Services\MenuResolver;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;
use Throwable;

/**
 * Single-screen cache management. Each action is a focused, idempotent
 * "flush this layer" — no destructive ops beyond cache removal. The Vue
 * page lists every clearable layer with a one-click button so an admin
 * doesn't need to hop into the terminal.
 */
final class CacheController extends Controller
{
    /**
     * The supported cache types. Each entry maps to a private method that
     * actually performs the flush; the key is what the Vue button posts.
     */
    private const TYPES = [
        'all',
        'application',
        'config',
        'route',
        'view',
        'event',
        'compiled',
        'menus',
        'site_settings',
        'permissions',
    ];

    public function index(): Response
    {
        return Inertia::render('admin/system/Cache', [
            'sections' => $this->presentSections(),
        ]);
    }

    public function clear(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'type' => ['required', 'string', Rule::in(self::TYPES)],
        ]);

        $type = $validated['type'];

        try {
            $message = match ($type) {
                'all' => $this->flushAll(),
                'application' => $this->flushApplication(),
                'config' => $this->flushConfig(),
                'route' => $this->flushRoute(),
                'view' => $this->flushView(),
                'event' => $this->flushEvent(),
                'compiled' => $this->flushCompiled(),
                'menus' => $this->flushMenus(),
                'site_settings' => $this->flushSiteSettings(),
                'permissions' => $this->flushPermissions(),
                default => (string) trans('admin.system.cache_toast.nothing'),
            };
        } catch (Throwable $e) {
            return back()->with('toast', [
                'type' => 'error',
                'message' => (string) trans('admin.system.cache_toast.failed', [
                    'type' => $type,
                    'error' => $e->getMessage(),
                ]),
            ]);
        }

        return back()->with('toast', [
            'type' => 'success',
            'message' => $message,
        ]);
    }

    // ============================================================
    // Section presenters → drive the cards on the Vue page
    // ============================================================

    /**
     * @return array<int, array<string, mixed>>
     */
    private function presentSections(): array
    {
        $sections = [
            ['type' => 'all', 'tone' => 'rose'],
            ['type' => 'application', 'tone' => 'sky'],
            ['type' => 'config', 'tone' => 'sky'],
            ['type' => 'route', 'tone' => 'sky'],
            ['type' => 'view', 'tone' => 'sky'],
            ['type' => 'event', 'tone' => 'sky'],
            ['type' => 'compiled', 'tone' => 'sky'],
            ['type' => 'menus', 'tone' => 'violet'],
            ['type' => 'site_settings', 'tone' => 'violet'],
            ['type' => 'permissions', 'tone' => 'violet'],
        ];

        return array_map(fn (array $s): array => [
            'type' => $s['type'],
            'title' => (string) trans("admin.system.cache_section.{$s['type']}.title"),
            'description' => (string) trans("admin.system.cache_section.{$s['type']}.description"),
            'tone' => $s['tone'],
        ], $sections);
    }

    // ============================================================
    // Flush implementations
    // ============================================================

    private function flushAll(): string
    {
        // Order matters: clear the framework layers first (which `cache:clear`
        // also exposes a key in the cache store for), then app-level keys.
        Artisan::call('optimize:clear');
        $this->flushMenus();
        $this->flushSiteSettings();
        $this->flushPermissions();

        return (string) trans('admin.system.cache_toast.all_cleared');
    }

    private function flushApplication(): string
    {
        Cache::flush();

        return (string) trans('admin.system.cache_toast.application_cleared');
    }

    private function flushConfig(): string
    {
        Artisan::call('config:clear');

        return (string) trans('admin.system.cache_toast.config_cleared');
    }

    private function flushRoute(): string
    {
        Artisan::call('route:clear');

        return (string) trans('admin.system.cache_toast.route_cleared');
    }

    private function flushView(): string
    {
        Artisan::call('view:clear');

        return (string) trans('admin.system.cache_toast.view_cleared');
    }

    private function flushEvent(): string
    {
        Artisan::call('event:clear');

        return (string) trans('admin.system.cache_toast.event_cleared');
    }

    private function flushCompiled(): string
    {
        Artisan::call('optimize:clear');

        return (string) trans('admin.system.cache_toast.compiled_cleared');
    }

    private function flushMenus(): string
    {
        $keys = [Menu::HEADER, Menu::FOOTER];
        foreach ($keys as $key) {
            MenuResolver::flush($key);
        }

        return (string) trans('admin.system.cache_toast.menus_cleared', [
            'keys' => implode(', ', $keys),
        ]);
    }

    private function flushSiteSettings(): string
    {
        SiteSetting::flush();

        return (string) trans('admin.system.cache_toast.site_settings_cleared');
    }

    private function flushPermissions(): string
    {
        Artisan::call('permission:cache-reset');

        return (string) trans('admin.system.cache_toast.permissions_cleared');
    }
}
