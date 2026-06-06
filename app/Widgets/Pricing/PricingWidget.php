<?php

declare(strict_types=1);

namespace App\Widgets\Pricing;

use App\Widgets\Concerns\ProvidesWidgetDefaults;
use App\Widgets\Contracts\WidgetContract;

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
        return [];
    }

    /**
     * @return array<string, mixed>
     */
    public static function defaultData(): array
    {
        return [
            'eyebrow' => 'Flexible Preise',
            'heading' => 'Transparente Preise für jeden Umzug',
            'plans' => [
                [
                    'name' => 'Privat',
                    'price' => 'ab €350',
                    'icon' => 'Home',
                    'cta_label' => 'Anfragen',
                    'cta_url' => '#angebot',
                    'features' => [
                        ['label' => 'Privatumzug in ganz Berlin', 'included' => true],
                        ['label' => 'Transport, Fahrzeug & Be-/Entladen', 'included' => true],
                        ['label' => 'Umzugsdecken & Spanngurte', 'included' => true],
                        ['label' => 'Versicherter Transport', 'included' => true],
                        ['label' => 'Möbelmontage', 'included' => false],
                        ['label' => 'Einlagerung (1 Monat)', 'included' => false],
                    ],
                ],
                [
                    'name' => 'Gewerbe',
                    'price' => 'ab €450',
                    'icon' => 'Building2',
                    'cta_label' => 'Anfragen',
                    'cta_url' => '#angebot',
                    'features' => [
                        ['label' => 'Büro- & Firmenumzug in Berlin', 'included' => true],
                        ['label' => 'Transport, Fahrzeug & Be-/Entladen', 'included' => true],
                        ['label' => 'Umzug am Wochenende möglich', 'included' => true],
                        ['label' => 'Versicherter Transport', 'included' => true],
                        ['label' => 'Möbelmontage', 'included' => true],
                        ['label' => 'Einlagerung (1 Monat)', 'included' => false],
                    ],
                ],
                [
                    'name' => 'Fernumzug',
                    'price' => 'auf Anfrage',
                    'icon' => 'Route',
                    'cta_label' => 'Anfragen',
                    'cta_url' => '#angebot',
                    'features' => [
                        ['label' => 'Fernumzug ab Berlin deutschlandweit', 'included' => true],
                        ['label' => 'Transport, Fahrzeug & Be-/Entladen', 'included' => true],
                        ['label' => 'Spezialtransport für empfindliches Gut', 'included' => true],
                        ['label' => 'Versicherter Transport', 'included' => true],
                        ['label' => 'Möbelmontage', 'included' => true],
                        ['label' => 'Einlagerung (1 Monat)', 'included' => true],
                    ],
                ],
            ],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public static function settingsRules(): array
    {
        return [];
    }

    /**
     * @return array<string, mixed>
     */
    public static function dataRules(): array
    {
        return [
            'eyebrow' => ['nullable', 'string', 'max:60'],
            'heading' => ['nullable', 'string', 'max:160'],
            'plans' => ['nullable', 'array', 'max:6'],
            'plans.*.name' => ['nullable', 'string', 'max:80'],
            'plans.*.price' => ['nullable', 'string', 'max:40'],
            'plans.*.icon' => ['nullable', 'string', 'max:64'],
            'plans.*.cta_label' => ['nullable', 'string', 'max:80'],
            'plans.*.cta_url' => ['nullable', 'string', 'max:2000'],
            'plans.*.features' => ['nullable', 'array', 'max:12'],
            'plans.*.features.*.label' => ['nullable', 'string', 'max:120'],
            'plans.*.features.*.included' => ['boolean'],
        ];
    }
}
