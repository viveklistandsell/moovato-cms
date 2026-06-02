<?php

declare(strict_types=1);

namespace App\Widgets\TrustBar;

use App\Widgets\Concerns\ProvidesWidgetDefaults;
use App\Widgets\Contracts\WidgetContract;

/**
 * Trust bar: a row of headline stats (big accent number + italic serif caption)
 * with an optional rotating circular-text badge. Dark by default; recolored to
 * the Moovato theme (orange numbers/badge on a midnight background).
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
            'show_badge' => true,
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
            ],
            'badge_text' => 'MOOVATO · UMZUG · BERLIN · ',
            'badge_icon' => 'ArrowUpRight',
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public static function settingsRules(): array
    {
        return [
            'theme' => ['required', 'in:dark,light'],
            'show_badge' => ['boolean'],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public static function dataRules(): array
    {
        return [
            'stats' => ['nullable', 'array', 'max:4'],
            'stats.*.value' => ['nullable', 'string', 'max:24'],
            'stats.*.line1' => ['nullable', 'string', 'max:80'],
            'stats.*.line2' => ['nullable', 'string', 'max:80'],
            'badge_text' => ['nullable', 'string', 'max:80'],
            'badge_icon' => ['nullable', 'string', 'max:64'],
        ];
    }
}
