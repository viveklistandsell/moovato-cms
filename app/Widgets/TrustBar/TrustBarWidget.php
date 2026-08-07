<?php

declare(strict_types=1);

namespace App\Widgets\TrustBar;

use App\Widgets\Concerns\ProvidesWidgetDefaults;
use App\Widgets\Contracts\WidgetContract;

/**
 * Trust fun fact: a staggered row of headline stats, each an outlined counter
 * number, a notched divider and a circular badge (count + two-line caption with
 * a gradient blob). Dark by default; recolored to the Moovato theme.
 */
final class TrustBarWidget implements WidgetContract
{
    use ProvidesWidgetDefaults;

    public static function type(): string
    {
        return 'trust_bar';
    }

    public static function label(): string
    {
        return 'Trust Fun Fact';
    }

    public static function icon(): string
    {
        return 'TrendingUp';
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
            'theme' => 'dark',  // dark | light
            'bg_image_path' => null,
            'bg_image_url' => null,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public static function defaultData(): array
    {
        return [
            'stats' => [
                ['value' => '1.200+', 'line1' => 'Umzüge in Berlin', 'line2' => 'erfolgreich durchgeführt'],
                ['value' => '4,9 ★', 'line1' => 'Google Bewertung', 'line2' => 'von zufriedenen Kunden'],
                ['value' => '100%', 'line1' => 'Festpreisgarantie', 'line2' => 'ohne versteckte Kosten'],
                ['value' => '24 Std.', 'line1' => 'Angebot erhalten', 'line2' => 'schnell & unverbindlich'],
            ],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public static function settingsRules(): array
    {
        return [
            'theme' => ['required', 'in:dark,light'],
            'bg_image_path' => ['nullable', 'string'],
            'bg_image_url' => ['nullable', 'string'],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public static function dataRules(): array
    {
        return [
            'stats' => ['nullable', 'array'],
            'stats.*.value' => ['nullable', 'string'],
            'stats.*.line1' => ['nullable', 'string'],
            'stats.*.line2' => ['nullable', 'string'],
        ];
    }
}
