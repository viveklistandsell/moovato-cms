<?php

declare(strict_types=1);

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\SiteSetting;
use Illuminate\Http\Response;

/**
 * Dynamic robots.txt — driven by Site Settings → SEO Defaults.
 *
 * When the admin turns "Allow search indexing" off, the entire site is
 * disallowed regardless of the default disallow paths (so a staging
 * snapshot can be hidden with a single toggle).
 */
final class RobotsController extends Controller
{
    public function __invoke(): Response
    {
        $settings = SiteSetting::current();
        $allowIndexing = (bool) ($settings->robots_index ?? true);
        $sitemapUrl = url('/sitemap.xml');

        $body = $allowIndexing
            ? $this->indexableBody($sitemapUrl)
            : $this->blockAllBody($sitemapUrl);

        return response($body, 200, [
            'Content-Type' => 'text/plain; charset=UTF-8',
        ]);
    }

    /**
     * Public-friendly robots.txt: bots may crawl, except admin / storage
     * paths that have no SEO value. The sitemap reference is always
     * included so crawlers can discover it without hunting.
     */
    private function indexableBody(string $sitemapUrl): string
    {
        return implode("\n", [
            'User-agent: *',
            'Disallow: /admin/',
            'Disallow: /dashboard',
            'Disallow: /login',
            'Disallow: /logout',
            'Disallow: /forgot-password',
            'Disallow: /reset-password',
            'Disallow: /two-factor-challenge',
            'Disallow: /settings/',
            'Disallow: /_boost/',
            'Disallow: /storage/private/',
            'Disallow: /build/',
            '',
            "Sitemap: {$sitemapUrl}",
            '',
        ]);
    }

    /**
     * Staging / pre-launch posture: block everything. The sitemap line is
     * intentionally left off — exposing a sitemap on a noindex site can
     * still leak content to Bing/etc. archives.
     */
    private function blockAllBody(string $sitemapUrl): string
    {
        return implode("\n", [
            'User-agent: *',
            'Disallow: /',
            '',
        ]);
    }
}
