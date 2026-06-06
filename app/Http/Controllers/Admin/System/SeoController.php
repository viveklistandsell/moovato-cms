<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin\System;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Frontend\RobotsController;
use App\Http\Controllers\Frontend\SitemapController;
use App\Models\SiteSetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response as HttpResponse;
use Inertia\Inertia;
use Inertia\Response;

/**
 * Admin preview pages for the SEO endpoints. Sitemap and robots each get
 * their own page so the sidebar can deep-link directly to the one the
 * operator wants — combined screens were nice for review but slow to
 * navigate when you just want to flush the sitemap cache.
 */
final class SeoController extends Controller
{
    public function sitemap(): Response
    {
        return Inertia::render('admin/system/Sitemap', [
            'sitemap' => [
                'url' => url('/sitemap.xml'),
                'counts' => SitemapController::counts(),
            ],
        ]);
    }

    public function robots(): Response
    {
        $settings = SiteSetting::current();
        // Capture the actual current response body so the admin sees the
        // exact bytes search engines will see (no second source of truth).
        $body = app(RobotsController::class)()->getContent();

        return Inertia::render('admin/system/Robots', [
            'robots' => [
                'url' => url('/robots.txt'),
                'body' => $body,
                'allowsIndexing' => (bool) ($settings->robots_index ?? true),
            ],
        ]);
    }

    /**
     * Drop the cached XML so the next crawler hit rebuilds. Useful after
     * bulk-publishing pages / posts. We don't preemptively rebuild — the
     * next request does that lazily.
     */
    public function flushSitemap(): RedirectResponse
    {
        SitemapController::flush();

        return back()->with('toast', [
            'type' => 'success',
            'message' => 'Sitemap cache cleared. Next request will rebuild it.',
        ]);
    }

    /**
     * Stream the current sitemap.xml body as a file download so admins
     * can grab a copy for offline review or to attach to a submission.
     * Reuses the public SitemapController so the bytes match exactly.
     */
    public function downloadSitemap(): HttpResponse
    {
        $xml = app(SitemapController::class)()->getContent();
        $filename = 'sitemap-'.now()->format('Y-m-d-Hi').'.xml';

        return response($xml, 200, [
            'Content-Type' => 'application/xml; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="'.$filename.'"',
            'Content-Length' => (string) mb_strlen($xml, '8bit'),
        ]);
    }
}
