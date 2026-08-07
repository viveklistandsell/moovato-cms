<?php

declare(strict_types=1);

namespace App\Widgets\Marquee;

use App\Widgets\Concerns\ProvidesWidgetDefaults;
use App\Widgets\Contracts\WidgetContract;

/**
 * Marquee ticker: an angled, edge-to-edge bar with words scrolling infinitely,
 * separated by asterisks. Ported from the reference design and recolored to the
 * theme (purple → orange gradient).
 */
final class MarqueeWidget implements WidgetContract
{
    use ProvidesWidgetDefaults;

    public static function type(): string
    {
        return 'marquee';
    }

    public static function label(): string
    {
        return 'Marquee Ticker';
    }

    public static function icon(): string
    {
        return 'Tags';
    }

    public static function category(): string
    {
        return 'content';
    }

    /**
     * @return array<string, mixed>
     */
    public static function defaultSettings(): array
    {
        return [
            'speed' => 'normal', // slow | normal | fast
            'reverse' => false,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public static function defaultData(): array
    {
        return [
            'items' => [
                'Mitte',
                'Kreuzberg',
                'Charlottenburg',
                'Prenzlauer Berg',
                'Neukölln',
                'Friedrichshain',
                'Schöneberg',
                'Spandau',
                'Steglitz',
                'Pankow',
                'Privat',
                'Gewerbe',
                'Fernumzug',
                'Spezialtransport',
            ],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public static function settingsRules(): array
    {
        return [
            'speed' => ['required', 'in:slow,normal,fast'],
            'reverse' => ['boolean'],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public static function dataRules(): array
    {
        return [
            'items' => ['nullable', 'array'],
            'items.*' => ['nullable', 'string'],
        ];
    }
}
