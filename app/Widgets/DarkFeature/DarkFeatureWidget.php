<?php

declare(strict_types=1);

namespace App\Widgets\DarkFeature;

use App\Widgets\Concerns\ProvidesWidgetDefaults;
use App\Widgets\Contracts\WidgetContract;

final class DarkFeatureWidget implements WidgetContract
{
    use ProvidesWidgetDefaults;

    public static function type(): string
    {
        return 'dark_feature';
    }

    public static function label(): string
    {
        return 'Dark Feature';
    }

    public static function icon(): string
    {
        return 'LayoutPanelLeft';
    }

    public static function category(): string
    {
        return 'content';
    }

    public static function defaultSettings(): array
    {
        return [
            'image_path' => null,
            'image_url' => null,
            'image_side' => 'left', // left | right
        ];
    }

    public static function defaultData(): array
    {
        return [
            'eyebrow' => '',
            'heading' => 'Ein Umzugspartner für jede Anforderung',
            'body' => "Egal ob privater Wohnungswechsel, Firmenumzug, Umzug in eine andere Stadt oder der Transport empfindlicher Güter: Unser erfahrenes Berliner Team plant jedes Projekt sorgfältig und führt es zuverlässig aus.\n\nWir packen, transportieren und montieren effizient – mit Liebe zum Detail, voll versichert und zum Festpreis.",
            'list_title' => 'Unsere Leistungen',
            'points' => [
                ['title' => 'Privatumzug', 'description' => 'Ihr Wohnungswechsel in Berlin – von der Einzimmerwohnung bis zum Familienhaus.'],
                ['title' => 'Gewerbeumzug', 'description' => 'Büro- und Firmenumzüge mit minimaler Ausfallzeit für Ihr Unternehmen.'],
                ['title' => 'Fernumzug', 'description' => 'Zuverlässiger Transport deutschlandweit, egal wie weit der Weg ist.'],
                ['title' => 'Spezialtransport', 'description' => 'Sicherer Transport für Klaviere, Kunstwerke und andere empfindliche Gegenstände.'],
            ],
            'button_label' => 'Service anfragen',
            'button_url' => '#kontakt',
        ];
    }

    public static function settingsRules(): array
    {
        return [
            'image_path' => ['nullable', 'string'],
            'image_url' => ['nullable', 'string'],
            'image_side' => ['required', 'in:left,right'],
        ];
    }

    public static function dataRules(): array
    {
        return [
            'eyebrow' => ['nullable', 'string'],
            'heading' => ['nullable', 'string'],
            'body' => ['nullable', 'string'],
            'list_title' => ['nullable', 'string'],
            'points' => ['nullable', 'array'],
            'points.*.title' => ['nullable', 'string'],
            'points.*.description' => ['nullable', 'string'],
            'button_label' => ['nullable', 'string'],
            'button_url' => ['nullable', 'string'],
        ];
    }
}
