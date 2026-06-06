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
                ['value' => '98%', 'title' => 'Zufriedenheit', 'description' => 'Garantierte Zufriedenheit'],
                ['value' => '150+', 'title' => 'Einsatzgebiete', 'description' => 'Aktiv in 25 Städten'],
            ],
            'button_label' => 'Mehr über uns',
            'button_url' => '#leistungen',
        ];
    }

    public static function settingsRules(): array
    {
        return [
            'image_path' => ['nullable', 'string', 'max:1000'],
            'image_url' => ['nullable', 'string', 'max:2000'],
            'image_2_path' => ['nullable', 'string', 'max:1000'],
            'image_2_url' => ['nullable', 'string', 'max:2000'],
        ];
    }

    public static function dataRules(): array
    {
        return [
            'eyebrow' => ['nullable', 'string', 'max:120'],
            'heading' => ['nullable', 'string', 'max:255'],
            'heading_accent' => ['nullable', 'string', 'max:255'],
            'body' => ['nullable', 'string', 'max:600'],
            'reviews_label' => ['nullable', 'string', 'max:120'],
            'avatars' => ['nullable', 'array', 'max:8'],
            'avatars.*' => ['nullable', 'string', 'max:3'],
            'image_alt' => ['nullable', 'string', 'max:160'],
            'image_2_alt' => ['nullable', 'string', 'max:160'],
            'stats' => ['nullable', 'array', 'max:4'],
            'stats.*.value' => ['nullable', 'string', 'max:24'],
            'stats.*.title' => ['nullable', 'string', 'max:80'],
            'stats.*.description' => ['nullable', 'string', 'max:160'],
            'button_label' => ['nullable', 'string', 'max:80'],
            'button_url' => ['nullable', 'string', 'max:2000'],
        ];
    }
}
