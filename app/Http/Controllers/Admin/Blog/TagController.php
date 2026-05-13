<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin\Blog;

use App\Actions\Admin\Blog\Tag\CreateBlogTag;
use App\Actions\Admin\Blog\Tag\DeleteBlogTag;
use App\Actions\Admin\Blog\Tag\UpdateBlogTag;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Blog\Tag\BulkActionBlogTagsRequest;
use App\Http\Requests\Admin\Blog\Tag\ReorderBlogTagsRequest;
use App\Http\Requests\Admin\Blog\Tag\StoreBlogTagRequest;
use App\Http\Requests\Admin\Blog\Tag\UpdateBlogTagRequest;
use App\Models\BlogTag;
use App\Models\Language;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

final class TagController extends Controller
{
    private const SORTABLE_COLUMNS = [
        'sort_order' => 'sort_order',
        'name' => 'name',
        'translations' => 'translations_count',
        'status' => 'status',
        'created_at' => 'created_at',
    ];

    public function index(Request $request): Response
    {
        $search = mb_trim((string) $request->query('q', ''));
        $sortBy = $request->query('sort_by');
        $sortDir = $request->query('sort_dir', 'asc') === 'desc' ? 'desc' : 'asc';
        $perPage = max(5, min(100, (int) $request->query('per_page', '10')));

        $query = BlogTag::query()
            ->with('translations')
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
            $query->orderBy(self::SORTABLE_COLUMNS[$sortBy], $sortDir);
        } else {
            $query->orderBy('sort_order');
        }

        $paginated = $query->paginate($perPage)->withQueryString();

        $tags = $paginated
            ->getCollection()
            ->map(fn (BlogTag $tag): array => $this->presentTag($tag))
            ->values()
            ->all();

        return Inertia::render('admin/blog/tags/Index', [
            'tags' => $tags,
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
        return Inertia::render('admin/blog/tags/Edit', [
            'tag' => null,
            'languages' => $this->presentLanguages(),
            'urlPrefixes' => $this->urlPrefixes(),
            'nextSortOrder' => BlogTag::nextSortOrder(),
        ]);
    }

    public function store(StoreBlogTagRequest $request, CreateBlogTag $action): RedirectResponse
    {
        /** @var array<string, mixed> $data */
        $data = $request->validated();
        $action->handle($data);

        return redirect()
            ->route('admin.blog.tags.index')
            ->with('toast', ['type' => 'success', 'message' => 'Tag created.']);
    }

    public function edit(BlogTag $tag): Response
    {
        $tag->load('translations');

        return Inertia::render('admin/blog/tags/Edit', [
            'tag' => $this->presentTag($tag),
            'languages' => $this->presentLanguages(),
            'urlPrefixes' => $this->urlPrefixes(),
            'nextSortOrder' => BlogTag::nextSortOrder(),
        ]);
    }

    public function update(
        UpdateBlogTagRequest $request,
        BlogTag $tag,
        UpdateBlogTag $action,
    ): RedirectResponse {
        /** @var array<string, mixed> $data */
        $data = $request->validated();
        $action->handle($tag, $data);

        return redirect()
            ->route('admin.blog.tags.index')
            ->with('toast', ['type' => 'success', 'message' => 'Tag updated.']);
    }

    public function destroy(BlogTag $tag, DeleteBlogTag $action): RedirectResponse
    {
        $action->handle($tag);

        return redirect()
            ->route('admin.blog.tags.index')
            ->with('toast', ['type' => 'success', 'message' => 'Tag deleted.']);
    }

    public function reorder(ReorderBlogTagsRequest $request): RedirectResponse
    {
        /** @var array{ordered_ids: array<int, int>} $data */
        $data = $request->validated();
        $orderedIds = $data['ordered_ids'];

        DB::transaction(function () use ($orderedIds): void {
            foreach ($orderedIds as $index => $id) {
                BlogTag::query()
                    ->where('id', $id)
                    ->update(['sort_order' => $index + 1]);
            }
        });

        return back()->with('toast', ['type' => 'success', 'message' => 'Order updated.']);
    }

    public function bulkAction(BulkActionBlogTagsRequest $request): RedirectResponse
    {
        /** @var array{action: string, ids: array<int, int>} $data */
        $data = $request->validated();
        $action = $data['action'];
        $ids = $data['ids'];

        $count = DB::transaction(function () use ($action, $ids): int {
            if ($action === 'delete') {
                $deleted = BlogTag::query()->whereIn('id', $ids)->delete();
                BlogTag::compactAll();

                return $deleted;
            }

            $update = match ($action) {
                'publish' => ['status' => 'published'],
                'draft' => ['status' => 'draft'],
                'inactive' => ['status' => 'inactive'],
                default => [],
            };

            return BlogTag::query()->whereIn('id', $ids)->update($update);
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
            'message' => "{$count} tag".($count === 1 ? '' : 's')." {$verb}.",
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    private function presentTag(BlogTag $tag): array
    {
        return [
            'id' => $tag->id,
            'name' => $tag->name,
            'permalink' => $tag->permalink,
            'short_description' => $tag->short_description,
            'status' => $tag->status,
            'sort_order' => $tag->sort_order,
            'translations' => $tag->translations
                ->mapWithKeys(fn ($t): array => [
                    $t->lang => [
                        'name' => $t->name,
                        'permalink' => $t->permalink,
                        'short_description' => $t->short_description,
                    ],
                ])->all(),
            'created_at' => $tag->created_at?->toIso8601String(),
            'updated_at' => $tag->updated_at?->toIso8601String(),
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
                $code => "{$base}/{$code}/blog/tag/",
            ])
            ->all();
    }
}
