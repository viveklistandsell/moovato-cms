<?php

declare(strict_types=1);

namespace App\Widgets\MissionVision;

use App\Widgets\Concerns\ProvidesWidgetDefaults;
use App\Widgets\Contracts\WidgetContract;

final class MissionVisionWidget implements WidgetContract
{
    use ProvidesWidgetDefaults;

    public static function type(): string
    {
        return 'mission_vision';
    }

    public static function label(): string
    {
        return 'Mission & Vision';
    }

    public static function icon(): string
    {
        return 'Target';
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
            'image_alt' => 'Moovato Team beim Handschlag nach einem erfolgreichen Umzug in Berlin',
            'heading' => 'Umzüge, auf die Sie sich verlassen können',
            'subheading' => 'Sicher, pünktlich und ohne Überraschungen',
            'body' => 'Wir sind spezialisiert auf zuverlässige, sichere und planbare Umzugsdienstleistungen für Privatpersonen und Unternehmen in Berlin und Umgebung.',
            'button_label' => 'Mehr erfahren',
            'button_url' => '#leistungen',
            'panel_one_heading' => 'Unser Auftrag',
            'panel_one_body' => 'Unser Auftrag ist es, jeden Umzug so stressfrei wie möglich zu gestalten – mit erfahrenem Personal, transparenten Festpreisen und einem Service, der wirklich hält, was er verspricht.',
            'panel_one_points' => [
                'Maßgeschneiderte Umzugslösungen',
                'Verlässliche Partnerschaft',
                'Höchster Anspruch an Qualität',
            ],
            'panel_two_heading' => 'Unsere Vision',
            'panel_two_body' => 'Wir wollen der Umzugspartner sein, dem Berlin vertraut – mit einem Service, der Umzüge so einfach macht, wie sie sein sollten.',
            'panel_two_points' => [
                'Innovativer Ansatz',
                'Kundenorientierung',
                'Regionale Verwurzelung',
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
            'image_alt' => ['nullable', 'string'],
            'heading' => ['nullable', 'string'],
            'subheading' => ['nullable', 'string'],
            'body' => ['nullable', 'string'],
            'button_label' => ['nullable', 'string'],
            'button_url' => ['nullable', 'string'],
            'panel_one_heading' => ['nullable', 'string'],
            'panel_one_body' => ['nullable', 'string'],
            'panel_one_points' => ['nullable', 'array'],
            'panel_one_points.*' => ['nullable', 'string'],
            'panel_two_heading' => ['nullable', 'string'],
            'panel_two_body' => ['nullable', 'string'],
            'panel_two_points' => ['nullable', 'array'],
            'panel_two_points.*' => ['nullable', 'string'],
        ];
    }
}
