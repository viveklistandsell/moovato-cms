<?php

declare(strict_types=1);

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Blog;
use App\Models\BlogCategory;
use App\Models\BlogTag;
use App\Models\BlogTranslation;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

final class BlogController extends Controller
{
    public function index(Request $request, string $locale): Response
    {
        $categorySlug = $request->query('category');
        $tagSlug = $request->query('tag');
        $perPage = 12;

        $query = Blog::query()
            ->published()
            ->with(['translations', 'categories.translations', 'tags.translations', 'user:id,name'])
            ->orderByDesc('is_sticky')
            ->orderByDesc('created_at');

        if (is_string($categorySlug) && $categorySlug !== '') {
            $query->whereHas('categories', function (Builder $q) use ($categorySlug, $locale): void {
                $q->where('blog_categories.permalink', $categorySlug)
                    ->orWhereHas('translations', function (Builder $t) use ($categorySlug, $locale): void {
                        $t->where('lang', $locale)->where('permalink', $categorySlug);
                    });
            });
        }

        if (is_string($tagSlug) && $tagSlug !== '') {
            $query->whereHas('tags', function (Builder $q) use ($tagSlug, $locale): void {
                $q->where('blog_tags.permalink', $tagSlug)
                    ->orWhereHas('translations', function (Builder $t) use ($tagSlug, $locale): void {
                        $t->where('lang', $locale)->where('permalink', $tagSlug);
                    });
            });
        }

        /** @var Blog|null $featured */
        $featured = (clone $query)
            ->orderByDesc('is_sticky')
            ->orderByDesc('is_featured')
            ->first();

        $paginator = $query
            ->when($featured !== null, fn (Builder $q) => $q->where('id', '!=', $featured->id))
            ->paginate($perPage)
            ->withQueryString();

        $posts = $paginator
            ->getCollection()
            ->map(fn (Blog $b): array => $this->presentCard($b, $locale))
            ->all();

        $categories = BlogCategory::query()
            ->where('status', 'published')
            ->with('translations')
            ->orderBy('sort_order')
            ->get()
            ->map(fn (BlogCategory $c): array => $this->presentCategoryChip($c, $locale))
            ->all();

        $tags = BlogTag::query()
            ->where('status', 'published')
            ->with('translations')
            ->orderBy('sort_order')
            ->get()
            ->map(fn (BlogTag $t): array => $this->presentTagChip($t, $locale))
            ->all();

        return Inertia::render('frontend/blog/Index', [
            'locale' => $locale,
            'featured' => $featured !== null
                ? $this->presentCard($featured, $locale, full: true)
                : null,
            'posts' => $posts,
            'categories' => $categories,
            'tags' => $tags,
            'activeCategory' => $categorySlug,
            'activeTag' => $tagSlug,
            'pagination' => [
                'current_page' => $paginator->currentPage(),
                'last_page' => $paginator->lastPage(),
                'total' => $paginator->total(),
                'links' => $paginator->linkCollection()->toArray(),
            ],
        ]);
    }

    public function show(string $locale, string $permalink): Response
    {
        $translation = BlogTranslation::query()
            ->where('lang', $locale)
            ->where('permalink', $permalink)
            ->first();

        $post = $translation !== null
            ? Blog::query()
                ->where('id', $translation->blog_id)
                ->published()
                ->first()
            : Blog::query()
                ->where('permalink', $permalink)
                ->published()
                ->first();

        if ($post === null) {
            throw new NotFoundHttpException();
        }

        $post->load(['translations', 'categories.translations', 'tags.translations', 'user:id,name']);
        $post->increment('view_count');

        $related = Blog::query()
            ->published()
            ->where('id', '!=', $post->id)
            ->whereHas('categories', function (Builder $q) use ($post): void {
                $q->whereIn('blog_categories.id', $post->categories->pluck('id'));
            })
            ->with(['translations', 'categories.translations'])
            ->orderByDesc('created_at')
            ->limit(3)
            ->get()
            ->map(fn (Blog $b): array => $this->presentCard($b, $locale))
            ->all();

        return Inertia::render('frontend/blog/Show', [
            'locale' => $locale,
            'post' => $this->presentDetail($post, $locale),
            'related' => $related,
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    private function presentCard(Blog $blog, string $locale, bool $full = false): array
    {
        $tr = $blog->translation($locale);

        return [
            'id' => $blog->id,
            'title' => $tr?->name ?? $blog->name,
            'permalink' => $tr?->permalink ?? $blog->permalink,
            'excerpt' => $tr?->short_description ?? $blog->short_description,
            'content' => $full ? ($tr?->content ?? $blog->content) : null,
            'image_url' => $blog->image !== null
                ? '/storage/'.mb_ltrim($blog->image, '/')
                : null,
            'author' => $blog->user?->name,
            'created_at' => $blog->created_at?->toIso8601String(),
            'reading_time' => $blog->reading_time,
            'is_sticky' => $blog->is_sticky,
            'is_featured' => $blog->is_featured,
            'categories' => $blog->relationLoaded('categories')
                ? $blog->categories->map(function ($c) use ($locale): array {
                    $ctr = $c->translation($locale);

                    return [
                        'id' => $c->id,
                        'name' => $ctr?->name ?? $c->name,
                        'permalink' => $ctr?->permalink ?? $c->permalink,
                    ];
                })->all()
                : [],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function presentDetail(Blog $blog, string $locale): array
    {
        $tr = $blog->translation($locale);
        $card = $this->presentCard($blog, $locale, full: true);

        return $card + [
            'meta_title' => $tr?->meta_title ?? null,
            'meta_description' => $tr?->meta_description ?? null,
            'view_count' => $blog->view_count,
            'tags' => $blog->relationLoaded('tags')
                ? $blog->tags->map(function ($t) use ($locale): array {
                    $ttr = $t->translation($locale);

                    return [
                        'id' => $t->id,
                        'name' => $ttr?->name ?? $t->name,
                        'permalink' => $ttr?->permalink ?? $t->permalink,
                    ];
                })->all()
                : [],
        ];
    }

    /**
     * @return array{id: int, name: string, permalink: string}
     */
    private function presentCategoryChip(BlogCategory $category, string $locale): array
    {
        $tr = $category->translation($locale);

        return [
            'id' => $category->id,
            'name' => $tr?->name ?? $category->name,
            'permalink' => $tr?->permalink ?? $category->permalink,
        ];
    }

    /**
     * @return array{id: int, name: string, permalink: string}
     */
    private function presentTagChip(BlogTag $tag, string $locale): array
    {
        $tr = $tag->translation($locale);

        return [
            'id' => $tag->id,
            'name' => $tr?->name ?? $tag->name,
            'permalink' => $tr?->permalink ?? $tag->permalink,
        ];
    }
}
