<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin\Service;

use App\Actions\Admin\Service\ParentCategory\CreateServiceParentCategory;
use App\Actions\Admin\Service\ParentCategory\DeleteServiceParentCategory;
use App\Actions\Admin\Service\ParentCategory\ExportParentCategoriesToCsv;
use App\Actions\Admin\Service\ParentCategory\ImportParentCategoriesFromCsv;
use App\Actions\Admin\Service\ParentCategory\UpdateServiceParentCategory;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Service\ParentCategory\BulkActionServiceParentCategoriesRequest;
use App\Http\Requests\Admin\Service\ParentCategory\ImportParentCategoriesRequest;
use App\Http\Requests\Admin\Service\ParentCategory\ReorderServiceParentCategoriesRequest;
use App\Http\Requests\Admin\Service\ParentCategory\StoreServiceParentCategoryRequest;
use App\Http\Requests\Admin\Service\ParentCategory\UpdateServiceParentCategoryRequest;
use App\Models\Language;
use App\Models\ServiceParentCategory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;
use RuntimeException;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Admin surface for the top-level "parent" service categories — the
 * umbrella groupings (Umzüge, Spezialtransport, …). Backed by the dedicated
 * `service_parent_categories` table so parents are cleanly separated from
 * regular categories (see the ServiceCategoryController for those).
 */
final class ParentCategoryController extends Controller
{
    private const SORTABLE_COLUMNS = [
        'sort_order' => 'sort_order',
        'name' => 'name',
        'translations' => 'translations_count',
        'status' => 'status',
        'flags' => 'is_featured',
        'popular' => 'is_popular',
        'children' => 'categories_count',
        'created_at' => 'created_at',
    ];

    public function index(Request $request): Response
    {
        $search = mb_trim((string) $request->query('q', ''));
        $sortBy = $request->query('sort_by');
        $sortDir = $request->query('sort_dir', 'asc') === 'desc' ? 'desc' : 'asc';
        $perPage = max(5, min(100, (int) $request->query('per_page', '10')));

        $query = ServiceParentCategory::query()
            ->with('translations')
            ->withCount(['translations', 'categories']);

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
            $query->orderBy(self::SORTABLE_COLUMNS[$sortBy], $sortDir);
        } else {
            $query->orderBy('sort_order');
        }

        $paginated = $query->paginate($perPage)->withQueryString();

        $categories = $paginated->getCollection()
            ->map(fn (ServiceParentCategory $c): array => $this->presentCategory($c))
            ->values()
            ->all();

        return Inertia::render('admin/services/parents/Index', [
            'categories' => $categories,
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
        return Inertia::render('admin/services/parents/Edit', [
            'category' => null,
            'languages' => $this->presentLanguages(),
            'nextSortOrder' => ServiceParentCategory::nextSortOrder(),
        ]);
    }

    public function store(StoreServiceParentCategoryRequest $request, CreateServiceParentCategory $action): RedirectResponse
    {
        /** @var array<string, mixed> $data */
        $data = $request->validated();
        $parent = $action->handle($data);
        $parent->reorderToCurrentPosition();

        return redirect()
            ->route('admin.services.parent-categories.index')
            ->with('toast', ['type' => 'success', 'message' => __('admin.service_parent_categories.parent_created_toast')]);
    }

    public function edit(ServiceParentCategory $category): Response
    {
        $category->load('translations');

        return Inertia::render('admin/services/parents/Edit', [
            'category' => $this->presentCategory($category->loadCount('categories')),
            'languages' => $this->presentLanguages(),
            'nextSortOrder' => $category->sort_order,
        ]);
    }

    public function update(
        UpdateServiceParentCategoryRequest $request,
        ServiceParentCategory $category,
        UpdateServiceParentCategory $action,
    ): RedirectResponse {
        /** @var array<string, mixed> $data */
        $data = $request->validated();
        $parent = $action->handle($category, $data);
        $parent->reorderToCurrentPosition();

        return redirect()
            ->route('admin.services.parent-categories.index')
            ->with('toast', ['type' => 'success', 'message' => __('admin.service_parent_categories.parent_updated_toast')]);
    }

    public function destroy(ServiceParentCategory $category, DeleteServiceParentCategory $action): RedirectResponse
    {
        try {
            $action->handle($category);
        } catch (RuntimeException $e) {
            return back()->with('toast', ['type' => 'error', 'message' => $e->getMessage()]);
        }
        ServiceParentCategory::compactSiblings();

        return redirect()
            ->route('admin.services.parent-categories.index')
            ->with('toast', ['type' => 'success', 'message' => __('admin.service_parent_categories.parent_deleted_toast')]);
    }

    public function reorder(ReorderServiceParentCategoriesRequest $request): RedirectResponse
    {
        /** @var array{ordered_ids: array<int, int>} $data */
        $data = $request->validated();
        $orderedIds = $data['ordered_ids'];

        DB::transaction(function () use ($orderedIds): void {
            foreach ($orderedIds as $index => $id) {
                ServiceParentCategory::query()
                    ->where('id', $id)
                    ->update(['sort_order' => $index + 1]);
            }
        });

        return back()->with('toast', ['type' => 'success', 'message' => __('admin.service_parent_categories.order_updated_toast')]);
    }

    public function bulkAction(
        BulkActionServiceParentCategoriesRequest $request,
        DeleteServiceParentCategory $deleteAction,
    ): RedirectResponse {
        /** @var array{action: string, ids: array<int, int>} $data */
        $data = $request->validated();
        $action = $data['action'];
        $ids = $data['ids'];

        $count = DB::transaction(function () use ($action, $ids, $deleteAction): int {
            if ($action === 'delete') {
                $affected = 0;
                /** @var Collection<int, ServiceParentCategory> $rows */
                $rows = ServiceParentCategory::query()->whereIn('id', $ids)->get();
                foreach ($rows as $row) {
                    try {
                        $deleteAction->handle($row);
                        $affected++;
                    } catch (RuntimeException) {
                        // Skip parents that still have children — the admin
                        // sees a partial success count and can investigate.
                    }
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

            return ServiceParentCategory::query()->whereIn('id', $ids)->update($update);
        });

        // Each bulk verb maps to its own lang key with pipe-separated
        // singular/plural forms. trans_choice picks the right side based
        // on $count and applies correct pluralization automatically.
        $key = match ($action) {
            'delete' => 'admin.service_parent_categories.bulk_deleted_toast',
            'publish' => 'admin.service_parent_categories.bulk_published_toast',
            'draft' => 'admin.service_parent_categories.bulk_draft_toast',
            'inactive' => 'admin.service_parent_categories.bulk_inactive_toast',
            'mark_featured' => 'admin.service_parent_categories.bulk_marked_featured_toast',
            'unmark_featured' => 'admin.service_parent_categories.bulk_unmarked_featured_toast',
            'mark_popular' => 'admin.service_parent_categories.bulk_marked_popular_toast',
            'unmark_popular' => 'admin.service_parent_categories.bulk_unmarked_popular_toast',
            default => 'admin.service_parent_categories.bulk_updated_toast',
        };

        return back()->with('toast', [
            'type' => 'success',
            'message' => trans_choice($key, $count, ['count' => $count]),
        ]);
    }

    /**
     * Stream the parent categories as a CSV download. Respects the same
     * search / status filters the Index page uses so admins can narrow
     * the export before clicking Export.
     */
    public function export(Request $request, ExportParentCategoriesToCsv $action): StreamedResponse
    {
        return $action->handle([
            'q' => mb_trim((string) $request->query('q', '')),
            'status' => mb_trim((string) $request->query('status', '')),
        ]);
    }

    /**
     * Static template CSV — same headers as export, 5 example rows using
     * the actual Moovato service umbrellas. Mixed filled/empty optional
     * cells so admins see what's required vs. auto-derived.
     */
    public function sampleCsv(ExportParentCategoriesToCsv $action): StreamedResponse
    {
        return $action->sample();
    }

    /**
     * Process a CSV upload. Partial-success semantics: good rows land in
     * the DB, bad rows are collected and flashed to the next request so
     * the Vue Index page can render a detailed error panel.
     */
    public function import(ImportParentCategoriesRequest $request, ImportParentCategoriesFromCsv $action): RedirectResponse
    {
        /** @var UploadedFile $file */
        $file = $request->file('file');

        try {
            $result = $action->handle($file);
        } catch (RuntimeException $e) {
            return back()->with('toast', ['type' => 'error', 'message' => $e->getMessage()]);
        }

        $type = $result->hasErrors() ? 'warning' : 'success';
        $summary = $result->hasErrors()
            ? __('admin.locations.import_toast_summary_with_skipped', [
                'created' => $result->created,
                'updated' => $result->updated,
                'skipped' => $result->skipped,
            ])
            : __('admin.locations.import_toast_summary', [
                'created' => $result->created,
                'updated' => $result->updated,
            ]);

        return redirect()
            ->route('admin.services.parent-categories.index')
            ->with('toast', ['type' => $type, 'message' => $summary])
            ->with('importResult', $result->toArray());
    }

    /**
     * JSON endpoint used by widget editors + the ServiceCategory Edit
     * form's parent picker. Returns every published parent with its
     * per-locale labels so the client can render whichever admin language
     * is active without a second round-trip.
     */
    public function options(): JsonResponse
    {
        $items = ServiceParentCategory::query()
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

        return response()->json(['items' => $items]);
    }

    /**
     * @return array<string, mixed>
     */
    private function presentCategory(ServiceParentCategory $category): array
    {
        return [
            'id' => $category->id,
            'name' => $category->name,
            'image' => $category->image,
            'image_url' => $category->image !== null
                ? '/storage/'.mb_ltrim($category->image, '/')
                : null,
            'status' => $category->status,
            'is_featured' => $category->is_featured,
            'is_popular' => $category->is_popular,
            'sort_order' => $category->sort_order,
            'children_count' => $category->categories_count ?? 0,
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
}
