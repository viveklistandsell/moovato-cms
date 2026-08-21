<?php

declare(strict_types=1);

namespace App\Http\Controllers\Partner\Plans;

use App\Enums\PlanTier;
use App\Http\Controllers\Controller;
use App\Mail\PlanChangeRequested;
use App\Models\Company;
use App\Models\CompanyUser;
use App\Models\PlanChangeRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Inertia\Inertia;
use Inertia\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

/**
 * Partner-facing plan management.
 *
 *   GET    /partner/plans          → 3 tier cards, current tier
 *                                    highlighted, and — if one is open
 *                                    — the pending change request
 *   POST   /partner/plans/choose   → opens a plan-change request for
 *                                    admin approval (does NOT flip the
 *                                    tier — that happens on approval,
 *                                    see admin PlanChangeRequestController)
 *   DELETE /partner/plans/request  → partner cancels their own pending
 *                                    request
 *
 * The tier only changes after a Super Admin approves the request.
 * This matches the "admin approves plan changes" product decision
 * (no self-serve upgrades).
 */
final class PlanController extends Controller
{
    public function index(Request $request): Response
    {
        /** @var CompanyUser|null $user */
        $user = $request->user('company');
        $company = $user !== null ? $this->companyOf($user) : null;

        $pending = $company === null
            ? null
            : PlanChangeRequest::query()
                ->forCompany((int) $company->id)
                ->pending()
                ->latest('created_at')
                ->first();

        return Inertia::render('partner/plans/Index', [
            'locale' => App::getLocale(),
            'user' => $user === null ? null : [
                'first_name' => (string) $user->first_name,
                'full_name' => $user->fullName(),
                'email' => (string) $user->email,
                'company_id' => $user->company_id,
            ],
            'company' => $company === null ? null : [
                'id' => (int) $company->id,
                'name' => $company->translation()?->name ?? '',
                'portal_url' => $this->portalUrl($company),
            ],
            'currentTier' => $company?->tier()->value ?? PlanTier::Basic->value,
            'pendingRequest' => $pending === null ? null : [
                'id' => (int) $pending->id,
                'from_tier' => $pending->from_tier->value,
                'from_label' => $pending->from_tier->label(),
                'to_tier' => $pending->to_tier->value,
                'to_label' => $pending->to_tier->label(),
                'created_at' => $pending->created_at?->toIso8601String(),
            ],
            'plans' => array_map(
                fn (PlanTier $tier): array => $tier->toArray(),
                PlanTier::ordered(),
            ),
            'loginUrl' => route('partner.login'),
            'registerUrl' => route('partner.register'),
        ]);
    }

    /**
     * Open a plan-change request.
     *
     * We only accept ONE pending request per company at a time — a
     * second submit hits the guard and gets bounced with a friendly
     * flash so the partner isn't confused about what happened to
     * their first request.
     */
    public function choose(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'plan_tier' => ['required', 'string', 'in:basic,premium,gold'],
        ]);

        $user = $this->partner($request);
        $company = $this->companyOf($user);

        if ($company === null) {
            return redirect()
                ->route('partner.plans.index')
                ->with('flash', ['warning' => __('partner.plans.no_company')]);
        }

        $newTier = PlanTier::from((string) $data['plan_tier']);
        $currentTier = $company->tier();

        if ($newTier === $currentTier) {
            return redirect()
                ->route('partner.plans.index')
                ->with('flash', ['success' => __('partner.plans.already_on', ['tier' => $newTier->label()])]);
        }

        $existing = PlanChangeRequest::query()
            ->forCompany((int) $company->id)
            ->pending()
            ->exists();

        if ($existing) {
            return redirect()
                ->route('partner.plans.index')
                ->with('flash', ['warning' => __('partner.plans.request_already_pending')]);
        }

        $req = DB::transaction(fn (): PlanChangeRequest => PlanChangeRequest::query()->create([
            'company_id' => (int) $company->id,
            'company_user_id' => (int) $user->id,
            'from_tier' => $currentTier->value,
            'to_tier' => $newTier->value,
            'status' => PlanChangeRequest::STATUS_PENDING,
        ]));

        $this->notifyAdmins($req, $user, $company);

        return redirect()
            ->route('partner.plans.index')
            ->with('flash', ['success' => __('partner.plans.request_submitted', ['tier' => $newTier->label()])]);
    }

    public function cancel(Request $request, PlanChangeRequest $planRequest): RedirectResponse
    {
        $user = $this->partner($request);

        if ((int) $planRequest->company_id !== (int) $user->company_id) {
            throw new NotFoundHttpException;
        }

        if ($planRequest->status !== PlanChangeRequest::STATUS_PENDING) {
            return redirect()
                ->route('partner.plans.index')
                ->with('flash', ['warning' => __('partner.plans.request_not_pending')]);
        }

        $planRequest->delete();

        return redirect()
            ->route('partner.plans.index')
            ->with('flash', ['success' => __('partner.plans.request_cancelled')]);
    }

    /* ---------------------------------------------- helpers */

    private function partner(Request $request): CompanyUser
    {
        /** @var CompanyUser|null $user */
        $user = $request->user('company');
        if ($user === null) {
            Log::warning('PlanController hit without an authenticated partner');
            throw new NotFoundHttpException;
        }

        return $user;
    }

    private function companyOf(CompanyUser $user): ?Company
    {
        if ($user->company_id === null) {
            return null;
        }

        return Company::query()->with('translations')->find($user->company_id);
    }

    private function portalUrl(?Company $company): ?string
    {
        if ($company === null) {
            return null;
        }

        $t = $company->translation();
        $permalink = $t?->permalink;

        return $permalink !== null && $permalink !== ''
            ? "/company-portal/{$company->id}/{$permalink}"
            : "/company-portal/{$company->id}";
    }

    private function notifyAdmins(PlanChangeRequest $req, CompanyUser $requester, Company $company): void
    {
        try {
            $recipients = User::query()
                ->permission('companies.view')
                ->pluck('email')
                ->filter()
                ->values()
                ->all();

            if ($recipients === []) {
                return;
            }

            Mail::to($recipients)->send(new PlanChangeRequested($req, $requester, $company));
        } catch (Throwable $e) {
            report($e);
        }
    }
}
