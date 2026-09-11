<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'state_id', 'name', 'permalink', 'postal_code',
    'is_popular', 'status', 'sort_order',
])]
final class City extends Model
{
    use HasFactory;

    protected $table = 'cities';

    public static function nextSortOrder(?int $stateId = null): int
    {
        $query = self::query();
        if ($stateId !== null) {
            $query->where('state_id', $stateId);
        }

        return ((int) $query->max('sort_order')) + 1;
    }

    public static function compactSiblings(int $stateId): void
    {
        $siblings = self::query()
            ->where('state_id', $stateId)
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

    public function reorderToCurrentPosition(): void
    {
        $siblings = self::query()
            ->where('state_id', $this->state_id)
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

    public function state(): BelongsTo
    {
        return $this->belongsTo(State::class);
    }

    public function districts(): HasMany
    {
        return $this->hasMany(District::class);
    }

    protected function casts(): array
    {
        return [
            'is_popular' => 'boolean',
            'sort_order' => 'integer',
        ];
    }
}
