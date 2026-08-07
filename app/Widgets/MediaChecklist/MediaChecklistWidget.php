<?php

declare(strict_types=1);

namespace App\Widgets\MediaChecklist;

use App\Widgets\Concerns\ProvidesWidgetDefaults;
use App\Widgets\Contracts\WidgetContract;

final class MediaChecklistWidget implements WidgetContract
{
    use ProvidesWidgetDefaults;

    public static function type(): string
    {
        return 'media_checklist';
    }

    public static function label(): string
    {
        return 'Media + Checklist';
    }

    public static function icon(): string
    {
        return 'ListChecks';
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
            'card' => true,
            'heading_style' => 'bold', // bold | italic
            'bg' => 'none', // none | tinted | dark
        ];
    }

    public static function defaultData(): array
    {
        return [
            'eyebrow' => '',
            'heading' => 'Schritte zu einem stressfreien Umzug',
            'body' => 'Persönliche Beratung und ein durchdachter Ablauf nehmen Ihnen den Stress – damit Sie Ihren Umzug in Berlin von Anfang bis Ende entspannt angehen.',
            'image_alt' => 'Moovato Beraterin plant einen Umzug in Berlin',
            'points' => [
                'Persönlicher Ansprechpartner für Ihren Umzug',
                'Termine, die zu Ihrem Zeitplan passen',
                'Faire Festpreise, transparent kalkuliert',
                'Rücksichtsvolles, geschultes Team',
            ],
            'button_label' => 'Beratung anfragen',
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
