<?php

declare(strict_types=1);

namespace App\Mail;

use App\Models\CompanyUser;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/**
 * Sent to a company user when an admin clicks "Reject" on their
 * pending registration.
 */
final class PartnerRejected extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public CompanyUser $companyUser,
        public string $reason,
    ) {}

    public function envelope(): Envelope
    {

        return new Envelope(
            subject: 'Status Ihrer Moovato-Registrierung',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.partner.rejected',
            with: [
                'firstName' => $this->companyUser->first_name,
                'reason' => $this->reason,
            ],
        );
    }
}
