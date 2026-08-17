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
 * Self-service password reset email.
 *
 * Fired by /partner/forgot-password when an approved partner clicks
 * "Forgot password?" — reuses the same set-password Blade template
 * that PartnerAccepted uses, but with a different intro so the copy
 * matches the user's context (recovering vs. first-time setup).
 */
final class PartnerPasswordReset extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public CompanyUser $companyUser,
        public string $resetUrl,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Passwort zurücksetzen — Moovato',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.partner.password-reset',
            with: [
                'firstName' => $this->companyUser->first_name,
                'resetUrl' => $this->resetUrl,
            ],
        );
    }
}
