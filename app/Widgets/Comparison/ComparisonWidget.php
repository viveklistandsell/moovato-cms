<?php

declare(strict_types=1);

namespace App\Widgets\Comparison;

use App\Widgets\Concerns\ProvidesWidgetDefaults;
use App\Widgets\Contracts\WidgetContract;

final class ComparisonWidget implements WidgetContract
{
    use ProvidesWidgetDefaults;

    public static function type(): string
    {
        return 'comparison';
    }

    public static function label(): string
    {
        return 'Comparison Table';
    }

    public static function icon(): string
    {
        return 'Table2';
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
            'eyebrow' => 'Warum Moovato',
            'heading' => 'Moovato im Vergleich',
            'tabs' => [
                [
                    'label' => 'Preisvergleich',
                    'mode' => 'value',
                    'col_left' => 'Andere Anbieter',
                    'col_right' => 'Moovato',
                    'use_logo' => true,
                    'rows' => [
                        ['label' => 'Privatumzug (2-Zimmer-Wohnung in Berlin)', 'left' => 'ab 890 €', 'right' => 'ab 540 €'],
                        ['label' => 'Gewerbeumzug (bis 10 Arbeitsplätze)', 'left' => 'ab 2.400 €', 'right' => 'ab 1.690 €'],
                        ['label' => 'Fernumzug innerhalb Deutschlands', 'left' => 'ab 1.900 €', 'right' => 'ab 1.290 €'],
                        ['label' => 'Spezialtransport (Klavier, Tresor)', 'left' => 'ab 320 €', 'right' => 'ab 190 €'],
                        ['label' => 'Anfahrt innerhalb Berlins', 'left' => 'auf Anfrage', 'right' => 'inklusive'],
                        ['label' => 'Versteckte Zusatzkosten', 'left' => 'möglich', 'right' => 'keine'],
                    ],
                ],
                [
                    'label' => 'Leistungsvergleich',
                    'mode' => 'check',
                    'col_left' => 'Kein Vergleich',
                    'col_right' => 'Moovato',
                    'use_logo' => true,
                    'rows' => [
                        ['label' => 'Preistransparenz', 'left' => false, 'right' => true],
                        ['label' => 'Festpreisgarantie', 'left' => false, 'right' => true],
                        ['label' => 'Geprüfte Umzugsteams', 'left' => false, 'right' => true],
                        ['label' => 'Angebot innerhalb 24 Stunden', 'left' => false, 'right' => true],
                        ['label' => 'Persönliche Beratung', 'left' => false, 'right' => true],
                        ['label' => 'Kostenloses Angebot anfordern', 'left' => false, 'right' => true],
                        ['label' => 'Bis zu 40% günstiger', 'left' => false, 'right' => true],
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
            'tabs' => ['nullable', 'array'],
            'tabs.*.label' => ['nullable', 'string'],
            'tabs.*.mode' => ['nullable', 'in:value,check'],
            'tabs.*.col_left' => ['nullable', 'string'],
            'tabs.*.col_right' => ['nullable', 'string'],
            'tabs.*.use_logo' => ['boolean'],
            'tabs.*.rows' => ['nullable', 'array'],
            'tabs.*.rows.*.label' => ['nullable', 'string'],
            'tabs.*.rows.*.left' => ['nullable'],
            'tabs.*.rows.*.right' => ['nullable'],
        ];
    }
}
