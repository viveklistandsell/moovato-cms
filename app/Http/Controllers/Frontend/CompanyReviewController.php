<?php

declare(strict_types=1);

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Http\Requests\Frontend\StoreCompanyReviewRequest;
use App\Models\Company;
use App\Models\CompanyReview;
use App\Models\CompanyTranslation;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Inertia\Inertia;
use Inertia\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Public "Write a review" flow.
 *
 * Endpoints (both DE root and /{locale}/ prefix variants):
 *   GET  /company/{slug}/review          → form
 *   POST /company/{slug}/review          → submit (throttled)
 *   GET  /company/{slug}/review/thanks   → confirmation
 *
 * Anti-spam is layered:
 *   1. Route-level throttle (5 submissions per hour per IP)
 *   2. Honeypot field in FormRequest — bots trip it
 *   3. `ip_address` + `user_agent` logged for audit / rate-limit tuning
 */
final class CompanyReviewController extends Controller
{
    public function create(Request $request): Response
    {
        $locale = App::getLocale();
        $slug = (string) $request->route('slug');
        $company = $this->resolveCompanyBySlug($slug);

        return Inertia::render('frontend/companies/ReviewNew', [
            'locale' => $locale,
            'company' => $this->presentCompanyForForm($company, $locale),
            'tags' => StoreCompanyReviewRequest::ALLOWED_TAGS,
            'sources' => StoreCompanyReviewRequest::ALLOWED_SOURCES,
        ]);
    }

    public function store(StoreCompanyReviewRequest $request): RedirectResponse
    {
        $slug = (string) $request->route('slug');
        $company = $this->resolveCompanyBySlug($slug);
        $data = $request->validated();

        $advantages = array_values(array_unique(array_map('strval', $data['advantages'] ?? [])));
        $disadvantages = array_values(array_unique(array_map('strval', $data['disadvantages'] ?? [])));
        $proofPath = null;
        if ($request->hasFile('proof_document')) {
            $proofPath = $request->file('proof_document')->store('review-proofs', 'local');
        }

        $name = mb_trim((string) $data['author_name']);
        CompanyReview::query()->create([
            'company_id' => $company->id,
            'user_id' => $request->user()?->id,
            'author_name' => $name,
            'author_email' => (string) $data['author_email'],
            'author_initials' => CompanyReview::initialsFor($name),
            'is_anonymous' => (bool) ($data['is_anonymous'] ?? false),
            'rating' => (int) $data['rating'],
            'body' => (string) $data['body'],
            'advantages' => $advantages,
            'disadvantages' => $disadvantages,
            'source' => $data['source'] ?? null,
            'proof_document' => $proofPath,
            'status' => 'published',
            'ip_address' => (string) $request->ip(),
            'user_agent' => mb_substr((string) $request->userAgent(), 0, 255),
            'published_at' => now(),
        ]);

        return redirect()->route(
            $this->routeName($request, 'companies.review.thanks'),
            $this->routeParams($request, ['slug' => $slug]),
        );
    }

    public function thanks(Request $request): Response
    {
        $locale = App::getLocale();
        $slug = (string) $request->route('slug');
        $company = $this->resolveCompanyBySlug($slug);

        return Inertia::render('frontend/companies/ReviewThanks', [
            'locale' => $locale,
            'company' => $this->presentCompanyForForm($company, $locale),
        ]);
    }
    public function helpful(Request $request, CompanyReview $review): RedirectResponse
    {
        if ($review->status !== 'published') {
            throw new NotFoundHttpException;
        }

        $marked = (array) $request->session()->get('helpful_reviews', []);
        if (! in_array($review->id, $marked, true)) {
            $review->increment('helpful_count');
            $marked[] = (int) $review->id;
            if (count($marked) > 200) {
                $marked = array_slice($marked, -200);
            }
            $request->session()->put('helpful_reviews', $marked);
        }

        return back(303);
    }

    /* ------------------------------------------------ helpers */
    private function resolveCompanyBySlug(string $slug): Company
    {
        $locale = App::getLocale();
        $translation = CompanyTranslation::query()
            ->where('permalink', $slug)
            ->orderByRaw('CASE WHEN lang = ? THEN 0 ELSE 1 END', [$locale])
            ->first();

        if ($translation === null) {
            throw new NotFoundHttpException;
        }

        $company = Company::query()
            ->where('id', $translation->company_id)
            ->where('status', 'published')
            ->with([
                'primaryCity:id,name',
                'translations',
                'contacts',
            ])
            ->first();

        if ($company === null) {
            throw new NotFoundHttpException;
        }

        return $company;
    }

    /**
     * @return array<string, mixed>
     */
    private function presentCompanyForForm(Company $company, string $locale): array
    {
        $t = $company->translations->firstWhere('lang', $locale) ?? $company->translations->first();
        $primaryContact = $company->contacts->firstWhere('is_primary', true) ?? $company->contacts->first();
        $primaryPhone = $company->contacts->firstWhere('type', 'phone');
        $primaryEmail = $company->contacts->firstWhere('type', 'email');
        $primaryWebsite = $company->contacts->firstWhere('type', 'website');

        return [
            'id' => $company->id,
            'name' => $t?->name ?? '—',
            'permalink' => $t?->permalink,
            'logo' => $company->logo,
            'street' => $company->street,
            'postal_code' => $company->postal_code,
            'city_name' => $company->primaryCity?->name,
            'phone' => $primaryPhone?->value,
            'email' => $primaryEmail?->value,
            'website' => $primaryWebsite?->value,
            'primary_contact' => $primaryContact?->value,
        ];
    }

    private function routeName(Request $request, string $suffix): string
    {
        $current = (string) ($request->route()?->getName() ?? '');

        return str_starts_with($current, 'localized.')
            ? "localized.{$suffix}"
            : $suffix;
    }

    /**
     * @param  array<string, mixed>  $params
     * @return array<string, mixed>
     */
    private function routeParams(Request $request, array $params): array
    {
        $current = (string) ($request->route()?->getName() ?? '');
        if (str_starts_with($current, 'localized.')) {
            $params['locale'] = App::getLocale();
        }

        return $params;
    }
}
