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

    public static function defaultData(): array
    {
        return [
            'heading' => 'Umzug verstehen – von A bis Z',
            'subheading' => 'Was bedeutet ein Rundum-Umzug?',
            'body' => "Ein guter Umzug betrachtet nicht nur einzelne Kartons, sondern den gesamten Ablauf – von der Planung über das Verpacken bis zur Möbelmontage in Ihrem neuen Zuhause.\n\nBei Moovato konzentrieren wir uns auf einen reibungslosen Gesamtablauf. Unser Ziel ist es, Privatpersonen und Unternehmen in Berlin einen planbaren, sorgenfreien Umzug zu ermöglichen.",
            'intro' => '<p>Mit Moovato profitieren Sie von einem Service, der Folgendes ermöglicht:</p><ul><li><strong>Termintreue statt Unsicherheit</strong> – Der vereinbarte Termin steht fest, Sie können sich auf den geplanten Ablauf verlassen.</li><li><strong>Klare Festpreise statt versteckter Kosten</strong> – Ihr Angebot weist jede Position einzeln aus, sodass keine Überraschungen entstehen.</li><li><strong>Sorgfältiger Umgang mit Ihrem Inventar</strong> – Wir verpacken und transportieren Ihr Hab und Gut, als wäre es unser eigenes.</li><li><strong>Komplettservice aus einer Hand</strong> – Von der Besichtigung über die Verpackung bis zur Montage übernehmen wir jeden Schritt.</li><li><strong>Erfahrenes, freundliches Team</strong> – Unsere Umzugsprofis bringen jahrelange Erfahrung und ein freundliches Auftreten mit.</li></ul>',
            'outro' => 'Unser Umzugsprozess ist immer respektvoll, individuell und auf Ihre Bedürfnisse zugeschnitten.',
            'button_label' => 'Mehr erfahren',
            'button_url' => '#leistungen',
        ];
    }

    public static function dataRules(): array
    {
        return [
            'heading' => ['nullable', 'string'],
            'subheading' => ['nullable', 'string'],
            'body' => ['nullable', 'string'],
            'intro' => ['nullable', 'string'],
            'outro' => ['nullable', 'string'],
            'button_label' => ['nullable', 'string'],
            'button_url' => ['nullable', 'string'],
        ];
    }
}
