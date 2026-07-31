<?php

declare(strict_types=1);

namespace App\Widgets\StatFeatures;

use App\Widgets\Concerns\ProvidesWidgetDefaults;
use App\Widgets\Contracts\WidgetContract;

final class StatFeaturesWidget implements WidgetContract
{
    use ProvidesWidgetDefaults;

    public static function type(): string
    {
        return 'stat_features';
    }

    public static function label(): string
    {
        return 'Numbered Features + Stat';
    }

    public static function icon(): string
    {
        return 'TrendingUp';
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
        ];
    }

    public static function defaultData(): array
    {
        return [
            'eyebrow' => '',
            'heading' => 'Warum Kundinnen und Kunden Moovato vertrauen',
            'body' => 'Wir setzen auf strukturierte Umzüge mit sorgfältiger Planung, geschultem Personal und einem klar organisierten Ablauf.',
            'image_alt' => 'Moovato Mitarbeiter trägt einen Karton vor einem Mehrfamilienhaus',
            'stat_value' => '1.5k+',
            'stat_label' => 'Kunden',
            'features' => [
                ['title' => 'Geschulte Umzugsprofis', 'description' => 'Unser erfahrenes Team nutzt die richtigen Techniken und Schutzmaterialien für einen sicheren Transport.'],
                ['title' => 'Pünktliche Termine', 'description' => 'Wir planen Zeitfenster und Routen so, dass Ihre Sachen genau wie vereinbart ankommen.'],
                ['title' => 'Sichere Verpackung', 'description' => 'Hochwertige Materialien und ein klares Beschriftungssystem verhindern Schäden während des Umzugs.'],
            ],
        ];
    }

    public static function settingsRules(): array
    {
        return [
            'image_path' => ['nullable', 'string', 'max:1000'],
            'image_url' => ['nullable', 'string', 'max:2000'],
        ];
    }

    public static function dataRules(): array
    {
        return [
            'eyebrow' => ['nullable', 'string', 'max:120'],
            'heading' => ['nullable', 'string', 'max:255'],
            'body' => ['nullable', 'string', 'max:600'],
            'image_alt' => ['nullable', 'string', 'max:160'],
            'stat_value' => ['nullable', 'string', 'max:16'],
            'stat_label' => ['nullable', 'string', 'max:40'],
            'features' => ['nullable', 'array', 'max:6'],
            'features.*.title' => ['nullable', 'string', 'max:120'],
            'features.*.description' => ['nullable', 'string', 'max:300'],
        ];
    }
}
