<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Models\EmailLog;
use Illuminate\Mail\Events\MessageSendingFailed;
use Throwable;

/**
 * Fires when the transport throws (SMTP refused, network error, etc.).
 * Flips the pending row to status=failed with the exception message
 * captured for the admin UI.
 *
 * Auto-discovered by Laravel 11+. Note: this is a relatively new
 * Laravel event (added in 11.x); older apps used to wrap Mail::send in
 * try/catch instead.
 */
final class MarkEmailFailed
{
    public function handle(MessageSendingFailed $event): void
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

            $log->update([
                'status' => EmailLog::STATUS_FAILED,
                'error' => $event->exception?->getMessage(),
            ]);
        } catch (Throwable $throwable) {
            report($throwable);
        }
    }
}
