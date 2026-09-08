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
        ];
    }

    public static function settingsRules(): array
    {
        return [
            'read_more_enabled' => ['boolean'],
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
        ];
    }
}
