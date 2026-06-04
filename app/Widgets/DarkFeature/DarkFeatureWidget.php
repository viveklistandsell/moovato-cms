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
            'heading' => 'Professionelle Küchenmontage & -demontage',
            'body' => "Egal ob Sie in eine neue Wohnung ziehen oder eine alte Küche ersetzen: Unser erfahrenes Team sorgt für eine sichere und professionelle Montage und Demontage Ihrer Küche.\n\nWir bauen bestehende Küchen sorgfältig ab, transportieren die Komponenten bei Bedarf und installieren Ihre Küche effizient – mit Liebe zum Detail und Sauberkeit.",
            'list_title' => 'Enthaltene Leistungen',
            'points' => [
                'Küchendemontage',
                'Küchenmontage',
                'Wiederaufbau beim Umzug',
                'Geräteanschluss',
                'Sicherer Umgang mit Bauteilen',
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
