<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin\Page;

use App\Actions\Admin\Page\Page\CreatePage;
use App\Actions\Admin\Page\Page\DeletePage;
use App\Actions\Admin\Page\Page\UpdatePage;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Page\Page\BulkActionPagesRequest;
use App\Http\Requests\Admin\Page\Page\StorePageRequest;
use App\Http\Requests\Admin\Page\Page\UpdatePageRequest;
use App\Models\Language;
use App\Models\Page;
use App\Models\PageCategory;
use App\Models\PageTranslation;
use App\Models\PageWidget;
use App\Models\PageWidgetTranslation;
use App\Widgets\Registry\WidgetRegistry;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

final class PageController extends Controller
{
    private const SORTABLE_COLUMNS = [
        'title' => 'title',
        'status' => 'status',
        'is_home' => 'is_home',
        'created_at' => 'created_at',
    ];

    public function index(Request $request): Response
    {
        $search = mb_trim((string) $request->query('q', ''));
        $sortBy = $request->query('sort_by');
        $sortDir = $request->query('sort_dir', 'asc') === 'desc' ? 'desc' : 'asc';
        $perPage = max(5, min(100, (int) $request->query('per_page', '10')));

        // Filter tabs: `all` (default), `published`, `draft`, `inactive`, `mine`.
        // `mine` is layered on top of the status filter — pages I authored AND
        // matching the active status (or any, if status=all).
        $statusFilter = (string) $request->query('status', 'all');
        $mineOnly = $request->boolean('mine');
        $categoryId = (int) $request->query('category_id', '0');
        $langFilter = (string) $request->query('lang', 'any'); // any | de | en | both

        $query = Page::query()
            ->with(['translations', 'categories:id,title', 'user:id,name']);

        if ($search !== '') {
            $query->where(function (Builder $q) use ($search): void {
                $like = "%{$search}%";
                $q->where('title', 'like', $like)
                    ->orWhere('permalink', 'like', $like)
                    ->orWhereHas('translations', function (Builder $t) use ($like): void {
                        $t->where('title', 'like', $like)->orWhere('permalink', 'like', $like);
                    });
            });
        }

        if (in_array($statusFilter, ['published', 'draft', 'inactive'], true)) {
            $query->where('status', $statusFilter);
        }

        if ($mineOnly && $request->user() !== null) {
            $query->where('user_id', $request->user()->id);
        }

        if ($categoryId > 0) {
            $query->whereHas('categories', function (Builder $c) use ($categoryId): void {
                $c->where('page_categories.id', $categoryId);
            });
        }

        $activeCodes = Language::query()
            ->where('status', true)
            ->pluck('code')
            ->all();

        if ($langFilter !== 'any' && in_array($langFilter, [...$activeCodes, 'both'], true)) {
            if ($langFilter === 'both' && count($activeCodes) > 0) {
                // Page must have a translation row for EVERY active language.
                foreach ($activeCodes as $code) {
                    $query->whereHas('translations', fn (Builder $t) => $t->where('lang', $code));
                }
            } else {
                $query->whereHas('translations', fn (Builder $t) => $t->where('lang', $langFilter));
            }
        }

        if (is_string($sortBy) && array_key_exists($sortBy, self::SORTABLE_COLUMNS)) {
            $query->orderBy(self::SORTABLE_COLUMNS[$sortBy], $sortDir);
        } else {
            $query->orderByDesc('created_at');
        }

        $paginated = $query->paginate($perPage)->withQueryString();

        $pages = $paginated
            ->getCollection()
            ->map(fn (Page $page): array => $this->presentPage($page))
            ->values()
            ->all();

        return Inertia::render('admin/page/pages/Index', [
            'pages' => $pages,
            'languages' => fn (): array => $this->presentLanguages(),
            'categoryOptions' => fn (): array => $this->categoryOptions(),
            'statusCounts' => fn (): array => $this->statusCounts($request->user()?->id),
            'filters' => [
                'q' => $search,
                'sort_by' => is_string($sortBy) && array_key_exists($sortBy, self::SORTABLE_COLUMNS) ? $sortBy : null,
                'sort_dir' => $sortDir,
                'per_page' => $perPage,
                'status' => in_array($statusFilter, ['all', 'published', 'draft', 'inactive'], true) ? $statusFilter : 'all',
                'mine' => $mineOnly,
                'category_id' => $categoryId > 0 ? $categoryId : null,
                'lang' => $langFilter,
            ],
            'pagination' => [
                'current_page' => $paginated->currentPage(),
                'last_page' => $paginated->lastPage(),
                'per_page' => $paginated->perPage(),
                'total' => $paginated->total(),
                'from' => $paginated->firstItem(),
                'to' => $paginated->lastItem(),
                'links' => $paginated->linkCollection()->toArray(),
            ],
        ]);
    }

    public function create(Request $request, WidgetRegistry $widgets): Response
    {
        return Inertia::render('admin/page/pages/Edit', [
            'page' => null,
            'languages' => $this->presentLanguages(),
            'urlPrefixes' => $this->urlPrefixes(),
            'categoryOptions' => $this->categoryOptions(),
            'templates' => $this->templateOptions(),
            'currentUserId' => $request->user()?->id,
            'availableWidgets' => $widgets->all(),
            'pageWidgets' => [],
        ]);
    }

    public function store(StorePageRequest $request, CreatePage $action): RedirectResponse
    {
        /** @var array<string, mixed> $data */
        $data = $request->validated();
        $data['image'] = $request->file('image');
        $data['user_id'] = $request->user()?->id;

        $action->handle($data);

        return redirect()
            ->route('admin.pages.index')
            ->with('toast', ['type' => 'success', 'message' => 'Page created.']);
    }

    public function edit(Page $page, WidgetRegistry $widgets): Response
    {
        $page->load(['translations', 'categories', 'widgets.translations']);

        return Inertia::render('admin/page/pages/Edit', [
            'page' => $this->presentPage($page),
            'languages' => $this->presentLanguages(),
            'urlPrefixes' => $this->urlPrefixes(),
            'categoryOptions' => $this->categoryOptions(),
            'templates' => $this->templateOptions(),
            'currentUserId' => $page->user_id,
            'availableWidgets' => $widgets->all(),
            'pageWidgets' => $this->presentWidgets($page),
        ]);
    }

    public function update(
        UpdatePageRequest $request,
        Page $page,
        UpdatePage $action,
    ): RedirectResponse {
        /** @var array<string, mixed> $data */
        $data = $request->validated();
        $data['image'] = $request->file('image');

        $action->handle($page, $data);

        return redirect()
            ->route('admin.pages.index')
            ->with('toast', ['type' => 'success', 'message' => 'Page updated.']);
    }

    public function destroy(Page $page, DeletePage $action): RedirectResponse
    {
        $action->handle($page);

        return redirect()
            ->route('admin.pages.index')
            ->with('toast', ['type' => 'success', 'message' => 'Page deleted.']);
    }

    public function bulkAction(BulkActionPagesRequest $request): RedirectResponse
    {
        /** @var array{action: string, ids: array<int, int>} $data */
        $data = $request->validated();
        $action = $data['action'];
        $ids = $data['ids'];

        $count = DB::transaction(function () use ($action, $ids): int {
            if ($action === 'delete') {
                $pages = Page::query()->whereIn('id', $ids)->get(['id', 'image']);
                foreach ($pages as $page) {
                    if ($page->image !== null) {
                        Storage::disk('public')->delete($page->image);
                    }
                }

                return Page::query()->whereIn('id', $ids)->delete();
            }

            $update = match ($action) {
                'publish' => ['status' => 'published'],
                'draft' => ['status' => 'draft'],
                'inactive' => ['status' => 'inactive'],
                default => [],
            };

            return Page::query()->whereIn('id', $ids)->update($update);
        });

        $verb = match ($action) {
            'delete' => 'deleted',
            'publish' => 'published',
            'draft' => 'set to draft',
            'inactive' => 'deactivated',
            default => 'updated',
        };

        return back()->with('toast', [
            'type' => 'success',
            'message' => "{$count} page".($count === 1 ? '' : 's')." {$verb}.",
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    private function presentPage(Page $page): array
    {
        return [
            'id' => $page->id,
            'title' => $page->title,
            'permalink' => $page->permalink,
            'image' => $page->image,
            'image_url' => $page->image !== null ? '/storage/'.mb_ltrim($page->image, '/') : null,
            'template' => $page->template,
            'is_home' => $page->is_home,
            'user_id' => $page->user_id,
            'user_name' => $page->user?->name,
            'category_ids' => $page->relationLoaded('categories')
                ? $page->categories->pluck('id')->all()
                : [],
            'category_names' => $page->relationLoaded('categories')
                ? $page->categories->pluck('title')->all()
                : [],
            'status' => $page->status,
            'translations' => $page->translations
                ->mapWithKeys(fn (PageTranslation $t): array => [
                    $t->lang => [
                        'title' => $t->title,
                        'permalink' => $t->permalink,
                    ],
                ])->all(),
            'created_at' => $page->created_at?->toIso8601String(),
            'updated_at' => $page->updated_at?->toIso8601String(),
        ];
    }

    /**
     * @return array<int, array{code: string, name: string, native_name: string, flag: ?string, is_default: bool}>
     */
    private function presentLanguages(): array
    {
        return Language::query()
            ->where('status', true)
            ->orderBy('sort_order')
            ->get()
            ->map(fn (Language $lang): array => [
                'code' => $lang->code,
                'name' => $lang->name,
                'native_name' => $lang->native_name,
                'flag' => $lang->flag,
                'is_default' => $lang->lang_is_default,
            ])
            ->values()
            ->all();
    }

    /**
     * @return array<string, string>
     */
    private function urlPrefixes(): array
    {
        $base = mb_rtrim((string) config('app.url'), '/');
        $defaultCode = Language::query()
            ->where('lang_is_default', true)
            ->where('status', true)
            ->value('code') ?? 'de';

        return Language::query()
            ->where('status', true)
            ->orderBy('sort_order')
            ->pluck('code')
            ->mapWithKeys(fn (string $code): array => [
                $code => $code === $defaultCode ? "{$base}/" : "{$base}/{$code}/",
            ])
            ->all();
    }

    /**
     * @return array<int, array{id: int, name: string}>
     */
    private function categoryOptions(): array
    {
        return PageCategory::query()
            ->where('status', '!=', 'inactive')
            ->orderBy('title')
            ->get(['id', 'title'])
            ->map(fn (PageCategory $c): array => ['id' => $c->id, 'name' => $c->title])
            ->all();
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function presentWidgets(Page $page): array
    {
        if (! $page->relationLoaded('widgets')) {
            return [];
        }

        return $page->widgets
            ->map(fn (PageWidget $w): array => [
                'id' => $w->id,
                'type' => $w->type,
                'position' => $w->position,
                'is_active' => $w->is_active,
                'settings' => $w->settings ?? [],
                'translations' => $w->translations
                    ->mapWithKeys(fn (PageWidgetTranslation $t): array => [
                        $t->lang => $t->data ?? [],
                    ])->all(),
            ])
            ->values()
            ->all();
    }

    /**
     * Per-filter row counts shown next to the colored pill buttons. Computed
     * against the global page set (NOT the currently filtered view) so the
     * numbers stay stable as the admin clicks between pills.
     *
     * @return array{all: int, mine: int, published: int, draft: int, inactive: int}
     */
    private function statusCounts(?int $userId): array
    {
        return [
            'all' => Page::query()->count(),
            'mine' => $userId !== null
                ? Page::query()->where('user_id', $userId)->count()
                : 0,
            'published' => Page::query()->where('status', 'published')->count(),
            'draft' => Page::query()->where('status', 'draft')->count(),
            'inactive' => Page::query()->where('status', 'inactive')->count(),
        ];
    }

    /**
     * @return array<int, array{value: string, label: string}>
     */
    private function templateOptions(): array
    {
        return [
            ['value' => 'default', 'label' => 'Default'],
            ['value' => 'fullwidth', 'label' => 'Full width'],
            ['value' => 'nolayout', 'label' => 'No layout'],
        ];
    }
}
