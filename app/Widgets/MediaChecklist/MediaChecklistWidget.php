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
            'image_path' => ['nullable', 'string', 'max:1000'],
            'image_url' => ['nullable', 'string', 'max:2000'],
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
