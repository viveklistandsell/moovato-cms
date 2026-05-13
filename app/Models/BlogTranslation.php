<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'blog_id', 'lang', 'name', 'permalink', 'short_description', 'content',
])]
final class BlogTranslation extends Model
{
    protected $table = 'blog_translation';

    public function blog(): BelongsTo
    {
        return $this->belongsTo(Blog::class);
    }

    public function language(): BelongsTo
    {
        return $this->belongsTo(Language::class, 'lang', 'code');
    }

    protected function casts(): array
    {
        return [
            'content' => 'array',
        ];
    }
}
