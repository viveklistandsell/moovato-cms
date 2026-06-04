<?php

declare(strict_types=1);

namespace App\Widgets\OrbitBanner;

use App\Widgets\Concerns\ProvidesWidgetDefaults;
use App\Widgets\Contracts\WidgetContract;

final class OrbitBannerWidget implements WidgetContract
{
    use ProvidesWidgetDefaults;

    public static function type(): string
    {
        return 'orbit_banner';
    }

    public static function label(): string
    {
        return 'Orbit Banner';
    }

    public static function icon(): string
    {
        return 'Orbit';
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
            'inline_image_path' => null,
            'inline_image_url' => null,
        ];
    }

    public static function defaultData(): array
    {
        return [
            'crumbs' => [
                ['label' => 'Startseite', 'url' => '/'],
                ['label' => 'Über uns', 'url' => ''],
            ],
            'highlight' => 'Ihr zuverlässiges',
            'heading' => 'Umzugsunternehmen in Berlin',
            'description' => 'Wir planen Ihren Umzug gemeinsam mit Ihnen – vom Privatumzug über den Büroumzug bis zur Entrümpelung. Mit Festpreis, geschultem Team und transparenter Kommunikation wird Ihr Umzug in Berlin stressfrei.',
            'primary_label' => 'Mehr erfahren',
            'primary_url' => '#angebot',
            'image_alt' => 'Moovato Umzugsteam in Berlin',
            'inline_image_alt' => 'Moovato Team beim Umzug',
        ];
    }

    public static function settingsRules(): array
    {
        return [
            'image_path' => ['nullable', 'string', 'max:1000'],
            'image_url' => ['nullable', 'string', 'max:2000'],
            'inline_image_path' => ['nullable', 'string', 'max:1000'],
            'inline_image_url' => ['nullable', 'string', 'max:2000'],
        ];
    }

    public static function dataRules(): array
    {
        return [
            'crumbs' => ['nullable', 'array', 'max:5'],
            'crumbs.*.label' => ['nullable', 'string', 'max:80'],
            'crumbs.*.url' => ['nullable', 'string', 'max:2000'],
            'highlight' => ['nullable', 'string', 'max:120'],
            'heading' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:600'],
            'primary_label' => ['nullable', 'string', 'max:80'],
            'primary_url' => ['nullable', 'string', 'max:2000'],
            'image_alt' => ['nullable', 'string', 'max:160'],
            'inline_image_alt' => ['nullable', 'string', 'max:160'],
        ];
    }
}
