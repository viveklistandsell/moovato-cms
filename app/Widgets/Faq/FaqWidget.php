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
                ['question' => 'Wie schnell erhalte ich ein Angebot?', 'answer' => 'Nach Ihrer Anfrage erhalten Sie innerhalb von 24 Stunden ein kostenloses und unverbindliches Festpreis-Angebot.'],
                ['question' => 'Sind meine Möbel während des Umzugs versichert?', 'answer' => 'Ja, alle Transporte sind versichert. Auf Wunsch bieten wir zusätzlichen Schutz für besonders wertvolle Gegenstände an.'],
                ['question' => 'In welchen Gebieten ist Moovato tätig?', 'answer' => 'Wir sind in ganz Berlin und Umgebung für Sie da – und übernehmen auf Anfrage auch deutschlandweite Fernumzüge.'],
                ['question' => 'Übernehmt ihr auch den Auf- und Abbau der Möbel?', 'answer' => 'Selbstverständlich. Unsere Umzugsprofis demontieren und montieren Ihre Möbel fachgerecht.'],
                ['question' => 'Was kostet ein Umzug bei Moovato?', 'answer' => 'Der Preis richtet sich nach Umfang, Entfernung und Zusatzleistungen. Dank Festpreisgarantie gibt es keine versteckten Kosten.'],
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
