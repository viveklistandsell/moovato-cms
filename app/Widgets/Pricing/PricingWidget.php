<?php

declare(strict_types=1);

namespace App\Widgets\Pricing;

use App\Widgets\Concerns\ProvidesWidgetDefaults;
use App\Widgets\Contracts\WidgetContract;

/**
 * Plan cards (price, features, caps) are NOT stored on the widget — they are
 * injected live from the `plans` table by
 * App\Http\Controllers\Frontend\PageController::resolveLivePlans() at render
 * time, the same way the `blog` and `services_category_grid` widgets inject
 * their live content. This keeps the public pricing table in sync with
 * whatever a Super Admin edits in Admin > Plans, with zero duplication.
 */
final class PricingWidget implements WidgetContract
{
    use ProvidesWidgetDefaults;

    public static function type(): string
    {
        return 'pricing';
    }

    public static function label(): string
    {
        return 'Pricing';
    }

    public static function icon(): string
    {
        return 'BadgeEuro';
    }

    public static function category(): string
    {
        return 'marketing';
    }

    /**
     * @return array<string, mixed>
     */
    public static function defaultSettings(): array
    {
        return [
            'highlight' => 'premium',
            'cta_url' => '/partner/register',
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public static function defaultData(): array
    {
        return [
            'eyebrow' => 'Für Umzugsunternehmen',
            'heading' => 'Werden Sie Moovato-Partner und erhalten Sie mehr Umzugsanfragen',
            'cta_label' => 'Partner werden',
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public static function settingsRules(): array
    {
        return [
            'highlight' => ['nullable', 'string', 'in:basic,premium,gold'],
            'cta_url' => ['nullable', 'string'],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public static function dataRules(): array
    {
        return [
            'eyebrow' => ['nullable', 'string'],
            'heading' => ['nullable', 'string'],
            'cta_label' => ['nullable', 'string'],
        ];
    }
}
