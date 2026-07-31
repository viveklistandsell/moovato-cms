<?php

declare(strict_types=1);

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Blog;
use App\Models\BlogTranslation;
use App\Models\Language;
use App\Models\Page;
use App\Models\PageTranslation;
use Carbon\CarbonInterface;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Cache;

/**
 * Dynamic sitemap.xml — lists the home page, every published Page, and
 * every published blog post for every active locale. Output is cached for
 * 30 minutes so a crawler hitting it doesn't keep round-tripping the DB.
 *
 * The cache key is independent of the request so the same XML is served
 * to every visitor; admins can flush it from System → Cache management
 * via the "Application cache" button.
 */
final class SitemapController extends Controller
{
    private const CACHE_KEY = 'sitemap:xml';

    private const CACHE_TTL_SECONDS = 1800;

    public function __invoke(): Response
    {
        $xml = Cache::remember(self::CACHE_KEY, self::CACHE_TTL_SECONDS, fn (): string => $this->build());

        return response($xml, 200, [
            'Content-Type' => 'application/xml; charset=UTF-8',
        ]);
    }

    /**
     * Bust the cache so the next request rebuilds. Wired up by the admin
     * Sitemap page's regenerate button + by content actions that publish
     * a page (future hook — not currently called by anything else).
     */
    public static function flush(): void
    {
        Cache::forget(self::CACHE_KEY);
    }

    /**
     * Aggregate count of all sitemap entries — used by the admin Sitemap
     * preview page so the operator can see at a glance what's included.
     *
     * @return array{home: int, pages: int, blog_posts: int, blog_index: int, locales: int}
     */
    public static function counts(): array
    {
        $locales = self::activeLocales();
        $localeCount = count($locales);

        return [
            'home' => $localeCount,
            'pages' => Page::query()->where('status', 'published')->count() * $localeCount,
            'blog_posts' => Blog::query()->where('status', 'published')->count() * $localeCount,
            'blog_index' => $localeCount,
            'locales' => $localeCount,
        ];
    }

    /**
     * @return array<int, string>
     */
    private static function activeLocales(): array
    {
        return Cache::remember(
            'sitemap:locales',
            300,
            fn () => Language::query()
                ->where('status', true)
                ->orderBy('sort_order')
                ->pluck('code')
                ->all(),
        );
    }

    private static function defaultLocale(): string
    {
        return Cache::remember(
            'locales.default.code',
            300,
            fn () => Language::query()
                ->where('lang_is_default', true)
                ->where('status', true)
                ->value('code') ?? (string) config('app.locale', 'de'),
        );
    }

    private function build(): string
    {
        $locales = self::activeLocales();
        $defaultLocale = self::defaultLocale();
        $base = mb_rtrim((string) config('app.url'), '/');

        $entries = [];

        // Home page per locale.
        foreach ($locales as $locale) {
            $entries[] = $this->entry(
                $this->absoluteUrl($base, $locale, $defaultLocale, '/'),
                now(),
                'daily',
                '1.0',
            );
        }

        // Blog index per locale.
        foreach ($locales as $locale) {
            $entries[] = $this->entry(
                $this->absoluteUrl($base, $locale, $defaultLocale, '/blog'),
                now(),
                'daily',
                '0.8',
            );
        }

        // Published pages × locales — translation permalink wins, falls
        // back to the canonical permalink so a half-translated page still
        // appears in every locale's sitemap.
        $pages = Page::query()
            ->where('status', 'published')
            ->with(['translations:id,page_id,lang,permalink'])
            ->get(['id', 'permalink', 'updated_at']);

        foreach ($pages as $page) {
            foreach ($locales as $locale) {
                $permalink = $this->resolvePermalink(
                    $page->translations->first(fn (PageTranslation $t): bool => $t->lang === $locale),
                    $page->translations->first(fn (PageTranslation $t): bool => $t->lang === $defaultLocale),
                    $page->permalink,
                );
                if ($permalink === null) {
                    continue;
                }
                $entries[] = $this->entry(
                    $this->absoluteUrl($base, $locale, $defaultLocale, '/'.$permalink),
                    $page->updated_at ?? now(),
                    'weekly',
                    '0.7',
                );
            }
        }

        // Published blog posts × locales.
        $posts = Blog::query()
            ->where('status', 'published')
            ->with(['translations:id,blog_id,lang,permalink'])
            ->get(['id', 'permalink', 'updated_at']);

        foreach ($posts as $post) {
            foreach ($locales as $locale) {
                $permalink = $this->resolvePermalink(
                    $post->translations->first(fn (BlogTranslation $t): bool => $t->lang === $locale),
                    $post->translations->first(fn (BlogTranslation $t): bool => $t->lang === $defaultLocale),
                    $post->permalink,
                );
                if ($permalink === null) {
                    continue;
                }
                $entries[] = $this->entry(
                    $this->absoluteUrl($base, $locale, $defaultLocale, '/blog/'.$permalink),
                    $post->updated_at ?? now(),
                    'weekly',
                    '0.6',
                );
            }
        }

        return "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n"
            ."<urlset xmlns=\"http://www.sitemaps.org/schemas/sitemap/0.9\">\n"
            .implode('', $entries)
            ."</urlset>\n";
    }

    private function entry(string $url, CarbonInterface $lastmod, string $changefreq, string $priority): string
    {
        return "  <url>\n"
            .'    <loc>'.htmlspecialchars($url, ENT_XML1).'</loc>'."\n"
            .'    <lastmod>'.$lastmod->toIso8601String().'</lastmod>'."\n"
            ."    <changefreq>{$changefreq}</changefreq>\n"
            ."    <priority>{$priority}</priority>\n"
            ."  </url>\n";
    }

    private function absoluteUrl(string $base, string $locale, string $defaultLocale, string $path): string
    {
        $prefix = $locale === $defaultLocale ? '' : "/{$locale}";
        $path = '/'.mb_ltrim($path, '/');

        return $base.$prefix.($path === '/' ? '' : $path);
    }

    /**
     * @param  PageTranslation|BlogTranslation|null  $localeTranslation
     * @param  PageTranslation|BlogTranslation|null  $defaultTranslation
     */
    private function resolvePermalink(mixed $localeTranslation, mixed $defaultTranslation, ?string $fallback): ?string
    {
        $permalink = $localeTranslation?->permalink
            ?? $defaultTranslation?->permalink
            ?? $fallback;

        if ($permalink === null || $permalink === '') {
            return null;
        }

        return mb_ltrim((string) $permalink, '/');
    }
}
