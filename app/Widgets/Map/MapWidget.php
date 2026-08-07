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
                ['x' => 41, 'y' => 43, 'city' => 'Mitte', 'label' => 'Hauptsitz · Umzugsservice', 'flag_path' => null, 'flag_url' => null],
                ['x' => 30, 'y' => 28, 'city' => 'Reinickendorf', 'label' => 'Umzugsservice verfügbar', 'flag_path' => null, 'flag_url' => null],
                ['x' => 50, 'y' => 26, 'city' => 'Pankow', 'label' => 'Umzugsservice verfügbar', 'flag_path' => null, 'flag_url' => null],
                ['x' => 16, 'y' => 47, 'city' => 'Spandau', 'label' => 'Umzugsservice verfügbar', 'flag_path' => null, 'flag_url' => null],
                ['x' => 28, 'y' => 53, 'city' => 'Charlottenburg-Wilmersdorf', 'label' => 'Umzugsservice verfügbar', 'flag_path' => null, 'flag_url' => null],
                ['x' => 60, 'y' => 42, 'city' => 'Lichtenberg', 'label' => 'Umzugsservice verfügbar', 'flag_path' => null, 'flag_url' => null],
                ['x' => 69, 'y' => 44, 'city' => 'Marzahn-Hellersdorf', 'label' => 'Umzugsservice verfügbar', 'flag_path' => null, 'flag_url' => null],
                ['x' => 49, 'y' => 50, 'city' => 'Friedrichshain-Kreuzberg', 'label' => 'Umzugsservice verfügbar', 'flag_path' => null, 'flag_url' => null],
                ['x' => 44, 'y' => 67, 'city' => 'Tempelhof-Schöneberg', 'label' => 'Umzugsservice verfügbar', 'flag_path' => null, 'flag_url' => null],
                ['x' => 54, 'y' => 66, 'city' => 'Neukölln', 'label' => 'Umzugsservice verfügbar', 'flag_path' => null, 'flag_url' => null],
                ['x' => 25, 'y' => 70, 'city' => 'Steglitz-Zehlendorf', 'label' => 'Umzugsservice verfügbar', 'flag_path' => null, 'flag_url' => null],
                ['x' => 75, 'y' => 68, 'city' => 'Treptow-Köpenick', 'label' => 'Umzugsservice verfügbar', 'flag_path' => null, 'flag_url' => null],
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
            'image_path' => ['nullable', 'string'],
            'image_url' => ['nullable', 'string'],
            'pins' => ['nullable', 'array'],
            'pins.*.x' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'pins.*.y' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'pins.*.city' => ['nullable', 'string'],
            'pins.*.label' => ['nullable', 'string'],
            'pins.*.flag_path' => ['nullable', 'string'],
            'pins.*.flag_url' => ['nullable', 'string'],
        ];
    }

    public static function dataRules(): array
    {
        return [
            'eyebrow' => ['nullable', 'string'],
            'heading' => ['nullable', 'string'],
            'subheading' => ['nullable', 'string'],
        ];
    }
}
