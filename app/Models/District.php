<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'city_id', 'name', 'code', 'permalink', 'postal_code_prefix',
    'is_popular', 'status', 'sort_order',
])]
final class District extends Model
{
    protected $table = 'districts';

    /**
     * Next sort_order for a new sibling within the given city scope.
     * Districts are flat under a city (no self-hierarchy in this MVP),
     * so sort_order only needs to be unique per city_id.
     */
    public static function nextSortOrder(?int $cityId = null): int
    {
        $query = self::query();
        if ($cityId !== null) {
            $query->where('city_id', $cityId);
        }

        return ((int) $query->max('sort_order')) + 1;
    }

    /**
     * Renumber a city's districts 1..N to close any gaps left after a
     * delete or a cross-city move. Same pattern as City::compactSiblings.
     */
    public static function compactSiblings(int $cityId): void
    {
        $siblings = self::query()
            ->where('city_id', $cityId)
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();

        foreach ($siblings as $index => $sibling) {
            $newOrder = $index + 1;
            if ((int) $sibling->sort_order !== $newOrder) {
                self::query()->where('id', $sibling->id)->update(['sort_order' => $newOrder]);
            }
        }
    }

    /**
     * Insert this district at the position implied by its current
     * `sort_order`, shifting siblings 1..N to close/create the gap.
     * Called after create/update so a manual sort_order edit takes
     * effect against the actual sibling list.
     */
    public function reorderToCurrentPosition(): void
    {
        $siblings = self::query()
            ->where('city_id', $this->city_id)
            ->where('id', '!=', $this->id)
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();

        $targetIndex = max(0, min($siblings->count(), $this->sort_order - 1));
        $siblings->splice($targetIndex, 0, [$this]);

        foreach ($siblings as $index => $sibling) {
            $newOrder = $index + 1;
            if ((int) $sibling->sort_order !== $newOrder) {
                self::query()->where('id', $sibling->id)->update(['sort_order' => $newOrder]);
            }
        }
    }

    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class);
    }

    protected function casts(): array
    {
        return [
            'is_popular' => 'boolean',
            'sort_order' => 'integer',
        ];
    }
}
