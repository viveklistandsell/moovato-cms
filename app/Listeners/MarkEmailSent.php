<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Models\EmailLog;
use Illuminate\Mail\Events\MessageSent;
use Throwable;

/**
 * Fires AFTER the transport accepts the message. Locates the pending
 * row written by LogOutgoingEmail via the `X-Email-Log-Id` header we
 * stashed, then flips it to status=sent with the final Message-ID and
 * timestamp.
 *
 * Auto-discovered by Laravel 11+ from the typed handle() signature.
 */
final class MarkEmailSent
{
    public function handle(MessageSent $event): void
    {
        try {
            $headers = $event->message->getHeaders();
            $logIdHeader = $headers->get('X-Email-Log-Id');
            if ($logIdHeader === null) {
                return;
            }

            $log = EmailLog::query()->find((int) $logIdHeader->getBodyAsString());
            if ($log === null) {
                return;
            }

            $messageIdHeader = $headers->get('Message-ID');
            $log->update([
                'status' => EmailLog::STATUS_SENT,
                'sent_at' => now(),
                'message_id' => $messageIdHeader?->getBodyAsString(),
            ]);
            $headers->remove('X-Email-Log-Id');
        } catch (Throwable $throwable) {
            report($throwable);
        }
    }
}
