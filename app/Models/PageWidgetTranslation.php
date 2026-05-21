<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'page_widget_id', 'lang', 'data',
])]
final class PageWidgetTranslation extends Model
{
    protected $table = 'page_widget_translation';

    public function widget(): BelongsTo
    {
        return $this->belongsTo(PageWidget::class, 'page_widget_id');
    }

    /**
     * @return array<string, mixed>
     */
    protected function casts(): array
    {
        return [
            'data' => 'array',
        ];
    }
}
