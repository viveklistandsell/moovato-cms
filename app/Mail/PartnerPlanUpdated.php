<?php

declare(strict_types=1);

namespace App\Mail;

use App\Models\Company;
use App\Models\CompanyUser;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/**
 * `$changes` is an array<int, array{label:string, from:string,
 * to:string}> — one row per changed field, pre-formatted for the
 * Blade template.
 */
final class PartnerPlanUpdated extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * @param  array<int, array{label:string, from:string, to:string}>  $changes
     */
    public function __construct(
        public CompanyUser $companyUser,
        public Company $company,
        public string $planLabel,
        public array $changes,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Ihr Moovato-'.$this->planLabel.'-Plan wurde aktualisiert',
        );
    }

    public function content(): Content
    {
        $portalPath = $this->companyUser->portalUrl();
        $portal = $portalPath !== null
            ? url($portalPath)
            : url('/partner/dashboard');

        return new Content(
            view: 'emails.partner.plan-updated',
            with: [
                'firstName' => $this->companyUser->first_name,
                'companyName' => $this->company->translation()?->name ?? '',
                'planLabel' => $this->planLabel,
                'changes' => $this->changes,
                'portalUrl' => $portal,
                'plansUrl' => url('/partner/plans'),
            ],
        );
    }
}
