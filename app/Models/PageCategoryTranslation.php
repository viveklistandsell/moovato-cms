<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'category_id', 'lang', 'title', 'permalink',
])]
final class PageCategoryTranslation extends Model
{
    protected $table = 'page_category_translation';

    public function category(): BelongsTo
    {
        return $this->belongsTo(PageCategory::class, 'category_id');
    }
}
