<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin\Blog;

use App\Actions\Admin\Blog\Post\CreateBlogPost;
use App\Actions\Admin\Blog\Post\DeleteBlogPost;
use App\Actions\Admin\Blog\Post\UpdateBlogPost;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Blog\Post\BulkActionBlogPostsRequest;
use App\Http\Requests\Admin\Blog\Post\ReorderBlogPostsRequest;
use App\Http\Requests\Admin\Blog\Post\StoreBlogPostRequest;
use App\Http\Requests\Admin\Blog\Post\UpdateBlogPostRequest;
use App\Models\Blog;
use App\Models\BlogCategory;
use App\Models\BlogTag;
use App\Models\BlogTranslation;
use App\Models\Language;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

final class PostController extends Controller
{
    private const SORTABLE_COLUMNS = [
        'sort_order' => 'sort_order',
        'name' => 'name',
        'status' => 'status',
        'view_count' => 'view_count',
        'reading_time' => 'reading_time',
        'created_at' => 'created_at',
    ];

    public function index(Request $request): Response
    {
        $search = mb_trim((string) $request->query('q', ''));
        $sortBy = $request->query('sort_by');
        $sortDir = $request->query('sort_dir', 'asc') === 'desc' ? 'desc' : 'asc';
        $perPage = max(5, min(100, (int) $request->query('per_page', '10')));

        $query = Blog::query()
            ->with(['translations', 'categories:id,name', 'tags:id,name', 'user:id,name']);

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

        $posts = $paginated
            ->getCollection()
            ->map(fn (Blog $post): array => $this->presentPost($post))
            ->values()
            ->all();

        return Inertia::render('admin/blog/posts/Index', [
            'posts' => $posts,
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

    public function create(Request $request): Response
    {
        return Inertia::render('admin/blog/posts/Edit', [
            'post' => null,
            'languages' => $this->presentLanguages(),
            'urlPrefixes' => $this->urlPrefixes(),
            'categoryOptions' => $this->categoryOptions(),
            'tagOptions' => $this->tagOptions(),
            'currentUserId' => $request->user()?->id,
            'nextSortOrder' => Blog::nextSortOrder(),
        ]);
    }

    public function store(StoreBlogPostRequest $request, CreateBlogPost $action): RedirectResponse
    {
        /** @var array<string, mixed> $data */
        $data = $request->validated();
        $data['image'] = $request->file('image');
        $data['user_id'] = $request->user()?->id;

        $action->handle($data);

        return redirect()
            ->route('admin.blog.posts.index')
            ->with('toast', ['type' => 'success', 'message' => 'Post created.']);
    }

    public function edit(Blog $post): Response
    {
        $post->load(['translations', 'categories', 'tags']);

        return Inertia::render('admin/blog/posts/Edit', [
            'post' => $this->presentPost($post),
            'languages' => $this->presentLanguages(),
            'urlPrefixes' => $this->urlPrefixes(),
            'categoryOptions' => $this->categoryOptions(),
            'tagOptions' => $this->tagOptions(),
            'currentUserId' => $post->user_id,
            'nextSortOrder' => Blog::nextSortOrder(),
        ]);
    }

    public function update(
        UpdateBlogPostRequest $request,
        Blog $post,
        UpdateBlogPost $action,
    ): RedirectResponse {
        /** @var array<string, mixed> $data */
        $data = $request->validated();
        $data['image'] = $request->file('image');

        $action->handle($post, $data);

        return redirect()
            ->route('admin.blog.posts.index')
            ->with('toast', ['type' => 'success', 'message' => 'Post updated.']);
    }

    public function destroy(Blog $post, DeleteBlogPost $action): RedirectResponse
    {
        $action->handle($post);

        return redirect()
            ->route('admin.blog.posts.index')
            ->with('toast', ['type' => 'success', 'message' => 'Post deleted.']);
    }

    public function reorder(ReorderBlogPostsRequest $request): RedirectResponse
    {
        /** @var array{ordered_ids: array<int, int>} $data */
        $data = $request->validated();
        $orderedIds = $data['ordered_ids'];

        DB::transaction(function () use ($orderedIds): void {
            foreach ($orderedIds as $index => $id) {
                Blog::query()
                    ->where('id', $id)
                    ->update(['sort_order' => $index + 1]);
            }
        });

        return back()->with('toast', ['type' => 'success', 'message' => 'Order updated.']);
    }

    public function bulkAction(BulkActionBlogPostsRequest $request): RedirectResponse
    {
        /** @var array{action: string, ids: array<int, int>} $data */
        $data = $request->validated();
        $action = $data['action'];
        $ids = $data['ids'];

        $count = DB::transaction(function () use ($action, $ids): int {
            if ($action === 'delete') {
                $posts = Blog::query()->whereIn('id', $ids)->get(['id', 'image']);
                foreach ($posts as $post) {
                    if ($post->image !== null) {
                        Storage::disk('public')->delete($post->image);
                    }
                }
                $deleted = Blog::query()->whereIn('id', $ids)->delete();
                Blog::compactAll();

                return $deleted;
            }

            $update = match ($action) {
                'publish' => ['status' => 'published'],
                'draft' => ['status' => 'draft'],
                'inactive' => ['status' => 'inactive'],
                default => [],
            };

            return Blog::query()->whereIn('id', $ids)->update($update);
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
            'message' => "{$count} post".($count === 1 ? '' : 's')." {$verb}.",
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    private function presentPost(Blog $post): array
    {
        return [
            'id' => $post->id,
            'name' => $post->name,
            'permalink' => $post->permalink,
            'short_description' => $post->short_description,
            'content' => $post->content,
            'image' => $post->image,
            'image_url' => $post->image !== null ? '/storage/'.mb_ltrim($post->image, '/') : null,
            'user_id' => $post->user_id,
            'user_name' => $post->user?->name,
            'category_ids' => $post->relationLoaded('categories')
                ? $post->categories->pluck('id')->all()
                : [],
            'category_names' => $post->relationLoaded('categories')
                ? $post->categories->pluck('name')->all()
                : [],
            'tag_ids' => $post->relationLoaded('tags')
                ? $post->tags->pluck('id')->all()
                : [],
            'tag_names' => $post->relationLoaded('tags')
                ? $post->tags->pluck('name')->all()
                : [],
            'status' => $post->status,
            'sort_order' => $post->sort_order,
            'reading_time' => $post->reading_time,
            'view_count' => $post->view_count,
            'is_sticky' => $post->is_sticky,
            'is_featured' => $post->is_featured,
            'translations' => $post->translations
                ->mapWithKeys(fn (BlogTranslation $t): array => [
                    $t->lang => [
                        'name' => $t->name,
                        'permalink' => $t->permalink,
                        'short_description' => $t->short_description,
                        'content' => $t->content,
                    ],
                ])->all(),
            'created_at' => $post->created_at?->toIso8601String(),
            'updated_at' => $post->updated_at?->toIso8601String(),
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
                $code => $code === $defaultCode
                    ? "{$base}/blog/"
                    : "{$base}/{$code}/blog/",
            ])
            ->all();
    }

    /**
     * @return array<int, array{id: int, name: string}>
     */
    private function categoryOptions(): array
    {
        return BlogCategory::query()
            ->where('status', '!=', 'inactive')
            ->orderBy('name')
            ->get(['id', 'name'])
            ->map(fn (BlogCategory $c): array => ['id' => $c->id, 'name' => $c->name])
            ->all();
    }

    /**
     * @return array<int, array{id: int, name: string}>
     */
    private function tagOptions(): array
    {
        return BlogTag::query()
            ->where('status', '!=', 'inactive')
            ->orderBy('name')
            ->get(['id', 'name'])
            ->map(fn (BlogTag $t): array => ['id' => $t->id, 'name' => $t->name])
            ->all();
    }
}
