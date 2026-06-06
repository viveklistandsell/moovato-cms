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
                'Privatumzug',
                'Gewerbeumzug',
                'Fernumzug',
                'Spezialtransport',
            ],
            'button_label' => 'Service anfragen',
            'button_url' => '#kontakt',
        ];
    }

    public static function settingsRules(): array
    {
        return [
            'image_path' => ['nullable', 'string', 'max:1000'],
            'image_url' => ['nullable', 'string', 'max:2000'],
            'image_side' => ['required', 'in:left,right'],
        ];
    }

    public static function dataRules(): array
    {
        return [
            'eyebrow' => ['nullable', 'string', 'max:120'],
            'heading' => ['nullable', 'string', 'max:255'],
            'body' => ['nullable', 'string', 'max:1200'],
            'list_title' => ['nullable', 'string', 'max:120'],
            'points' => ['nullable', 'array', 'max:8'],
            'points.*' => ['nullable', 'string', 'max:160'],
            'button_label' => ['nullable', 'string', 'max:80'],
            'button_url' => ['nullable', 'string', 'max:2000'],
        ];
    }
}
