<?php

declare(strict_types=1);

namespace App\Widgets\TextColumns;

use App\Widgets\Concerns\ProvidesWidgetDefaults;
use App\Widgets\Contracts\WidgetContract;

final class TextColumnsWidget implements WidgetContract
{
    use ProvidesWidgetDefaults;

    public static function type(): string
    {
        return 'text_columns';
    }

    public static function label(): string
    {
        return 'Two Column Text';
    }

    public static function icon(): string
    {
        return 'Columns2';
    }

    public static function category(): string
    {
        return 'content';
    }

    public static function defaultSettings(): array
    {
        return [
            'read_more_enabled' => false,
            'image_card_enabled' => false,
            'image_card_read_more_enabled' => false,
            'image_path' => null,
            'image_url' => null,
        ];
    }

    public static function defaultData(): array
    {
        return [
            'heading' => 'Umzug verstehen – von A bis Z',
            'subheading' => 'Was bedeutet ein Rundum-Umzug?',
            'body' => "Ein guter Umzug betrachtet nicht nur einzelne Kartons, sondern den gesamten Ablauf – von der Planung über das Verpacken bis zur Möbelmontage in Ihrem neuen Zuhause.\n\nBei Moovato konzentrieren wir uns auf einen reibungslosen Gesamtablauf. Unser Ziel ist es, Privatpersonen und Unternehmen in Berlin einen planbaren, sorgenfreien Umzug zu ermöglichen.",
            'intro' => 'Mit Moovato profitieren Sie von einem Service, der Folgendes ermöglicht:',
            'points' => [
                'Termintreue statt Unsicherheit',
                'Klare Festpreise statt versteckter Kosten',
                'Sorgfältiger Umgang mit Ihrem Inventar',
                'Komplettservice aus einer Hand',
                'Erfahrenes, freundliches Team',
            ],
            'outro' => 'Unser Umzugsprozess ist immer respektvoll, individuell und auf Ihre Bedürfnisse zugeschnitten.',
            'image_alt' => 'Glückliches Paar mit Umzugskartons in ihrer neuen Wohnung in Berlin',
            'image_card_heading' => 'Festpreis statt Stundenzettel',
            'image_card_body' => "Nach der kostenlosen Besichtigung bekommen Sie ein schriftliches Angebot, das jede Position einzeln ausweist: Personal, Fahrzeug, Packmaterial, Möbellift, Halteverbot sowie De- und Montage. Sie sehen genau, wofür Sie zahlen.\n\nWas dort steht, wird berechnet – auch wenn der Umzugstag länger dauert als gedacht. Nachträgliche Stundenzuschläge gibt es bei uns nicht, und ein Sofa, das im Treppenhaus klemmt, geht nicht zu Ihren Lasten.\n\nNur wenn Sie selbst den Umfang ändern, etwa weil der Keller doch mitkommt, passen wir das Angebot gemeinsam an. Diese Entscheidung treffen Sie – nicht der Vorarbeiter am Umzugstag.",
        ];
    }

    public static function settingsRules(): array
    {
        return [
            'read_more_enabled' => ['boolean'],
            'image_card_enabled' => ['boolean'],
            'image_card_read_more_enabled' => ['boolean'],
            'image_path' => ['nullable', 'string'],
            'image_url' => ['nullable', 'string'],
        ];
    }

    public static function dataRules(): array
    {
        return [
            'heading' => ['nullable', 'string'],
            'subheading' => ['nullable', 'string'],
            'body' => ['nullable', 'string'],
            'intro' => ['nullable', 'string'],
            'points' => ['nullable', 'array'],
            'points.*' => ['nullable', 'string'],
            'outro' => ['nullable', 'string'],
            'image_alt' => ['nullable', 'string'],
            'image_card_heading' => ['nullable', 'string'],
            'image_card_body' => ['nullable', 'string'],
        ];
    }
}
