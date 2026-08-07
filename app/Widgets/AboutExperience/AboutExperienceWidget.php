<?php

declare(strict_types=1);

namespace App\Widgets\AboutExperience;

use App\Widgets\Concerns\ProvidesWidgetDefaults;
use App\Widgets\Contracts\WidgetContract;

final class AboutExperienceWidget implements WidgetContract
{
    use ProvidesWidgetDefaults;

    public static function type(): string
    {
        return 'about_experience';
    }

    public static function label(): string
    {
        return 'About + Experience';
    }

    public static function icon(): string
    {
        return 'BadgeCheck';
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
            'founder_path' => null,
            'founder_url' => null,
        ];
    }

    public static function defaultData(): array
    {
        return [
            'eyebrow' => 'Über uns',
            'heading' => 'Wir bieten die beste Umzugslösung',
            'heading_accent' => 'in Berlin',
            'body' => 'Die Umzugsbranche lebt von Vertrauen und einem guten Ruf. An jedem Berührungspunkt soll unser Service professionell, zuverlässig und fürsorglich wirken – besonders an einem Tag voller Aufregung wie Ihrem Umzug.',
            'experience_value' => '25+',
            'experience_label' => 'Jahre Erfahrung',
            'image_alt' => 'Moovato Kundin trägt einen Umzugskarton in ihrer neuen Wohnung',
            'image_2_alt' => 'Moovato Mitarbeiter prüft Pakete im Umzugswagen',
            'points' => [
                'Gewissheit, dass der Umzug wie geplant stattfindet',
                'Professionelles Management komplexer Logistik',
                'Pünktlich, sorgfältig und ohne versteckte Kosten',
                'Professionelle Verpackungsmaterialien und -techniken',
            ],
            'button_label' => 'Mehr über uns',
            'button_url' => '#leistungen',
            'founder_name' => 'Lukas Hoffmann',
            'founder_role' => 'Gründer',
            'founder_alt' => 'Porträt von Lukas Hoffmann, Gründer von Moovato',
        ];
    }

    public static function settingsRules(): array
    {
        return [
            'image_path' => ['nullable', 'string'],
            'image_url' => ['nullable', 'string'],
            'image_2_path' => ['nullable', 'string'],
            'image_2_url' => ['nullable', 'string'],
            'founder_path' => ['nullable', 'string'],
            'founder_url' => ['nullable', 'string'],
        ];
    }

    public static function dataRules(): array
    {
        return [
            'eyebrow' => ['nullable', 'string'],
            'heading' => ['nullable', 'string'],
            'heading_accent' => ['nullable', 'string'],
            'body' => ['nullable', 'string'],
            'experience_value' => ['nullable', 'string'],
            'experience_label' => ['nullable', 'string'],
            'image_alt' => ['nullable', 'string'],
            'image_2_alt' => ['nullable', 'string'],
            'points' => ['nullable', 'array'],
            'points.*' => ['nullable', 'string'],
            'button_label' => ['nullable', 'string'],
            'button_url' => ['nullable', 'string'],
            'founder_name' => ['nullable', 'string'],
            'founder_role' => ['nullable', 'string'],
            'founder_alt' => ['nullable', 'string'],
        ];
    }
}
