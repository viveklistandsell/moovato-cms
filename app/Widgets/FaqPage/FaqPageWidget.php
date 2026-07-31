<?php

declare(strict_types=1);

namespace App\Widgets\FaqPage;

use App\Widgets\Concerns\ProvidesWidgetDefaults;
use App\Widgets\Contracts\WidgetContract;

final class FaqPageWidget implements WidgetContract
{
    use ProvidesWidgetDefaults;

    public static function type(): string
    {
        return 'faq_page';
    }

    public static function label(): string
    {
        return 'FAQ Page';
    }

    public static function icon(): string
    {
        return 'MessagesSquare';
    }

    public static function category(): string
    {
        return 'content';
    }

    public static function defaultSettings(): array
    {
        return [
            'show_search' => true,
            'show_categories' => true,
            'first_open' => true,
        ];
    }

    public static function defaultData(): array
    {
        return [
            'eyebrow' => 'Hilfe & Support',
            'heading' => 'Häufig gestellte Fragen',
            'subheading' => 'Alles rund um Ihren Umzug mit Moovato – von der ersten Anfrage bis zur Möbelmontage. Nicht gefunden, wonach Sie suchen? Unser Berliner Team hilft Ihnen gern persönlich weiter.',
            'search_placeholder' => 'Frage durchsuchen …',
            'categories' => [
                [
                    'name' => 'Umzug & Leistungen',
                    'items' => [
                        ['question' => 'Welche Umzüge führt Moovato durch?', 'answer' => '<p>Wir bieten vier Hauptleistungen an: <strong>Privatumzug</strong> für Wohnungen und Häuser, <strong>Gewerbeumzug</strong> für Büros und Firmen, <strong>Fernumzug</strong> innerhalb Deutschlands sowie <strong>Spezialtransport</strong> für empfindliche und sperrige Güter – alles ab Berlin.</p>'],
                        ['question' => 'Bietet Moovato auch Möbelmontage an?', 'answer' => '<p>Ja. Unser Team demontiert Ihre Möbel vor dem Transport fachgerecht und baut sie am Zielort wieder auf – auf Wunsch inklusive Küchen- und Schrankmontage.</p>'],
                        ['question' => 'Übernehmen Sie auch Entrümpelung und Einlagerung?', 'answer' => '<p>Selbstverständlich. Wir entrümpeln Wohnungen, Keller und Gewerbeflächen und bieten sichere Einlagerung in unseren Berliner Lagerräumen – flexibel für kurze oder lange Zeiträume.</p>'],
                    ],
                ],
                [
                    'name' => 'Angebot & Preise',
                    'items' => [
                        ['question' => 'Wie schnell erhalte ich ein Angebot?', 'answer' => '<p>Nach Ihrer Anfrage erhalten Sie innerhalb von <strong>24 Stunden</strong> ein kostenloses und unverbindliches Festpreis-Angebot von unserem Team.</p>'],
                        ['question' => 'Was kostet ein Umzug bei Moovato?', 'answer' => '<p>Der Preis richtet sich nach Leistung, Umfang und Entfernung. Ob Privatumzug, Gewerbeumzug, Fernumzug oder Spezialtransport – dank Festpreisgarantie gibt es keine versteckten Kosten.</p>'],
                        ['question' => 'Gibt es versteckte Zusatzkosten?', 'answer' => '<p>Nein. Unser Angebot ist ein verbindlicher Festpreis. Alle Leistungen wie Anfahrt, Verpackung oder Montage werden vorab transparent aufgeführt.</p>'],
                    ],
                ],
                [
                    'name' => 'Ablauf & Termine',
                    'items' => [
                        ['question' => 'Wie läuft ein Umzug mit Moovato ab?', 'answer' => '<p>In vier Schritten: <strong>1.</strong> Kostenlose Anfrage, <strong>2.</strong> Festpreis-Angebot binnen 24 Stunden, <strong>3.</strong> Terminbestätigung und Planung, <strong>4.</strong> Umzugstag mit unserem eingespielten Team.</p>'],
                        ['question' => 'Wie weit im Voraus sollte ich buchen?', 'answer' => '<p>Wir empfehlen eine Buchung 2–3 Wochen im Voraus. Kurzfristige Umzüge sind je nach Verfügbarkeit aber oft auch innerhalb weniger Tage möglich.</p>'],
                        ['question' => 'Kann ich meinen Umzugstermin verschieben?', 'answer' => '<p>Ja, eine Terminverschiebung ist bis kurz vor dem Umzug unkompliziert möglich. Melden Sie sich einfach bei Ihrem Ansprechpartner.</p>'],
                    ],
                ],
                [
                    'name' => 'Versicherung & Sicherheit',
                    'items' => [
                        ['question' => 'Sind meine Möbel während des Umzugs versichert?', 'answer' => '<p>Ja, alle Transporte sind versichert – von Privatumzug bis Spezialtransport. Auf Wunsch bieten wir zusätzlichen Schutz für besonders wertvolle Gegenstände an.</p>'],
                        ['question' => 'Was passiert, wenn doch einmal etwas beschädigt wird?', 'answer' => '<p>Sollte trotz sorgfältiger Arbeit ein Schaden entstehen, wird dieser über unsere Transportversicherung reguliert. Melden Sie den Schaden einfach direkt bei unserem Team.</p>'],
                    ],
                ],
            ],
        ];
    }

    public static function settingsRules(): array
    {
        return [
            'show_search' => ['boolean'],
            'show_categories' => ['boolean'],
            'first_open' => ['boolean'],
        ];
    }

    public static function dataRules(): array
    {
        return [
            'eyebrow' => ['nullable', 'string', 'max:120'],
            'heading' => ['nullable', 'string', 'max:255'],
            'subheading' => ['nullable', 'string', 'max:1000'],
            'search_placeholder' => ['nullable', 'string', 'max:120'],
            'categories' => ['nullable', 'array', 'max:12'],
            'categories.*.name' => ['nullable', 'string', 'max:120'],
            'categories.*.items' => ['nullable', 'array', 'max:40'],
            'categories.*.items.*.question' => ['nullable', 'string', 'max:500'],
            'categories.*.items.*.answer' => ['nullable', 'string'],
        ];
    }
}
