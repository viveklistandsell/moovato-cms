<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['company_id', 'sort_order', 'status'])]
final class CompanyFaq extends Model
{
    protected $table = 'company_faqs';

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function translations(): HasMany
    {
        return $this->hasMany(CompanyFaqTranslation::class, 'faq_id');
    }

    public function translation(?string $lang = null): ?CompanyFaqTranslation
    {
        $lang ??= app()->getLocale();

        return $this->translations->firstWhere('lang', $lang)
            ?? $this->translations->first();
    }
}
