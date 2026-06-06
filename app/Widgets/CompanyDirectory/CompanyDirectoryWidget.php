<?php

declare(strict_types=1);

namespace App\Widgets\CompanyDirectory;

use App\Widgets\Concerns\ProvidesWidgetDefaults;
use App\Widgets\Contracts\WidgetContract;

final class CompanyDirectoryWidget implements WidgetContract
{
    use ProvidesWidgetDefaults;

    public static function type(): string
    {
        return 'company_directory';
    }

    public static function label(): string
    {
        return 'Company Directory';
    }

    public static function icon(): string
    {
        return 'Building2';
    }

    public static function category(): string
    {
        return 'marketing';
    }

    /**
     * @return array<string, mixed>
     */
    public static function defaultSettings(): array
    {
        return [
            'companies' => [
                [
                    'logo_path' => null,
                    'logo_url' => null,
                    'name' => 'Moovato Express Umzüge',
                    'verified' => true,
                    'top_pro' => true,
                    'score' => '9,9',
                    'reviews_count' => 312,
                    'badges' => 'Sofort verfügbar, Antwort innerhalb 1 Stunde',
                    'description' => 'Ihr Umzugsunternehmen in Berlin für stressfreie Privat- und Gewerbeumzüge. Festpreisgarantie, voll versichert und ein eingespieltes Team vom ersten Karton bis zum Aufbau.',
                    'address' => 'Weißenseer Weg 2, Berlin',
                    'founded' => 'Gegründet 2014',
                    'services' => 'Privatumzug, Gewerbeumzug, Fernumzug, Spezialtransport',
                    'request_url' => '#',
                    'quote_url' => '#',
                ],
                [
                    'logo_path' => null,
                    'logo_url' => null,
                    'name' => 'BärenStark Umzüge Berlin',
                    'verified' => true,
                    'top_pro' => true,
                    'score' => '9,8',
                    'reviews_count' => 248,
                    'badges' => 'Kostenlose Erstberatung, Reagiert schnell',
                    'description' => 'Kräftiges Team für schwere Möbel und enge Altbau-Treppenhäuser. Wir übernehmen Demontage, Transport und Montage – pünktlich und sorgfältig in ganz Berlin.',
                    'address' => 'Sonnenallee 144, Berlin-Neukölln',
                    'founded' => 'Vor 12 Jahren gegründet',
                    'services' => 'Privatumzug, Gewerbeumzug, Spezialtransport',
                    'request_url' => '#',
                    'quote_url' => '#',
                ],
                [
                    'logo_path' => null,
                    'logo_url' => null,
                    'name' => 'Hauptstadt Möbeltransporte',
                    'verified' => true,
                    'top_pro' => false,
                    'score' => '9,7',
                    'reviews_count' => 176,
                    'badges' => 'Reagiert schnell',
                    'description' => 'Spezialist für Büro- und Gewerbeumzüge am Wochenende. Damit Sie am Montag ohne Ausfallzeit weiterarbeiten – inklusive IT-Ab- und Aufbau.',
                    'address' => 'Landsberger Allee 52, Berlin',
                    'founded' => 'Vor 9 Jahren gegründet',
                    'services' => 'Gewerbeumzug, Fernumzug, Spezialtransport',
                    'request_url' => '#',
                    'quote_url' => '#',
                ],
                [
                    'logo_path' => null,
                    'logo_url' => null,
                    'name' => 'Spree Umzugslogistik',
                    'verified' => false,
                    'top_pro' => false,
                    'score' => '9,6',
                    'reviews_count' => 134,
                    'badges' => 'Kostenlose Erstberatung',
                    'description' => 'Fern- und Privatumzüge mit transparenten Festpreisen. Auf Wunsch mit Spezialtransport und besenreiner Übergabe der alten Wohnung.',
                    'address' => 'Müllerstraße 27, Berlin-Wedding',
                    'founded' => 'Vor 7 Jahren gegründet',
                    'services' => 'Privatumzug, Fernumzug, Spezialtransport',
                    'request_url' => '#',
                    'quote_url' => '#',
                ],
                [
                    'logo_path' => null,
                    'logo_url' => null,
                    'name' => 'Berlin Move Profis',
                    'verified' => false,
                    'top_pro' => false,
                    'score' => '9,5',
                    'reviews_count' => 98,
                    'badges' => 'Sofort verfügbar',
                    'description' => 'Schnelle und faire Umzüge für Studenten und kleine Haushalte. Inklusive Stellung von Umzugskartons und Halteverbotszone.',
                    'address' => 'Karl-Marx-Straße 88, Berlin',
                    'founded' => 'Gegründet 2019',
                    'services' => 'Privatumzug, Gewerbeumzug, Fernumzug',
                    'request_url' => '#',
                    'quote_url' => '#',
                ],
            ],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public static function defaultData(): array
    {
        return [
            'heading' => 'Umzugsunternehmen in Ihrer Nähe',
            'subheading' => 'Diese geprüften Umzugspartner haben über Moovato eine Umzugsanfrage erhalten. Vergleichen Sie Bewertungen, Leistungen und holen Sie kostenlos Ihr Angebot ein.',
            'all_label' => 'Alle Dienstleistungen',
            'score_label' => 'Bewertungen',
            'request_label' => 'Verfügbarkeit anfragen',
            'quote_label' => 'Angebot einholen',
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public static function settingsRules(): array
    {
        return [
            'companies' => ['nullable', 'array', 'max:30'],
            'companies.*.logo_path' => ['nullable', 'string', 'max:2000'],
            'companies.*.logo_url' => ['nullable', 'string', 'max:2000'],
            'companies.*.name' => ['nullable', 'string', 'max:160'],
            'companies.*.verified' => ['nullable', 'boolean'],
            'companies.*.top_pro' => ['nullable', 'boolean'],
            'companies.*.score' => ['nullable', 'string', 'max:10'],
            'companies.*.reviews_count' => ['nullable', 'integer', 'min:0', 'max:1000000'],
            'companies.*.badges' => ['nullable', 'string', 'max:300'],
            'companies.*.description' => ['nullable', 'string', 'max:600'],
            'companies.*.address' => ['nullable', 'string', 'max:200'],
            'companies.*.founded' => ['nullable', 'string', 'max:80'],
            'companies.*.services' => ['nullable', 'string', 'max:300'],
            'companies.*.request_url' => ['nullable', 'string', 'max:2000'],
            'companies.*.quote_url' => ['nullable', 'string', 'max:2000'],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public static function dataRules(): array
    {
        return [
            'heading' => ['nullable', 'string', 'max:200'],
            'subheading' => ['nullable', 'string', 'max:400'],
            'all_label' => ['nullable', 'string', 'max:80'],
            'score_label' => ['nullable', 'string', 'max:40'],
            'request_label' => ['nullable', 'string', 'max:80'],
            'quote_label' => ['nullable', 'string', 'max:80'],
        ];
    }
}
