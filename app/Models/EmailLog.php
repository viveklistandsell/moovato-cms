<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * One row per Symfony Mailer message Laravel attempted to send. Rows
 * start with status='pending' the moment the mailer fires `MessageSending`,
 * then flip to 'sent' (with Message-ID + sent_at) or 'failed' (with
 * error) when the lifecycle event arrives.
 *
 * `triggered_by_user_id` is best-effort: set by listeners that have a
 * request context. Queued mails fired from console commands will leave
 * it null.
 */
#[Fillable([
    'message_id', 'mailer',
    'from_address', 'from_name',
    'to_addresses', 'cc_addresses', 'bcc_addresses', 'reply_to',
    'subject', 'body_html', 'body_text', 'headers',
    'attachment_count', 'status', 'error',
    'triggered_by_user_id', 'sent_at',
])]
final class EmailLog extends Model
{
    use HasFactory;
    use HasFactory;

    public const string STATUS_PENDING = 'pending';

    public const string STATUS_SENT = 'sent';

    public const string STATUS_FAILED = 'failed';

    /**
     * The actor whose request triggered the email, if any. Notifications
     * fired from queued jobs or scheduled commands will have no user.
     */
    public function triggeredBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'triggered_by_user_id');
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'to_addresses' => 'array',
            'cc_addresses' => 'array',
            'bcc_addresses' => 'array',
            'reply_to' => 'array',
            'headers' => 'array',
            'attachment_count' => 'integer',
            'sent_at' => 'datetime',
        ];
    }
}
