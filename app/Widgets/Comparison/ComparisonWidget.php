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
                        ['label' => '2-Zimmer-Wohnung in Berlin', 'left' => 'ab 890 €', 'right' => 'ab 540 €'],
                        ['label' => 'Büroumzug (bis 10 Arbeitsplätze)', 'left' => 'ab 2.400 €', 'right' => 'ab 1.690 €'],
                        ['label' => 'Möbelmontage pro Stunde', 'left' => '65 €', 'right' => '45 €'],
                        ['label' => 'Einlagerung pro m³ / Monat', 'left' => '19 €', 'right' => '12 €'],
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
            'eyebrow' => ['nullable', 'string', 'max:60'],
            'heading' => ['nullable', 'string', 'max:160'],
            'tabs' => ['nullable', 'array', 'max:4'],
            'tabs.*.label' => ['nullable', 'string', 'max:80'],
            'tabs.*.mode' => ['nullable', 'in:value,check'],
            'tabs.*.col_left' => ['nullable', 'string', 'max:80'],
            'tabs.*.col_right' => ['nullable', 'string', 'max:80'],
            'tabs.*.use_logo' => ['boolean'],
            'tabs.*.rows' => ['nullable', 'array', 'max:20'],
            'tabs.*.rows.*.label' => ['nullable', 'string', 'max:160'],
            'tabs.*.rows.*.left' => ['nullable'],
            'tabs.*.rows.*.right' => ['nullable'],
        ];
    }
}
