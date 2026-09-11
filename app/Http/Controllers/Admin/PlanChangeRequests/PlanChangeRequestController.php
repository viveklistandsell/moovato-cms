<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin\PlanChangeRequests;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\PlanChangeRequests\RejectPlanChangeRequestRequest;
use App\Mail\PartnerPlanChanged;
use App\Mail\PartnerPlanChangeRejected;
use App\Models\PlanChangeRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Throwable;

/**
 * Approve / reject partner plan-change requests.
 *
 * No dedicated queue page — admins act on requests inline from the
 * admin Companies list, which shows an amber card with two buttons
 * under the Plan column when a company has an open request.
 *
 * Approve  → flips company.plan_tier, prunes over-cap content on
 *            downgrade via Company::enforceLimits(), fires the
 *            PartnerPlanChanged confirmation email.
 * Reject   → keeps the tier as-is, records the admin note, fires
 *            the PartnerPlanChangeRejected email so the partner
 *            knows why (and can retry / contact support).
 */
final class PlanChangeRequestController extends Controller
{
    public function approve(Request $request, PlanChangeRequest $planRequest): RedirectResponse
    {
        if ($planRequest->status !== PlanChangeRequest::STATUS_PENDING) {
            return back()->with('toast', [
                'type' => 'error',
                'message' => __('admin.plan_requests.flash.not_pending'),
            ]);
        }

        $company = $planRequest->company;
        if ($company === null) {
            return back()->with('toast', [
                'type' => 'error',
                'message' => __('admin.plan_requests.flash.company_missing'),
            ]);
        }

        $newTier = $planRequest->to_tier;
        $oldTier = $planRequest->from_tier;
        $isDowngrade = $newTier->isLowerThan($oldTier);

        DB::transaction(function () use ($company, $newTier, $isDowngrade, $planRequest, $request): void {
            $company->update(['plan_tier' => $newTier->value]);
            if ($isDowngrade) {
                $company->refresh()->enforceLimits();
            }
            $planRequest->update([
                'status' => PlanChangeRequest::STATUS_APPROVED,
                'reviewed_by_user_id' => $request->user()?->id,
                'reviewed_at' => now(),
            ]);
        });

        $requester = $planRequest->requester;
        if ($requester !== null) {
            try {
                Mail::to($requester->email)->send(
                    new PartnerPlanChanged($requester, $company->fresh(), $newTier, $oldTier),
                );
            } catch (Throwable $e) {
                report($e);
            }
        }

        return back()->with('toast', [
            'type' => 'success',
            'message' => __('admin.plan_requests.flash.approved', [
                'from' => $oldTier->label(),
                'to' => $newTier->label(),
            ]),
        ]);
    }

    public function reject(RejectPlanChangeRequestRequest $request, PlanChangeRequest $planRequest): RedirectResponse
    {
        if ($planRequest->status !== PlanChangeRequest::STATUS_PENDING) {
            return back()->with('toast', [
                'type' => 'error',
                'message' => __('admin.plan_requests.flash.not_pending'),
            ]);
        }

        /** @var array{admin_note?: string|null} $data */
        $data = $request->validated();

        $planRequest->update([
            'status' => PlanChangeRequest::STATUS_REJECTED,
            'admin_note' => $data['admin_note'] ?? null,
            'reviewed_by_user_id' => $request->user()?->id,
            'reviewed_at' => now(),
        ]);

        $requester = $planRequest->requester;
        $company = $planRequest->company;
        if ($requester !== null && $company !== null) {
            try {
                Mail::to($requester->email)->send(
                    new PartnerPlanChangeRejected($requester, $company, $planRequest->fresh()),
                );
            } catch (Throwable $e) {
                report($e);
            }
        }

        return back()->with('toast', [
            'type' => 'success',
            'message' => __('admin.plan_requests.flash.rejected'),
        ]);
    }
}
