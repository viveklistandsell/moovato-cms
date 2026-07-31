<?php

declare(strict_types=1);

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\City;
use App\Models\Company;
use App\Models\CompanyTranslation;
use App\Models\District;
use App\Models\Language;
use App\Models\ServiceCategory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Inertia\Inertia;
use Inertia\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Public frontend for company listings + detail pages.
 *
 * URL structure:
 *   /companies                         → all companies
 *   /companies?city={id}&service={id}  → filtered
 *   /company/{slug}                    → detail page
 *
 * All queries respect the current locale for translated names / slugs.
 * The core listing query is the same "companies ⋈ services ⋈ areas"
 * intersection sketched in the roadmap.
 */
final class CompanyController extends Controller
{
    private const PER_PAGE = 12;

    private const SORT_OPTIONS = ['relevant', 'rating', 'newest'];

    public function index(Request $request): Response
    {
        $locale = App::getLocale();
        $cityId = self::intQuery($request, 'city');
        $districtId = self::intQuery($request, 'district');
        $serviceIds = self::intArrayQuery($request, 'services');
        if ($serviceIds === [] && ($legacy = self::intQuery($request, 'service')) !== null) {
            $serviceIds = [$legacy];
        }
        $verifiedOnly = $request->query('verified') === '1';
        $topRatedOnly = $request->query('top_rated') === '1';
        $minRating = (float) $request->query('min_rating', '0');
        $minReviews = (int) $request->query('min_reviews', '0');
        $priceMin = self::floatQuery($request, 'price_min');
        $priceMax = self::floatQuery($request, 'price_max');
        $sort = $request->query('sort');
        $sort = is_string($sort) && in_array($sort, self::SORT_OPTIONS, true) ? $sort : 'relevant';

        $query = Company::query()
            ->where('status', 'published')
            ->with([
                'primaryCity:id,state_id,name,permalink',
                'primaryCity.state:id,country_id,name,code',
                'translations',
                'contacts',
                'services.translations',
                'serviceAreas.city:id,name,permalink',
            ]);

        if ($districtId !== null) {
            $query->whereHas('serviceAreas', fn (Builder $q) => $q->where('districts.id', $districtId));
        } elseif ($cityId !== null) {
            $query->where('primary_city_id', $cityId);
        }
        if ($serviceIds !== []) {
            $query->whereHas('services', fn (Builder $q) => $q->whereIn('service_categories.id', $serviceIds));
        }
        if ($verifiedOnly) {
            $query->where('verified', true);
        }
        if ($topRatedOnly) {
            $query->where('is_top_rated', true);
        }
        if ($minRating > 0) {
            $query->where('rating_avg', '>=', $minRating);
        }
        if ($minReviews > 0) {
            $query->where('review_count', '>=', $minReviews);
        }
        if ($priceMin !== null || $priceMax !== null) {
            $query->whereHas('services', function (Builder $q) use ($priceMin, $priceMax): void {
                $q->whereNotNull('company_services.price_from');
                if ($priceMin !== null) {
                    $q->where('company_services.price_from', '>=', $priceMin);
                }
                if ($priceMax !== null) {
                    $q->where('company_services.price_from', '<=', $priceMax);
                }
            });
        }

        match ($sort) {
            'rating' => $query->orderByDesc('rating_avg')->orderByDesc('review_count'),
            'newest' => $query->orderByDesc('created_at'),
            default => $query
                ->orderByRaw("FIELD(plan_tier, 'gold', 'silver', 'free')")
                ->orderByDesc('is_top_rated')
                ->orderByDesc('rating_avg'),
        };

        $paginated = $query->paginate(self::PER_PAGE)->withQueryString();

        $companies = $paginated->getCollection()
            ->map(fn (Company $c): array => $this->presentCard($c, $locale))
            ->values()
            ->all();

        return Inertia::render('frontend/companies/Index', [
            'locale' => $locale,
            'companies' => $companies,
            'cities' => $this->presentCities(),
            'districts' => $this->presentDistricts(),
            'services' => $this->presentServices($locale),
            'activeCityId' => $cityId,
            'activeDistrictId' => $districtId,
            'activeServiceIds' => $serviceIds,
            'verifiedOnly' => $verifiedOnly,
            'topRatedOnly' => $topRatedOnly,
            'minRating' => $minRating,
            'minReviews' => $minReviews,
            'priceMin' => $priceMin,
            'priceMax' => $priceMax,
            'sort' => $sort,
            'pagination' => [
                'current_page' => $paginated->currentPage(),
                'last_page' => $paginated->lastPage(),
                'total' => $paginated->total(),
                'per_page' => $paginated->perPage(),
                'links' => $paginated->linkCollection()->toArray(),
            ],
        ]);
    }

    public function show(Request $request): Response
    {
        $locale = App::getLocale();
        $permalink = (string) $request->route('permalink');
        $translation = CompanyTranslation::query()
            ->where('permalink', $permalink)
            ->orderByRaw('CASE WHEN lang = ? THEN 0 ELSE 1 END', [$locale])
            ->first();

        if ($translation === null) {
            throw new NotFoundHttpException();
        }

        $company = Company::query()
            ->where('id', $translation->company_id)
            ->where('status', 'published')
            ->with([
                'translations',
                'primaryCity:id,state_id,name,permalink',
                'primaryCity.state:id,country_id,name,code',
                'primaryCity.state.country:id,name,iso_code',
                'primaryDistrict:id,city_id,name,permalink',
                'contacts',
                'services.translations',
                'services.parentCategory.translations',
                'serviceAreas.city:id,name,permalink',
                'media.mediaFile',
                'faqs' => fn ($q) => $q->where('status', 'published')->orderBy('sort_order'),
                'faqs.translations',
            ])
            ->firstOrFail();

        $defaultLang = Language::query()
            ->where('lang_is_default', true)
            ->where('status', true)
            ->value('code') ?? 'de';

        $localeAlternates = [];
        foreach ($company->translations as $t) {
            if (empty($t->permalink)) {
                continue;
            }
            $localeAlternates[$t->lang] = $t->lang === $defaultLang
                ? "/company/{$t->permalink}"
                : "/{$t->lang}/company/{$t->permalink}";
        }

        return Inertia::render('frontend/companies/Show', [
            'locale' => $locale,
            'company' => $this->presentDetail($company, $locale),
            // Shared with SiteHeader — see `switchHref` computed there.
            'localeAlternates' => $localeAlternates,
        ]);
    }

    private static function intQuery(Request $request, string $key): ?int
    {
        $v = $request->query($key);

        return is_numeric($v) ? (int) $v : null;
    }

    private static function floatQuery(Request $request, string $key): ?float
    {
        $v = $request->query($key);

        return is_numeric($v) ? (float) $v : null;
    }

    /**
     * Parses a query param as an array of ints. Accepts both the
     * `?services[]=1&services[]=2` bracket form and the comma-delimited
     * `?services=1,2` form so shareable URLs stay short.
     *
     * @return array<int, int>
     */
    private static function intArrayQuery(Request $request, string $key): array
    {
        $raw = $request->query($key);
        if ($raw === null || $raw === '') {
            return [];
        }

        $values = is_array($raw) ? $raw : explode(',', (string) $raw);

        return collect($values)
            ->filter(fn ($v) => is_numeric($v))
            ->map(fn ($v): int => (int) $v)
            ->unique()
            ->values()
            ->all();
    }

    /* ---------------------------------------------------------- presenters */

    /**
     * @return array<string, mixed>
     */
    private function presentCard(Company $c, string $locale): array
    {
        $t = $this->pickTranslation($c->translations, $locale);
        $primaryContact = $c->contacts->firstWhere('is_primary', true) ?? $c->contacts->first();
        $starred = $c->services->filter(fn ($s) => (bool) ($s->pivot->is_primary ?? false));
        $servicesForCard = ($starred->isEmpty() ? $c->services->take(3) : $starred)
            ->map(fn ($s) => $this->pickTranslation($s->translations, $locale)?->name)
            ->filter()
            ->values()
            ->all();
        $coverageCities = $c->serviceAreas
            ->map(fn ($d) => $d->city ? ['id' => $d->city->id, 'name' => $d->city->name] : null)
            ->filter()
            ->unique('id')
            ->values();
        if ($c->primaryCity !== null) {
            $coverageCities = $coverageCities
                ->reject(fn ($x) => $x['id'] === $c->primaryCity->id)
                ->prepend(['id' => $c->primaryCity->id, 'name' => $c->primaryCity->name])
                ->values();
        }
        $coverageCityNames = $coverageCities->pluck('name')->all();

        return [
            'id' => $c->id,
            'name' => $t?->name ?? '—',
            'permalink' => $t?->permalink,
            'short_description' => $t?->short_description,
            'logo' => $c->logo,
            'cover' => $c->cover,
            'verified' => (bool) $c->verified,
            'is_top_rated' => (bool) $c->is_top_rated,
            'plan_tier' => $c->plan_tier,
            'rating_avg' => (float) $c->rating_avg,
            'review_count' => (int) $c->review_count,
            'recommend_pct' => (int) $c->recommend_pct,
            'city_name' => $c->primaryCity?->name,
            'coverage_cities' => $coverageCityNames,
            'primary_services' => $servicesForCard,
            'primary_phone' => $primaryContact?->type === 'phone' ? $primaryContact->value : null,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function presentDetail(Company $c, string $locale): array
    {
        $t = $this->pickTranslation($c->translations, $locale);
        $galleryUrls = $c->media
            ->where('kind', 'gallery')
            ->sortBy('sort_order')
            ->map(fn ($m) => [
                'url' => $m->mediaFile?->url,
                'name' => $m->mediaFile?->original_name,
            ])
            ->values()
            ->all();

        $servicesGrouped = $c->services
            ->map(function ($s) use ($locale) {
                $st = $this->pickTranslation($s->translations, $locale);
                $pt = $s->parentCategory !== null
                    ? $this->pickTranslation($s->parentCategory->translations, $locale)
                    : null;

                return [
                    'id' => $s->id,
                    'name' => $st?->name ?? '—',
                    'parent_name' => $pt?->name,
                    'is_primary' => (bool) ($s->pivot->is_primary ?? false),
                    'price_from' => $s->pivot->price_from,
                    'price_unit' => $s->pivot->price_unit,
                ];
            })
            ->values()
            ->all();

        $areasByCity = [];
        foreach ($c->serviceAreas as $d) {
            $key = $d->city_id;
            $areasByCity[$key] = $areasByCity[$key] ?? [
                'city_id' => $d->city_id,
                'city_name' => $d->city?->name ?? '—',
                'districts' => [],
            ];
            $areasByCity[$key]['districts'][] = ['id' => $d->id, 'name' => $d->name];
        }

        return [
            'id' => $c->id,
            'name' => $t?->name ?? '—',
            'permalink' => $t?->permalink,
            'about' => $t?->about,
            'short_description' => $t?->short_description,
            'logo' => $c->logo,
            'cover' => $c->cover,
            'verified' => (bool) $c->verified,
            'is_top_rated' => (bool) $c->is_top_rated,
            'plan_tier' => $c->plan_tier,
            'rating_avg' => (float) $c->rating_avg,
            'review_count' => (int) $c->review_count,
            'recommend_pct' => (int) $c->recommend_pct,
            'rating_breakdown' => $c->rating_breakdown,
            'google_rating' => $c->google_rating,
            'google_review_count' => (int) $c->google_review_count,
            'founded_year' => $c->founded_year,
            'employee_count' => $c->employee_count,
            'street' => $c->street,
            'postal_code' => $c->postal_code,
            'city_name' => $c->primaryCity?->name,
            'district_name' => $c->primaryDistrict?->name,
            'state_name' => $c->primaryCity?->state?->name,
            'country_name' => $c->primaryCity?->state?->country?->name,
            'contacts' => $c->contacts->map(fn ($ct) => [
                'type' => $ct->type,
                'value' => $ct->value,
                'label' => $ct->label,
                'is_primary' => (bool) $ct->is_primary,
            ])->values()->all(),
            'services' => $servicesGrouped,
            'service_areas' => array_values($areasByCity),
            'gallery' => $galleryUrls,
            'faqs' => $c->faqs->map(function ($f) use ($locale) {
                $ft = $this->pickTranslation($f->translations, $locale);

                return [
                    'question' => $ft?->question ?? '',
                    'answer' => $ft?->answer ?? '',
                ];
            })->values()->all(),
        ];
    }

    /**
     * @return array<int, array{id: int, name: string}>
     */
    private function presentCities(): array
    {
        return City::query()
            ->where('status', 'published')
            ->orderBy('name')
            ->get(['id', 'name'])
            ->map(fn (City $c): array => ['id' => $c->id, 'name' => $c->name])
            ->all();
    }

    /**
     * All districts across all cities — the filter sidebar filters them
     * client-side by the chosen city, so we ship the full list once
     * instead of round-tripping on every city change.
     *
     * @return array<int, array{id: int, city_id: int, name: string}>
     */
    private function presentDistricts(): array
    {
        return District::query()
            ->where('status', 'published')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get(['id', 'city_id', 'name'])
            ->map(fn (District $d): array => [
                'id' => $d->id,
                'city_id' => $d->city_id,
                'name' => $d->name,
            ])
            ->all();
    }

    /**
     * @return array<int, array{id: int, name: string}>
     */
    private function presentServices(string $locale): array
    {
        return ServiceCategory::query()
            ->where('status', 'published')
            ->with('translations:id,category_id,lang,name')
            ->orderBy('sort_order')
            ->get(['id'])
            ->map(fn (ServiceCategory $s): array => [
                'id' => $s->id,
                'name' => $this->pickTranslation($s->translations, $locale)?->name ?? '—',
            ])
            ->all();
    }

    /**
     * Locale-first pick with graceful fallback to the first row — same
     * pattern used by every Model::translation() helper in this codebase.
     */
    private function pickTranslation($translations, string $locale)
    {
        return $translations->firstWhere('lang', $locale) ?? $translations->first();
    }
}
