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
                    'ribbon_label' => 'Bestbewertetes Umzugsunternehmen',
                    'score' => '9,9',
                    'reviews_count' => 312,
                    'pro_reviews_count' => 74,
                    'description' => 'Ihr Umzugsunternehmen in Berlin für stressfreie Privat- und Gewerbeumzüge. Festpreisgarantie, voll versichert und ein eingespieltes Team vom ersten Karton bis zum Aufbau.',
                    'address' => 'Weißenseer Weg 2, Berlin',
                    'services' => 'Privatumzug, Gewerbeumzug, Fernumzug, Spezialtransport',
                    'pros' => 'Professionell, Freundlich, Fairer Preis',
                    'cons' => 'Terminplanung',
                    'quote_url' => '#',
                ],
                [
                    'logo_path' => null,
                    'logo_url' => null,
                    'name' => 'BärenStark Umzüge Berlin',
                    'ribbon_label' => null,
                    'score' => '9,8',
                    'reviews_count' => 248,
                    'pro_reviews_count' => 51,
                    'description' => 'Kräftiges Team für schwere Möbel und enge Altbau-Treppenhäuser. Wir übernehmen Demontage, Transport und Montage – pünktlich und sorgfältig in ganz Berlin.',
                    'address' => 'Sonnenallee 144, Berlin-Neukölln',
                    'services' => 'Privatumzug, Gewerbeumzug, Spezialtransport',
                    'pros' => 'Professionell, Pünktlich, Sorgfältig',
                    'cons' => 'Kommunikation',
                    'quote_url' => '#',
                ],
                [
                    'logo_path' => null,
                    'logo_url' => null,
                    'name' => 'Hauptstadt Möbeltransporte',
                    'ribbon_label' => null,
                    'score' => '9,7',
                    'reviews_count' => 176,
                    'pro_reviews_count' => 38,
                    'description' => 'Spezialist für Büro- und Gewerbeumzüge am Wochenende. Damit Sie am Montag ohne Ausfallzeit weiterarbeiten – inklusive IT-Ab- und Aufbau.',
                    'address' => 'Landsberger Allee 52, Berlin',
                    'services' => 'Gewerbeumzug, Fernumzug, Spezialtransport',
                    'pros' => 'Reagiert schnell, Fairer Preis',
                    'cons' => 'Nur am Wochenende verfügbar',
                    'quote_url' => '#',
                ],
                [
                    'logo_path' => null,
                    'logo_url' => null,
                    'name' => 'Spree Umzugslogistik',
                    'ribbon_label' => null,
                    'score' => '9,6',
                    'reviews_count' => 134,
                    'pro_reviews_count' => 29,
                    'description' => 'Fern- und Privatumzüge mit transparenten Festpreisen. Auf Wunsch mit Spezialtransport und besenreiner Übergabe der alten Wohnung.',
                    'address' => 'Müllerstraße 27, Berlin-Wedding',
                    'services' => 'Privatumzug, Fernumzug, Spezialtransport',
                    'pros' => 'Transparente Preise, Freundlich',
                    'cons' => 'Vorlaufzeit',
                    'quote_url' => '#',
                ],
                [
                    'logo_path' => null,
                    'logo_url' => null,
                    'name' => 'Berlin Move Profis',
                    'ribbon_label' => null,
                    'score' => '9,5',
                    'reviews_count' => 98,
                    'pro_reviews_count' => 21,
                    'description' => 'Schnelle und faire Umzüge für Studenten und kleine Haushalte. Inklusive Stellung von Umzugskartons und Halteverbotszone.',
                    'address' => 'Karl-Marx-Straße 88, Berlin',
                    'services' => 'Privatumzug, Gewerbeumzug, Fernumzug',
                    'pros' => 'Preis, Schnelligkeit',
                    'cons' => 'Nur kleine Haushalte',
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
            'pro_reviews_label' => 'Bewertungen als Profi',
            'rating_prefix' => 'So bewerten Kunden',
            'pros_heading' => 'Vorteile',
            'cons_heading' => 'Nachteile',
            'quote_label' => 'Angebot anfordern',
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public static function settingsRules(): array
    {
        return [
            'companies' => ['nullable', 'array'],
            'companies.*.logo_path' => ['nullable', 'string'],
            'companies.*.logo_url' => ['nullable', 'string'],
            'companies.*.name' => ['nullable', 'string'],
            'companies.*.ribbon_label' => ['nullable', 'string'],
            'companies.*.score' => ['nullable', 'string'],
            'companies.*.reviews_count' => ['nullable', 'integer', 'min:0', 'max:1000000'],
            'companies.*.pro_reviews_count' => ['nullable', 'integer', 'min:0', 'max:1000000'],
            'companies.*.description' => ['nullable', 'string'],
            'companies.*.address' => ['nullable', 'string'],
            'companies.*.services' => ['nullable', 'string'],
            'companies.*.pros' => ['nullable', 'string'],
            'companies.*.cons' => ['nullable', 'string'],
            'companies.*.quote_url' => ['nullable', 'string'],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public static function dataRules(): array
    {
        return [
            'heading' => ['nullable', 'string'],
            'subheading' => ['nullable', 'string'],
            'all_label' => ['nullable', 'string'],
            'score_label' => ['nullable', 'string'],
            'pro_reviews_label' => ['nullable', 'string'],
            'rating_prefix' => ['nullable', 'string'],
            'pros_heading' => ['nullable', 'string'],
            'cons_heading' => ['nullable', 'string'],
            'quote_label' => ['nullable', 'string'],
        ];
    }
}
