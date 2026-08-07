<?php

declare(strict_types=1);

namespace App\Widgets\ServiceCards;

use App\Widgets\Concerns\ProvidesWidgetDefaults;
use App\Widgets\Contracts\WidgetContract;

final class ServiceCardsWidget implements WidgetContract
{
    use ProvidesWidgetDefaults;

    public static function type(): string
    {
        return 'service_cards';
    }

    public static function label(): string
    {
        return 'Service Cards';
    }

    public static function icon(): string
    {
        return 'LayoutGrid';
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
            'columns' => 4,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public static function defaultData(): array
    {
        return [
            'eyebrow' => 'Unsere Leistungen',
            'heading' => 'Umzugsservices für jeden Bedarf',
            'items' => [
                ['icon' => 'Home', 'title' => 'Privat', 'description' => 'Stressfreier Privatumzug in Berlin – von der Einzimmerwohnung bis zum Familienhaus, sorgfältig geplant und zuverlässig durchgeführt.'],
                ['icon' => 'Building2', 'title' => 'Gewerbe', 'description' => 'Büro- und Firmenumzüge mit minimaler Ausfallzeit – wir bringen Ihr Unternehmen strukturiert an den neuen Standort.'],
                ['icon' => 'Route', 'title' => 'Fernumzug', 'description' => 'Deutschland- und europaweite Fernumzüge ab Berlin – sicher verpackt, versichert transportiert und pünktlich geliefert.'],
                ['icon' => 'PackageOpen', 'title' => 'Spezialtransport', 'description' => 'Klaviere, Antiquitäten und empfindliche Güter – unser Spezialtransport bringt auch das Außergewöhnliche sicher ans Ziel.'],
            ],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public static function settingsRules(): array
    {
        return [
            'columns' => ['required', 'integer', 'in:2,3,4'],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public static function dataRules(): array
    {
        return [
            'eyebrow' => ['nullable', 'string'],
            'heading' => ['nullable', 'string'],
            'items' => ['nullable', 'array'],
            'items.*.icon' => ['nullable', 'string'],
            'items.*.title' => ['nullable', 'string'],
            'items.*.description' => ['nullable', 'string'],
        ];
    }
}
