<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin\User;

use App\Http\Controllers\Controller;
use App\Models\Permission;
use App\Models\Role;
use Inertia\Inertia;
use Inertia\Response;

/**
 * Read-only listing of every permission in the system, bucketed by `group`,
 * with a list of roles that currently hold each permission. Editing happens
 * inside the Role form — this page is a discoverability aid only.
 */
final class PermissionController extends Controller
{
    public function index(): Response
    {
        $roles = Role::query()->orderBy('id')->get(['id', 'name', 'display_name', 'color']);

        $permissions = Permission::query()
            ->with('roles:id,name,display_name,color')
            ->orderBy('group')
            ->orderBy('id')
            ->get();

        $groups = $permissions
            ->groupBy(fn (Permission $p): string => $p->group ?? 'Other')
            ->map(fn ($items, $group): array => [
                'group' => $group,
                'items' => $items->map(fn (Permission $p): array => [
                    'id' => $p->id,
                    'name' => $p->name,
                    'display_name' => $p->display_name ?? $p->name,
                    'roles' => $p->roles->map(fn (Role $r): array => [
                        'id' => $r->id,
                        'name' => $r->name,
                        'display_name' => $r->display_name ?? $r->name,
                        'color' => $r->color ?? 'neutral',
                    ])->values()->all(),
                ])->values()->all(),
            ])
            ->values()
            ->all();

        return Inertia::render('admin/permissions/Index', [
            'groups' => $groups,
            'roles' => $roles->map(fn (Role $r): array => [
                'id' => $r->id,
                'name' => $r->name,
                'display_name' => $r->display_name ?? $r->name,
                'color' => $r->color ?? 'neutral',
            ])->all(),
        ]);
    }
}
