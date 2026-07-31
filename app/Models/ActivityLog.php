<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * Immutable audit row. We disable `updated_at` because activity log entries
 * are appended once and queried back — never edited.
 */
#[Fillable([
    'user_id', 'ip_address', 'user_agent', 'action', 'description',
    'subject_type', 'subject_id', 'properties',
])]
final class ActivityLog extends Model
{
    public const UPDATED_AT = null;

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function subject(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'created_at' => 'datetime',
            'properties' => 'array',
        ];
    }
}
