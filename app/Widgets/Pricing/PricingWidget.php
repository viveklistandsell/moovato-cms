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
            'eyebrow' => 'Für Umzugsunternehmen',
            'heading' => 'Werden Sie Moovato-Partner und erhalten Sie mehr Umzugsanfragen',
            'plans' => [
                [
                    'name' => 'Basic',
                    'price' => 'ab €99/Monat',
                    'icon' => 'Truck',
                    'cta_label' => 'Partner werden',
                    'cta_url' => '#partner',
                    'features' => [
                        ['label' => 'Bis zu 10 Leads pro Monat', 'included' => true],
                        ['label' => 'Eintrag im Unternehmensverzeichnis', 'included' => true],
                        ['label' => 'E-Mail-Benachrichtigung bei neuen Anfragen', 'included' => true],
                        ['label' => 'Geteilte Leads (bis zu 3 Partner)', 'included' => true],
                        ['label' => 'Bevorzugte Platzierung', 'included' => false],
                        ['label' => 'Exklusive Leads', 'included' => false],
                        ['label' => 'Persönlicher Account Manager', 'included' => false],
                    ],
                ],
                [
                    'name' => 'Premium',
                    'price' => 'ab €249/Monat',
                    'icon' => 'Sparkles',
                    'cta_label' => 'Partner werden',
                    'cta_url' => '#partner',
                    'features' => [
                        ['label' => 'Bis zu 40 Leads pro Monat', 'included' => true],
                        ['label' => 'Bevorzugte Platzierung im Verzeichnis', 'included' => true],
                        ['label' => 'Geteilte Leads (max. 2 Partner)', 'included' => true],
                        ['label' => '"Top Partner"-Bewertungs-Badge', 'included' => true],
                        ['label' => 'Monatlicher Performance-Report', 'included' => true],
                        ['label' => 'Exklusive Leads', 'included' => false],
                        ['label' => 'Persönlicher Account Manager', 'included' => false],
                    ],
                ],
                [
                    'name' => 'Exklusiv',
                    'price' => 'auf Anfrage',
                    'icon' => 'Crown',
                    'cta_label' => 'Beratung anfordern',
                    'cta_url' => '#partner',
                    'features' => [
                        ['label' => 'Unbegrenzte Leads', 'included' => true],
                        ['label' => 'Exklusive Leads (nur Sie erhalten die Anfrage)', 'included' => true],
                        ['label' => 'Top-Platzierung & Featured-Badge', 'included' => true],
                        ['label' => 'Persönlicher Account Manager', 'included' => true],
                        ['label' => 'Individuelles Reporting & API-Zugang', 'included' => true],
                        ['label' => 'Prioritäts-Support', 'included' => true],
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
            'eyebrow' => ['nullable', 'string'],
            'heading' => ['nullable', 'string'],
            'plans' => ['nullable', 'array'],
            'plans.*.name' => ['nullable', 'string'],
            'plans.*.price' => ['nullable', 'string'],
            'plans.*.icon' => ['nullable', 'string'],
            'plans.*.cta_label' => ['nullable', 'string'],
            'plans.*.cta_url' => ['nullable', 'string'],
            'plans.*.features' => ['nullable', 'array'],
            'plans.*.features.*.label' => ['nullable', 'string'],
            'plans.*.features.*.included' => ['boolean'],
        ];
    }
}
