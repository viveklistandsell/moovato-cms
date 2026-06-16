<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Models\EmailLog;
use App\Models\User;
use Illuminate\Mail\Events\MessageSending;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Header\HeaderInterface;
use Symfony\Component\Mime\Header\Headers;
use Throwable;

/**
 * Fires BEFORE the message actually hits the transport. We create a
 * pending EmailLog row here and tag the message with a custom header
 * (`X-Email-Log-Id`) so MarkEmailSent / MarkEmailFailed can find the
 * same row to update.
 *
 * Auto-discovered by Laravel 11+ from the typed handle() signature —
 * NOT registered manually anywhere. If you ever add a manual binding
 * for this listener, remember to remove the auto-discovery side or
 * you'll get duplicate rows (the bug we just fixed on the login event).
 */
final class LogOutgoingEmail
{
    public function handle(MessageSending $event): void
    {
        try {
            $message = $event->message;
            $headers = $message->getHeaders();

            $log = EmailLog::query()->create([
                'mailer' => (string) ($event->data['__laravel_mailer'] ?? config('mail.default')),
                'from_address' => $this->firstAddress($message->getFrom())['address'] ?? '',
                'from_name' => $this->firstAddress($message->getFrom())['name'] ?? null,
                'to_addresses' => $this->mapAddresses($message->getTo()),
                'cc_addresses' => $message->getCc() !== [] ? $this->mapAddresses($message->getCc()) : null,
                'bcc_addresses' => $message->getBcc() !== [] ? $this->mapAddresses($message->getBcc()) : null,
                'reply_to' => $message->getReplyTo() !== [] ? $this->mapAddresses($message->getReplyTo()) : null,
                'subject' => mb_substr((string) $message->getSubject(), 0, 500),
                'body_html' => $message->getHtmlBody(),
                'body_text' => $message->getTextBody(),
                'headers' => $this->safeHeaders($headers),
                'attachment_count' => count($message->getAttachments()),
                'status' => EmailLog::STATUS_PENDING,
                'triggered_by_user_id' => $this->currentUserId(),
            ]);
            $headers->addTextHeader('X-Email-Log-Id', (string) $log->id);
        } catch (Throwable $throwable) {
            report($throwable);
        }
    }

    /**
     * @param  array<int, Address>  $addresses
     * @return list<array{address: string, name: ?string}>
     */
    private function mapAddresses(array $addresses): array
    {
        return array_map(
            fn (Address $a): array => ['address' => $a->getAddress(), 'name' => $a->getName() ?: null],
            $addresses,
        );
    }

    /**
     * @param  array<int, Address>  $addresses
     * @return array{address: ?string, name: ?string}
     */
    private function firstAddress(array $addresses): array
    {
        $first = $addresses[0] ?? null;
        if (! $first instanceof Address) {
            return ['address' => null, 'name' => null];
        }

        return ['address' => $first->getAddress(), 'name' => $first->getName() ?: null];
    }

    /**
     * Capture diagnostic headers but skip anything that could leak
     * credentials (Authorization, X-Auth-*, etc.).
     *
     * @return array<string, string>
     */
    private function safeHeaders(Headers $headers): array
    {
        $allow = [
            'Message-ID', 'In-Reply-To', 'References',
            'Reply-To', 'Date', 'X-Priority', 'X-Mailer',
            'Content-Type', 'MIME-Version',
        ];

        $out = [];
        foreach ($allow as $name) {
            $header = $headers->get($name);
            if ($header instanceof HeaderInterface) {
                $out[$name] = $header->getBodyAsString();
            }
        }

        return $out;
    }

    /**
     * Authenticated user (if any). Queued mails fired from console
     * commands will have no session and return null.
     */
    private function currentUserId(): ?int
    {
        try {
            $user = Auth::user();
        } catch (Throwable) {
            return null;
        }

        return $user instanceof User ? $user->id : null;
    }
}
