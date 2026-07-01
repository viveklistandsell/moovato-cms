<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'country_id', 'name', 'code', 'permalink', 'status', 'sort_order',
])]
final class State extends Model
{
    protected $table = 'states';

    public static function nextSortOrder(?int $countryId = null): int
    {
        $query = self::query();
        if ($countryId !== null) {
            $query->where('country_id', $countryId);
        }

        return ((int) $query->max('sort_order')) + 1;
    }

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    public function cities(): HasMany
    {
        return $this->hasMany(City::class)->orderBy('sort_order');
    }

    protected function casts(): array
    {
        return [
            'sort_order' => 'integer',
        ];
    }
}
