<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin\Page;

use App\Actions\Admin\Page\Category\CreatePageCategory;
use App\Actions\Admin\Page\Category\DeletePageCategory;
use App\Actions\Admin\Page\Category\UpdatePageCategory;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Page\Category\BulkActionPageCategoriesRequest;
use App\Http\Requests\Admin\Page\Category\ReorderPageCategoriesRequest;
use App\Http\Requests\Admin\Page\Category\StorePageCategoryRequest;
use App\Http\Requests\Admin\Page\Category\UpdatePageCategoryRequest;
use App\Models\Language;
use App\Models\PageCategory;
use App\Models\PageCategoryTranslation;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

final class CategoryController extends Controller
{
    private const SORTABLE_COLUMNS = [
        'sort_order' => 'sort_order',
        'title' => 'title',
        'status' => 'status',
        'is_default' => 'is_default',
        'created_at' => 'created_at',
    ];

    public function index(Request $request): Response
    {
        $search = mb_trim((string) $request->query('q', ''));
        $sortBy = $request->query('sort_by');
        $sortDir = $request->query('sort_dir', 'asc') === 'desc' ? 'desc' : 'asc';

        $query = PageCategory::query()
            ->with(['translations', 'parent:id,title']);

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

        if (is_string($sortBy) && array_key_exists($sortBy, self::SORTABLE_COLUMNS)) {
            $query->orderBy(self::SORTABLE_COLUMNS[$sortBy], $sortDir);
        } else {
            $query->orderBy('sort_order');
        }

        $categories = $query->get()
            ->map(fn (PageCategory $c): array => $this->presentCategory($c))
            ->values()
            ->all();

        return Inertia::render('admin/page/categories/Index', [
            'categories' => $categories,
            'languages' => fn (): array => $this->presentLanguages(),
            'filters' => [
                'q' => $search,
                'sort_by' => is_string($sortBy) && array_key_exists($sortBy, self::SORTABLE_COLUMNS) ? $sortBy : null,
                'sort_dir' => $sortDir,
            ],
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('admin/page/categories/Edit', [
            'category' => null,
            'languages' => $this->presentLanguages(),
            'urlPrefixes' => $this->urlPrefixes(),
            'parentOptions' => $this->parentOptions(),
            'nextSortOrder' => PageCategory::nextSortOrder(),
        ]);
    }

    public function store(StorePageCategoryRequest $request, CreatePageCategory $action): RedirectResponse
    {
        /** @var array<string, mixed> $data */
        $data = $request->validated();

        $action->handle($data);

        return redirect()
            ->route('admin.pages.categories.index')
            ->with('toast', ['type' => 'success', 'message' => 'Category created.']);
    }

    public function edit(PageCategory $category): Response
    {
        $category->load('translations');

        return Inertia::render('admin/page/categories/Edit', [
            'category' => $this->presentCategory($category),
            'languages' => $this->presentLanguages(),
            'urlPrefixes' => $this->urlPrefixes(),
            'parentOptions' => $this->parentOptions($category->id),
            'nextSortOrder' => PageCategory::nextSortOrder(),
        ]);
    }

    public function update(
        UpdatePageCategoryRequest $request,
        PageCategory $category,
        UpdatePageCategory $action,
    ): RedirectResponse {
        /** @var array<string, mixed> $data */
        $data = $request->validated();

        $action->handle($category, $data);

        return redirect()
            ->route('admin.pages.categories.index')
            ->with('toast', ['type' => 'success', 'message' => 'Category updated.']);
    }

    public function destroy(PageCategory $category, DeletePageCategory $action): RedirectResponse
    {
        $action->handle($category);

        return redirect()
            ->route('admin.pages.categories.index')
            ->with('toast', ['type' => 'success', 'message' => 'Category deleted.']);
    }

    public function reorder(ReorderPageCategoriesRequest $request): RedirectResponse
    {
        /** @var array{ordered_ids: array<int, int>} $data */
        $data = $request->validated();
        $orderedIds = $data['ordered_ids'];

        DB::transaction(function () use ($orderedIds): void {
            foreach ($orderedIds as $index => $id) {
                PageCategory::query()
                    ->where('id', $id)
                    ->update(['sort_order' => $index + 1]);
            }
        });

        return back()->with('toast', ['type' => 'success', 'message' => 'Order updated.']);
    }

    public function bulkAction(BulkActionPageCategoriesRequest $request): RedirectResponse
    {
        /** @var array{action: string, ids: array<int, int>} $data */
        $data = $request->validated();
        $action = $data['action'];
        $ids = $data['ids'];

        $count = DB::transaction(function () use ($action, $ids): int {
            if ($action === 'delete') {
                $deleted = PageCategory::query()->whereIn('id', $ids)->delete();
                PageCategory::compactAll();

                return $deleted;
            }

            $update = match ($action) {
                'publish' => ['status' => 'published'],
                'draft' => ['status' => 'draft'],
                'inactive' => ['status' => 'inactive'],
                default => [],
            };

            return PageCategory::query()->whereIn('id', $ids)->update($update);
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
    private function presentCategory(PageCategory $category): array
    {
        return [
            'id' => $category->id,
            'title' => $category->title,
            'permalink' => $category->permalink,
            'parent_id' => $category->parent_id,
            'parent_title' => $category->parent?->title,
            'is_default' => $category->is_default,
            'status' => $category->status,
            'sort_order' => $category->sort_order,
            'translations' => $category->translations
                ->mapWithKeys(fn (PageCategoryTranslation $t): array => [
                    $t->lang => [
                        'title' => $t->title,
                        'permalink' => $t->permalink,
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
        $defaultCode = Language::query()
            ->where('lang_is_default', true)
            ->where('status', true)
            ->value('code') ?? 'de';

        return Language::query()
            ->where('status', true)
            ->orderBy('sort_order')
            ->pluck('code')
            ->mapWithKeys(fn (string $code): array => [
                $code => $code === $defaultCode ? "{$base}/page-category/" : "{$base}/{$code}/page-category/",
            ])
            ->all();
    }

    /**
     * @return array<int, array{id: int, title: string}>
     */
    private function parentOptions(?int $excludeId = null): array
    {
        return PageCategory::query()
            ->when($excludeId !== null, fn (Builder $q) => $q->where('id', '!=', $excludeId))
            ->orderBy('title')
            ->get(['id', 'title'])
            ->map(fn (PageCategory $c): array => ['id' => $c->id, 'title' => $c->title])
            ->all();
    }
}
