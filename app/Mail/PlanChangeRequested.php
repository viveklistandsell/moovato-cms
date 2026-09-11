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
 * Sent to every admin with the manage-companies permission when a
 * partner opens a plan-change request. Includes the from/to tiers,
 * requester identity, and a direct link into the approval queue.
 *
 * Subject reads as an action item so admins triaging their inbox
 * can spot the queue depth at a glance.
 */
final class PlanChangeRequested extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public PlanChangeRequest $request,
        public CompanyUser $requester,
        public Company $company,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Plan-Änderungsanfrage: '.$this->request->from_tier->label().' → '.$this->request->to_tier->label(),
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.admin.plan-change-requested',
            with: [
                'companyName' => $this->company->translation()?->name ?? '',
                'requesterName' => $this->requester->fullName(),
                'requesterEmail' => (string) $this->requester->email,
                'fromLabel' => $this->request->from_tier->label(),
                'toLabel' => $this->request->to_tier->label(),
                'queueUrl' => url('/admin/plan-change-requests'),
            ],
        );
    }
}
