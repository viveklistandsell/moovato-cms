<?php

declare(strict_types=1);

namespace App\Widgets\TeamCta;

use App\Widgets\Concerns\ProvidesWidgetDefaults;
use App\Widgets\Contracts\WidgetContract;

final class TeamCtaWidget implements WidgetContract
{
    use ProvidesWidgetDefaults;

    public static function type(): string
    {
        return 'team_cta';
    }

    public static function label(): string
    {
        return 'Team + Hiring CTA';
    }

    public static function icon(): string
    {
        return 'Users';
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
            'image_side' => 'right', // left | right
        ];
    }

    public static function defaultData(): array
    {
        return [
            'eyebrow' => '',
            'heading' => 'Die Profis hinter unserem guten Ruf',
            'body' => 'Unser Team besteht nicht aus Aushilfen. Jedes Mitglied durchläuft eine fundierte Einarbeitung in Verpackung, Montage und das Heben schwerer Lasten – mit Fokus auf Sicherheit, Effizienz und Kundenfreundlichkeit.',
            'points' => [
                'Geprüfte Fachkräfte',
                'Laufende Sicherheitsschulungen',
                'Mehrsprachiger Support',
                'Kunde-zuerst-Mentalität',
            ],
            'image_alt' => 'Moovato Umzugsteam trägt Kartons zum Transporter',
            'cta_title' => 'Werden Sie Teil des Moovato-Teams?',
            'cta_body' => 'Wir suchen stets engagierte, zuverlässige Menschen für unser wachsendes Berliner Team.',
            'cta_button_label' => 'Offene Stellen',
            'cta_button_url' => '#karriere',
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
            'points' => ['nullable', 'array'],
            'points.*' => ['nullable', 'string'],
            'image_alt' => ['nullable', 'string'],
            'cta_title' => ['nullable', 'string'],
            'cta_body' => ['nullable', 'string'],
            'cta_button_label' => ['nullable', 'string'],
            'cta_button_url' => ['nullable', 'string'],
        ];
    }
}
