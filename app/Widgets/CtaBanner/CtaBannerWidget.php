<?php

declare(strict_types=1);

namespace App\Widgets\CtaBanner;

use App\Widgets\Concerns\ProvidesWidgetDefaults;
use App\Widgets\Contracts\WidgetContract;

final class CtaBannerWidget implements WidgetContract
{
    use ProvidesWidgetDefaults;

    public static function type(): string
    {
        return 'cta_banner';
    }

    public static function label(): string
    {
        return 'CTA Banner';
    }

    public static function icon(): string
    {
        return 'RectangleHorizontal';
    }

    public static function category(): string
    {
        return 'layout';
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
            'heading' => 'Ihr zuverlässiges Umzugsunternehmen in Berlin',
            'primary_label' => 'Kostenloses Angebot anfordern',
            'primary_url' => '#angebot',
            'points' => [
                'Privat, Gewerbe, Fernumzug & Spezialtransport',
                'Engagierter, zuverlässiger und stressfreier Service',
                'Kundenorientiert mit transparenter Kommunikation',
                'Für Privatkunden und Unternehmen mit Sorgfalt',
            ],
            'image_alt' => 'Moovato Umzugsteam bei der Arbeit in Berlin',
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
            'heading' => ['nullable', 'string'],
            'primary_label' => ['nullable', 'string'],
            'primary_url' => ['nullable', 'string'],
            'points' => ['nullable', 'array'],
            'points.*' => ['nullable', 'string'],
            'image_alt' => ['nullable', 'string'],
        ];
    }
}
