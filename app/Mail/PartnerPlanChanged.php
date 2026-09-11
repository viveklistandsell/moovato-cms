<?php

declare(strict_types=1);

namespace App\Mail;

use App\Enums\PlanTier;
use App\Models\Company;
use App\Models\CompanyUser;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/**
 * SMTP config comes from the DB-backed EmailSetting singleton
 * (AppServiceProvider::configureMailFromDatabase()).
 */
final class PartnerPlanChanged extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public CompanyUser $companyUser,
        public Company $company,
        public PlanTier $newTier,
        public PlanTier $oldTier,
    ) {}

    public function envelope(): Envelope
    {
        $subject = $this->newTier->isFree()
            ? 'Ihr Moovato-Plan wurde auf '.$this->newTier->label().' geändert'
            : 'Willkommen im Moovato '.$this->newTier->label().'-Plan';

        return new Envelope(subject: $subject);
    }

    public function content(): Content
    {
        $portalPath = $this->companyUser->portalUrl();
        $portal = $portalPath !== null
            ? url($portalPath)
            : url('/partner/dashboard');
        $isDowngrade = $this->newTier->isLowerThan($this->oldTier);

        return new Content(
            view: 'emails.partner.plan-changed',
            with: [
                'firstName' => $this->companyUser->first_name,
                'companyName' => $this->company->translation()?->name ?? '',
                'oldLabel' => $this->oldTier->label(),
                'newLabel' => $this->newTier->label(),
                'newPrice' => $this->newTier->price(),
                'currency' => (string) config('plans.currency', '€'),
                'period' => (string) config('plans.period', 'month'),
                'isDowngrade' => $isDowngrade,
                'isFree' => $this->newTier->isFree(),
                'portalUrl' => $portal,
            ],
        );
    }
}
