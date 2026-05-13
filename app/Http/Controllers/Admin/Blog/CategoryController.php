<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin\Blog;

use App\Actions\Admin\Blog\Category\CreateBlogCategory;
use App\Actions\Admin\Blog\Category\DeleteBlogCategory;
use App\Actions\Admin\Blog\Category\UpdateBlogCategory;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Blog\Category\BulkActionBlogCategoriesRequest;
use App\Http\Requests\Admin\Blog\Category\ReorderBlogCategoriesRequest;
use App\Http\Requests\Admin\Blog\Category\StoreBlogCategoryRequest;
use App\Http\Requests\Admin\Blog\Category\UpdateBlogCategoryRequest;
use App\Models\BlogCategory;
use App\Models\Language;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

final class CategoryController extends Controller
{
    private const SORTABLE_COLUMNS = [
        'sort_order' => 'sort_order',
        'name' => 'name',
        'parent' => 'parent',
        'translations' => 'translations_count',
        'status' => 'status',
        'flags' => 'is_featured',
        'is_default' => 'is_default',
        'created_at' => 'created_at',
    ];

    public function index(Request $request): Response
    {
        $search = mb_trim((string) $request->query('q', ''));
        $sortBy = $request->query('sort_by');
        $sortDir = $request->query('sort_dir', 'asc') === 'desc' ? 'desc' : 'asc';
        $perPage = max(5, min(100, (int) $request->query('per_page', '10')));

        $query = BlogCategory::query()
            ->with(['translations', 'parent:id,name'])
            ->withCount('translations');

        if ($search !== '') {
            $query->where(function (Builder $q) use ($search): void {
                $like = "%{$search}%";
                $q->where('name', 'like', $like)
                    ->orWhere('permalink', 'like', $like)
                    ->orWhereHas('translations', function (Builder $t) use ($like): void {
                        $t->where('name', 'like', $like)->orWhere('permalink', 'like', $like);
                    });
            });
        }

        if (is_string($sortBy) && array_key_exists($sortBy, self::SORTABLE_COLUMNS)) {
            if ($sortBy === 'parent') {
                // Sort by the parent category's name via correlated subquery.
                $query->orderBy(
                    BlogCategory::query()
                        ->select('name')
                        ->whereColumn('id', 'blog_categories.parent_id'),
                    $sortDir,
                );
            } else {
                $query->orderBy(self::SORTABLE_COLUMNS[$sortBy], $sortDir);
            }
        } else {
            $query->orderBy('parent_id')->orderBy('sort_order');
        }

        $paginated = $query->paginate($perPage)->withQueryString();

        $categories = $paginated
            ->getCollection()
            ->map(fn (BlogCategory $category): array => $this->presentCategory($category))
            ->values()
            ->all();

        return Inertia::render('admin/blog/categories/Index', [
            'categories' => $categories,
            // Closures only run on full page loads; partial reloads with `only`
            // (search/sort/paginate) skip languages entirely.
            'languages' => fn (): array => $this->presentLanguages(),
            'filters' => [
                'q' => $search,
                'sort_by' => is_string($sortBy) && array_key_exists($sortBy, self::SORTABLE_COLUMNS) ? $sortBy : null,
                'sort_dir' => $sortDir,
                'per_page' => $perPage,
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

    public function create(): Response
    {
        return Inertia::render('admin/blog/categories/Edit', [
            'category' => null,
            'languages' => $this->presentLanguages(),
            'parentOptions' => $this->parentOptions(),
            'urlPrefixes' => $this->urlPrefixes(),
            'nextSortOrder' => BlogCategory::nextSortOrder(null),
        ]);
    }

    public function store(StoreBlogCategoryRequest $request, CreateBlogCategory $action): RedirectResponse
    {
        /** @var array<string, mixed> $data */
        $data = $request->validated();
        $action->handle($data);

        return redirect()
            ->route('admin.blog.categories.index')
            ->with('toast', ['type' => 'success', 'message' => 'Category created.']);
    }

    public function edit(BlogCategory $category): Response
    {
        $category->load('translations');

        return Inertia::render('admin/blog/categories/Edit', [
            'category' => $this->presentCategory($category),
            'languages' => $this->presentLanguages(),
            'parentOptions' => $this->parentOptions($category->id),
            'urlPrefixes' => $this->urlPrefixes(),
            'nextSortOrder' => BlogCategory::nextSortOrder($category->parent_id),
        ]);
    }

    public function update(
        UpdateBlogCategoryRequest $request,
        BlogCategory $category,
        UpdateBlogCategory $action,
    ): RedirectResponse {
        /** @var array<string, mixed> $data */
        $data = $request->validated();
        $action->handle($category, $data);

        return redirect()
            ->route('admin.blog.categories.index')
            ->with('toast', ['type' => 'success', 'message' => 'Category updated.']);
    }

    public function destroy(BlogCategory $category, DeleteBlogCategory $action): RedirectResponse
    {
        $action->handle($category);

        return redirect()
            ->route('admin.blog.categories.index')
            ->with('toast', ['type' => 'success', 'message' => 'Category deleted.']);
    }

    public function reorder(ReorderBlogCategoriesRequest $request): RedirectResponse
    {
        /** @var array{parent_id: int|null, ordered_ids: array<int, int>} $data */
        $data = $request->validated();
        $parentId = $data['parent_id'] ?? null;
        $orderedIds = $data['ordered_ids'];

        $validIds = BlogCategory::query()
            ->where('parent_id', $parentId)
            ->whereIn('id', $orderedIds)
            ->pluck('id')
            ->all();

        if (count($validIds) !== count($orderedIds)) {
            return back()->with('toast', [
                'type' => 'error',
                'message' => 'Reorder rejected: some categories do not share the same parent.',
            ]);
        }

        DB::transaction(function () use ($orderedIds): void {
            foreach ($orderedIds as $index => $id) {
                BlogCategory::query()
                    ->where('id', $id)
                    ->update(['sort_order' => $index + 1]);
            }
        });

        return back()->with('toast', ['type' => 'success', 'message' => 'Order updated.']);
    }

    public function bulkAction(
        BulkActionBlogCategoriesRequest $request,
        DeleteBlogCategory $deleteAction,
    ): RedirectResponse {
        /** @var array{action: string, ids: array<int, int>} $data */
        $data = $request->validated();
        $action = $data['action'];
        $ids = $data['ids'];

        $count = DB::transaction(function () use ($action, $ids, $deleteAction): int {
            if ($action === 'delete') {
                $affected = 0;
                /** @var Collection<int, BlogCategory> $categories */
                $categories = BlogCategory::query()->whereIn('id', $ids)->get();

                foreach ($categories as $category) {
                    $deleteAction->handle($category);
                    $affected++;
                }

                return $affected;
            }

            $update = match ($action) {
                'publish' => ['status' => 'published'],
                'draft' => ['status' => 'draft'],
                'inactive' => ['status' => 'inactive'],
                default => [],
            };

            return BlogCategory::query()->whereIn('id', $ids)->update($update);
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
            'message' => "{$count} categor".($count === 1 ? 'y' : 'ies')." {$verb}.",
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    private function presentCategory(BlogCategory $category): array
    {
        return [
            'id' => $category->id,
            'name' => $category->name,
            'permalink' => $category->permalink,
            'parent_id' => $category->parent_id,
            'parent_name' => $category->parent?->name,
            'icon' => $category->icon,
            'short_description' => $category->short_description,
            'status' => $category->status,
            'is_featured' => $category->is_featured,
            'is_default' => $category->is_default,
            'sort_order' => $category->sort_order,
            'translations' => $category->translations
                ->mapWithKeys(fn ($t): array => [
                    $t->lang => [
                        'name' => $t->name,
                        'permalink' => $t->permalink,
                        'short_description' => $t->short_description,
                    ],
                ])->all(),
            'created_at' => $category->created_at?->toIso8601String(),
            'updated_at' => $category->updated_at?->toIso8601String(),
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

        return Language::query()
            ->where('status', true)
            ->orderBy('sort_order')
            ->pluck('code')
            ->mapWithKeys(fn (string $code): array => [
                $code => "{$base}/{$code}/blog/category/",
            ])
            ->all();
    }

    /**
     * @return array<int, array{id: int, label: string, depth: int}>
     */
    private function parentOptions(?int $excludeId = null): array
    {
        $all = BlogCategory::query()
            ->orderBy('parent_id')
            ->orderBy('sort_order')
            ->get(['id', 'name', 'parent_id'])
            ->keyBy('id');

        $depth = static function (BlogCategory $cat) use ($all, &$depth): int {
            $level = 0;
            $current = $cat;
            while ($current->parent_id !== null && $all->has($current->parent_id)) {
                $level++;
                $current = $all->get($current->parent_id);
                if ($level > 10) {
                    break;
                }
            }

            return $level;
        };

        return $all
            ->reject(fn (BlogCategory $c): bool => $excludeId !== null && $c->id === $excludeId)
            ->map(fn (BlogCategory $c): array => [
                'id' => $c->id,
                'label' => str_repeat('— ', $depth($c)).$c->name,
                'depth' => $depth($c),
            ])
            ->values()
            ->all();
    }
}
