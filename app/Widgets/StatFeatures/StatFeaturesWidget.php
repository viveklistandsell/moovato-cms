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
            'image_path' => ['nullable', 'string'],
            'image_url' => ['nullable', 'string'],
        ];
    }

    public static function dataRules(): array
    {
        return [
            'eyebrow' => ['nullable', 'string'],
            'heading' => ['nullable', 'string'],
            'body' => ['nullable', 'string'],
            'image_alt' => ['nullable', 'string'],
            'stat_value' => ['nullable', 'string'],
            'stat_label' => ['nullable', 'string'],
            'features' => ['nullable', 'array'],
            'features.*.title' => ['nullable', 'string'],
            'features.*.description' => ['nullable', 'string'],
        ];
    }
}
