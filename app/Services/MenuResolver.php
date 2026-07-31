<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Language;
use App\Models\Menu;
use App\Models\MenuItem;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

/**
 * Resolves a stored menu tree into a render-ready, per-locale structure:
 * each item already has its label and final URL for the requested language.
 *
 * Returns a JSON-serializable array (no Eloquent models) so the frontend can
 * consume it directly via Inertia props without further shaping.
 *
 * Cache key includes the locale because URLs and labels differ per language.
 */
final readonly class MenuResolver
{
    /**
     * Drop every locale-specific cache entry for the given menu key so the
     * next public page hit rebuilds from the database. Callers (menu-mutation
     * actions) hit this after writing so admin changes appear immediately.
     */
    public static function flush(string $key): void
    {
        $locales = Language::query()
            ->where('status', true)
            ->pluck('code')
            ->all();

        foreach ($locales as $locale) {
            Cache::forget(self::cacheKey($key, $locale));
        }
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function resolve(string $key, string $locale): array
    {
        return Cache::remember(
            self::cacheKey($key, $locale),
            3600,
            fn (): array => $this->build($key, $locale),
        );
    }

    private static function cacheKey(string $key, string $locale): string
    {
        return "menu:{$key}:{$locale}";
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function build(string $key, string $locale): array
    {
        $menu = Menu::query()
            ->where('key', $key)
            ->where('is_active', true)
            ->first();

        if ($menu === null) {
            return [];
        }

        $menu->load([
            'items' => fn ($q) => $q->where('is_active', true),
            'items.translations',
            'items.linkedPage.translations',
            'items.linkedCategory.translations',
        ]);

        $defaultLang = Language::query()
            ->where('lang_is_default', true)
            ->where('status', true)
            ->value('code') ?? 'de';

        $byParent = $menu->items->groupBy(fn (MenuItem $i): string => (string) ($i->parent_id ?? 'root'));

        return $this->treeFor($byParent, 'root', $locale, $defaultLang);
    }

    /**
     * @param  Collection<string, \Illuminate\Database\Eloquent\Collection<int, MenuItem>>  $byParent
     * @return array<int, array<string, mixed>>
     */
    private function treeFor($byParent, string $parentKey, string $locale, string $defaultLang): array
    {
        if (! $byParent->has($parentKey)) {
            return [];
        }

        return $byParent->get($parentKey)
            ->map(fn (MenuItem $item): array => [
                'id' => $item->id,
                'type' => $item->link_type,
                'label' => $this->resolveLabel($item, $locale, $defaultLang),
                'url' => $this->resolveUrl($item, $locale, $defaultLang),
                'open_in_new_tab' => $item->open_in_new_tab,
                'css_class' => $item->css_class,
                'children' => $this->treeFor($byParent, (string) $item->id, $locale, $defaultLang),
            ])
            ->values()
            ->all();
    }

    /**
     * Translation lookup order: requested locale → default locale → linked
     * entity title → bracketed item id (last-resort fallback so a missing
     * translation never renders an empty <a>).
     */
    private function resolveLabel(MenuItem $item, string $locale, string $defaultLang): string
    {
        $tr = $item->translations->firstWhere('lang', $locale)
            ?? $item->translations->firstWhere('lang', $defaultLang);
        if ($tr !== null && $tr->label !== '') {
            return $tr->label;
        }

        // Fall back to the linked entity's title for page/category items.
        if ($item->link_type === MenuItem::TYPE_PAGE && $item->linkedPage !== null) {
            return $item->linkedPage->title;
        }
        if ($item->link_type === MenuItem::TYPE_CATEGORY && $item->linkedCategory !== null) {
            return $item->linkedCategory->title;
        }

        return "[Item #{$item->id}]";
    }

    /**
     * Builds the final URL for a given locale. Returns null for items that
     * are pure dropdown triggers (category-type) — the frontend renders these
     * as labels with a chevron and no link.
     */
    private function resolveUrl(MenuItem $item, string $locale, string $defaultLang): ?string
    {
        $prefix = $locale === $defaultLang ? '' : "/{$locale}";

        return match ($item->link_type) {
            MenuItem::TYPE_HOME => $prefix === '' ? '/' : $prefix,
            MenuItem::TYPE_CATEGORY => null,
            MenuItem::TYPE_URL => $this->resolveCustomUrl($item, $locale, $defaultLang),
            MenuItem::TYPE_PAGE => $this->resolvePageUrl($item, $locale, $defaultLang, $prefix),
            default => null,
        };
    }

    private function resolveCustomUrl(MenuItem $item, string $locale, string $defaultLang): ?string
    {
        $tr = $item->translations->firstWhere('lang', $locale)
            ?? $item->translations->firstWhere('lang', $defaultLang);
        $url = $tr?->link_url;
        if ($url === null || $url === '') {
            return null;
        }

        if (preg_match('~^(?:[a-z][a-z0-9+.-]*:|//|/|#|\?)~i', $url) === 1) {
            return $url;
        }

        return '/'.$url;
    }

    private function resolvePageUrl(MenuItem $item, string $locale, string $defaultLang, string $prefix): ?string
    {
        if ($item->linkedPage === null) {
            return null;
        }

        $tr = $item->linkedPage->translations->firstWhere('lang', $locale)
            ?? $item->linkedPage->translations->firstWhere('lang', $defaultLang);
        $permalink = $tr?->permalink ?? $item->linkedPage->permalink;
        if ($permalink === null || $permalink === '') {
            return null;
        }

        return "{$prefix}/".mb_ltrim($permalink, '/');
    }
}
