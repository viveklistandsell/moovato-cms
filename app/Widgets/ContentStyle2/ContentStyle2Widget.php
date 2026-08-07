<?php

declare(strict_types=1);

namespace App\Widgets\ContentStyle2;

use App\Widgets\Concerns\ProvidesWidgetDefaults;
use App\Widgets\Contracts\WidgetContract;

final class ContentStyle2Widget implements WidgetContract
{
    use ProvidesWidgetDefaults;

    public static function type(): string
    {
        return 'content_style_2';
    }

    public static function label(): string
    {
        return 'Content Style 2 (Text + Image Right)';
    }

    public static function icon(): string
    {
        return 'PanelRight';
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
            'image_side' => 'right',
            'marker_style' => 'check',
            'card' => false,
            'heading_style' => 'italic',
            'bg' => 'none',
        ];
    }

    public static function defaultData(): array
    {
        return [
            'eyebrow' => '',
            'heading' => 'Von Berlin aus für jeden Umzug an Ihrer Seite',
            'body' => 'Unser Zuhause ist Berlin, aber unser Leistungsspektrum reicht weit: vom Privatumzug über den Gewerbeumzug und den Fernumzug quer durch Deutschland bis zum Spezialtransport empfindlicher Güter. Egal welches Projekt Sie planen – wir kennen die Abläufe, die Wege und die Anforderungen und bringen Ihr Hab und Gut sicher ans Ziel.',
            'image_alt' => 'Moovato Mitarbeiter mit Umzugskartons',
            'points' => [],
            'button_label' => '',
            'button_url' => '',
        ];
    }

    public static function settingsRules(): array
    {
        return [
            'image_path' => ['nullable', 'string'],
            'image_url' => ['nullable', 'string'],
            'image_side' => ['required', 'in:left,right'],
            'marker_style' => ['required', 'in:check,chevron'],
            'card' => ['boolean'],
            'heading_style' => ['nullable', 'in:bold,italic'],
            'bg' => ['nullable', 'in:none,tinted,dark'],
        ];
    }

    public static function dataRules(): array
    {
        return [
            'eyebrow' => ['nullable', 'string'],
            'heading' => ['nullable', 'string'],
            'body' => ['nullable', 'string'],
            'image_alt' => ['nullable', 'string'],
            'points' => ['nullable', 'array'],
            'points.*' => ['nullable', 'string'],
            'button_label' => ['nullable', 'string'],
            'button_url' => ['nullable', 'string'],
        ];
    }
}
