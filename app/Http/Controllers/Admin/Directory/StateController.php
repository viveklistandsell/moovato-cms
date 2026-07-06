<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin\Directory;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Directory\State\BulkActionStatesRequest;
use App\Http\Requests\Admin\Directory\State\ReorderStatesRequest;
use App\Http\Requests\Admin\Directory\State\StoreStateRequest;
use App\Http\Requests\Admin\Directory\State\UpdateStateRequest;
use App\Models\Country;
use App\Models\State;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

final class StateController extends Controller
{
    private const SORTABLE_COLUMNS = [
        'sort_order' => 'sort_order',
        'name' => 'name',
        'code' => 'code',
        'country' => 'country',
        'status' => 'status',
        'cities' => 'cities_count',
        'created_at' => 'created_at',
    ];

    public function index(Request $request): Response
    {
        $search = mb_trim((string) $request->query('q', ''));
        $countryId = $request->query('country_id');
        $countryId = is_numeric($countryId) ? (int) $countryId : null;
        $sortBy = $request->query('sort_by');
        $sortDir = $request->query('sort_dir', 'asc') === 'desc' ? 'desc' : 'asc';
        $perPage = max(5, min(100, (int) $request->query('per_page', '10')));

        $query = State::query()
            ->with('country:id,name,iso_code')
            ->withCount('cities');

        if ($countryId !== null) {
            $query->where('country_id', $countryId);
        }

        if ($search !== '') {
            $query->where(function (Builder $q) use ($search): void {
                $like = "%{$search}%";
                $q->where('name', 'like', $like)
                    ->orWhere('code', 'like', $like)
                    ->orWhere('permalink', 'like', $like);
            });
        }

        if (is_string($sortBy) && array_key_exists($sortBy, self::SORTABLE_COLUMNS)) {
            if ($sortBy === 'country') {
                $query->orderBy(
                    Country::query()
                        ->select('name')
                        ->whereColumn('id', 'states.country_id'),
                    $sortDir,
                );
            } else {
                $query->orderBy(self::SORTABLE_COLUMNS[$sortBy], $sortDir);
            }
        } else {
            $query->orderBy('country_id')->orderBy('sort_order');
        }

        $paginated = $query->paginate($perPage)->withQueryString();

        $states = $paginated->getCollection()
            ->map(fn (State $s): array => $this->presentState($s))
            ->values()
            ->all();

        return Inertia::render('admin/directory/states/Index', [
            'states' => $states,
            'countries' => fn (): array => $this->presentCountries(),
            'filters' => [
                'q' => $search,
                'country_id' => $countryId,
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
        return Inertia::render('admin/directory/states/Edit', [
            'state' => null,
            'countries' => $this->presentCountries(),
            'nextSortOrder' => State::nextSortOrder(),
        ]);
    }

    public function store(StoreStateRequest $request): RedirectResponse
    {
        /** @var array<string, mixed> $data */
        $data = $request->validated();
        $data['sort_order'] = $data['sort_order'] ?? State::nextSortOrder((int) $data['country_id']);

        /** @var State $state */
        $state = State::query()->create($data);
        $state->reorderToCurrentPosition();

        return redirect()
            ->route('admin.directory.states.index')
            ->with('toast', ['type' => 'success', 'message' => 'State created.']);
    }

    public function edit(State $state): Response
    {
        return Inertia::render('admin/directory/states/Edit', [
            'state' => $this->presentState($state->loadCount('cities')->load('country:id,name,iso_code')),
            'countries' => $this->presentCountries(),
            'nextSortOrder' => $state->sort_order,
        ]);
    }

    public function update(UpdateStateRequest $request, State $state): RedirectResponse
    {
        /** @var array<string, mixed> $data */
        $data = $request->validated();
        $oldCountryId = (int) $state->country_id;
        $state->update($data);
        $state->reorderToCurrentPosition();

        if ($oldCountryId !== (int) $state->country_id) {
            State::compactSiblings($oldCountryId);
        }

        return redirect()
            ->route('admin.directory.states.index')
            ->with('toast', ['type' => 'success', 'message' => 'State updated.']);
    }

    public function destroy(State $state): RedirectResponse
    {
        $countryId = (int) $state->country_id;
        $state->delete();
        State::compactSiblings($countryId);

        return redirect()
            ->route('admin.directory.states.index')
            ->with('toast', ['type' => 'success', 'message' => 'State deleted.']);
    }

    public function reorder(ReorderStatesRequest $request): RedirectResponse
    {
        /** @var array{country_id: int, ordered_ids: array<int, int>} $data */
        $data = $request->validated();
        $countryId = (int) $data['country_id'];
        $orderedIds = $data['ordered_ids'];

        $validIds = State::query()
            ->where('country_id', $countryId)
            ->whereIn('id', $orderedIds)
            ->pluck('id')
            ->all();

        if (count($validIds) !== count($orderedIds)) {
            return back()->with('toast', [
                'type' => 'error',
                'message' => 'Reorder rejected: some states do not belong to the selected country.',
            ]);
        }

        DB::transaction(function () use ($orderedIds): void {
            foreach ($orderedIds as $index => $id) {
                State::query()
                    ->where('id', $id)
                    ->update(['sort_order' => $index + 1]);
            }
        });

        return back()->with('toast', ['type' => 'success', 'message' => 'Order updated.']);
    }

    public function bulkAction(BulkActionStatesRequest $request): RedirectResponse
    {
        /** @var array{action: string, ids: array<int, int>} $data */
        $data = $request->validated();
        $action = $data['action'];
        $ids = $data['ids'];

        $count = DB::transaction(function () use ($action, $ids): int {
            if ($action === 'delete') {
                return State::query()->whereIn('id', $ids)->delete();
            }

            $update = match ($action) {
                'publish' => ['status' => 'published'],
                'draft' => ['status' => 'draft'],
                'inactive' => ['status' => 'inactive'],
                default => [],
            };

            return State::query()->whereIn('id', $ids)->update($update);
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
            'message' => "{$count} state".($count === 1 ? '' : 's')." {$verb}.",
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    private function presentState(State $state): array
    {
        return [
            'id' => $state->id,
            'country_id' => $state->country_id,
            'country_name' => $state->country?->name,
            'country_iso' => $state->country?->iso_code,
            'name' => $state->name,
            'code' => $state->code,
            'permalink' => $state->permalink,
            'status' => $state->status,
            'sort_order' => $state->sort_order,
            'cities_count' => $state->cities_count ?? 0,
            'created_at' => $state->created_at?->toIso8601String(),
            'updated_at' => $state->updated_at?->toIso8601String(),
        ];
    }

    /**
     * @return array<int, array{id: int, name: string, iso_code: string}>
     */
    private function presentCountries(): array
    {
        return Country::query()
            ->where('status', 'published')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get(['id', 'name', 'iso_code'])
            ->map(fn (Country $c): array => [
                'id' => $c->id,
                'name' => $c->name,
                'iso_code' => $c->iso_code,
            ])
            ->values()
            ->all();
    }
}
