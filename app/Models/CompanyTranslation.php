<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['company_id', 'lang', 'name', 'permalink', 'short_description', 'about'])]
final class CompanyTranslation extends Model
{
    protected $table = 'company_translation';

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function language(): BelongsTo
    {
        return $this->belongsTo(Language::class, 'lang', 'code');
    }
}
