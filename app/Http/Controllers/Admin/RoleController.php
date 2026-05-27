<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Actions\Admin\Role\CreateRole;
use App\Actions\Admin\Role\DeleteRole;
use App\Actions\Admin\Role\UpdateRole;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Role\StoreRoleRequest;
use App\Http\Requests\Admin\Role\UpdateRoleRequest;
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;
use Throwable;

final class RoleController extends Controller
{
    private const COLOR_OPTIONS = [
        ['value' => 'rose', 'label' => 'Rose'],
        ['value' => 'amber', 'label' => 'Amber'],
        ['value' => 'emerald', 'label' => 'Emerald'],
        ['value' => 'sky', 'label' => 'Sky'],
        ['value' => 'violet', 'label' => 'Violet'],
        ['value' => 'neutral', 'label' => 'Neutral'],
    ];

    public function index(): Response
    {
        $roles = Role::query()
            ->withCount(['users', 'permissions'])
            ->orderBy('id')
            ->get()
            ->map(fn (Role $r): array => $this->present($r))
            ->all();

        return Inertia::render('admin/roles/Index', [
            'roles' => $roles,
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('admin/roles/Edit', [
            'role' => null,
            'permissionGroups' => $this->permissionGroups(),
            'colorOptions' => self::COLOR_OPTIONS,
        ]);
    }

    public function store(StoreRoleRequest $request, CreateRole $action): RedirectResponse
    {
        /** @var array<string, mixed> $data */
        $data = $request->validated();
        $action->handle($data);

        return redirect()
            ->route('admin.roles.index')
            ->with('toast', ['type' => 'success', 'message' => 'Role created.']);
    }

    public function edit(Role $role): Response
    {
        $role->load('permissions:id');

        return Inertia::render('admin/roles/Edit', [
            'role' => $this->present($role, withPermissionIds: true),
            'permissionGroups' => $this->permissionGroups(),
            'colorOptions' => self::COLOR_OPTIONS,
        ]);
    }

    public function update(UpdateRoleRequest $request, Role $role, UpdateRole $action): RedirectResponse
    {
        /** @var array<string, mixed> $data */
        $data = $request->validated();
        $action->handle($role, $data);

        return redirect()
            ->route('admin.roles.index')
            ->with('toast', ['type' => 'success', 'message' => 'Role updated.']);
    }

    public function destroy(Role $role, DeleteRole $action): RedirectResponse
    {
        try {
            $action->handle($role);
        } catch (Throwable $e) {
            return back()->with('toast', ['type' => 'error', 'message' => $e->getMessage()]);
        }

        return redirect()
            ->route('admin.roles.index')
            ->with('toast', ['type' => 'success', 'message' => 'Role deleted.']);
    }

    /**
     * @return array<string, mixed>
     */
    private function present(Role $role, bool $withPermissionIds = false): array
    {
        return [
            'id' => $role->id,
            'name' => $role->name,
            'display_name' => $role->display_name ?? $role->name,
            'description' => $role->description,
            'color' => $role->color ?? 'neutral',
            'is_system' => (bool) $role->is_system,
            'user_count' => (int) ($role->users_count ?? 0),
            'permission_count' => (int) ($role->permissions_count ?? 0),
            'permission_ids' => $withPermissionIds
                ? $role->permissions->pluck('id')->all()
                : [],
        ];
    }

    /**
     * All permissions bucketed by `group` for the form. Order is preserved
     * via the PermissionSeeder definition order (we re-sort by id within
     * each group so seeded order is stable across runs).
     *
     * @return array<int, array{group: string, items: array<int, array{id: int, name: string, display_name: string}>}>
     */
    private function permissionGroups(): array
    {
        $perms = Permission::query()
            ->orderBy('group')
            ->orderBy('id')
            ->get(['id', 'name', 'display_name', 'group']);

        return $perms
            ->groupBy(fn (Permission $p): string => $p->group ?? 'Other')
            ->map(fn ($items, $group): array => [
                'group' => $group,
                'items' => $items->map(fn (Permission $p): array => [
                    'id' => $p->id,
                    'name' => $p->name,
                    'display_name' => $p->display_name ?? $p->name,
                ])->values()->all(),
            ])
            ->values()
            ->all();
    }
}
