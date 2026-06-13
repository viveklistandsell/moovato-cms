<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Blog;
use App\Models\MediaFile;
use App\Models\Page;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Powers the admin Cmd+K palette. Returns a small JSON payload grouped by
 * content type so the frontend can render headed sections. Each group is
 * capped per-request so the response stays under a few KB even on large
 * databases.
 *
 * Permissions are honoured per group — a user without `users.view` won't
 * see user results, etc.
 */
final class SearchController extends Controller
{
    private const PER_GROUP = 5;

    public function __invoke(Request $request): JsonResponse
    {
        $term = mb_trim((string) $request->query('q', ''));

        if ($term === '' || mb_strlen($term) < 2) {
            return response()->json(['groups' => []]);
        }

        $user = $request->user();
        $like = "%{$term}%";
        $groups = [];

        if ($user?->can('pages.view')) {
            $groups[] = [
                'key' => 'pages',
                'label' => 'Pages',
                'items' => Page::query()
                    ->where('title', 'like', $like)
                    ->orWhere('permalink', 'like', $like)
                    ->orderByDesc('updated_at')
                    ->limit(self::PER_GROUP)
                    ->get(['id', 'title', 'permalink', 'status'])
                    ->map(fn (Page $p): array => [
                        'id' => $p->id,
                        'title' => $p->title,
                        'subtitle' => '/'.mb_ltrim((string) $p->permalink, '/').' · '.$p->status,
                        'href' => "/admin/pages/{$p->id}/edit",
                    ])
                    ->all(),
            ];
        }

        if ($user?->can('blog.view')) {
            $groups[] = [
                'key' => 'posts',
                'label' => 'Blog posts',
                'items' => Blog::query()
                    ->where('name', 'like', $like)
                    ->orWhere('permalink', 'like', $like)
                    ->orderByDesc('updated_at')
                    ->limit(self::PER_GROUP)
                    ->get(['id', 'name', 'permalink', 'status'])
                    ->map(fn (Blog $b): array => [
                        'id' => $b->id,
                        'title' => $b->name,
                        'subtitle' => '/'.mb_ltrim((string) $b->permalink, '/').' · '.$b->status,
                        'href' => "/admin/blog/posts/{$b->id}/edit",
                    ])
                    ->all(),
            ];
        }

        if ($user?->can('media.view')) {
            $groups[] = [
                'key' => 'media',
                'label' => 'Media',
                'items' => MediaFile::query()
                    ->where(function ($q) use ($like): void {
                        $q->where('name', 'like', $like)
                            ->orWhere('original_name', 'like', $like);
                    })
                    ->orderByDesc('updated_at')
                    ->limit(self::PER_GROUP)
                    ->get(['id', 'name', 'original_name', 'mime_type', 'folder_id'])
                    ->map(fn (MediaFile $m): array => [
                        'id' => $m->id,
                        'title' => $m->original_name ?: $m->name,
                        'subtitle' => $m->mime_type,
                        'href' => $m->folder_id !== null
                            ? "/admin/media?folder={$m->folder_id}"
                            : '/admin/media',
                    ])
                    ->all(),
            ];
        }

        if ($user?->can('users.view')) {
            $groups[] = [
                'key' => 'users',
                'label' => 'Users',
                'items' => User::query()
                    ->where(function ($q) use ($like): void {
                        $q->where('name', 'like', $like)
                            ->orWhere('email', 'like', $like);
                    })
                    ->orderByDesc('updated_at')
                    ->limit(self::PER_GROUP)
                    ->get(['id', 'name', 'email'])
                    ->map(fn (User $u): array => [
                        'id' => $u->id,
                        'title' => $u->name,
                        'subtitle' => $u->email,
                        'href' => '/admin/users?focus='.$u->id,
                    ])
                    ->all(),
            ];
        }

        // Drop empty groups so the UI doesn't render a heading with no items.
        $groups = array_values(array_filter(
            $groups,
            fn (array $g): bool => count($g['items']) > 0,
        ));

        return response()->json(['groups' => $groups]);
    }
}
