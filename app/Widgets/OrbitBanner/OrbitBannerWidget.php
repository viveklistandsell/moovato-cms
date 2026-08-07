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
            'theme' => 'light',
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
            'description' => 'Wir planen Ihren Umzug gemeinsam mit Ihnen – Privat, Gewerbe, Fernumzug und Spezialtransport. Mit Festpreis, geschultem Team und transparenter Kommunikation wird Ihr Umzug in Berlin stressfrei.',
            'primary_label' => 'Mehr erfahren',
            'primary_url' => '#angebot',
            'image_alt' => 'Moovato Umzugsteam in Berlin',
            'inline_image_alt' => 'Moovato Team beim Umzug',
        ];
    }

    public static function settingsRules(): array
    {
        return [
            'theme' => ['required', 'in:light,dark'],
            'image_path' => ['nullable', 'string'],
            'image_url' => ['nullable', 'string'],
            'inline_image_path' => ['nullable', 'string'],
            'inline_image_url' => ['nullable', 'string'],
        ];
    }

    public static function dataRules(): array
    {
        return [
            'crumbs' => ['nullable', 'array'],
            'crumbs.*.label' => ['nullable', 'string'],
            'crumbs.*.url' => ['nullable', 'string'],
            'highlight' => ['nullable', 'string'],
            'heading' => ['nullable', 'string'],
            'description' => ['nullable', 'string'],
            'primary_label' => ['nullable', 'string'],
            'primary_url' => ['nullable', 'string'],
            'image_alt' => ['nullable', 'string'],
            'inline_image_alt' => ['nullable', 'string'],
        ];
    }
}
