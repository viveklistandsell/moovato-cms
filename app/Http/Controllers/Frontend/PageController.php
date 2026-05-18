<?php

declare(strict_types=1);

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Page;
use App\Models\PageTranslation;
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
            ->with(['translations', 'categories.translations', 'user:id,name'])
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

        $translation = PageTranslation::query()
            ->where('lang', $locale)
            ->where('permalink', $permalink)
            ->first();

        $page = $translation !== null
            ? Page::query()
                ->where('id', $translation->page_id)
                ->published()
                ->first()
            : Page::query()
                ->where('permalink', $permalink)
                ->published()
                ->first();

        if ($page === null) {
            throw new NotFoundHttpException();
        }

        $page->load(['translations', 'categories.translations', 'user:id,name']);

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
                'content' => $tr?->content ?? $page->content,
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
        ]);
    }
}
