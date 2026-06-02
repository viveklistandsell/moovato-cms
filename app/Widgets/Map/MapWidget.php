<?php

declare(strict_types=1);

namespace App\Widgets\Map;

use App\Widgets\Concerns\ProvidesWidgetDefaults;
use App\Widgets\Contracts\WidgetContract;

final class MapWidget implements WidgetContract
{
    use ProvidesWidgetDefaults;

    public static function type(): string
    {
        return 'map';
    }

    public static function label(): string
    {
        return 'Service Map';
    }

    public static function icon(): string
    {
        return 'MapPinned';
    }

    public static function category(): string
    {
        return 'content';
    }

    public static function defaultSettings(): array
    {
        return [
            'image_path' => null,
            'image_url' => '/images/berlin-map.webp',
            'pins' => [
                ['x' => 47, 'y' => 50, 'city' => 'Mitte', 'label' => 'Hauptsitz · Umzugsservice', 'flag_path' => null, 'flag_url' => null],
                ['x' => 32, 'y' => 23, 'city' => 'Reinickendorf', 'label' => 'Umzugsservice verfügbar', 'flag_path' => null, 'flag_url' => null],
                ['x' => 55, 'y' => 21, 'city' => 'Pankow', 'label' => 'Umzugsservice verfügbar', 'flag_path' => null, 'flag_url' => null],
                ['x' => 15, 'y' => 46, 'city' => 'Spandau', 'label' => 'Umzugsservice verfügbar', 'flag_path' => null, 'flag_url' => null],
                ['x' => 33, 'y' => 61, 'city' => 'Charlottenburg-Wilmersdorf', 'label' => 'Umzugsservice verfügbar', 'flag_path' => null, 'flag_url' => null],
                ['x' => 67, 'y' => 44, 'city' => 'Lichtenberg', 'label' => 'Umzugsservice verfügbar', 'flag_path' => null, 'flag_url' => null],
                ['x' => 83, 'y' => 42, 'city' => 'Marzahn-Hellersdorf', 'label' => 'Umzugsservice verfügbar', 'flag_path' => null, 'flag_url' => null],
                ['x' => 56, 'y' => 58, 'city' => 'Friedrichshain-Kreuzberg', 'label' => 'Umzugsservice verfügbar', 'flag_path' => null, 'flag_url' => null],
                ['x' => 45, 'y' => 72, 'city' => 'Tempelhof-Schöneberg', 'label' => 'Umzugsservice verfügbar', 'flag_path' => null, 'flag_url' => null],
                ['x' => 57, 'y' => 73, 'city' => 'Neukölln', 'label' => 'Umzugsservice verfügbar', 'flag_path' => null, 'flag_url' => null],
                ['x' => 28, 'y' => 78, 'city' => 'Steglitz-Zehlendorf', 'label' => 'Umzugsservice verfügbar', 'flag_path' => null, 'flag_url' => null],
                ['x' => 73, 'y' => 76, 'city' => 'Treptow-Köpenick', 'label' => 'Umzugsservice verfügbar', 'flag_path' => null, 'flag_url' => null],
            ],
        ];
    }

    public static function defaultData(): array
    {
        return [
            'eyebrow' => 'Einsatzgebiete',
            'heading' => 'In jedem Berliner Bezirk für Sie da',
            'subheading' => 'Von Mitte bis Köpenick – Moovato übernimmt Ihren Umzug in ganz Berlin. Fahren Sie über die Bezirke, um mehr zu erfahren.',
        ];
    }

    public static function settingsRules(): array
    {
        return [
            'image_path' => ['nullable', 'string', 'max:1000'],
            'image_url' => ['nullable', 'string', 'max:2000'],
            'pins' => ['nullable', 'array', 'max:30'],
            'pins.*.x' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'pins.*.y' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'pins.*.city' => ['nullable', 'string', 'max:80'],
            'pins.*.label' => ['nullable', 'string', 'max:160'],
            'pins.*.flag_path' => ['nullable', 'string', 'max:1000'],
            'pins.*.flag_url' => ['nullable', 'string', 'max:2000'],
        ];
    }

    public static function dataRules(): array
    {
        return [
            'eyebrow' => ['nullable', 'string', 'max:120'],
            'heading' => ['nullable', 'string', 'max:255'],
            'subheading' => ['nullable', 'string', 'max:500'],
        ];
    }
}
