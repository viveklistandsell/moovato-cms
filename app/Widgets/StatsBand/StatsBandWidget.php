<?php

declare(strict_types=1);

namespace App\Widgets\StatsBand;

use App\Widgets\Concerns\ProvidesWidgetDefaults;
use App\Widgets\Contracts\WidgetContract;

final class StatsBandWidget implements WidgetContract
{
    use ProvidesWidgetDefaults;

    public static function type(): string
    {
        return 'stats_band';
    }

    public static function label(): string
    {
        return 'Stats Band';
    }

    public static function icon(): string
    {
        return 'ChartColumnBig';
    }

    public static function category(): string
    {
        return 'content';
    }

    public static function defaultSettings(): array
    {
        return [
            'variant' => 'dark', // light | dark
        ];
    }

    public static function defaultData(): array
    {
        return [
            'eyebrow' => '',
            'heading' => 'Moovato in Zahlen',
            'stats' => [
                ['value' => '12.500+', 'label' => 'Umzüge in Berlin'],
                ['value' => '15', 'label' => 'Jahre Erfahrung'],
                ['value' => '98%', 'label' => 'Zufriedene Kunden'],
                ['value' => '4.9/5', 'label' => 'Durchschnittliche Bewertung'],
            ],
        ];
    }

    public static function settingsRules(): array
    {
        return [
            'variant' => ['required', 'in:light,dark'],
        ];
    }

    public static function dataRules(): array
    {
        return [
            'eyebrow' => ['nullable', 'string', 'max:120'],
            'heading' => ['nullable', 'string', 'max:255'],
            'stats' => ['nullable', 'array', 'max:6'],
            'stats.*.value' => ['nullable', 'string', 'max:24'],
            'stats.*.label' => ['nullable', 'string', 'max:120'],
        ];
    }
}
