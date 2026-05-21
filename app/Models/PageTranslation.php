<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'page_id', 'lang', 'title', 'permalink',
])]
final class PageTranslation extends Model
{
    protected $table = 'page_translation';

    public function page(): BelongsTo
    {
        return $this->belongsTo(Page::class);
    }
}
