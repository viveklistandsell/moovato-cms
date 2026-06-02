<?php

declare(strict_types=1);

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Blog;
use App\Models\Page;
use App\Models\PageTranslation;
use App\Models\PageWidget;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Inertia\Inertia;
use Inertia\Response;
use Laravel\Fortify\Features;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

final class PageController extends Controller
{
    public function home(): Response
    {
        $page = Page::query()
            ->published()
            ->where('is_home', true)
            ->with(['translations', 'categories.translations', 'user:id,name', 'widgets.translations'])
            ->first();

        if ($page === null) {
            // Falls back to the default Welcome view when no homepage is set.
            return Inertia::render('Welcome', [
                'canRegister' => Features::enabled(Features::registration()),
            ]);
        }

        return $this->renderPage($page);
    }

    public function show(Request $request): Response
    {
        $locale = App::getLocale();
        $permalink = (string) $request->route('permalink');

        // Permalink identifies the page (a translation in ANY language is
        // enough to find it); the locale prefix decides which translation
        // gets rendered. We 404 only when the active locale has no
        // translation for that page — so /en/<slug> is a 404 if the page
        // hasn't been translated to English, regardless of whether <slug>
        // is the German permalink.
        $anyTranslation = PageTranslation::query()
            ->where('permalink', $permalink)
            ->first();

        if ($anyTranslation === null) {
            throw new NotFoundHttpException();
        }

        $page = Page::query()
            ->where('id', $anyTranslation->page_id)
            ->published()
            ->first();

        if ($page === null) {
            throw new NotFoundHttpException();
        }

        $hasActiveTranslation = PageTranslation::query()
            ->where('page_id', $page->id)
            ->where('lang', $locale)
            ->exists();

        if (! $hasActiveTranslation) {
            throw new NotFoundHttpException();
        }

        $page->load(['translations', 'categories.translations', 'user:id,name', 'widgets.translations']);

        return $this->renderPage($page);
    }

    private function renderPage(Page $page): Response
    {
        $locale = App::getLocale();
        $tr = $page->translation($locale);

        return Inertia::render('frontend/page/Index', [
            'locale' => $locale,
            'page' => [
                'id' => $page->id,
                'title' => $tr?->title ?? $page->title,
                'permalink' => $tr?->permalink ?? $page->permalink,
                'image_url' => $page->image !== null ? '/storage/'.mb_ltrim($page->image, '/') : null,
                'template' => $page->template,
                'is_home' => $page->is_home,
                'author' => $page->user?->name,
                'created_at' => $page->created_at?->toIso8601String(),
                'categories' => $page->categories->map(function ($c) use ($locale): array {
                    $ctr = $c->translation($locale);

                    return [
                        'id' => $c->id,
                        'title' => $ctr?->title ?? $c->title,
                        'permalink' => $ctr?->permalink ?? $c->permalink,
                    ];
                })->all(),
            ],
            'widgets' => $this->presentWidgetsForLocale($page, $locale),
        ]);
    }

    /**
     * Flatten active widgets into a render-ready payload keyed to the current
     * locale. Falls back to the first available translation if a widget has
     * no row for the requested lang. Visibility and css_class travel with each
     * widget so the frontend wrapper can hide/style them per breakpoint.
     *
     * @return array<int, array{type: string, settings: array<string, mixed>, data: array<string, mixed>, visibility: array{desktop: bool, tablet: bool, mobile: bool}, css_class: string}>
     */
    private function presentWidgetsForLocale(Page $page, string $locale): array
    {
        if (! $page->relationLoaded('widgets')) {
            return [];
        }

        return $page->widgets
            ->where('is_active', true)
            ->sortBy('position')
            ->map(function (PageWidget $w) use ($locale): array {
                $translation = $w->translations->firstWhere('lang', $locale)
                    ?? $w->translations->first();

                $visibility = $w->visibility ?? [];

                $settings = $w->settings ?? [];

                if ($w->type === 'blog') {
                    $settings['posts'] = $this->resolveBlogPosts($settings, $locale);
                }

                return [
                    'type' => $w->type,
                    'settings' => $settings,
                    'data' => $translation?->data ?? [],
                    'visibility' => [
                        'desktop' => (bool) ($visibility['desktop'] ?? true),
                        'tablet' => (bool) ($visibility['tablet'] ?? true),
                        'mobile' => (bool) ($visibility['mobile'] ?? true),
                    ],
                    'css_class' => $w->css_class ?? '',
                ];
            })
            ->values()
            ->all();
    }

    /**
     * Resolve the latest published posts for a Blog widget, honouring its
     * count and optional category-permalink filter. Injected into the widget's
     * settings at render time — never persisted.
     *
     * @param  array<string, mixed>  $settings
     * @return array<int, array{title: string, href: string, excerpt: ?string, image_url: ?string, created_at: ?string, reading_time: ?int, category: ?string}>
     */
    private function resolveBlogPosts(array $settings, string $locale): array
    {
        $count = (int) ($settings['count'] ?? 3);
        $count = max(1, min(12, $count));
        $categorySlug = is_string($settings['category'] ?? null) ? $settings['category'] : '';

        $query = Blog::query()
            ->published()
            ->with(['translations', 'categories.translations'])
            ->orderByDesc('is_sticky')
            ->orderByDesc('created_at');

        if ($categorySlug !== '') {
            $query->whereHas('categories', function (Builder $q) use ($categorySlug, $locale): void {
                $q->where('blog_categories.permalink', $categorySlug)
                    ->orWhereHas('translations', function (Builder $t) use ($categorySlug, $locale): void {
                        $t->where('lang', $locale)->where('permalink', $categorySlug);
                    });
            });
        }

        return $query
            ->limit($count)
            ->get()
            ->map(function (Blog $blog) use ($locale): array {
                $tr = $blog->translation($locale);
                $permalink = $tr?->permalink ?? $blog->permalink;
                $category = $blog->categories->first();
                $ctr = $category?->translation($locale);

                return [
                    'title' => $tr?->name ?? $blog->name,
                    'href' => $this->localizedPath($locale, '/blog/'.$permalink),
                    'excerpt' => $tr?->short_description ?? $blog->short_description,
                    'image_url' => $blog->image !== null
                        ? '/storage/'.mb_ltrim($blog->image, '/')
                        : null,
                    'created_at' => $blog->created_at?->toIso8601String(),
                    'reading_time' => $blog->reading_time,
                    'category' => $category !== null ? ($ctr?->name ?? $category->name) : null,
                ];
            })
            ->all();
    }

    /**
     * Mirror of the frontend localizedUrl() helper: the default locale lives at
     * the root, other locales keep their /<locale>/ prefix.
     */
    private function localizedPath(string $locale, string $path): string
    {
        $normalized = str_starts_with($path, '/') ? $path : '/'.$path;

        if ($locale === config('app.locale')) {
            return $normalized;
        }

        return '/'.$locale.$normalized;
    }
}
