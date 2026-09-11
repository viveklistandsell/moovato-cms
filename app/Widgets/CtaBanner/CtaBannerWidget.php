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
                ['title' => 'Privat, Gewerbe, Fernumzug & Spezialtransport', 'description' => 'Wir decken jede Art von Umzug ab – ganz gleich, wie groß oder klein.'],
                ['title' => 'Engagierter, zuverlässiger und stressfreier Service', 'description' => 'Unser Team sorgt dafür, dass Ihr Umzugstag entspannt und reibungslos verläuft.'],
                ['title' => 'Kundenorientiert mit transparenter Kommunikation', 'description' => 'Sie erfahren jederzeit, was als Nächstes ansteht – ohne versteckte Überraschungen.'],
                ['title' => 'Für Privatkunden und Unternehmen mit Sorgfalt', 'description' => 'Ob Wohnung oder Büro – wir behandeln jeden Auftrag mit derselben Sorgfalt.'],
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
            'points.*.title' => ['nullable', 'string'],
            'points.*.description' => ['nullable', 'string'],
            'image_alt' => ['nullable', 'string'],
        ];
    }
}
