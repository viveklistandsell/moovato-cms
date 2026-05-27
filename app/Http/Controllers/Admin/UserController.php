<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Actions\Admin\User\BulkUserAction;
use App\Actions\Admin\User\CreateUser;
use App\Actions\Admin\User\DeleteUser;
use App\Actions\Admin\User\ResetUserPassword;
use App\Actions\Admin\User\UpdateUser;
use App\Enums\UserStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\User\BulkUserActionRequest;
use App\Http\Requests\Admin\User\ResetUserPasswordRequest;
use App\Http\Requests\Admin\User\StoreUserRequest;
use App\Http\Requests\Admin\User\UpdateUserRequest;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Throwable;

final class UserController extends Controller
{
    private const SORTABLE_COLUMNS = [
        'name' => 'name',
        'email' => 'email',
        'status' => 'status',
        'created_at' => 'created_at',
        'last_login_at' => 'last_login_at',
    ];

    public function index(Request $request): Response
    {
        $search = mb_trim((string) $request->query('q', ''));
        $sortBy = $request->query('sort_by');
        $sortDir = $request->query('sort_dir', 'desc') === 'asc' ? 'asc' : 'desc';
        $perPage = max(5, min(100, (int) $request->query('per_page', '10')));

        $statusFilter = (string) $request->query('status', 'all');
        $roleFilter = (string) $request->query('role', 'all');

        $query = User::query()->with('roles:id,name,display_name,color');

        if ($search !== '') {
            $query->where(function (Builder $q) use ($search): void {
                $like = "%{$search}%";
                $q->where('name', 'like', $like)->orWhere('email', 'like', $like);
            });
        }

        if (in_array($statusFilter, UserStatus::values(), true)) {
            $query->where('status', $statusFilter);
        }

        if ($roleFilter !== 'all' && $roleFilter !== '') {
            $query->whereHas('roles', static fn (Builder $r) => $r->where('name', $roleFilter));
        }

        $column = is_string($sortBy) && array_key_exists($sortBy, self::SORTABLE_COLUMNS)
            ? self::SORTABLE_COLUMNS[$sortBy]
            : 'created_at';
        $query->orderBy($column, $sortDir);

        $paginated = $query->paginate($perPage)->withQueryString();
        $users = $paginated->getCollection()->map(fn (User $u): array => $this->present($u))->all();

        return Inertia::render('admin/users/Index', [
            'users' => $users,
            'roleOptions' => fn (): array => $this->roleOptions(),
            'statusOptions' => fn (): array => $this->statusOptions(),
            'stats' => fn (): array => $this->stats(),
            'currentUserId' => $request->user()?->id,
            'filters' => [
                'q' => $search,
                'sort_by' => is_string($sortBy) && array_key_exists($sortBy, self::SORTABLE_COLUMNS) ? $sortBy : null,
                'sort_dir' => $sortDir,
                'per_page' => $perPage,
                'status' => in_array($statusFilter, [...UserStatus::values(), 'all'], true) ? $statusFilter : 'all',
                'role' => $roleFilter,
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

    public function store(StoreUserRequest $request, CreateUser $action): RedirectResponse
    {
        /** @var array<string, mixed> $data */
        $data = $request->validated();
        $data['avatar'] = $request->file('avatar');

        $action->handle($data);

        return back()->with('toast', ['type' => 'success', 'message' => 'User created.']);
    }

    public function update(UpdateUserRequest $request, User $user, UpdateUser $action): RedirectResponse
    {
        /** @var array<string, mixed> $data */
        $data = $request->validated();
        $data['avatar'] = $request->file('avatar');

        $action->handle($user, $data);

        return back()->with('toast', ['type' => 'success', 'message' => 'User updated.']);
    }

    public function destroy(Request $request, User $user, DeleteUser $action): RedirectResponse
    {
        $actor = $request->user();
        abort_if($actor === null || ! $actor->can('users.delete'), 403);

        try {
            $action->handle($user, $actor);
        } catch (Throwable $e) {
            return back()->with('toast', ['type' => 'error', 'message' => $e->getMessage()]);
        }

        return back()->with('toast', ['type' => 'success', 'message' => 'User deleted.']);
    }

    public function resetPassword(ResetUserPasswordRequest $request, User $user, ResetUserPassword $action): RedirectResponse
    {
        /** @var array{password: string} $data */
        $data = $request->validated();

        $action->handle($user, $data['password']);

        return back()->with('toast', ['type' => 'success', 'message' => 'Password reset.']);
    }

    public function bulkAction(BulkUserActionRequest $request, BulkUserAction $action): RedirectResponse
    {
        /** @var array{action: string, ids: array<int, int>} $data */
        $data = $request->validated();
        $actor = $request->user();
        abort_if($actor === null, 403);

        $permissionFor = [
            'activate' => 'users.update',
            'deactivate' => 'users.update',
            'ban' => 'users.ban',
            'delete' => 'users.delete',
        ];
        abort_if(! $actor->can($permissionFor[$data['action']]), 403);

        try {
            $count = $action->handle($data['action'], $data['ids'], $actor);
        } catch (Throwable $e) {
            return back()->with('toast', ['type' => 'error', 'message' => $e->getMessage()]);
        }

        $verb = match ($data['action']) {
            'activate' => 'activated',
            'deactivate' => 'deactivated',
            'ban' => 'banned',
            'delete' => 'deleted',
            default => 'updated',
        };

        return back()->with('toast', [
            'type' => 'success',
            'message' => "{$count} user".($count === 1 ? '' : 's')." {$verb}.",
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    private function present(User $user): array
    {
        $role = $user->roles->first();

        return [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'avatar' => $user->avatar,
            'avatar_url' => $user->avatar !== null ? '/storage/'.mb_ltrim($user->avatar, '/') : null,
            'status' => $user->status?->value,
            'status_label' => $user->status?->label(),
            'status_color' => $user->status?->color(),
            'role' => $role?->name,
            'role_display_name' => $role?->display_name ?? $role?->name,
            'role_color' => $role?->color,
            'last_login_at' => $user->last_login_at?->toIso8601String(),
            'last_login_ip' => $user->last_login_ip,
            'created_at' => $user->created_at?->toIso8601String(),
        ];
    }

    /**
     * @return array<int, array{value: string, label: string, color: string}>
     */
    private function roleOptions(): array
    {
        return Role::query()
            ->orderBy('id')
            ->get(['name', 'display_name', 'color'])
            ->map(fn (Role $r): array => [
                'value' => $r->name,
                'label' => $r->display_name ?? $r->name,
                'color' => $r->color ?? 'neutral',
            ])
            ->all();
    }

    /**
     * @return array<int, array{value: string, label: string, color: string}>
     */
    private function statusOptions(): array
    {
        return array_map(static fn (UserStatus $s): array => [
            'value' => $s->value,
            'label' => $s->label(),
            'color' => $s->color(),
        ], UserStatus::cases());
    }

    /**
     * @return array{total: int, active: int, inactive: int, admins: int}
     */
    private function stats(): array
    {
        $adminRoleNames = [User::SUPER_ADMIN_ROLE, 'Admin'];

        return [
            'total' => User::query()->count(),
            'active' => User::query()->where('status', UserStatus::Active->value)->count(),
            'inactive' => User::query()->where('status', UserStatus::Inactive->value)->count(),
            'admins' => User::query()->whereHas('roles', static fn (Builder $r) => $r->whereIn('name', $adminRoleNames))->count(),
        ];
    }
}
