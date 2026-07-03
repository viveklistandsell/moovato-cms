<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'state_id', 'name', 'permalink', 'postal_code',
    'is_popular', 'status', 'sort_order',
])]
final class City extends Model
{
    protected $table = 'cities';

    public static function nextSortOrder(?int $stateId = null): int
    {
        $query = self::query();
        if ($stateId !== null) {
            $query->where('state_id', $stateId);
        }

        return ((int) $query->max('sort_order')) + 1;
    }

    public function state(): BelongsTo
    {
        return $this->belongsTo(State::class);
    }

    protected function casts(): array
    {
        return [
            'is_popular' => 'boolean',
            'sort_order' => 'integer',
        ];
    }
}
