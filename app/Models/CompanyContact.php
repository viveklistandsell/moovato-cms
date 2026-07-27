<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['company_id', 'type', 'value', 'label', 'is_primary', 'sort_order'])]
final class CompanyContact extends Model
{
    protected $table = 'company_contacts';

    protected $casts = [
        'is_primary' => 'boolean',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }
}
