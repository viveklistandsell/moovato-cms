<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Blog;
use App\Models\BlogTranslation;
use App\Models\Language;
use App\Models\MediaFile;
use App\Models\MediaFolder;
use App\Models\Page;
use App\Models\PageCategory;
use App\Models\PageTranslation;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Inertia\Inertia;
use Inertia\Response;

/**
 * Builds the admin dashboard. Permission-gated cards are wrapped in optional()
 * so the frontend can decide whether to render them and the query never runs
 * for an admin who can't see the result.
 */
final class DashboardController extends Controller
{
    public function __invoke(Request $request): Response
    {
        $user = $request->user();
        $now = Carbon::now();
        $weekAgo = $now->copy()->subDays(7);
        $monthAgo = $now->copy()->subDays(30);

        return Inertia::render('Dashboard', [
            'welcome' => [
                'name' => $user?->name,
                'avatar_url' => $user?->avatar !== null
                    ? '/storage/'.mb_ltrim($user->avatar, '/')
                    : null,
                'role_display_name' => $this->roleDisplayName($user),
                'member_since' => $user?->created_at?->toIso8601String(),
            ],
            'kpis' => fn (): array => $this->kpis($weekAgo),
            'chart' => fn (): array => $this->contentChart($now),
            'topPosts' => fn (): array => $this->topPosts(),
            'recentPages' => fn (): array => $this->recentPages(),
            'recentPosts' => fn (): array => $this->recentPosts(),
            'recentUsers' => Inertia::optional(
                fn (): array => $this->recentUsers(),
            ),
            'languageCoverage' => fn (): array => $this->languageCoverage(),
            'topCategories' => fn (): array => $this->topCategories(),
            'attention' => fn (): array => $this->attention($monthAgo),
            'permissions' => [
                'users_view' => $user?->can('users.view') ?? false,
                'pages_create' => $user?->can('pages.create') ?? false,
                'posts_create' => $user?->can('blog.create') ?? false,
                'users_create' => $user?->can('users.create') ?? false,
            ],
        ]);
    }

    private function roleDisplayName(?User $user): ?string
    {
        if ($user === null) {
            return null;
        }

        $user->loadMissing('roles:id,name,display_name');
        $role = $user->roles->first();

        return $role?->display_name ?? $role?->name;
    }

    /**
     * @return array{
     *   pages: array{total: int, published: int, draft: int, inactive: int},
     *   posts: array{total: int, published: int, draft: int, inactive: int, views: int},
     *   users: array{total: int, verified: int, unverified: int, new_this_week: int},
     *   media: array{files: int, folders: int, bytes: int}
     * }
     */
    private function kpis(Carbon $weekAgo): array
    {
        $pageCounts = Page::query()
            ->selectRaw('status, count(*) as c')
            ->groupBy('status')
            ->pluck('c', 'status')
            ->all();

        $postCounts = Blog::query()
            ->selectRaw('status, count(*) as c')
            ->groupBy('status')
            ->pluck('c', 'status')
            ->all();

        return [
            'pages' => [
                'total' => array_sum($pageCounts),
                'published' => (int) ($pageCounts['published'] ?? 0),
                'draft' => (int) ($pageCounts['draft'] ?? 0),
                'inactive' => (int) ($pageCounts['inactive'] ?? 0),
            ],
            'posts' => [
                'total' => array_sum($postCounts),
                'published' => (int) ($postCounts['published'] ?? 0),
                'draft' => (int) ($postCounts['draft'] ?? 0),
                'inactive' => (int) ($postCounts['inactive'] ?? 0),
                'views' => (int) Blog::query()->sum('view_count'),
            ],
            'users' => [
                'total' => User::query()->count(),
                'verified' => User::query()->whereNotNull('email_verified_at')->count(),
                'unverified' => User::query()->whereNull('email_verified_at')->count(),
                'new_this_week' => User::query()->where('created_at', '>=', $weekAgo)->count(),
            ],
            'media' => [
                'files' => MediaFile::query()->count(),
                'folders' => MediaFolder::query()->count(),
                'bytes' => (int) MediaFile::query()->sum('size'),
            ],
        ];
    }

    /**
     * Last 12 ISO weeks of created pages/posts, bucketed for the stacked bar
     * chart. We pull all rows from the cutoff onwards and bucket in PHP so the
     * shape is identical across SQLite / MySQL / Postgres without driver-specific
     * date functions.
     *
     * @return array{
     *   weeks: array<int, string>,
     *   pages: array<int, int>,
     *   posts: array<int, int>
     * }
     */
    private function contentChart(Carbon $now): array
    {
        $cutoff = $now->copy()->startOfWeek()->subWeeks(11);

        $weekLabels = [];
        $pagesByWeek = [];
        $postsByWeek = [];
        for ($i = 0; $i < 12; $i++) {
            $start = $cutoff->copy()->addWeeks($i);
            $key = $start->isoFormat('GGGG-[W]WW');
            $weekLabels[] = $start->isoFormat('DD MMM');
            $pagesByWeek[$key] = 0;
            $postsByWeek[$key] = 0;
        }

        Page::query()
            ->where('created_at', '>=', $cutoff)
            ->get(['created_at'])
            ->each(function (Page $p) use (&$pagesByWeek): void {
                $key = $p->created_at?->isoFormat('GGGG-[W]WW');
                if ($key !== null && isset($pagesByWeek[$key])) {
                    $pagesByWeek[$key]++;
                }
            });

        Blog::query()
            ->where('created_at', '>=', $cutoff)
            ->get(['created_at'])
            ->each(function (Blog $b) use (&$postsByWeek): void {
                $key = $b->created_at?->isoFormat('GGGG-[W]WW');
                if ($key !== null && isset($postsByWeek[$key])) {
                    $postsByWeek[$key]++;
                }
            });

        return [
            'weeks' => $weekLabels,
            'pages' => array_values($pagesByWeek),
            'posts' => array_values($postsByWeek),
        ];
    }

    /**
     * @return array<int, array{id: int, title: string, permalink: string, views: int, author: ?string}>
     */
    private function topPosts(): array
    {
        return Blog::query()
            ->with('user:id,name')
            ->orderByDesc('view_count')
            ->orderByDesc('created_at')
            ->limit(5)
            ->get(['id', 'name', 'permalink', 'view_count', 'user_id'])
            ->map(fn (Blog $b): array => [
                'id' => $b->id,
                'title' => $b->name,
                'permalink' => $b->permalink,
                'views' => (int) $b->view_count,
                'author' => $b->user?->name,
            ])
            ->all();
    }

    /**
     * @return array<int, array{id: int, title: string, permalink: string, status: string, author: ?string, created_at: ?string}>
     */
    private function recentPages(): array
    {
        return Page::query()
            ->with('user:id,name')
            ->orderByDesc('created_at')
            ->limit(5)
            ->get(['id', 'title', 'permalink', 'status', 'user_id', 'created_at'])
            ->map(fn (Page $p): array => [
                'id' => $p->id,
                'title' => $p->title,
                'permalink' => $p->permalink,
                'status' => $p->status,
                'author' => $p->user?->name,
                'created_at' => $p->created_at?->toIso8601String(),
            ])
            ->all();
    }

    /**
     * @return array<int, array{id: int, title: string, permalink: string, status: string, views: int, created_at: ?string}>
     */
    private function recentPosts(): array
    {
        return Blog::query()
            ->orderByDesc('created_at')
            ->limit(5)
            ->get(['id', 'name', 'permalink', 'status', 'view_count', 'created_at'])
            ->map(fn (Blog $b): array => [
                'id' => $b->id,
                'title' => $b->name,
                'permalink' => $b->permalink,
                'status' => $b->status,
                'views' => (int) $b->view_count,
                'created_at' => $b->created_at?->toIso8601String(),
            ])
            ->all();
    }

    /**
     * @return array<int, array{id: int, name: string, email: string, role: ?string, created_at: ?string}>
     */
    private function recentUsers(): array
    {
        return User::query()
            ->with('roles:id,name,display_name')
            ->orderByDesc('created_at')
            ->limit(5)
            ->get(['id', 'name', 'email', 'created_at'])
            ->map(function (User $u): array {
                $role = $u->roles->first();

                return [
                    'id' => $u->id,
                    'name' => $u->name,
                    'email' => $u->email,
                    'role' => $role?->display_name ?? $role?->name,
                    'created_at' => $u->created_at?->toIso8601String(),
                ];
            })
            ->all();
    }

    /**
     * Per-language translation coverage. Coverage = (items translated to lang)
     * / (total items). Reported across both pages and posts so the admin sees
     * where translators should focus.
     *
     * @return array<int, array{code: string, native_name: string, flag: ?string, is_default: bool, page_coverage: int, post_coverage: int}>
     */
    private function languageCoverage(): array
    {
        $totalPages = Page::query()->count();
        $totalPosts = Blog::query()->count();

        return Language::query()
            ->where('status', true)
            ->orderBy('sort_order')
            ->get(['code', 'native_name', 'flag', 'lang_is_default'])
            ->map(function (Language $lang) use ($totalPages, $totalPosts): array {
                $pageT = $totalPages > 0
                    ? (int) round(100 * (PageTranslation::query()
                        ->where('lang', $lang->code)
                        ->distinct('page_id')
                        ->count('page_id') / $totalPages))
                    : 0;

                $postT = $totalPosts > 0
                    ? (int) round(100 * (BlogTranslation::query()
                        ->where('lang', $lang->code)
                        ->distinct('blog_id')
                        ->count('blog_id') / $totalPosts))
                    : 0;

                return [
                    'code' => $lang->code,
                    'native_name' => $lang->native_name,
                    'flag' => $lang->flag,
                    'is_default' => (bool) $lang->lang_is_default,
                    'page_coverage' => $pageT,
                    'post_coverage' => $postT,
                ];
            })
            ->all();
    }

    /**
     * @return array<int, array{id: int, name: string, page_count: int}>
     */
    private function topCategories(): array
    {
        return PageCategory::query()
            ->withCount('pages')
            ->orderByDesc('pages_count')
            ->limit(5)
            ->get(['id', 'title'])
            ->map(fn (PageCategory $c): array => [
                'id' => $c->id,
                'name' => $c->title,
                'page_count' => (int) $c->pages_count,
            ])
            ->all();
    }

    /**
     * Counts the admin should act on. Each row links to a filtered list view
     * so the action is one click away.
     *
     * @return array{page_drafts: int, post_drafts: int, unverified_users: int, missing_translations: int}
     */
    private function attention(Carbon $monthAgo): array
    {
        $activeCodes = Language::query()
            ->where('status', true)
            ->pluck('code')
            ->all();

        $missingTranslations = 0;
        if (count($activeCodes) > 1) {
            // A page/post is "missing translations" when it doesn't have a row
            // for every active language. We compute it per-content-type and sum.
            $configs = [
                [PageTranslation::class, Page::class, 'page_id'],
                [BlogTranslation::class, Blog::class, 'blog_id'],
            ];

            foreach ($configs as [$translationClass, $modelClass, $fk]) {
                $perItem = $translationClass::query()
                    ->selectRaw("{$fk}, count(distinct lang) as langs_present")
                    ->whereIn('lang', $activeCodes)
                    ->groupBy($fk)
                    ->get();

                $totalItems = $modelClass::query()->count();
                $itemsWithFull = $perItem->where('langs_present', count($activeCodes))->count();
                $missingTranslations += max(0, $totalItems - $itemsWithFull);
            }
        }

        return [
            'page_drafts' => Page::query()->where('status', 'draft')->count(),
            'post_drafts' => Blog::query()->where('status', 'draft')->count(),
            'unverified_users' => User::query()
                ->whereNull('email_verified_at')
                ->where('created_at', '>=', $monthAgo)
                ->count(),
            'missing_translations' => $missingTranslations,
        ];
    }
}
