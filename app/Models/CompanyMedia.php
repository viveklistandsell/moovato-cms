<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['company_id', 'media_file_id', 'kind', 'sort_order'])]
final class CompanyMedia extends Model
{
    protected $table = 'company_media';

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function mediaFile(): BelongsTo
    {
        return $this->belongsTo(MediaFile::class);
    }
}
