<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin\System;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

/**
 * Read-only viewer for the audit trail. Listing is filterable by user,
 * action verb, subject type, and free-text description; results are
 * paginated server-side.
 *
 * Permission: `settings.activity` (already in PermissionSeeder).
 */
final class ActivityLogController extends Controller
{
    public function index(Request $request): Response
    {
        $userId = $request->query('user_id');
        $action = $request->query('action');
        $subjectType = $request->query('subject_type');
        $search = mb_trim((string) $request->query('q', ''));

        $query = ActivityLog::query()
            ->with(['user:id,name,email'])
            ->orderByDesc('created_at')
            ->orderByDesc('id');

        if (is_numeric($userId)) {
            $query->where('user_id', (int) $userId);
        }

        if (is_string($action) && $action !== '') {
            $query->where('action', $action);
        }

        if (is_string($subjectType) && $subjectType !== '') {
            $query->where('subject_type', $subjectType);
        }

        if ($search !== '') {
            $like = "%{$search}%";
            $query->where(function (Builder $q) use ($like): void {
                $q->where('description', 'like', $like)
                    ->orWhere('action', 'like', $like)
                    ->orWhere('ip_address', 'like', $like);
            });
        }

        $paginator = $query->paginate(25)->withQueryString();

        return Inertia::render('admin/system/Activity', [
            'logs' => [
                'data' => $paginator->getCollection()->map(fn (ActivityLog $log): array => [
                    'id' => $log->id,
                    'action' => $log->action,
                    'description' => $log->description,
                    'subject_type' => $log->subject_type,
                    'subject_id' => $log->subject_id,
                    'ip_address' => $log->ip_address,
                    'user_agent' => $log->user_agent,
                    'properties' => $log->properties,
                    'created_at' => $log->created_at?->toIso8601String(),
                    'user' => $log->user !== null
                        ? ['id' => $log->user->id, 'name' => $log->user->name, 'email' => $log->user->email]
                        : null,
                ])->all(),
                'current_page' => $paginator->currentPage(),
                'last_page' => $paginator->lastPage(),
                'total' => $paginator->total(),
                'links' => $paginator->linkCollection()->toArray(),
            ],
            'filters' => [
                'user_id' => is_numeric($userId) ? (int) $userId : null,
                'action' => $action,
                'subject_type' => $subjectType,
                'q' => $search,
            ],

            'options' => [
                'users' => User::query()
                    ->whereIn('id', ActivityLog::query()->distinct()->pluck('user_id')->filter())
                    ->orderBy('name')
                    ->get(['id', 'name'])
                    ->map(fn (User $u): array => ['id' => $u->id, 'name' => $u->name])
                    ->all(),
                'actions' => ActivityLog::query()
                    ->distinct()
                    ->orderBy('action')
                    ->pluck('action')
                    ->filter()
                    ->values()
                    ->all(),
                'subject_types' => ActivityLog::query()
                    ->distinct()
                    ->orderBy('subject_type')
                    ->pluck('subject_type')
                    ->filter()
                    ->values()
                    ->all(),
            ],
        ]);
    }
}
