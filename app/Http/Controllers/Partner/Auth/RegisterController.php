<?php

declare(strict_types=1);

namespace App\Http\Controllers\Partner\Auth;

use App\Enums\PlanTier;
use App\Http\Controllers\Controller;
use App\Http\Requests\Partner\Auth\StorePartnerRegistrationRequest;
use App\Models\City;
use App\Models\Company;
use App\Models\CompanyUser;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

/**
 * Public register flow for partner (company) accounts.
 */
final class RegisterController extends Controller
{
    public function create(): Response
    {
        $locale = App::getLocale();

        return Inertia::render('partner/auth/Register', [
            'locale' => $locale,
            'cities' => $this->cityOptions(),
            'recaptchaSiteKey' => config('captcha.sitekey') ?: null,
            'plans' => array_map(
                fn (PlanTier $tier): array => $tier->toArray(),
                PlanTier::ordered(),
            ),
        ]);
    }

    public function store(StorePartnerRegistrationRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $docPaths = $this->storeDocs($request->file('documents') ?? []);

        $companyUser = DB::transaction(function () use ($data, $docPaths): CompanyUser {
            $city = City::query()->findOrFail((int) $data['city_id']);
            $chosenTier = PlanTier::tryFrom((string) ($data['plan_tier'] ?? '')) ?? PlanTier::Basic;

            $company = Company::query()->create([
                'primary_city_id' => $city->id,
                'primary_district_id' => null,
                'street' => $data['street'] ?? null,
                'postal_code' => $data['postal_code'] ?? null,
                'status' => 'draft',
                'sort_order' => Company::nextSortOrder(),
                'plan_tier' => $chosenTier->value,
            ]);
            $company->translations()->create([
                'lang' => 'de',
                'name' => (string) $data['company_name'],
                'permalink' => Str::slug((string) $data['company_name']),
            ]);

            return CompanyUser::query()->create([
                'company_id' => $company->id,
                'first_name' => mb_trim((string) $data['first_name']),
                'last_name' => mb_trim((string) $data['last_name']),
                'email' => mb_strtolower(mb_trim((string) $data['email'])),
                'phone' => isset($data['phone']) ? mb_trim((string) $data['phone']) : null,
                'password' => null,
                'status' => CompanyUser::STATUS_PENDING,
                'registration_docs' => $docPaths !== [] ? $docPaths : null,
            ]);
        });

        Log::info('New partner registration pending review', [
            'company_user_id' => $companyUser->id,
            'company_id' => $companyUser->company_id,
            'email' => $companyUser->email,
        ]);

        return redirect()->route('partner.register.thanks');
    }

    public function thanks(): Response
    {
        return Inertia::render('partner/auth/RegisterThanks', [
            'locale' => App::getLocale(),
        ]);
    }

    /* ---------------------------------------------- helpers */

    /**
     * Store each uploaded doc on the PRIVATE `local` disk under
     * `partner-registration-docs/`. Returns the array we'll persist
     * into `company_users.registration_docs`.
     *
     * @param  array<int, UploadedFile>  $files
     * @return array<int, array{name: string, path: string}>
     */
    private function storeDocs(array $files): array
    {
        $out = [];
        foreach ($files as $file) {
            if (! $file instanceof UploadedFile) {
                continue;
            }
            $out[] = [
                'name' => $file->getClientOriginalName(),
                'path' => $file->store('partner-registration-docs', 'local'),
            ];
        }

        return $out;
    }

    /**
     * Slim list of published cities for the register form dropdown.
     *
     * @return array<int, array<string, mixed>>
     */
    private function cityOptions(): array
    {
        return City::query()
            ->where('status', 'published')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get(['id', 'name'])
            ->map(fn (City $c): array => [
                'id' => (int) $c->id,
                'name' => (string) $c->name,
            ])
            ->values()
            ->all();
    }
}
