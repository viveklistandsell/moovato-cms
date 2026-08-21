<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'company_user_id',
    'type',
    'title',
    'body',
    'data',
    'read_at',
])]
final class PartnerNotification extends Model
{
    public const TYPE_PLAN_UPDATED = 'plan_updated';

    protected $table = 'partner_notifications';

    protected $casts = [
        'data' => 'array',
        'read_at' => 'datetime',
    ];

    public function companyUser(): BelongsTo
    {
        return $this->belongsTo(CompanyUser::class);
    }

    /* -------------------------------------------------- scopes */

    public function scopeForUser(Builder $q, int $companyUserId): Builder
    {
        return $q->where('company_user_id', $companyUserId);
    }

    public function scopeUnread(Builder $q): Builder
    {
        return $q->whereNull('read_at');
    }
}
