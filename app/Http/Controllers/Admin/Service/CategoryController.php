<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin\Service;

use App\Actions\Admin\Service\Category\CreateServiceCategory;
use App\Actions\Admin\Service\Category\DeleteServiceCategory;
use App\Actions\Admin\Service\Category\UpdateServiceCategory;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Service\Category\BulkActionServiceCategoriesRequest;
use App\Http\Requests\Admin\Service\Category\ReorderServiceCategoriesRequest;
use App\Http\Requests\Admin\Service\Category\StoreServiceCategoryRequest;
use App\Http\Requests\Admin\Service\Category\UpdateServiceCategoryRequest;
use App\Models\Language;
use App\Models\ServiceCategory;
use App\Models\ServiceParentCategory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

/**
 * Admin surface for the flat list of service categories. Every category
 * pins to exactly one parent umbrella (`parent_category_id`), managed on
 * the dedicated Parent Categories admin. No self-hierarchy — categories
 * do not nest under each other.
 */
final class CategoryController extends Controller
{
    private const SORTABLE_COLUMNS = [
        'sort_order' => 'sort_order',
        'name' => 'name',
        'parent' => 'parent',
        'translations' => 'translations_count',
        'status' => 'status',
        'flags' => 'is_featured',
        'popular' => 'is_popular',
        'created_at' => 'created_at',
    ];

    public function index(Request $request): Response
    {
        $search = mb_trim((string) $request->query('q', ''));
        $parentId = $request->query('parent_category_id');
        $parentId = is_numeric($parentId) ? (int) $parentId : null;
        $sortBy = $request->query('sort_by');
        $sortDir = $request->query('sort_dir', 'asc') === 'desc' ? 'desc' : 'asc';
        $perPage = max(5, min(100, (int) $request->query('per_page', '10')));

        $query = ServiceCategory::query()
            ->with([
                'translations',
                'parentCategory' => fn ($q) => $q->select('id', 'name')->with('translations'),
            ])
            ->withCount('translations');

        if ($parentId !== null) {
            $query->where('parent_category_id', $parentId);
        }

        if ($search !== '') {
            $query->where(function (Builder $q) use ($search): void {
                $like = "%{$search}%";
                $q->where('name', 'like', $like)
                    ->orWhereHas('translations', function (Builder $t) use ($like): void {
                        $t->where('name', 'like', $like)->orWhere('permalink', 'like', $like);
                    });
            });
        }

        if (is_string($sortBy) && array_key_exists($sortBy, self::SORTABLE_COLUMNS)) {
            if ($sortBy === 'parent') {
                // Sort by the parent umbrella's default-locale name.
                $query->orderBy(
                    ServiceParentCategory::query()
                        ->select('name')
                        ->whereColumn('id', 'service_categories.parent_category_id'),
                    $sortDir,
                );
            } else {
                $query->orderBy(self::SORTABLE_COLUMNS[$sortBy], $sortDir);
            }
        } else {
            $query->orderBy('parent_category_id')->orderBy('sort_order');
        }

        $paginated = $query->paginate($perPage)->withQueryString();

        $categories = $paginated->getCollection()
            ->map(fn (ServiceCategory $c): array => $this->presentCategory($c))
            ->values()
            ->all();

        return Inertia::render('admin/services/categories/Index', [
            'categories' => $categories,
            'languages' => fn (): array => $this->presentLanguages(),
            'parentCategories' => fn (): array => $this->presentParentCategoriesForFilter(),
            'filters' => [
                'q' => $search,
                'parent_category_id' => $parentId,
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
        return Inertia::render('admin/services/categories/Edit', [
            'category' => null,
            'languages' => $this->presentLanguages(),
            'parentOptions' => $this->parentOptions(),
            'nextSortOrder' => ServiceCategory::nextSortOrder(null),
        ]);
    }

    public function store(StoreServiceCategoryRequest $request, CreateServiceCategory $action): RedirectResponse
    {
        /** @var array<string, mixed> $data */
        $data = $request->validated();
        $action->handle($data);

        return redirect()
            ->route('admin.services.categories.index')
            ->with('toast', ['type' => 'success', 'message' => 'Service category created.']);
    }

    public function edit(ServiceCategory $category): Response
    {
        $category->load('translations');

        return Inertia::render('admin/services/categories/Edit', [
            'category' => $this->presentCategory($category),
            'languages' => $this->presentLanguages(),
            'parentOptions' => $this->parentOptions(),
            'nextSortOrder' => ServiceCategory::nextSortOrder($category->parent_category_id),
        ]);
    }

    public function update(
        UpdateServiceCategoryRequest $request,
        ServiceCategory $category,
        UpdateServiceCategory $action,
    ): RedirectResponse {
        /** @var array<string, mixed> $data */
        $data = $request->validated();
        $action->handle($category, $data);

        return redirect()
            ->route('admin.services.categories.index')
            ->with('toast', ['type' => 'success', 'message' => 'Service category updated.']);
    }

    public function destroy(ServiceCategory $category, DeleteServiceCategory $action): RedirectResponse
    {
        $action->handle($category);

        return redirect()
            ->route('admin.services.categories.index')
            ->with('toast', ['type' => 'success', 'message' => 'Service category deleted.']);
    }

    public function reorder(ReorderServiceCategoriesRequest $request): RedirectResponse
    {
        /** @var array{parent_category_id: int, ordered_ids: array<int, int>} $data */
        $data = $request->validated();
        $parentCategoryId = (int) $data['parent_category_id'];
        $orderedIds = $data['ordered_ids'];

        $validIds = ServiceCategory::query()
            ->where('parent_category_id', $parentCategoryId)
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
                ServiceCategory::query()
                    ->where('id', $id)
                    ->update(['sort_order' => $index + 1]);
            }
        });

        return back()->with('toast', ['type' => 'success', 'message' => 'Order updated.']);
    }

    public function bulkAction(
        BulkActionServiceCategoriesRequest $request,
        DeleteServiceCategory $deleteAction,
    ): RedirectResponse {
        /** @var array{action: string, ids: array<int, int>} $data */
        $data = $request->validated();
        $action = $data['action'];
        $ids = $data['ids'];

        $count = DB::transaction(function () use ($action, $ids, $deleteAction): int {
            if ($action === 'delete') {
                $affected = 0;
                /** @var Collection<int, ServiceCategory> $categories */
                $categories = ServiceCategory::query()->whereIn('id', $ids)->get();
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
                'mark_featured' => ['is_featured' => true],
                'unmark_featured' => ['is_featured' => false],
                'mark_popular' => ['is_popular' => true],
                'unmark_popular' => ['is_popular' => false],
                default => [],
            };

            return ServiceCategory::query()->whereIn('id', $ids)->update($update);
        });

        $verb = match ($action) {
            'delete' => 'deleted',
            'publish' => 'published',
            'draft' => 'set to draft',
            'inactive' => 'deactivated',
            'mark_featured' => 'marked featured',
            'unmark_featured' => 'unmarked featured',
            'mark_popular' => 'marked popular',
            'unmark_popular' => 'unmarked popular',
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
    private function presentCategory(ServiceCategory $category): array
    {
        $parent = $category->parentCategory;

        return [
            'id' => $category->id,
            'name' => $category->name,
            'parent_category_id' => $category->parent_category_id,
            'parent_name' => $parent?->name,
            'parent_translations' => $parent
                ? $parent->translations
                    ->mapWithKeys(fn ($t): array => [$t->lang => $t->name])
                    ->all()
                : [],
            'icon' => $category->icon,
            'status' => $category->status,
            'is_featured' => $category->is_featured,
            'is_popular' => $category->is_popular,
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
     * Options for the "Parent umbrella" picker on the Edit form. Reads from
     * `service_parent_categories` — every option carries per-locale labels
     * so the picker follows the admin's header language switcher.
     *
     * @return array<int, array{id: int, label: string, labels: array<string, string>}>
     */
    private function parentOptions(): array
    {
        return ServiceParentCategory::query()
            ->with('translations')
            ->where('status', 'published')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get()
            ->map(function (ServiceParentCategory $p): array {
                $labels = $p->translations
                    ->mapWithKeys(fn ($t): array => [$t->lang => $t->name])
                    ->all();

                return [
                    'id' => $p->id,
                    'label' => $p->name,
                    'labels' => $labels,
                ];
            })
            ->values()
            ->all();
    }

    /**
     * Compact list of parents for the Index page's filter dropdown.
     *
     * @return array<int, array{id: int, label: string, labels: array<string, string>}>
     */
    private function presentParentCategoriesForFilter(): array
    {
        return $this->parentOptions();
    }
}
