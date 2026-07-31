<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin\Directory;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Directory\Country\BulkActionCountriesRequest;
use App\Http\Requests\Admin\Directory\Country\ReorderCountriesRequest;
use App\Http\Requests\Admin\Directory\Country\StoreCountryRequest;
use App\Http\Requests\Admin\Directory\Country\UpdateCountryRequest;
use App\Models\Country;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

final class CountryController extends Controller
{
    private const SORTABLE_COLUMNS = [
        'sort_order' => 'sort_order',
        'name' => 'name',
        'iso_code' => 'iso_code',
        'status' => 'status',
        'states' => 'states_count',
        'created_at' => 'created_at',
    ];

    public function index(Request $request): Response
    {
        $search = mb_trim((string) $request->query('q', ''));
        $sortBy = $request->query('sort_by');
        $sortDir = $request->query('sort_dir', 'asc') === 'desc' ? 'desc' : 'asc';
        $perPage = max(5, min(100, (int) $request->query('per_page', '10')));

        $query = Country::query()->withCount('states');

        if ($search !== '') {
            $query->where(function (Builder $q) use ($search): void {
                $like = "%{$search}%";
                $q->where('name', 'like', $like)
                    ->orWhere('iso_code', 'like', $like)
                    ->orWhere('phone_code', 'like', $like);
            });
        }

        if (is_string($sortBy) && array_key_exists($sortBy, self::SORTABLE_COLUMNS)) {
            $query->orderBy(self::SORTABLE_COLUMNS[$sortBy], $sortDir);
        } else {
            $query->orderBy('sort_order')->orderBy('name');
        }

        $paginated = $query->paginate($perPage)->withQueryString();

        $countries = $paginated->getCollection()
            ->map(fn (Country $c): array => $this->presentCountry($c))
            ->values()
            ->all();

        return Inertia::render('admin/directory/countries/Index', [
            'countries' => $countries,
            'filters' => [
                'q' => $search,
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
        return Inertia::render('admin/directory/countries/Edit', [
            'country' => null,
            'nextSortOrder' => Country::nextSortOrder(),
        ]);
    }

    public function store(StoreCountryRequest $request): RedirectResponse
    {
        /** @var array<string, mixed> $data */
        $data = $request->validated();
        $data['sort_order'] = $data['sort_order'] ?? Country::nextSortOrder();

        /** @var Country $country */
        $country = Country::query()->create($data);
        $country->reorderToCurrentPosition();

        return redirect()
            ->route('admin.directory.countries.index')
            ->with('toast', ['type' => 'success', 'message' => 'Country created.']);
    }

    public function edit(Country $country): Response
    {
        return Inertia::render('admin/directory/countries/Edit', [
            'country' => $this->presentCountry($country->loadCount('states')),
            'nextSortOrder' => $country->sort_order,
        ]);
    }

    public function update(UpdateCountryRequest $request, Country $country): RedirectResponse
    {
        /** @var array<string, mixed> $data */
        $data = $request->validated();
        $country->update($data);
        $country->reorderToCurrentPosition();

        return redirect()
            ->route('admin.directory.countries.index')
            ->with('toast', ['type' => 'success', 'message' => 'Country updated.']);
    }

    public function destroy(Country $country): RedirectResponse
    {
        $country->delete();
        Country::compactSiblings();

        return redirect()
            ->route('admin.directory.countries.index')
            ->with('toast', ['type' => 'success', 'message' => 'Country deleted.']);
    }

    public function reorder(ReorderCountriesRequest $request): RedirectResponse
    {
        /** @var array{ordered_ids: array<int, int>} $data */
        $data = $request->validated();
        $orderedIds = $data['ordered_ids'];

        DB::transaction(function () use ($orderedIds): void {
            foreach ($orderedIds as $index => $id) {
                Country::query()
                    ->where('id', $id)
                    ->update(['sort_order' => $index + 1]);
            }
        });

        return back()->with('toast', ['type' => 'success', 'message' => 'Order updated.']);
    }

    public function bulkAction(BulkActionCountriesRequest $request): RedirectResponse
    {
        /** @var array{action: string, ids: array<int, int>} $data */
        $data = $request->validated();
        $action = $data['action'];
        $ids = $data['ids'];

        $count = DB::transaction(function () use ($action, $ids): int {
            if ($action === 'delete') {
                return Country::query()->whereIn('id', $ids)->delete();
            }

            $update = match ($action) {
                'publish' => ['status' => 'published'],
                'draft' => ['status' => 'draft'],
                'inactive' => ['status' => 'inactive'],
                default => [],
            };

            return Country::query()->whereIn('id', $ids)->update($update);
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
            'message' => "{$count} ".($count === 1 ? 'country' : 'countries')." {$verb}.",
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    private function presentCountry(Country $country): array
    {
        return [
            'id' => $country->id,
            'name' => $country->name,
            'iso_code' => $country->iso_code,
            'phone_code' => $country->phone_code,
            'status' => $country->status,
            'sort_order' => $country->sort_order,
            'states_count' => $country->states_count ?? 0,
            'created_at' => $country->created_at?->toIso8601String(),
            'updated_at' => $country->updated_at?->toIso8601String(),
        ];
    }
}
