<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin\Companies;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Companies\Company\BulkActionCompaniesRequest;
use App\Http\Requests\Admin\Companies\Company\ReorderCompaniesRequest;
use App\Http\Requests\Admin\Companies\Company\StoreCompanyRequest;
use App\Http\Requests\Admin\Companies\Company\UpdateCompanyRequest;
use App\Models\City;
use App\Models\Company;
use App\Models\CompanyFaq;
use App\Models\CompanyFaqTranslation;
use App\Models\Country;
use App\Models\District;
use App\Models\Language;
use App\Models\ServiceCategory;
use App\Models\ServiceParentCategory;
use App\Models\State;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

/**
 * Admin CRUD for the Companies (business-listing) entity.
 *
 * Companies are the glue between Services (what they do) and Locations
 * (where they work). Every listing page on the public site intersects
 * these three sets — so this controller keeps the pivots (services,
 * service_areas), the translation sidecar, and the per-company FAQ
 * repeater all consistent inside a single DB transaction.
 */
final class CompanyController extends Controller
{
    private const SORTABLE_COLUMNS = [
        'sort_order' => 'sort_order',
        'city' => 'city',
        'rating_avg' => 'rating_avg',
        'review_count' => 'review_count',
        'status' => 'status',
        'created_at' => 'created_at',
    ];

    public function index(Request $request): Response
    {
        $search = mb_trim((string) $request->query('q', ''));
        $countryId = self::intQuery($request, 'country_id');
        $stateId = self::intQuery($request, 'state_id');
        $cityId = self::intQuery($request, 'city_id');
        $districtId = self::intQuery($request, 'district_id');
        $serviceId = self::intQuery($request, 'service_id');
        $verified = $request->query('verified');
        $topRated = $request->query('top_rated');
        $sortBy = $request->query('sort_by');
        $sortDir = $request->query('sort_dir', 'asc') === 'desc' ? 'desc' : 'asc';
        $perPage = max(5, min(100, (int) $request->query('per_page', '10')));

        $query = Company::query()
            ->with([
                'primaryCity:id,state_id,name,permalink',
                'primaryCity.state:id,country_id,name,code',
                'primaryCity.state.country:id,name,iso_code',
                'primaryDistrict:id,city_id,name,permalink',
                'translations:id,company_id,lang,name,permalink',
            ]);

        // cascading location filter — most-specific wins
        if ($districtId !== null) {
            $query->whereHas('serviceAreas', fn (Builder $q) => $q->where('districts.id', $districtId));
        } elseif ($cityId !== null) {
            $query->where('primary_city_id', $cityId);
        } elseif ($stateId !== null) {
            $query->whereHas('primaryCity', fn (Builder $q) => $q->where('state_id', $stateId));
        } elseif ($countryId !== null) {
            $query->whereHas('primaryCity.state', fn (Builder $q) => $q->where('country_id', $countryId));
        }

        if ($serviceId !== null) {
            $query->whereHas('services', fn (Builder $q) => $q->where('service_categories.id', $serviceId));
        }

        if ($verified === '1') {
            $query->where('verified', true);
        } elseif ($verified === '0') {
            $query->where('verified', false);
        }
        if ($topRated === '1') {
            $query->where('is_top_rated', true);
        } elseif ($topRated === '0') {
            $query->where('is_top_rated', false);
        }

        if ($search !== '') {
            $like = "%{$search}%";
            $query->where(function (Builder $q) use ($like): void {
                $q->whereHas('translations', fn (Builder $qq) => $qq
                    ->where('name', 'like', $like)
                    ->orWhere('permalink', 'like', $like))
                    ->orWhere('street', 'like', $like)
                    ->orWhere('postal_code', 'like', $like);
            });
        }

        if (is_string($sortBy) && array_key_exists($sortBy, self::SORTABLE_COLUMNS)) {
            if ($sortBy === 'city') {
                $query->orderBy(
                    City::query()->select('name')->whereColumn('id', 'companies.primary_city_id'),
                    $sortDir,
                );
            } else {
                $query->orderBy(self::SORTABLE_COLUMNS[$sortBy], $sortDir);
            }
        } else {
            $query->orderBy('sort_order');
        }

        $paginated = $query->paginate($perPage)->withQueryString();

        $companies = $paginated->getCollection()
            ->map(fn (Company $c): array => $this->presentCompany($c))
            ->values()
            ->all();

        return Inertia::render('admin/companies/Index', [
            'companies' => $companies,
            'countries' => fn (): array => $this->presentCountries(),
            'states' => fn (): array => $this->presentStates($countryId),
            'cities' => fn (): array => $this->presentCities($stateId),
            'districts' => fn (): array => $this->presentDistricts($cityId),
            'services' => fn (): array => $this->presentServiceCategories(),
            'filters' => [
                'q' => $search,
                'country_id' => $countryId,
                'state_id' => $stateId,
                'city_id' => $cityId,
                'district_id' => $districtId,
                'service_id' => $serviceId,
                'verified' => is_string($verified) ? $verified : null,
                'top_rated' => is_string($topRated) ? $topRated : null,
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
        return Inertia::render('admin/companies/Edit', [
            'company' => null,
            'languages' => $this->presentLanguages(),
            'countries' => $this->presentCountries(),
            'states' => $this->presentStates(null),
            'cities' => $this->presentCities(null),
            'districts' => $this->presentDistricts(null),
            'parentCategories' => $this->presentParentCategories(),
            'serviceCategories' => $this->presentServiceCategories(),
            'nextSortOrder' => Company::nextSortOrder(),
        ]);
    }

    public function store(StoreCompanyRequest $request): RedirectResponse
    {
        /** @var array<string, mixed> $data */
        $data = $request->validated();

        DB::transaction(function () use ($data): Company {
            $data['sort_order'] = $data['sort_order'] ?? Company::nextSortOrder();
            $data['verified'] = (bool) ($data['verified'] ?? false);
            $data['is_top_rated'] = (bool) ($data['is_top_rated'] ?? false);

            /** @var Company $company */
            $company = Company::query()->create($this->companyAttributes($data));
            $company->reorderToCurrentPosition();

            $this->syncTranslations($company, $data['translations'] ?? []);
            $this->syncContacts($company, $data['contacts'] ?? []);
            $this->syncServices($company, $data['services'] ?? []);
            $this->syncServiceAreas($company, $data['service_areas'] ?? []);
            $this->syncMedia($company, $data['media'] ?? []);
            $this->syncFaqs($company, $data['faqs'] ?? []);

            return $company;
        });

        return redirect()
            ->route('admin.companies.index')
            ->with('toast', ['type' => 'success', 'message' => __('admin.companies.company_created_toast')]);
    }

    public function edit(Company $company): Response
    {
        $company->load([
            'primaryCity:id,state_id,name,permalink',
            'primaryCity.state:id,country_id,name,code',
            'primaryDistrict:id,city_id,name,permalink',
            'translations',
            'contacts',
            'services',
            'serviceAreas.city:id,state_id,name,permalink',
            'media.mediaFile',
            'faqs.translations',
        ]);

        return Inertia::render('admin/companies/Edit', [
            'company' => $this->presentCompanyForEdit($company),
            'languages' => $this->presentLanguages(),
            'countries' => $this->presentCountries(),
            'states' => $this->presentStates(null),
            'cities' => $this->presentCities(null),
            'districts' => $this->presentDistricts(null),
            'parentCategories' => $this->presentParentCategories(),
            'serviceCategories' => $this->presentServiceCategories(),
            'nextSortOrder' => $company->sort_order,
        ]);
    }

    public function update(UpdateCompanyRequest $request, Company $company): RedirectResponse
    {
        /** @var array<string, mixed> $data */
        $data = $request->validated();

        DB::transaction(function () use ($data, $company): void {
            $data['verified'] = (bool) ($data['verified'] ?? false);
            $data['is_top_rated'] = (bool) ($data['is_top_rated'] ?? false);

            $company->update($this->companyAttributes($data));
            $company->reorderToCurrentPosition();

            $this->syncTranslations($company, $data['translations'] ?? []);
            $this->syncContacts($company, $data['contacts'] ?? []);
            $this->syncServices($company, $data['services'] ?? []);
            $this->syncServiceAreas($company, $data['service_areas'] ?? []);
            $this->syncMedia($company, $data['media'] ?? []);
            $this->syncFaqs($company, $data['faqs'] ?? []);
        });

        return redirect()
            ->route('admin.companies.index')
            ->with('toast', ['type' => 'success', 'message' => __('admin.companies.company_updated_toast')]);
    }

    public function destroy(Company $company): RedirectResponse
    {
        abort_unless($this->currentUserCan('companies.delete'), 403);
        $company->delete();
        Company::compactSiblings();

        return redirect()
            ->route('admin.companies.index')
            ->with('toast', ['type' => 'success', 'message' => __('admin.companies.company_deleted_toast')]);
    }

    public function reorder(ReorderCompaniesRequest $request): RedirectResponse
    {
        /** @var array{ordered_ids: array<int, int>} $data */
        $data = $request->validated();
        $orderedIds = $data['ordered_ids'];
        $validIds = Company::query()->whereIn('id', $orderedIds)->pluck('id')->all();
        if (count($validIds) !== count($orderedIds)) {
            return back()->with('toast', [
                'type' => 'error',
                'message' => __('admin.companies.company_reorder_rejected_toast'),
            ]);
        }

        DB::transaction(function () use ($orderedIds): void {
            foreach ($orderedIds as $index => $id) {
                Company::query()->where('id', $id)->update(['sort_order' => $index + 1]);
            }
        });

        return back()->with('toast', [
            'type' => 'success',
            'message' => __('admin.companies.company_order_updated_toast'),
        ]);
    }

    public function bulkAction(BulkActionCompaniesRequest $request): RedirectResponse
    {
        /** @var array{action: string, ids: array<int, int>} $data */
        $data = $request->validated();
        $ids = $data['ids'];
        $action = $data['action'];
        $count = count($ids);

        $updated = match ($action) {
            'delete' => tap(count($ids), fn () => Company::query()->whereIn('id', $ids)->delete()),
            default => Company::query()->whereIn('id', $ids)->update(match ($action) {
                'publish' => ['status' => 'published'],
                'draft' => ['status' => 'draft'],
                'inactive' => ['status' => 'inactive'],
                'mark_verified' => ['verified' => true],
                'unmark_verified' => ['verified' => false],
                'mark_top_rated' => ['is_top_rated' => true],
                'unmark_top_rated' => ['is_top_rated' => false],
                default => [],
            }),
        };

        if ($action === 'delete') {
            Company::compactSiblings();
        }

        $messageKey = 'admin.companies.company_bulk_'.$action.'_toast';

        return back()->with('toast', [
            'type' => 'success',
            'message' => trans_choice($messageKey, $updated, ['count' => $count]),
        ]);
    }

    private static function intQuery(Request $request, string $key): ?int
    {
        $v = $request->query($key);

        return is_numeric($v) ? (int) $v : null;
    }

    /* -----------------------------------------------------------------
     |  Sync helpers — nested arrays → child rows in one place
     * ----------------------------------------------------------------- */

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    private function companyAttributes(array $data): array
    {
        return collect($data)->only([
            'primary_city_id', 'primary_district_id', 'street', 'postal_code',
            'logo', 'cover', 'verified', 'is_top_rated', 'plan_tier',
            'google_rating', 'google_review_count',
            'founded_year', 'employee_count', 'status', 'sort_order',
        ])->all();
    }

    /**
     * @param  array<int, array<string, mixed>>  $translations
     */
    private function syncTranslations(Company $company, array $translations): void
    {
        $keepIds = [];
        foreach ($translations as $t) {
            $name = mb_trim((string) ($t['name'] ?? ''));
            $permalink = mb_trim((string) ($t['permalink'] ?? ''));
            if ($name === '' && $permalink === '') {
                continue;
            }

            $row = $company->translations()->updateOrCreate(
                ['lang' => (string) $t['lang']],
                [
                    'name' => $name,
                    'permalink' => $permalink,
                    'short_description' => $t['short_description'] ?? null,
                    'about' => $t['about'] ?? null,
                ],
            );
            $keepIds[] = $row->id;
        }
        $company->translations()->whereNotIn('id', $keepIds)->delete();
    }

    /**
     * @param  array<int, array<string, mixed>>  $contacts
     */
    private function syncContacts(Company $company, array $contacts): void
    {
        $company->contacts()->delete();
        foreach ($contacts as $index => $c) {
            if (empty($c['value'])) {
                continue;
            }
            $company->contacts()->create([
                'type' => (string) $c['type'],
                'value' => (string) $c['value'],
                'label' => $c['label'] ?? null,
                'is_primary' => (bool) ($c['is_primary'] ?? false),
                'sort_order' => $index + 1,
            ]);
        }
    }

    /**
     * @param  array<int, array<string, mixed>>  $services
     */
    private function syncServices(Company $company, array $services): void
    {
        $sync = [];
        foreach ($services as $s) {
            $sync[(int) $s['service_category_id']] = [
                'is_primary' => (bool) ($s['is_primary'] ?? false),
                'price_from' => $s['price_from'] ?? null,
                'price_unit' => $s['price_unit'] ?? null,
            ];
        }
        $company->services()->sync($sync);
    }

    /**
     * @param  array<int, array<string, mixed>>  $areas
     */
    private function syncServiceAreas(Company $company, array $areas): void
    {
        $sync = [];
        foreach ($areas as $a) {
            $sync[(int) $a['district_id']] = [
                'is_home_base' => (bool) ($a['is_home_base'] ?? false),
                'response_hours' => $a['response_hours'] ?? null,
            ];
        }
        $company->serviceAreas()->sync($sync);
    }

    /**
     * @param  array<int, array<string, mixed>>  $media
     */
    private function syncMedia(Company $company, array $media): void
    {
        $company->media()->delete();
        foreach ($media as $index => $m) {
            $company->media()->create([
                'media_file_id' => (int) $m['media_file_id'],
                'kind' => (string) $m['kind'],
                'sort_order' => $index + 1,
            ]);
        }
    }

    /**
     * @param  array<int, array<string, mixed>>  $faqs
     */
    private function syncFaqs(Company $company, array $faqs): void
    {
        $company->faqs()->each(fn (CompanyFaq $f) => $f->delete());
        foreach ($faqs as $index => $f) {
            $faq = $company->faqs()->create([
                'sort_order' => $index + 1,
                'status' => $f['status'] ?? 'published',
            ]);
            foreach ($f['translations'] ?? [] as $t) {
                CompanyFaqTranslation::query()->create([
                    'faq_id' => $faq->id,
                    'lang' => (string) $t['lang'],
                    'question' => (string) $t['question'],
                    'answer' => (string) $t['answer'],
                ]);
            }
        }
    }

    /* -----------------------------------------------------------------
     |  Presenters
     * ----------------------------------------------------------------- */

    /**
     * @return array<string, mixed>
     */
    private function presentCompany(Company $company): array
    {
        return [
            'id' => $company->id,
            'primary_city_id' => $company->primary_city_id,
            'primary_district_id' => $company->primary_district_id,
            'city_name' => $company->primaryCity?->name,
            'state_id' => $company->primaryCity?->state_id,
            'state_name' => $company->primaryCity?->state?->name,
            'state_code' => $company->primaryCity?->state?->code,
            'country_id' => $company->primaryCity?->state?->country_id,
            'country_iso' => $company->primaryCity?->state?->country?->iso_code,
            'district_name' => $company->primaryDistrict?->name,
            'logo' => $company->logo,
            'cover' => $company->cover,
            'verified' => (bool) $company->verified,
            'is_top_rated' => (bool) $company->is_top_rated,
            'plan_tier' => $company->plan_tier,
            'rating_avg' => (float) $company->rating_avg,
            'review_count' => (int) $company->review_count,
            'recommend_pct' => (int) $company->recommend_pct,
            'status' => $company->status,
            'sort_order' => (int) $company->sort_order,
            'translations' => $company->translations->map(fn ($t): array => [
                'lang' => $t->lang,
                'name' => $t->name,
                'permalink' => $t->permalink,
            ])->all(),
            'created_at' => $company->created_at?->toIso8601String(),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function presentCompanyForEdit(Company $company): array
    {
        return [
            'id' => $company->id,
            'primary_city_id' => $company->primary_city_id,
            'primary_district_id' => $company->primary_district_id,
            'state_id' => $company->primaryCity?->state_id,
            'country_id' => $company->primaryCity?->state?->country_id,
            'street' => $company->street,
            'postal_code' => $company->postal_code,
            'logo' => $company->logo,
            'cover' => $company->cover,
            'verified' => (bool) $company->verified,
            'is_top_rated' => (bool) $company->is_top_rated,
            'plan_tier' => $company->plan_tier,
            'google_rating' => $company->google_rating,
            'google_review_count' => (int) $company->google_review_count,
            'founded_year' => $company->founded_year,
            'employee_count' => $company->employee_count,
            'status' => $company->status,
            'sort_order' => (int) $company->sort_order,
            'translations' => $company->translations->map(fn ($t): array => [
                'lang' => $t->lang,
                'name' => $t->name,
                'permalink' => $t->permalink,
                'short_description' => $t->short_description,
                'about' => $t->about,
            ])->values()->all(),
            'contacts' => $company->contacts->map(fn ($c): array => [
                'type' => $c->type,
                'value' => $c->value,
                'label' => $c->label,
                'is_primary' => (bool) $c->is_primary,
            ])->values()->all(),
            'services' => $company->services->map(fn ($s): array => [
                'service_category_id' => $s->id,
                'name' => $s->translation()?->name ?? '',
                'parent_category_id' => $s->parent_category_id,
                'is_primary' => (bool) ($s->pivot->is_primary ?? false),
                'price_from' => $s->pivot->price_from,
                'price_unit' => $s->pivot->price_unit,
            ])->values()->all(),
            'service_areas' => $company->serviceAreas->map(fn ($d): array => [
                'district_id' => $d->id,
                'district_name' => $d->name,
                'city_id' => $d->city_id,
                'city_name' => $d->city?->name,
                'is_home_base' => (bool) ($d->pivot->is_home_base ?? false),
                'response_hours' => $d->pivot->response_hours,
            ])->values()->all(),
            'media' => $company->media->map(fn ($m): array => [
                'media_file_id' => $m->media_file_id,
                'kind' => $m->kind,
                'url' => $m->mediaFile?->url,
                'name' => $m->mediaFile?->original_name,
            ])->values()->all(),
            'faqs' => $company->faqs->map(fn ($f): array => [
                'id' => $f->id,
                'status' => $f->status,
                'translations' => $f->translations->map(fn ($t): array => [
                    'lang' => $t->lang,
                    'question' => $t->question,
                    'answer' => $t->answer,
                ])->values()->all(),
            ])->values()->all(),
        ];
    }

    /**
     * @return array<int, array{code: string, name: string, native_name: string, flag: ?string, is_default: bool}>
     */
    private function presentLanguages(): array
    {
        return Language::query()
            ->where('status', true)
            ->orderBy('sort_order')
            ->get()
            ->map(fn (Language $l): array => [
                'code' => $l->code,
                'name' => $l->name,
                'native_name' => $l->native_name,
                'flag' => $l->flag,
                'is_default' => (bool) $l->lang_is_default,
            ])
            ->values()
            ->all();
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
        $q = State::query()->where('status', 'published');
        if ($countryId !== null) {
            $q->where('country_id', $countryId);
        }

        return $q->orderBy('name')
            ->get(['id', 'country_id', 'name', 'code'])
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
    private function presentCities(?int $stateId): array
    {
        $q = City::query()->where('status', 'published');
        if ($stateId !== null) {
            $q->where('state_id', $stateId);
        }

        return $q->orderBy('name')
            ->get(['id', 'state_id', 'name', 'permalink'])
            ->map(fn (City $c): array => [
                'id' => $c->id,
                'state_id' => $c->state_id,
                'name' => $c->name,
                'permalink' => $c->permalink,
            ])
            ->values()
            ->all();
    }

    /**
     * @return array<int, array{id: int, city_id: int, name: string, permalink: string}>
     */
    private function presentDistricts(?int $cityId): array
    {
        $q = District::query()->where('status', 'published');
        if ($cityId !== null) {
            $q->where('city_id', $cityId);
        }

        return $q->orderBy('sort_order')
            ->orderBy('name')
            ->get(['id', 'city_id', 'name', 'permalink'])
            ->map(fn (District $d): array => [
                'id' => $d->id,
                'city_id' => $d->city_id,
                'name' => $d->name,
                'permalink' => $d->permalink,
            ])
            ->values()
            ->all();
    }

    /**
     * @return array<int, array{id: int, name: string}>
     */
    private function presentParentCategories(): array
    {
        return ServiceParentCategory::query()
            ->where('status', 'published')
            ->with('translations:id,parent_category_id,lang,name')
            ->orderBy('sort_order')
            ->get()
            ->map(fn (ServiceParentCategory $p): array => [
                'id' => $p->id,
                'name' => $p->translation()?->name ?? '—',
            ])
            ->values()
            ->all();
    }

    /**
     * @return array<int, array{id: int, parent_category_id: int, name: string}>
     */
    private function presentServiceCategories(): array
    {
        return ServiceCategory::query()
            ->where('status', 'published')
            ->with('translations:id,category_id,lang,name')
            ->orderBy('sort_order')
            ->get(['id', 'parent_category_id'])
            ->map(fn (ServiceCategory $s): array => [
                'id' => $s->id,
                'parent_category_id' => $s->parent_category_id,
                'name' => $s->translation()?->name ?? '—',
            ])
            ->values()
            ->all();
    }

    private function currentUserCan(string $ability): bool
    {
        $user = request()->user();

        return $user !== null && $user->can($ability);
    }
}
