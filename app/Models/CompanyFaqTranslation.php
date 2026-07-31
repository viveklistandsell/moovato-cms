<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['faq_id', 'lang', 'question', 'answer'])]
final class CompanyFaqTranslation extends Model
{
    protected $table = 'company_faq_translation';

    public function faq(): BelongsTo
    {
        return $this->belongsTo(CompanyFaq::class, 'faq_id');
    }

    public function language(): BelongsTo
    {
        return $this->belongsTo(Language::class, 'lang', 'code');
    }
}
