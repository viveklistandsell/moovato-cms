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
            'body' => 'Wir sind spezialisiert auf zuverlässige, sichere und planbare Umzugsdienstleistungen für Privatpersonen und Unternehmen in Berlin und Umgebung.',
            'button_label' => 'Mehr erfahren',
            'button_url' => '#leistungen',
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
            'body' => ['nullable', 'string'],
            'button_label' => ['nullable', 'string'],
            'button_url' => ['nullable', 'string'],
        ];
    }
}
