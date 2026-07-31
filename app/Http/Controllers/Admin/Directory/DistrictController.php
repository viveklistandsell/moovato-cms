<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin\Directory;

use App\Actions\Admin\Directory\District\ExportDistrictsToCsv;
use App\Actions\Admin\Directory\District\ImportDistrictsFromCsv;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Directory\District\BulkActionDistrictsRequest;
use App\Http\Requests\Admin\Directory\District\ImportDistrictsRequest;
use App\Http\Requests\Admin\Directory\District\ReorderDistrictsRequest;
use App\Http\Requests\Admin\Directory\District\StoreDistrictRequest;
use App\Http\Requests\Admin\Directory\District\UpdateDistrictRequest;
use App\Models\City;
use App\Models\Country;
use App\Models\District;
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

/**
 * Admin CRUD for the Districts geo tier (below City).
 *
 * Cascading filter design: admin picks Country → State → City to narrow
 * the district list. When no filter is set, shows all districts across
 * all cities so power users can bulk-manage without drilling in every
 * time. Matches the Cities admin's UX exactly.
 */
final class DistrictController extends Controller
{
    private const SORTABLE_COLUMNS = [
        'sort_order' => 'sort_order',
        'name' => 'name',
        'city' => 'city',
        'postal_code_prefix' => 'postal_code_prefix',
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
        $cityId = $request->query('city_id');
        $cityId = is_numeric($cityId) ? (int) $cityId : null;
        $popular = $request->query('popular');
        $sortBy = $request->query('sort_by');
        $sortDir = $request->query('sort_dir', 'asc') === 'desc' ? 'desc' : 'asc';
        $perPage = max(5, min(100, (int) $request->query('per_page', '10')));

        $query = District::query()
            ->with(
                'city:id,state_id,name,permalink',
                'city.state:id,country_id,name,code',
                'city.state.country:id,name,iso_code',
            );
        // Cascading filter: city > state > country. Most-specific wins.
        if ($cityId !== null) {
            $query->where('city_id', $cityId);
        } elseif ($stateId !== null) {
            $query->whereHas('city', fn (Builder $q) => $q->where('state_id', $stateId));
        } elseif ($countryId !== null) {
            $query->whereHas('city.state', fn (Builder $q) => $q->where('country_id', $countryId));
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
                    ->orWhere('code', 'like', $like)
                    ->orWhere('postal_code_prefix', 'like', $like);
            });
        }

        if (is_string($sortBy) && array_key_exists($sortBy, self::SORTABLE_COLUMNS)) {
            if ($sortBy === 'city') {
                $query->orderBy(
                    City::query()
                        ->select('name')
                        ->whereColumn('id', 'districts.city_id'),
                    $sortDir,
                );
            } else {
                $query->orderBy(self::SORTABLE_COLUMNS[$sortBy], $sortDir);
            }
        } else {
            $query->orderBy('city_id')->orderBy('sort_order');
        }

        $paginated = $query->paginate($perPage)->withQueryString();

        $districts = $paginated->getCollection()
            ->map(fn (District $d): array => $this->presentDistrict($d))
            ->values()
            ->all();

        return Inertia::render('admin/directory/districts/Index', [
            'districts' => $districts,
            'countries' => fn (): array => $this->presentCountries(),
            'states' => fn (): array => $this->presentStates($countryId),
            'cities' => fn (): array => $this->presentCities($stateId, $countryId),
            'filters' => [
                'q' => $search,
                'country_id' => $countryId,
                'state_id' => $stateId,
                'city_id' => $cityId,
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
        return Inertia::render('admin/directory/districts/Edit', [
            'district' => null,
            'countries' => $this->presentCountries(),
            'states' => $this->presentStates(null),
            'cities' => $this->presentCities(null, null),
            'nextSortOrder' => District::nextSortOrder(),
        ]);
    }

    public function store(StoreDistrictRequest $request): RedirectResponse
    {
        /** @var array<string, mixed> $data */
        $data = $request->validated();
        $data['sort_order'] = $data['sort_order'] ?? District::nextSortOrder((int) $data['city_id']);
        $data['is_popular'] = (bool) ($data['is_popular'] ?? false);

        /** @var District $district */
        $district = District::query()->create($data);
        $district->reorderToCurrentPosition();

        return redirect()
            ->route('admin.directory.districts.index')
            ->with('toast', ['type' => 'success', 'message' => __('admin.locations.district_created_toast')]);
    }

    public function edit(District $district): Response
    {
        $district->load('city:id,state_id,name,permalink', 'city.state:id,country_id,name,code');
        $stateId = $district->city?->state_id;
        $countryId = $district->city?->state?->country_id;

        return Inertia::render('admin/directory/districts/Edit', [
            'district' => $this->presentDistrict($district),
            'countries' => $this->presentCountries(),
            'states' => $this->presentStates($countryId),
            'cities' => $this->presentCities($stateId, $countryId),
            'nextSortOrder' => $district->sort_order,
        ]);
    }
    public function update(UpdateDistrictRequest $request, District $district): RedirectResponse
    {
        /** @var array<string, mixed> $data */
        $data = $request->validated();
        $data['is_popular'] = (bool) ($data['is_popular'] ?? false);
        $oldCityId = (int) $district->city_id;
        $district->update($data);
        $district->reorderToCurrentPosition();

        if ($oldCityId !== (int) $district->city_id) {
            District::compactSiblings($oldCityId);
        }

        return redirect()
            ->route('admin.directory.districts.index')
            ->with('toast', ['type' => 'success', 'message' => __('admin.locations.district_updated_toast')]);
    }
    public function destroy(District $district): RedirectResponse
    {
        $cityId = (int) $district->city_id;
        $district->delete();
        District::compactSiblings($cityId);

        return redirect()
            ->route('admin.directory.districts.index')
            ->with('toast', ['type' => 'success', 'message' => __('admin.locations.district_deleted_toast')]);
    }
    public function reorder(ReorderDistrictsRequest $request): RedirectResponse
    {
        /** @var array{city_id: int, ordered_ids: array<int, int>} $data */
        $data = $request->validated();
        $cityId = (int) $data['city_id'];
        $orderedIds = $data['ordered_ids'];

        $validIds = District::query()
            ->where('city_id', $cityId)
            ->whereIn('id', $orderedIds)
            ->pluck('id')
            ->all();
        if (count($validIds) !== count($orderedIds)) {
            return back()->with('toast', [
                'type' => 'error',
                'message' => __('admin.locations.district_reorder_rejected_toast'),
            ]);
        }

        DB::transaction(function () use ($orderedIds): void {
            foreach ($orderedIds as $index => $id) {
                District::query()
                    ->where('id', $id)
                    ->update(['sort_order' => $index + 1]);
            }
        });

        return back()->with('toast', ['type' => 'success', 'message' => __('admin.locations.district_order_updated_toast')]);
    }
    public function bulkAction(BulkActionDistrictsRequest $request): RedirectResponse
    {
        /** @var array{action: string, ids: array<int, int>} $data */
        $data = $request->validated();
        $action = $data['action'];
        $ids = $data['ids'];

        $count = DB::transaction(function () use ($action, $ids): int {
            if ($action === 'delete') {
                return District::query()->whereIn('id', $ids)->delete();
            }

            $update = match ($action) {
                'publish' => ['status' => 'published'],
                'draft' => ['status' => 'draft'],
                'inactive' => ['status' => 'inactive'],
                'mark_popular' => ['is_popular' => true],
                'unmark_popular' => ['is_popular' => false],
                default => [],
            };

            return District::query()->whereIn('id', $ids)->update($update);
        });

        $key = match ($action) {
            'delete' => 'admin.locations.district_bulk_deleted_toast',
            'publish' => 'admin.locations.district_bulk_published_toast',
            'draft' => 'admin.locations.district_bulk_draft_toast',
            'inactive' => 'admin.locations.district_bulk_inactive_toast',
            'mark_popular' => 'admin.locations.district_bulk_marked_popular_toast',
            'unmark_popular' => 'admin.locations.district_bulk_unmarked_popular_toast',
            default => 'admin.locations.district_bulk_updated_toast',
        };

        return back()->with('toast', [
            'type' => 'success',
            'message' => trans_choice($key, $count, ['count' => $count]),
        ]);
    }

    /**
     * Stream the districts list as a CSV download. Respects the same
     * country/state/city/popular/search filters the Index page uses so
     * admins can narrow the export before clicking Export.
     */
    public function export(Request $request, ExportDistrictsToCsv $action): StreamedResponse
    {
        $countryId = $request->query('country_id');
        $countryId = is_numeric($countryId) ? (int) $countryId : null;
        $stateId = $request->query('state_id');
        $stateId = is_numeric($stateId) ? (int) $stateId : null;
        $cityId = $request->query('city_id');
        $cityId = is_numeric($cityId) ? (int) $cityId : null;
        $popular = $request->query('popular');

        return $action->handle([
            'country_id' => $countryId,
            'state_id' => $stateId,
            'city_id' => $cityId,
            'popular' => is_string($popular) ? $popular : null,
            'q' => mb_trim((string) $request->query('q', '')),
        ]);
    }

    /**
     * Static template CSV — same headers as export, 5 example rows.
     */
    public function sampleCsv(ExportDistrictsToCsv $action): StreamedResponse
    {
        return $action->sample();
    }

    /**
     * Process a CSV upload. Partial-success semantics: good rows land
     * in the DB, bad rows are collected and flashed for the Vue Index
     * page's error panel.
     */
    public function import(ImportDistrictsRequest $request, ImportDistrictsFromCsv $action): RedirectResponse
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
            ->route('admin.directory.districts.index')
            ->with('toast', ['type' => $type, 'message' => $summary])
            ->with('importResult', $result->toArray());
    }

    /**
     * @return array<string, mixed>
     */
    private function presentDistrict(District $district): array
    {
        return [
            'id' => $district->id,
            'city_id' => $district->city_id,
            'city_name' => $district->city?->name,
            'city_permalink' => $district->city?->permalink,
            'state_id' => $district->city?->state_id,
            'state_name' => $district->city?->state?->name,
            'state_code' => $district->city?->state?->code,
            'country_id' => $district->city?->state?->country_id,
            'country_name' => $district->city?->state?->country?->name,
            'country_iso' => $district->city?->state?->country?->iso_code,
            'name' => $district->name,
            'code' => $district->code,
            'permalink' => $district->permalink,
            'postal_code_prefix' => $district->postal_code_prefix,
            'is_popular' => $district->is_popular,
            'status' => $district->status,
            'sort_order' => $district->sort_order,
            'created_at' => $district->created_at?->toIso8601String(),
            'updated_at' => $district->updated_at?->toIso8601String(),
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

    /**
     * @return array<int, array{id: int, state_id: int, name: string, permalink: string}>
     */
    private function presentCities(?int $stateId, ?int $countryId): array
    {
        $query = City::query()
            ->where('status', 'published')
            ->orderBy('name');

        if ($stateId !== null) {
            $query->where('state_id', $stateId);
        } elseif ($countryId !== null) {
            $query->whereHas('state', fn (Builder $q) => $q->where('country_id', $countryId));
        }

        return $query->get(['id', 'state_id', 'name', 'permalink'])
            ->map(fn (City $c): array => [
                'id' => $c->id,
                'state_id' => $c->state_id,
                'name' => $c->name,
                'permalink' => $c->permalink,
            ])
            ->values()
            ->all();
    }
}
