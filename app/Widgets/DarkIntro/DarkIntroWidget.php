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
            'heading' => 'Ihr zuverlässiger Partner für jeden Umzug',
            'body' => "Moovato unterstützt Kundinnen und Kunden in ganz Berlin – von Privatumzug über Gewerbeumzug und Fernumzug bis Spezialtransport. Unser Ziel ist einfach: ein professioneller Service vom ersten Kontakt bis zum letzten Karton.\n\nWir setzen auf Sorgfalt, Sauberkeit, transparente Kommunikation und Ihre Zufriedenheit bei jedem Projekt.",
            'list_title' => '',
            'points' => [],
            'button_label' => 'Mehr erfahren',
            'button_url' => '#leistungen',
        ];
    }

    public static function settingsRules(): array
    {
        return [
            'image_path' => ['nullable', 'string'],
            'image_url' => ['nullable', 'string'],
            'image_side' => ['required', 'in:left,right'],
        ];
    }

    public static function dataRules(): array
    {
        return [
            'eyebrow' => ['nullable', 'string'],
            'heading' => ['nullable', 'string'],
            'body' => ['nullable', 'string'],
            'list_title' => ['nullable', 'string'],
            'points' => ['nullable', 'array'],
            'points.*' => ['nullable', 'string'],
            'button_label' => ['nullable', 'string'],
            'button_url' => ['nullable', 'string'],
        ];
    }
}
