<?php

declare(strict_types=1);

namespace App\Widgets\AboutStats;

use App\Widgets\Concerns\ProvidesWidgetDefaults;
use App\Widgets\Contracts\WidgetContract;

final class AboutStatsWidget implements WidgetContract
{
    use ProvidesWidgetDefaults;

    public static function type(): string
    {
        return 'about_stats';
    }

    public static function label(): string
    {
        return 'About + Stats';
    }

    public static function icon(): string
    {
        return 'ChartNoAxesColumn';
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
            'image_2_path' => null,
            'image_2_url' => null,
        ];
    }

    public static function defaultData(): array
    {
        return [
            'eyebrow' => 'Über uns',
            'heading' => 'Erleben Sie einen besseren Umzug',
            'heading_accent' => 'mit einem Team, das wirklich anpackt',
            'body' => 'Von Privatumzug über Gewerbeumzug und Fernumzug bis Spezialtransport steht Moovato für Termintreue, faire Festpreise und ein erfahrenes Team. So wird Ihr Umzug in Berlin von Anfang bis Ende planbar und stressfrei.',
            'reviews_label' => 'Basierend auf 204 Bewertungen',
            'avatars' => ['M', 'T', 'A', 'S'],
            'image_alt' => 'Moovato Team verpackt Umzugskartons in einer Berliner Wohnung',
            'image_2_alt' => 'Lächelnder Moovato Umzugshelfer mit Paket',
            'stats' => [
                ['description' => 'Garantierte Zufriedenheit'],
                ['description' => 'Aktiv in 25 Städten'],
            ],
            'button_label' => 'Mehr über uns',
            'button_url' => '#leistungen',
        ];
    }

    public static function settingsRules(): array
    {
        return [
            'image_path' => ['nullable', 'string'],
            'image_url' => ['nullable', 'string'],
            'image_2_path' => ['nullable', 'string'],
            'image_2_url' => ['nullable', 'string'],
        ];
    }

    public static function dataRules(): array
    {
        return [
            'eyebrow' => ['nullable', 'string'],
            'heading' => ['nullable', 'string'],
            'heading_accent' => ['nullable', 'string'],
            'body' => ['nullable', 'string'],
            'reviews_label' => ['nullable', 'string'],
            'avatars' => ['nullable', 'array'],
            'avatars.*' => ['nullable', 'string'],
            'image_alt' => ['nullable', 'string'],
            'image_2_alt' => ['nullable', 'string'],
            'stats' => ['nullable', 'array'],
            'stats.*.description' => ['nullable', 'string'],
            'button_label' => ['nullable', 'string'],
            'button_url' => ['nullable', 'string'],
        ];
    }
}
