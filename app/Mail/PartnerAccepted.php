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
 * Sent to a company user the moment an admin clicks "Accept" on
 * their pending registration.
 *
 * The body contains a one-time set-password link (Laravel's password
 * broker under the hood — same primitives as the forgot-password
 * flow). Token TTL is 7 days per config/auth.php (`company_users`
 * password broker) so a busy applicant has time to click through.
 */
final class PartnerAccepted extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public CompanyUser $companyUser,
        public string $setPasswordUrl,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Willkommen bei Moovato — Passwort einrichten',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.partner.accepted',
            with: [
                'firstName' => $this->companyUser->first_name,
                'setPasswordUrl' => $this->setPasswordUrl,
            ],
        );
    }
}
