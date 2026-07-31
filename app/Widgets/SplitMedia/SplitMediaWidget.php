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
            'marker_style' => 'check', // check | chevron
            'heading_style' => 'italic', // bold | italic
        ];
    }

    public static function defaultData(): array
    {
        return [
            'eyebrow' => '',
            'heading' => 'Ihre Vorteile bei Moovato Berlin',
            'body' => '',
            'image_alt' => 'Moovato Mitarbeiter belädt einen Umzugswagen in Berlin',
            'points' => [
                'Günstige Umzugskosten bei hoher Servicequalität',
                'Persönliche Beratung und individuelle Planung',
                'Zuverlässige und diskrete Fachpersonen',
                'Auch Umzüge ohne Ihre Anwesenheit möglich',
                'Transparente Preisgestaltung ohne versteckte Kosten',
            ],
            'button_label' => '',
            'button_url' => '',
        ];
    }

    public static function settingsRules(): array
    {
        return [
            'image_path' => ['nullable', 'string', 'max:1000'],
            'image_url' => ['nullable', 'string', 'max:2000'],
            'image_side' => ['required', 'in:left,right'],
            'marker_style' => ['required', 'in:check,chevron'],
            'heading_style' => ['nullable', 'in:bold,italic'],
        ];
    }

    public static function dataRules(): array
    {
        return [
            'eyebrow' => ['nullable', 'string', 'max:120'],
            'heading' => ['nullable', 'string', 'max:255'],
            'body' => ['nullable', 'string', 'max:1200'],
            'image_alt' => ['nullable', 'string', 'max:160'],
            'points' => ['nullable', 'array', 'max:8'],
            'points.*' => ['nullable', 'string', 'max:160'],
            'button_label' => ['nullable', 'string', 'max:80'],
            'button_url' => ['nullable', 'string', 'max:2000'],
        ];
    }
}
