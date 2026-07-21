<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Blog;
use App\Models\City;
use App\Models\Country;
use App\Models\District;
use App\Models\Language;
use App\Models\MediaFile;
use App\Models\Menu;
use App\Models\Page;
use App\Models\ServiceCategory;
use App\Models\ServiceParentCategory;
use App\Models\State;
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
 * see user results, etc. Groups where the user has no matches at all are
 * dropped from the payload so the UI never renders an empty section.
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

        if ($user?->can('locations.view')) {
            $groups[] = [
                'key' => 'countries',
                'label' => 'Countries',
                'items' => Country::query()
                    ->where(function ($q) use ($like): void {
                        $q->where('name', 'like', $like)
                            ->orWhere('iso_code', 'like', $like);
                    })
                    ->orderByDesc('updated_at')
                    ->limit(self::PER_GROUP)
                    ->get(['id', 'name', 'iso_code', 'status'])
                    ->map(fn (Country $c): array => [
                        'id' => $c->id,
                        'title' => $c->name,
                        'subtitle' => $c->iso_code.' · '.$c->status,
                        'href' => "/admin/directory/countries/{$c->id}/edit",
                    ])
                    ->all(),
            ];

            $groups[] = [
                'key' => 'states',
                'label' => 'States',
                'items' => State::query()
                    ->with('country:id,name,iso_code')
                    ->where(function ($q) use ($like): void {
                        $q->where('name', 'like', $like)
                            ->orWhere('code', 'like', $like);
                    })
                    ->orderByDesc('updated_at')
                    ->limit(self::PER_GROUP)
                    ->get(['id', 'country_id', 'name', 'code', 'status'])
                    ->map(fn (State $s): array => [
                        'id' => $s->id,
                        'title' => $s->name,
                        'subtitle' => ($s->country?->name ?? '—').' · '.$s->code.' · '.$s->status,
                        'href' => "/admin/directory/states/{$s->id}/edit",
                    ])
                    ->all(),
            ];

            $groups[] = [
                'key' => 'cities',
                'label' => 'Cities',
                'items' => City::query()
                    ->with('state:id,name')
                    ->where(function ($q) use ($like): void {
                        $q->where('name', 'like', $like)
                            ->orWhere('postal_code', 'like', $like);
                    })
                    ->orderByDesc('updated_at')
                    ->limit(self::PER_GROUP)
                    ->get(['id', 'state_id', 'name', 'postal_code', 'status'])
                    ->map(fn (City $c): array => [
                        'id' => $c->id,
                        'title' => $c->name,
                        'subtitle' => ($c->state?->name ?? '—')
                            .($c->postal_code !== null ? ' · '.$c->postal_code : '')
                            .' · '.$c->status,
                        'href' => "/admin/directory/cities/{$c->id}/edit",
                    ])
                    ->all(),
            ];

            $groups[] = [
                'key' => 'districts',
                'label' => 'Districts',
                'items' => District::query()
                    ->with('city:id,name')
                    ->where(function ($q) use ($like): void {
                        $q->where('name', 'like', $like)
                            ->orWhere('code', 'like', $like)
                            ->orWhere('postal_code_prefix', 'like', $like);
                    })
                    ->orderByDesc('updated_at')
                    ->limit(self::PER_GROUP)
                    ->get(['id', 'city_id', 'name', 'code', 'postal_code_prefix', 'status'])
                    ->map(fn (District $d): array => [
                        'id' => $d->id,
                        'title' => $d->name,
                        'subtitle' => ($d->city?->name ?? '—')
                            .($d->code !== null ? ' · '.$d->code : '')
                            .' · '.$d->status,
                        'href' => "/admin/directory/districts/{$d->id}/edit",
                    ])
                    ->all(),
            ];
        }

        if ($user?->can('service_categories.view')) {
            $groups[] = [
                'key' => 'service_parents',
                'label' => 'Parent categories',
                'items' => ServiceParentCategory::query()
                    ->where('name', 'like', $like)
                    ->orderByDesc('updated_at')
                    ->limit(self::PER_GROUP)
                    ->get(['id', 'name', 'status'])
                    ->map(fn (ServiceParentCategory $p): array => [
                        'id' => $p->id,
                        'title' => $p->name,
                        'subtitle' => (string) $p->status,
                        'href' => "/admin/services/parent-categories/{$p->id}/edit",
                    ])
                    ->all(),
            ];

            $groups[] = [
                'key' => 'service_categories',
                'label' => 'Service categories',
                'items' => ServiceCategory::query()
                    ->with('parentCategory:id,name')
                    ->where('name', 'like', $like)
                    ->orderByDesc('updated_at')
                    ->limit(self::PER_GROUP)
                    ->get(['id', 'parent_category_id', 'name', 'status'])
                    ->map(fn (ServiceCategory $c): array => [
                        'id' => $c->id,
                        'title' => $c->name,
                        'subtitle' => ($c->parentCategory?->name ?? '—').' · '.$c->status,
                        'href' => "/admin/services/categories/{$c->id}/edit",
                    ])
                    ->all(),
            ];
        }

        if ($user !== null) {
            $groups[] = [
                'key' => 'languages',
                'label' => 'Languages',
                'items' => Language::query()
                    ->where(function ($q) use ($like): void {
                        $q->where('name', 'like', $like)
                            ->orWhere('native_name', 'like', $like)
                            ->orWhere('code', 'like', $like);
                    })
                    ->orderByDesc('updated_at')
                    ->limit(self::PER_GROUP)
                    ->get(['id', 'name', 'native_name', 'code'])
                    ->map(fn (Language $l): array => [
                        'id' => $l->id,
                        'title' => $l->name.' ('.$l->native_name.')',
                        'subtitle' => mb_strtoupper($l->code),
                        'href' => "/admin/languages/{$l->id}/edit",
                    ])
                    ->all(),
            ];
        }

        if ($user?->can('menus.view')) {
            $groups[] = [
                'key' => 'menus',
                'label' => 'Menus',
                'items' => Menu::query()
                    ->where(function ($q) use ($like): void {
                        $q->where('name', 'like', $like)
                            ->orWhere('key', 'like', $like);
                    })
                    ->orderByDesc('updated_at')
                    ->limit(self::PER_GROUP)
                    ->get(['id', 'name', 'key'])
                    ->map(fn (Menu $m): array => [
                        'id' => $m->id,
                        'title' => $m->name,
                        'subtitle' => (string) $m->key,
                        'href' => "/admin/menus/{$m->id}/edit",
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
