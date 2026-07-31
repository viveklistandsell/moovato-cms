<?php

declare(strict_types=1);

namespace App\Widgets\ContentCollage;

use App\Widgets\Concerns\ProvidesWidgetDefaults;
use App\Widgets\Contracts\WidgetContract;

final class ContentCollageWidget implements WidgetContract
{
    use ProvidesWidgetDefaults;

    public static function type(): string
    {
        return 'content_collage';
    }

    public static function label(): string
    {
        return 'Content + Image Collage';
    }

    public static function icon(): string
    {
        return 'LayoutGrid';
    }

    public static function category(): string
    {
        return 'content';
    }

    public static function defaultSettings(): array
    {
        return [
            'images' => [], // array of {path, url}
            'image_side' => 'left', // left | right
            'marker_style' => 'check', // check | chevron
        ];
    }

    public static function defaultData(): array
    {
        return [
            'eyebrow' => '',
            'heading' => 'Zuverlässige Umzugslösungen für Privat & Gewerbe',
            'body' => 'Umzüge können stressig sein – müssen es aber nicht. Wir nehmen Ihnen die Last ab: mit erfahrenen Teams, klarer Planung und einem Service, der genau auf Ihre Bedürfnisse zugeschnitten ist.',
            'points' => [
                'Privatumzug',
                'Gewerbeumzug',
                'Fernumzug',
                'Spezialtransport',
            ],
            'alts' => [
                'Moovato Team belädt einen Umzugswagen',
                'Moovato Mitarbeiter trägt Umzugskartons',
                'Gestapelte Umzugskartons in einer Berliner Wohnung',
            ],
            'button_label' => 'Angebot anfordern',
            'button_url' => '#kontakt',
        ];
    }

    public static function settingsRules(): array
    {
        return [
            'images' => ['nullable', 'array', 'max:3'],
            'images.*.path' => ['nullable', 'string', 'max:1000'],
            'images.*.url' => ['nullable', 'string', 'max:2000'],
            'image_side' => ['required', 'in:left,right'],
            'marker_style' => ['required', 'in:check,chevron'],
        ];
    }

    public static function dataRules(): array
    {
        return [
            'eyebrow' => ['nullable', 'string', 'max:120'],
            'heading' => ['nullable', 'string', 'max:255'],
            'body' => ['nullable', 'string', 'max:1200'],
            'points' => ['nullable', 'array', 'max:8'],
            'points.*' => ['nullable', 'string', 'max:160'],
            'alts' => ['nullable', 'array', 'max:3'],
            'alts.*' => ['nullable', 'string', 'max:160'],
            'button_label' => ['nullable', 'string', 'max:80'],
            'button_url' => ['nullable', 'string', 'max:2000'],
        ];
    }
}
