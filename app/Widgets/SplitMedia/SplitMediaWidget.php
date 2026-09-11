<?php

declare(strict_types=1);

namespace App\Widgets\SplitMedia;

use App\Widgets\Concerns\ProvidesWidgetDefaults;
use App\Widgets\Contracts\WidgetContract;

final class SplitMediaWidget implements WidgetContract
{
    use ProvidesWidgetDefaults;

    public static function type(): string
    {
        return 'split_media';
    }

    public static function label(): string
    {
        return 'Split Media (Half Image)';
    }

    public static function icon(): string
    {
        return 'SquareSplitHorizontal';
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
            'image_side' => 'right', // left | right
        ];
    }

    public static function defaultData(): array
    {
        return [
            'eyebrow' => '',
            'heading' => 'Ihre Vorteile bei Moovato Berlin',
            'body' => '<ul><li><strong>Günstige Umzugskosten bei hoher Servicequalität</strong> – Faire Preise, ohne Abstriche bei Sorgfalt und Zuverlässigkeit.</li><li><strong>Persönliche Beratung und individuelle Planung</strong> – Wir gehen auf Ihre individuellen Wünsche ein und planen den Umzug entsprechend.</li><li><strong>Zuverlässige und diskrete Fachpersonen</strong> – Unser Team arbeitet diskret und respektvoll in Ihrem privaten Umfeld.</li><li><strong>Auch Umzüge ohne Ihre Anwesenheit möglich</strong> – Auf Wunsch übernehmen wir den Umzug auch, wenn Sie selbst nicht vor Ort sein können.</li><li><strong>Transparente Preisgestaltung ohne versteckte Kosten</strong> – Ihr Festpreis-Angebot zeigt alle Kosten offen und nachvollziehbar.</li></ul>',
            'image_alt' => 'Moovato Mitarbeiter belädt einen Umzugswagen in Berlin',
            'button_label' => '',
            'button_url' => '',
        ];
    }

    public static function settingsRules(): array
    {
        return [
            'image_path' => ['nullable', 'string'],
            'image_url' => ['nullable', 'string'],
            'image_side' => ['required', 'in:left,right'],
        ];
    }

    public static function dataRules(): array
    {
        return [
            'eyebrow' => ['nullable', 'string'],
            'heading' => ['nullable', 'string'],
            'body' => ['nullable', 'string'],
            'image_alt' => ['nullable', 'string'],
            'button_label' => ['nullable', 'string'],
            'button_url' => ['nullable', 'string'],
        ];
    }
}
