<?php

declare(strict_types=1);

namespace App\Widgets\FaqMedia;

use App\Widgets\Concerns\ProvidesWidgetDefaults;
use App\Widgets\Contracts\WidgetContract;

final class FaqMediaWidget implements WidgetContract
{
    use ProvidesWidgetDefaults;

    public static function type(): string
    {
        return 'faq_media';
    }

    public static function label(): string
    {
        return 'FAQ (Image)';
    }

    public static function icon(): string
    {
        return 'MessageCircleQuestion';
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
            'first_open' => true,
        ];
    }

    public static function defaultData(): array
    {
        return [
            'vertical_label' => 'Umzugsservice Berlin',
            'image_alt' => 'Moovato Umzugswagen vor einem Berliner Altbau',
            'eyebrow' => 'Check FAQ',
            'heading_lead' => 'Häufig gestellte',
            'heading_highlight' => 'Fragen',
            'heading_tail' => 'rund um Ihren Umzug.',
            'items' => [
                ['question' => 'Welche Umzüge führt Moovato durch?', 'answer' => 'Wir bieten Privatumzug, Gewerbeumzug, Fernumzug und Spezialtransport an – von der Wohnung über das Büro bis zum sicheren Transport empfindlicher Güter, alles ab Berlin.'],
                ['question' => 'Wie schnell erhalte ich ein Angebot?', 'answer' => 'Nach Ihrer Anfrage erhalten Sie innerhalb von 24 Stunden ein kostenloses und unverbindliches Festpreis-Angebot von unserem Berliner Team.'],
                ['question' => 'Sind meine Möbel während des Umzugs versichert?', 'answer' => 'Ja, alle Transporte sind versichert – vom Privatumzug bis zum Spezialtransport. Auf Wunsch bieten wir zusätzlichen Schutz für besonders wertvolle Gegenstände an.'],
                ['question' => 'In welchen Gebieten ist Moovato tätig?', 'answer' => 'Wir sind in ganz Berlin und Umgebung für Sie da – und übernehmen mit unserem Fernumzug auf Anfrage auch deutschlandweite Umzüge.'],
            ],
            'button_label' => 'Weitere Fragen? Kontaktieren Sie uns',
            'button_url' => '#kontakt',
        ];
    }

    public static function settingsRules(): array
    {
        return [
            'image_path' => ['nullable', 'string'],
            'image_url' => ['nullable', 'string'],
            'first_open' => ['boolean'],
        ];
    }

    public static function dataRules(): array
    {
        return [
            'vertical_label' => ['nullable', 'string'],
            'image_alt' => ['nullable', 'string'],
            'eyebrow' => ['nullable', 'string'],
            'heading_lead' => ['nullable', 'string'],
            'heading_highlight' => ['nullable', 'string'],
            'heading_tail' => ['nullable', 'string'],
            'items' => ['nullable', 'array'],
            'items.*.question' => ['nullable', 'string'],
            'items.*.answer' => ['nullable', 'string'],
            'button_label' => ['nullable', 'string'],
            'button_url' => ['nullable', 'string'],
        ];
    }
}
