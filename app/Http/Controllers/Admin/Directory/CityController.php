<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin\Directory;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Directory\City\BulkActionCitiesRequest;
use App\Http\Requests\Admin\Directory\City\StoreCityRequest;
use App\Http\Requests\Admin\Directory\City\UpdateCityRequest;
use App\Models\City;
use App\Models\Country;
use App\Models\State;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

final class CityController extends Controller
{
    private const SORTABLE_COLUMNS = [
        'sort_order' => 'sort_order',
        'name' => 'name',
        'state' => 'state',
        'postal_code' => 'postal_code',
        'status' => 'status',
        'popular' => 'is_popular',
        'created_at' => 'created_at',
    ];

    public function index(Request $request): Response
    {
        $search = mb_trim((string) $request->query('q', ''));
        $countryId = $request->query('country_id');
        $countryId = is_numeric($countryId) ? (int) $countryId : null;
        $stateId = $request->query('state_id');
        $stateId = is_numeric($stateId) ? (int) $stateId : null;
        $popular = $request->query('popular');
        $sortBy = $request->query('sort_by');
        $sortDir = $request->query('sort_dir', 'asc') === 'desc' ? 'desc' : 'asc';
        $perPage = max(5, min(100, (int) $request->query('per_page', '10')));

        $query = City::query()->with('state:id,country_id,name,code');

        if ($stateId !== null) {
            $query->where('state_id', $stateId);
        } elseif ($countryId !== null) {
            $query->whereHas('state', fn (Builder $q) => $q->where('country_id', $countryId));
        }

        if ($popular === '1') {
            $query->where('is_popular', true);
        } elseif ($popular === '0') {
            $query->where('is_popular', false);
        }

        if ($search !== '') {
            $query->where(function (Builder $q) use ($search): void {
                $like = "%{$search}%";
                $q->where('name', 'like', $like)
                    ->orWhere('permalink', 'like', $like)
                    ->orWhere('postal_code', 'like', $like);
            });
        }

        if (is_string($sortBy) && array_key_exists($sortBy, self::SORTABLE_COLUMNS)) {
            if ($sortBy === 'state') {
                $query->orderBy(
                    State::query()
                        ->select('name')
                        ->whereColumn('id', 'cities.state_id'),
                    $sortDir,
                );
            } else {
                $query->orderBy(self::SORTABLE_COLUMNS[$sortBy], $sortDir);
            }
        } else {
            $query->orderBy('state_id')->orderBy('sort_order');
        }

        $paginated = $query->paginate($perPage)->withQueryString();

        $cities = $paginated->getCollection()
            ->map(fn (City $c): array => $this->presentCity($c))
            ->values()
            ->all();

        return Inertia::render('admin/directory/cities/Index', [
            'cities' => $cities,
            'countries' => fn (): array => $this->presentCountries(),
            'states' => fn (): array => $this->presentStates($countryId),
            'filters' => [
                'q' => $search,
                'country_id' => $countryId,
                'state_id' => $stateId,
                'popular' => is_string($popular) ? $popular : null,
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
        return Inertia::render('admin/directory/cities/Edit', [
            'city' => null,
            'countries' => $this->presentCountries(),
            'states' => $this->presentStates(null),
            'nextSortOrder' => City::nextSortOrder(),
        ]);
    }

    public function store(StoreCityRequest $request): RedirectResponse
    {
        /** @var array<string, mixed> $data */
        $data = $request->validated();
        $data['sort_order'] = $data['sort_order'] ?? City::nextSortOrder((int) $data['state_id']);
        $data['is_popular'] = (bool) ($data['is_popular'] ?? false);

        City::query()->create($data);

        return redirect()
            ->route('admin.directory.cities.index')
            ->with('toast', ['type' => 'success', 'message' => 'City created.']);
    }

    public function edit(City $city): Response
    {
        $city->load('state:id,country_id,name,code');
        $countryId = $city->state?->country_id;

        return Inertia::render('admin/directory/cities/Edit', [
            'city' => $this->presentCity($city),
            'countries' => $this->presentCountries(),
            'states' => $this->presentStates($countryId),
            'nextSortOrder' => $city->sort_order,
        ]);
    }

    public function update(UpdateCityRequest $request, City $city): RedirectResponse
    {
        /** @var array<string, mixed> $data */
        $data = $request->validated();
        $data['is_popular'] = (bool) ($data['is_popular'] ?? false);
        $city->update($data);

        return redirect()
            ->route('admin.directory.cities.index')
            ->with('toast', ['type' => 'success', 'message' => 'City updated.']);
    }

    public function destroy(City $city): RedirectResponse
    {
        $city->delete();

        return redirect()
            ->route('admin.directory.cities.index')
            ->with('toast', ['type' => 'success', 'message' => 'City deleted.']);
    }

    public function bulkAction(BulkActionCitiesRequest $request): RedirectResponse
    {
        /** @var array{action: string, ids: array<int, int>} $data */
        $data = $request->validated();
        $action = $data['action'];
        $ids = $data['ids'];

        $count = DB::transaction(function () use ($action, $ids): int {
            if ($action === 'delete') {
                return City::query()->whereIn('id', $ids)->delete();
            }

            $update = match ($action) {
                'publish' => ['status' => 'published'],
                'draft' => ['status' => 'draft'],
                'inactive' => ['status' => 'inactive'],
                'mark_popular' => ['is_popular' => true],
                'unmark_popular' => ['is_popular' => false],
                default => [],
            };

            return City::query()->whereIn('id', $ids)->update($update);
        });

        $verb = match ($action) {
            'delete' => 'deleted',
            'publish' => 'published',
            'draft' => 'set to draft',
            'inactive' => 'deactivated',
            'mark_popular' => 'marked popular',
            'unmark_popular' => 'unmarked',
            default => 'updated',
        };

        return back()->with('toast', [
            'type' => 'success',
            'message' => "{$count} ".($count === 1 ? 'city' : 'cities')." {$verb}.",
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    private function presentCity(City $city): array
    {
        return [
            'id' => $city->id,
            'state_id' => $city->state_id,
            'state_name' => $city->state?->name,
            'state_code' => $city->state?->code,
            'country_id' => $city->state?->country_id,
            'name' => $city->name,
            'permalink' => $city->permalink,
            'postal_code' => $city->postal_code,
            'is_popular' => $city->is_popular,
            'status' => $city->status,
            'sort_order' => $city->sort_order,
            'created_at' => $city->created_at?->toIso8601String(),
            'updated_at' => $city->updated_at?->toIso8601String(),
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

    /**
     * @return array<int, array{id: int, country_id: int, name: string, code: string}>
     */
    private function presentStates(?int $countryId): array
    {
        $query = State::query()
            ->where('status', 'published')
            ->orderBy('sort_order')
            ->orderBy('name');

        if ($countryId !== null) {
            $query->where('country_id', $countryId);
        }

        return $query->get(['id', 'country_id', 'name', 'code'])
            ->map(fn (State $s): array => [
                'id' => $s->id,
                'country_id' => $s->country_id,
                'name' => $s->name,
                'code' => $s->code,
            ])
            ->values()
            ->all();
    }
}
