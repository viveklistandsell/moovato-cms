<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

#[Fillable([
    'slug',
    'label',
    'price',
    'currency',
    'period',
    'positioning',
    'placement',
    'lead_url',
    'lead_label',
    'features',
    'caps',
    'sort_order',
    'is_active',
])]
final class Plan extends Model
{
    public const CACHE_KEY = 'plans.registry.v5';

    public $incrementing = false;

    protected $table = 'plans';

    protected $primaryKey = 'slug';

    protected $keyType = 'string';

    protected $casts = [
        'price' => 'integer',
        'features' => 'array',
        'caps' => 'array',
        'sort_order' => 'integer',
        'is_active' => 'boolean',
    ];

    /**
     * @return Collection<string, array<string, mixed>>
     */
    public static function registry(): Collection
    {
        $rows = Cache::rememberForever(
            self::CACHE_KEY,
            static fn (): array => self::query()
                ->orderBy('sort_order')
                ->get()
                ->map(static fn (self $p): array => [
                    'slug' => (string) $p->slug,
                    'label' => (string) $p->label,
                    'price' => (int) $p->price,
                    'currency' => (string) $p->currency,
                    'period' => (string) $p->period,
                    'positioning' => $p->positioning === null ? null : (string) $p->positioning,
                    'placement' => (string) $p->placement,
                    'lead_url' => $p->lead_url === null ? null : (string) $p->lead_url,
                    'lead_label' => $p->lead_label === null ? null : (string) $p->lead_label,
                    'features' => (array) ($p->features ?? []),
                    'caps' => (array) ($p->caps ?? []),
                    'sort_order' => (int) $p->sort_order,
                    'is_active' => (bool) $p->is_active,
                ])
                ->all()
        );

        return collect($rows)->keyBy('slug');
    }

    /**
     * @return array<string, mixed>|null
     */
    public static function findBySlug(string $slug): ?array
    {
        return self::registry()->get($slug);
    }

    protected static function booted(): void
    {
        self::saved(fn () => Cache::forget(self::CACHE_KEY));
        self::deleted(fn () => Cache::forget(self::CACHE_KEY));
    }
}
