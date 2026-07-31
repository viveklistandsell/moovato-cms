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
            'body' => ['nullable', 'string', 'max:800'],
            'points' => ['nullable', 'array', 'max:8'],
            'points.*' => ['nullable', 'string', 'max:160'],
            'image_alt' => ['nullable', 'string', 'max:160'],
            'cta_title' => ['nullable', 'string', 'max:160'],
            'cta_body' => ['nullable', 'string', 'max:400'],
            'cta_button_label' => ['nullable', 'string', 'max:80'],
            'cta_button_url' => ['nullable', 'string', 'max:2000'],
        ];
    }
}
