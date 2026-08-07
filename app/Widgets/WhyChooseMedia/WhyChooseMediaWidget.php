<?php

declare(strict_types=1);

namespace App\Widgets\WhyChooseMedia;

use App\Widgets\Concerns\ProvidesWidgetDefaults;
use App\Widgets\Contracts\WidgetContract;

final class WhyChooseMediaWidget implements WidgetContract
{
    use ProvidesWidgetDefaults;

    public static function type(): string
    {
        return 'why_choose_media';
    }

    public static function label(): string
    {
        return 'Why Choose (Media)';
    }

    public static function icon(): string
    {
        return 'BadgeCheck';
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
            'image_side' => 'right',
            'marker_style' => 'check',
            'card' => false,
            'heading_style' => 'bold',
            'bg' => 'none',
        ];
    }

    public static function defaultData(): array
    {
        return [
            'eyebrow' => 'Warum wir',
            'heading' => 'Warum Moovato die richtige Wahl ist',
            'body' => 'Wir verbinden Sie mit erfahrenen Umzugsprofis aus Berlin – zuverlässig, sicher und zu fairen Festpreisen, ganz bequem von zu Hause aus geplant.',
            'image_alt' => 'Moovato Berater im Gespräch mit einem Kunden',
            'points' => [
                'Der passende Umzugsexperte für Sie',
                'Termine rund um Ihren Zeitplan',
                'Faire Preise, transparent kalkuliert',
                'Freundlicher, aufmerksamer Service',
            ],
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
            'marker_style' => ['required', 'in:check,chevron'],
            'card' => ['boolean'],
            'heading_style' => ['nullable', 'in:bold,italic'],
            'bg' => ['nullable', 'in:none,tinted,dark'],
        ];
    }

    public static function dataRules(): array
    {
        return [
            'eyebrow' => ['nullable', 'string'],
            'heading' => ['nullable', 'string'],
            'body' => ['nullable', 'string'],
            'image_alt' => ['nullable', 'string'],
            'points' => ['nullable', 'array'],
            'points.*' => ['nullable', 'string'],
            'button_label' => ['nullable', 'string'],
            'button_url' => ['nullable', 'string'],
        ];
    }
}
