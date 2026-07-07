<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['parent_category_id', 'lang', 'name', 'permalink', 'short_description'])]
final class ServiceParentCategoryTranslation extends Model
{
    protected $table = 'service_parent_category_translation';

    public function parentCategory(): BelongsTo
    {
        return $this->belongsTo(ServiceParentCategory::class, 'parent_category_id');
    }

    public function language(): BelongsTo
    {
        return $this->belongsTo(Language::class, 'lang', 'code');
    }
}
