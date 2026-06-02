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
                ['question' => 'Wie schnell erhalte ich ein Angebot?', 'answer' => 'Nach Ihrer Anfrage erhalten Sie innerhalb von 24 Stunden ein kostenloses und unverbindliches Festpreis-Angebot von unserem Berliner Team.'],
                ['question' => 'Sind meine Möbel während des Umzugs versichert?', 'answer' => 'Ja, alle Transporte sind versichert. Auf Wunsch bieten wir zusätzlichen Schutz für besonders wertvolle Gegenstände an.'],
                ['question' => 'Übernehmt ihr auch den Auf- und Abbau der Möbel?', 'answer' => 'Selbstverständlich. Unsere Umzugsprofis demontieren und montieren Ihre Möbel fachgerecht – inklusive Küchen und Einbauschränke.'],
                ['question' => 'In welchen Gebieten ist Moovato tätig?', 'answer' => 'Wir sind in ganz Berlin und Umgebung für Sie da – und übernehmen auf Anfrage auch deutschlandweite Fernumzüge.'],
            ],
        ];
    }

    public static function settingsRules(): array
    {
        return [
            'image_path' => ['nullable', 'string', 'max:1000'],
            'image_url' => ['nullable', 'string', 'max:2000'],
            'first_open' => ['boolean'],
        ];
    }

    public static function dataRules(): array
    {
        return [
            'vertical_label' => ['nullable', 'string', 'max:120'],
            'image_alt' => ['nullable', 'string', 'max:160'],
            'eyebrow' => ['nullable', 'string', 'max:120'],
            'heading_lead' => ['nullable', 'string', 'max:160'],
            'heading_highlight' => ['nullable', 'string', 'max:80'],
            'heading_tail' => ['nullable', 'string', 'max:160'],
            'items' => ['nullable', 'array', 'max:30'],
            'items.*.question' => ['nullable', 'string', 'max:500'],
            'items.*.answer' => ['nullable', 'string'],
        ];
    }
}
