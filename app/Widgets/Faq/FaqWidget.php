<?php

declare(strict_types=1);

namespace App\Widgets\Faq;

use App\Widgets\Concerns\ProvidesWidgetDefaults;
use App\Widgets\Contracts\WidgetContract;

final class FaqWidget implements WidgetContract
{
    use ProvidesWidgetDefaults;

    public static function type(): string
    {
        return 'faq';
    }

    public static function label(): string
    {
        return 'FAQ';
    }

    public static function icon(): string
    {
        return 'HelpCircle';
    }

    public static function category(): string
    {
        return 'content';
    }

    public static function defaultSettings(): array
    {
        return [
            'layout' => 'accordion', // accordion | grid
            'first_open' => true,
        ];
    }

    public static function defaultData(): array
    {
        return [
            'heading' => 'Häufig gestellte Fragen',
            'items' => [
                ['question' => 'Welche Umzüge führt Moovato durch?', 'answer' => 'Wir bieten vier Hauptleistungen an: Privatumzug für Wohnungen und Häuser, Gewerbeumzug für Büros und Firmen, Fernumzug innerhalb Deutschlands sowie Spezialtransport für empfindliche und sperrige Güter – alles ab Berlin.'],
                ['question' => 'Wie schnell erhalte ich ein Angebot?', 'answer' => 'Nach Ihrer Anfrage erhalten Sie innerhalb von 24 Stunden ein kostenloses und unverbindliches Festpreis-Angebot.'],
                ['question' => 'Sind meine Möbel während des Umzugs versichert?', 'answer' => 'Ja, alle Transporte sind versichert – von Privatumzug bis Spezialtransport. Auf Wunsch bieten wir zusätzlichen Schutz für besonders wertvolle Gegenstände an.'],
                ['question' => 'In welchen Gebieten ist Moovato tätig?', 'answer' => 'Wir sind in ganz Berlin und Umgebung für Sie da – und übernehmen mit unserem Fernumzug auf Anfrage auch deutschlandweite Umzüge.'],
                ['question' => 'Was kostet ein Umzug bei Moovato?', 'answer' => 'Der Preis richtet sich nach Leistung, Umfang und Entfernung. Ob Privatumzug, Gewerbeumzug, Fernumzug oder Spezialtransport – dank Festpreisgarantie gibt es keine versteckten Kosten.'],
            ],
        ];
    }

    public static function settingsRules(): array
    {
        return [
            'layout' => ['required', 'in:accordion,grid'],
            'first_open' => ['boolean'],
        ];
    }

    public static function dataRules(): array
    {
        return [
            'heading' => ['nullable', 'string', 'max:255'],
            'items' => ['nullable', 'array', 'max:30'],
            'items.*.question' => ['nullable', 'string', 'max:500'],
            'items.*.answer' => ['nullable', 'string'],
        ];
    }
}
