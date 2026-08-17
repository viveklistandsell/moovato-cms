<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin\CompanyApplications;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CompanyApplications\RejectApplicationRequest;
use App\Mail\PartnerAccepted;
use App\Mail\PartnerRejected;
use App\Models\CompanyUser;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

/**
 * Admin approval queue for pending partner (company) registrations.
 *
 * Approve fires PartnerAccepted mailable with a set-password link.
 * Reject fires PartnerRejected with the admin-typed reason.
 *
 * Edit isn't a separate endpoint — the frontend links straight to
 * the existing /admin/companies/{id}/edit page.
 */
final class CompanyApplicationController extends Controller
{
    private const ALLOWED_STATUS_FILTERS = ['pending', 'approved', 'rejected', 'all'];

    public function index(Request $request): Response
    {
        $status = (string) $request->query('status', 'pending');
        if (! in_array($status, self::ALLOWED_STATUS_FILTERS, true)) {
            $status = 'pending';
        }

        $search = mb_trim((string) $request->query('q', ''));
        $perPage = max(5, min(100, (int) $request->query('per_page', '15')));

        $query = CompanyUser::query()
            ->with([
                'company:id,primary_city_id,street,postal_code,status',
                'company.primaryCity:id,name',
                'company.translations:id,company_id,lang,name,permalink',
                'reviewer:id,name',
            ])
            ->latest('created_at');

        if ($status !== 'all') {
            $query->where('status', $status);
        }

        if ($search !== '') {
            $like = "%{$search}%";
            $query->where(function (Builder $q) use ($like): void {
                $q->where('first_name', 'like', $like)
                    ->orWhere('last_name', 'like', $like)
                    ->orWhere('email', 'like', $like)
                    ->orWhere('phone', 'like', $like)
                    ->orWhereHas('company.translations', fn (Builder $qq) => $qq->where('name', 'like', $like));
            });
        }

        $paginated = $query->paginate($perPage)->withQueryString();
        $locale = App::getLocale();

        $applications = $paginated->getCollection()
            ->map(fn (CompanyUser $u): array => $this->present($u, $locale))
            ->values()
            ->all();

        return Inertia::render('admin/company-applications/Index', [
            'applications' => $applications,
            'stats' => $this->headlineStats(),
            'filters' => [
                'status' => $status,
                'q' => $search,
                'per_page' => $perPage,
            ],
            'allowed' => [
                'statuses' => self::ALLOWED_STATUS_FILTERS,
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

    /**
     * Approve the application.
     *
     * Flips CompanyUser → approved AND Company → published (so the
     * owner sees their listing go live immediately). Generates a
     * password-reset token via the `company_users` broker and mails
     * the applicant a "set your password" link.
     *
     * If the email send fails we still commit the DB changes — the
     * applicant can request a fresh link later via
     * /partner/forgot-password without the admin re-doing the whole
     * approval.
     */
    public function approve(Request $request, CompanyUser $application): RedirectResponse
    {
        if ($application->status === CompanyUser::STATUS_APPROVED) {
            return back()->with('flash', ['warning' => __('admin.applications.flash.already_approved')]);
        }

        DB::transaction(function () use ($request, $application): void {
            $application->update([
                'status' => CompanyUser::STATUS_APPROVED,
                'rejection_reason' => null,
                'reviewed_by_user_id' => $request->user()?->id,
                'reviewed_at' => now(),
            ]);

            if ($application->company !== null && $application->company->status !== 'published') {
                $application->company->update(['status' => 'published']);
            }
        });

        $token = Password::broker('company_users')->createToken($application);
        $setPasswordUrl = url('/partner/reset-password/'.$token.'?email='.urlencode($application->email));

        try {
            Mail::to($application->email)->send(new PartnerAccepted($application, $setPasswordUrl));
        } catch (Throwable $e) {
            report($e);

            return back()->with('flash', ['warning' => __('admin.applications.flash.approved_email_failed')]);
        }

        return back()->with('flash', ['success' => __('admin.applications.flash.approved')]);
    }

    /**
     * Reject the application.
     * Keeps the row (for audit) — never can log in but the record
     * stays so admins can see who applied.
     */
    public function reject(RejectApplicationRequest $request, CompanyUser $application): RedirectResponse
    {
        if ($application->status === CompanyUser::STATUS_REJECTED) {
            return back()->with('flash', ['warning' => __('admin.applications.flash.already_rejected')]);
        }

        $reason = (string) $request->validated('reason');

        $application->update([
            'status' => CompanyUser::STATUS_REJECTED,
            'rejection_reason' => $reason,
            'reviewed_by_user_id' => $request->user()?->id,
            'reviewed_at' => now(),
        ]);

        try {
            Mail::to($application->email)->send(new PartnerRejected($application, $reason));
        } catch (Throwable $e) {
            report($e);

            return back()->with('flash', ['warning' => __('admin.applications.flash.rejected_email_failed')]);
        }

        return back()->with('flash', ['success' => __('admin.applications.flash.rejected')]);
    }

    /**
     * Permanently delete a partner registration and everything it
     * owns.
     */
    public function destroy(CompanyUser $application): RedirectResponse
    {
        $name = $application->fullName();

        $application->forceDelete();

        return back()->with('flash', [
            'success' => __('admin.applications.flash.deleted', ['name' => $name]),
        ]);
    }
    public function downloadDoc(CompanyUser $application, int $index): StreamedResponse
    {
        $docs = (array) ($application->registration_docs ?? []);
        $doc = $docs[$index] ?? null;
        if ($doc === null || empty($doc['path']) || ! Storage::disk('local')->exists($doc['path'])) {
            throw new NotFoundHttpException;
        }

        return Storage::disk('local')->response(
            $doc['path'],
            $doc['name'] ?? basename($doc['path']),
            ['Content-Type' => Storage::disk('local')->mimeType($doc['path']) ?: 'application/octet-stream'],
        );
    }

    /* ---------------------------------------------- helpers */

    /**
     * @return array<string, mixed>
     */
    private function present(CompanyUser $u, string $locale): array
    {
        $translation = $u->company?->translations->firstWhere('lang', $locale)
            ?? $u->company?->translations->first();

        $docs = array_values(array_map(
            fn (array $doc, int $i): array => [
                'name' => (string) ($doc['name'] ?? basename((string) ($doc['path'] ?? ''))),
                'url' => route('admin.applications.doc', ['application' => $u->id, 'index' => $i]),
            ],
            (array) ($u->registration_docs ?? []),
            array_keys((array) ($u->registration_docs ?? []))
        ));

        return [
            'id' => (int) $u->id,
            'first_name' => (string) $u->first_name,
            'last_name' => (string) $u->last_name,
            'full_name' => $u->fullName(),
            'email' => (string) $u->email,
            'phone' => $u->phone,
            'status' => (string) $u->status,
            'rejection_reason' => $u->rejection_reason,
            'company_id' => $u->company_id,
            'company_name' => $translation?->name,
            'company_permalink' => $translation?->permalink,
            'company_city' => $u->company?->primaryCity?->name,
            'company_street' => $u->company?->street,
            'company_postal' => $u->company?->postal_code,
            'company_status' => $u->company?->status,
            'reviewer_name' => $u->reviewer?->name,
            'reviewed_at' => $u->reviewed_at?->toIso8601String(),
            'created_at' => $u->created_at?->toIso8601String(),
            'documents' => $docs,
            'edit_url' => $u->company_id !== null
                ? route('admin.companies.edit', $u->company_id)
                : null,
        ];
    }

    /**
     * Headline strip counts — quick pulse on the approval queue.
     *
     * @return array<string, int>
     */
    private function headlineStats(): array
    {
        return [
            'pending' => CompanyUser::query()->where('status', CompanyUser::STATUS_PENDING)->count(),
            'approved' => CompanyUser::query()->where('status', CompanyUser::STATUS_APPROVED)->count(),
            'rejected' => CompanyUser::query()->where('status', CompanyUser::STATUS_REJECTED)->count(),
            'total' => CompanyUser::query()->count(),
        ];
    }
}
