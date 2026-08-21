<?php

declare(strict_types=1);

namespace App\Enums;

use App\Models\Plan;

/**
 * Partner plan tier — the type-safe identifier for a plan.
 *
 * The three enum cases are the fixed set of slugs the
 * `companies.plan_tier` column can hold. The FLEXIBLE data
 * (label, price, features, caps, ...) lives in the `plans` DB
 * table so admins can edit it from the dashboard — every helper
 * on this enum reads from Plan::registry() with a config fallback
 * so nothing crashes if the DB row is missing (fresh install
 * before migrate, or if the row gets deleted).
 *
 * Ordering matters for compareTo(): 'basic' < 'premium' < 'gold'.
 */
enum PlanTier: string
{
    case Basic = 'basic';
    case Premium = 'premium';
    case Gold = 'gold';

    /**
     * Every tier in display order.
     *
     * @return array<int, self>
     */
    public static function ordered(): array
    {
        return [self::Basic, self::Premium, self::Gold];
    }

    public function label(): string
    {
        return (string) ($this->plan()['label']
            ?? config("plans.tiers.{$this->value}.label", ucfirst($this->value)));
    }

    public function price(): int
    {
        return (int) ($this->plan()['price']
            ?? config("plans.tiers.{$this->value}.price", 0));
    }

    public function positioning(): string
    {
        return (string) ($this->plan()['positioning']
            ?? config("plans.tiers.{$this->value}.positioning", ''));
    }

    public function placement(): string
    {
        return (string) ($this->plan()['placement']
            ?? config("plans.tiers.{$this->value}.placement", 'standard'));
    }

    public function currency(): string
    {
        return (string) ($this->plan()['currency']
            ?? config('plans.currency', '€'));
    }

    public function period(): string
    {
        return (string) ($this->plan()['period']
            ?? config('plans.period', 'month'));
    }

    public function hasFeature(string $slug): bool
    {
        $plan = $this->plan();
        if ($plan !== null) {
            return (bool) (($plan['features'] ?? [])[$slug] ?? false);
        }

        return (bool) config("plans.tiers.{$this->value}.features.{$slug}", false);
    }

    public function cap(string $resource): ?int
    {
        $plan = $this->plan();
        if ($plan !== null) {
            $caps = $plan['caps'] ?? [];
            if (! array_key_exists($resource, $caps)) {
                return null;
            }

            return $caps[$resource] === null ? null : (int) $caps[$resource];
        }

        $value = config("plans.tiers.{$this->value}.caps.{$resource}");

        return $value === null ? null : (int) $value;
    }

    /*
    |--------------------------------------------------------------------------
    | Named cap helpers — thin wrappers over cap() for readability.
    |--------------------------------------------------------------------------
    */

    public function photoLimit(): ?int
    {
        return $this->cap('gallery');
    }

    public function contactLimit(): ?int
    {
        return $this->cap('contacts');
    }

    public function serviceLimit(): ?int
    {
        return $this->cap('services');
    }

    public function areaLimit(): ?int
    {
        return $this->cap('areas');
    }

    public function faqLimit(): ?int
    {
        return $this->cap('faqs');
    }

    /*
    |--------------------------------------------------------------------------
    | Comparison / upgrade helpers
    |--------------------------------------------------------------------------
    */

    public function rank(): int
    {
        $order = self::ordered();
        foreach ($order as $i => $tier) {
            if ($tier === $this) {
                return $i;
            }
        }

        return 0;
    }

    public function isHigherThan(self $other): bool
    {
        return $this->rank() > $other->rank();
    }

    public function isLowerThan(self $other): bool
    {
        return $this->rank() < $other->rank();
    }

    public function isFree(): bool
    {
        return $this->price() === 0;
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        $plan = $this->plan();

        return [
            'slug' => $this->value,
            'label' => $this->label(),
            'price' => $this->price(),
            'currency' => $this->currency(),
            'period' => $this->period(),
            'positioning' => $this->positioning(),
            'placement' => $this->placement(),
            'features' => (array) ($plan['features']
                ?? config("plans.tiers.{$this->value}.features", [])),
            'caps' => (array) ($plan['caps']
                ?? config("plans.tiers.{$this->value}.caps", [])),
            'is_free' => $this->isFree(),
        ];
    }
    /**
     * @return array<string, mixed>|null
     */
    private function plan(): ?array
    {
        return Plan::findBySlug($this->value);
    }
}
