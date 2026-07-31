<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

/**
 * Single-write audit row. The presence of this row is the evidence we
 * need to satisfy GDPR Art. 7(1). Rows are not edited or deleted in
 * normal operation — they are append-only.
 */
#[Fillable([
    'anonymized_ip',
    'user_agent_hash',
    'categories_accepted',
    'action',
    'banner_version',
    'locale',
])]
final class CookieConsent extends Model
{
    public const UPDATED_AT = null;

    public const ACTION_ACCEPT_ALL = 'accept_all';

    public const ACTION_REJECT_ALL = 'reject_all';

    public const ACTION_CUSTOM = 'custom';

    public const ACTION_REVOKE = 'revoke';

    /**
     * @var array<string, string>
     */
    protected $casts = [
        'categories_accepted' => 'array',
        'created_at' => 'datetime',
    ];
}
