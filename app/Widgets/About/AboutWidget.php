<?php

declare(strict_types=1);

namespace App\Widgets\About;

use App\Widgets\Concerns\ProvidesWidgetDefaults;
use App\Widgets\Contracts\WidgetContract;

final class AboutWidget implements WidgetContract
{
    use ProvidesWidgetDefaults;

    public static function type(): string
    {
        return 'about';
    }

    public static function label(): string
    {
        return 'About Us';
    }

    public static function icon(): string
    {
        return 'Info';
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
            'eyebrow' => 'Über uns',
            'heading' => 'Ihr zuverlässiger Partner für stressfreie Umzüge in Berlin',
            'image_alt' => 'Moovato Umzugsteam beim Be- und Entladen in Berlin',
            'body' => 'Von Privatumzug über Gewerbeumzug und Fernumzug bis Spezialtransport steht Moovato für Termintreue, faire Festpreise und ein erfahrenes Team – damit Ihr Umzug reibungslos gelingt.',
            'badge' => 'Umzug & Transport',
            'features' => [
                'Privat-, Gewerbe- & Fernumzug',
                'Spezialtransport',
                'Faire Festpreise',
                'Versichert & geprüft',
            ],
            'trusted_label' => 'Zufriedene Kunden',
            'avatars' => ['M', 'T', 'A', 'S', 'L'],
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
            'eyebrow' => ['nullable', 'string'],
            'heading' => ['nullable', 'string'],
            'image_alt' => ['nullable', 'string'],
            'body' => ['nullable', 'string'],
            'badge' => ['nullable', 'string'],
            'features' => ['nullable', 'array'],
            'features.*' => ['nullable', 'string'],
            'trusted_label' => ['nullable', 'string'],
            'avatars' => ['nullable', 'array'],
            'avatars.*' => ['nullable', 'string'],
            'button_label' => ['nullable', 'string'],
            'button_url' => ['nullable', 'string'],
        ];
    }
}
