<?php

declare(strict_types=1);

namespace App\Widgets\DarkIntro;

use App\Widgets\Concerns\ProvidesWidgetDefaults;
use App\Widgets\Contracts\WidgetContract;

final class DarkIntroWidget implements WidgetContract
{
    use ProvidesWidgetDefaults;

    public static function type(): string
    {
        return 'dark_intro';
    }

    public static function label(): string
    {
        return 'Dark Intro';
    }

    public static function icon(): string
    {
        return 'PanelLeft';
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
        ];
    }

    public static function defaultData(): array
    {
        return [
            'eyebrow' => '',
            'heading' => 'Ihr zuverlässiger Partner für Umzug & Entrümpelung',
            'body' => "Moovato unterstützt Kundinnen und Kunden in ganz Berlin – zuverlässig bei Umzug, Entsorgung und Montage. Unser Ziel ist einfach: ein professioneller Service vom ersten Kontakt bis zum letzten Karton.\n\nWir setzen auf Sorgfalt, Sauberkeit, transparente Kommunikation und Ihre Zufriedenheit bei jedem Projekt.",
            'list_title' => '',
            'points' => [],
            'button_label' => 'Mehr erfahren',
            'button_url' => '#leistungen',
        ];
    }

    public static function settingsRules(): array
    {
        return [
            'image_path' => ['nullable', 'string', 'max:1000'],
            'image_url' => ['nullable', 'string', 'max:2000'],
            'image_side' => ['required', 'in:left,right'],
        ];
    }

    public static function dataRules(): array
    {
        return [
            'eyebrow' => ['nullable', 'string', 'max:120'],
            'heading' => ['nullable', 'string', 'max:255'],
            'body' => ['nullable', 'string', 'max:1200'],
            'list_title' => ['nullable', 'string', 'max:120'],
            'points' => ['nullable', 'array', 'max:8'],
            'points.*' => ['nullable', 'string', 'max:160'],
            'button_label' => ['nullable', 'string', 'max:80'],
            'button_url' => ['nullable', 'string', 'max:2000'],
        ];
    }
}
