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
            'image_path' => ['nullable', 'string'],
            'image_url' => ['nullable', 'string'],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public static function dataRules(): array
    {
        return [
            'badge' => ['nullable', 'string'],
            'title_lead' => ['nullable', 'string'],
            'title_highlight' => ['nullable', 'string'],
            'title_tail' => ['nullable', 'string'],
            'image_alt' => ['nullable', 'string'],
            'features' => ['nullable', 'array'],
            'features.*' => ['nullable', 'string'],
            'primary_label' => ['nullable', 'string'],
            'primary_url' => ['nullable', 'string'],
            'secondary_label' => ['nullable', 'string'],
            'secondary_url' => ['nullable', 'string'],
            'pills' => ['nullable', 'array'],
            'pills.*.icon' => ['nullable', 'string'],
            'pills.*.label' => ['nullable', 'string'],
            'avatars' => ['nullable', 'array'],
            'avatars.*' => ['nullable', 'string'],
            'trust_title' => ['nullable', 'string'],
            'trust_subtitle' => ['nullable', 'string'],
        ];
    }
}
