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
            'body' => '<p>Wir setzen auf strukturierte Umzüge mit sorgfältiger Planung, geschultem Personal und einem klar organisierten Ablauf.</p><ol><li><strong>Geschulte Umzugsprofis</strong> – Unser erfahrenes Team nutzt die richtigen Techniken und Schutzmaterialien für einen sicheren Transport.</li><li><strong>Pünktliche Termine</strong> – Wir planen Zeitfenster und Routen so, dass Ihre Sachen genau wie vereinbart ankommen.</li><li><strong>Sichere Verpackung</strong> – Hochwertige Materialien und ein klares Beschriftungssystem verhindern Schäden während des Umzugs.</li></ol>',
            'image_alt' => 'Moovato Mitarbeiter trägt einen Karton vor einem Mehrfamilienhaus',
            'stat_value' => '1.5k+',
            'stat_label' => 'Kunden',
            'button_label' => 'Kostenloses Angebot anfordern',
            'button_url' => '#angebot',
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
            'button_label' => ['nullable', 'string'],
            'button_url' => ['nullable', 'string'],
        ];
    }
}
