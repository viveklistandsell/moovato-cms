<?php

declare(strict_types=1);

namespace App\Mail;

use App\Models\Company;
use App\Models\CompanyUser;
use App\Models\PlanChangeRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/**
 * Sent to the partner when an admin rejects their plan-change
 * request. Includes any admin-supplied note so the partner knows
 * why (e.g. "Please contact us to discuss the enterprise tier").
 */
final class PartnerPlanChangeRejected extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public CompanyUser $companyUser,
        public Company $company,
        public PlanChangeRequest $request,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Ihre Plan-Änderungsanfrage wurde abgelehnt',
        );
    }

    public function content(): Content
    {
        $portalPath = $this->companyUser->portalUrl();
        $portal = $portalPath !== null
            ? url($portalPath)
            : url('/partner/dashboard');

        return new Content(
            view: 'emails.partner.plan-change-rejected',
            with: [
                'firstName' => $this->companyUser->first_name,
                'companyName' => $this->company->translation()?->name ?? '',
                'fromLabel' => $this->request->from_tier->label(),
                'toLabel' => $this->request->to_tier->label(),
                'adminNote' => (string) ($this->request->admin_note ?? ''),
                'plansUrl' => url('/partner/plans'),
                'portalUrl' => $portal,
            ],
        );
    }
}
