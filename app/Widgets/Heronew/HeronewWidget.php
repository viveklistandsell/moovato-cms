<?php

declare(strict_types=1);

namespace App\Widgets\Heronew;

use App\Widgets\Concerns\ProvidesWidgetDefaults;
use App\Widgets\Contracts\WidgetContract;

/**
 * Moovato split hero: left content column (badge, headline with highlight,
 * checklist, CTAs) and a right visual column (masked photo, floating service
 * pills, trust strip). Ported from the axion-studio template — the animated
 * shader background is replaced by a lightweight CSS gradient for speed.
 */
final class HeronewWidget implements WidgetContract
{
    use ProvidesWidgetDefaults;

    public static function type(): string
    {
        return 'heronew';
    }

    public static function label(): string
    {
        return 'Moovato Hero';
    }

    public static function icon(): string
    {
        return 'Truck';
    }

    public static function category(): string
    {
        return 'layout';
    }

    /**
     * @return array<string, mixed>
     */
    public static function defaultSettings(): array
    {
        return [
            'image_path' => null,
            'image_url' => null,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public static function defaultData(): array
    {
        return [
            'badge' => 'Moovato Umzugsservice · Berlin',
            'title_lead' => 'Dein modernes & effizientes',
            'title_highlight' => 'UMZUGS',
            'title_tail' => 'Unternehmen in Berlin',
            'image_alt' => 'Moovato Mover',
            'features' => [
                'Festpreisgarantie',
                'Versichert & geprüft',
                'Pünktlich & zuverlässig',
                'Erfahrene Umzugsprofis',
            ],
            'primary_label' => 'Angebot anfordern',
            'primary_url' => '#angebot',
            'secondary_label' => 'Unsere Leistungen',
            'secondary_url' => '#leistungen',
            'pills' => [
                ['icon' => 'Home', 'label' => 'Privatumzug'],
                ['icon' => 'Briefcase', 'label' => 'Gewerbeumzug'],
                ['icon' => 'Truck', 'label' => 'Fernumzug'],
                ['icon' => 'Package', 'label' => 'Spezialtransport'],
            ],
            'avatars' => ['M', 'T', 'A'],
            'trust_title' => '4,9 ★ · 1.200+ Umzüge',
            'trust_subtitle' => 'in Berlin durchgeführt',
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public static function settingsRules(): array
    {
        return [
            'image_path' => ['nullable', 'string', 'max:1000'],
            'image_url' => ['nullable', 'string', 'max:2000'],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public static function dataRules(): array
    {
        return [
            'badge' => ['nullable', 'string', 'max:120'],
            'title_lead' => ['nullable', 'string', 'max:160'],
            'title_highlight' => ['nullable', 'string', 'max:40'],
            'title_tail' => ['nullable', 'string', 'max:160'],
            'image_alt' => ['nullable', 'string', 'max:160'],
            'features' => ['nullable', 'array', 'max:8'],
            'features.*' => ['nullable', 'string', 'max:120'],
            'primary_label' => ['nullable', 'string', 'max:80'],
            'primary_url' => ['nullable', 'string', 'max:2000'],
            'secondary_label' => ['nullable', 'string', 'max:80'],
            'secondary_url' => ['nullable', 'string', 'max:2000'],
            'pills' => ['nullable', 'array', 'max:6'],
            'pills.*.icon' => ['nullable', 'string', 'max:64'],
            'pills.*.label' => ['nullable', 'string', 'max:60'],
            'avatars' => ['nullable', 'array', 'max:5'],
            'avatars.*' => ['nullable', 'string', 'max:3'],
            'trust_title' => ['nullable', 'string', 'max:120'],
            'trust_subtitle' => ['nullable', 'string', 'max:120'],
        ];
    }
}
