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
            'image_path' => null,
            'image_url' => null,
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
            'image_alt' => 'Moovato Team belädt einen Umzugswagen in Berlin',
            'button_label' => 'Angebot anfordern',
            'button_url' => '#kontakt',
        ];
    }

    public static function settingsRules(): array
    {
        return [
            'image_path' => ['nullable', 'string'],
            'image_url' => ['nullable', 'string'],
            'image_side' => ['required', 'in:left,right'],
            'marker_style' => ['required', 'in:check,chevron'],
        ];
    }

    public static function dataRules(): array
    {
        return [
            'eyebrow' => ['nullable', 'string'],
            'heading' => ['nullable', 'string'],
            'body' => ['nullable', 'string'],
            'points' => ['nullable', 'array'],
            'points.*' => ['nullable', 'string'],
            'image_alt' => ['nullable', 'string'],
            'button_label' => ['nullable', 'string'],
            'button_url' => ['nullable', 'string'],
        ];
    }
}
