<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin\Directory;

use App\Actions\Admin\Directory\City\ExportCitiesToCsv;
use App\Actions\Admin\Directory\City\ImportCitiesFromCsv;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Directory\City\BulkActionCitiesRequest;
use App\Http\Requests\Admin\Directory\City\ImportCitiesRequest;
use App\Http\Requests\Admin\Directory\City\ReorderCitiesRequest;
use App\Http\Requests\Admin\Directory\City\StoreCityRequest;
use App\Http\Requests\Admin\Directory\City\UpdateCityRequest;
use App\Models\City;
use App\Models\Country;
use App\Models\State;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;
use RuntimeException;
use Symfony\Component\HttpFoundation\StreamedResponse;

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

        /** @var City $city */
        $city = City::query()->create($data);
        $city->reorderToCurrentPosition();

        return redirect()
            ->route('admin.directory.cities.index')
            ->with('toast', ['type' => 'success', 'message' => __('admin.locations.city_created_toast')]);
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
        $oldStateId = (int) $city->state_id;
        $city->update($data);
        $city->reorderToCurrentPosition();

        if ($oldStateId !== (int) $city->state_id) {
            City::compactSiblings($oldStateId);
        }

        return redirect()
            ->route('admin.directory.cities.index')
            ->with('toast', ['type' => 'success', 'message' => __('admin.locations.city_updated_toast')]);
    }

    public function destroy(City $city): RedirectResponse
    {
        $stateId = (int) $city->state_id;
        $city->delete();
        City::compactSiblings($stateId);

        return redirect()
            ->route('admin.directory.cities.index')
            ->with('toast', ['type' => 'success', 'message' => __('admin.locations.city_deleted_toast')]);
    }

    public function reorder(ReorderCitiesRequest $request): RedirectResponse
    {
        /** @var array{state_id: int, ordered_ids: array<int, int>} $data */
        $data = $request->validated();
        $stateId = (int) $data['state_id'];
        $orderedIds = $data['ordered_ids'];

        $validIds = City::query()
            ->where('state_id', $stateId)
            ->whereIn('id', $orderedIds)
            ->pluck('id')
            ->all();
        if (count($validIds) !== count($orderedIds)) {
            return back()->with('toast', [
                'type' => 'error',
                'message' => __('admin.locations.city_reorder_rejected_toast'),
            ]);
        }

        DB::transaction(function () use ($orderedIds): void {
            foreach ($orderedIds as $index => $id) {
                City::query()
                    ->where('id', $id)
                    ->update(['sort_order' => $index + 1]);
            }
        });

        return back()->with('toast', ['type' => 'success', 'message' => __('admin.locations.city_order_updated_toast')]);
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

        $key = match ($action) {
            'delete' => 'admin.locations.city_bulk_deleted_toast',
            'publish' => 'admin.locations.city_bulk_published_toast',
            'draft' => 'admin.locations.city_bulk_draft_toast',
            'inactive' => 'admin.locations.city_bulk_inactive_toast',
            'mark_popular' => 'admin.locations.city_bulk_marked_popular_toast',
            'unmark_popular' => 'admin.locations.city_bulk_unmarked_popular_toast',
            default => 'admin.locations.city_bulk_updated_toast',
        };

        return back()->with('toast', [
            'type' => 'success',
            'message' => trans_choice($key, $count, ['count' => $count]),
        ]);
    }
    public function export(Request $request, ExportCitiesToCsv $action): StreamedResponse
    {
        $countryId = $request->query('country_id');
        $countryId = is_numeric($countryId) ? (int) $countryId : null;
        $stateId = $request->query('state_id');
        $stateId = is_numeric($stateId) ? (int) $stateId : null;
        $popular = $request->query('popular');

        return $action->handle([
            'country_id' => $countryId,
            'state_id' => $stateId,
            'popular' => is_string($popular) ? $popular : null,
            'q' => mb_trim((string) $request->query('q', '')),
        ]);
    }
    public function sampleCsv(ExportCitiesToCsv $action): StreamedResponse
    {
        return $action->sample();
    }
    public function import(ImportCitiesRequest $request, ImportCitiesFromCsv $action): RedirectResponse
    {
        /** @var UploadedFile $file */
        $file = $request->file('file');

        try {
            $result = $action->handle($file);
        } catch (RuntimeException $e) {
            return back()->with('toast', ['type' => 'error', 'message' => $e->getMessage()]);
        }

        $type = $result->hasErrors() ? 'warning' : 'success';
        $summary = $result->hasErrors()
            ? __('admin.locations.import_toast_summary_with_skipped', [
                'created' => $result->created,
                'updated' => $result->updated,
                'skipped' => $result->skipped,
            ])
            : __('admin.locations.import_toast_summary', [
                'created' => $result->created,
                'updated' => $result->updated,
            ]);

        return redirect()
            ->route('admin.directory.cities.index')
            ->with('toast', ['type' => $type, 'message' => $summary])
            ->with('importResult', $result->toArray());
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
