<?php

declare(strict_types=1);

namespace App\Widgets\ContentStyle1;

use App\Widgets\Concerns\ProvidesWidgetDefaults;
use App\Widgets\Contracts\WidgetContract;

final class ContentStyle1Widget implements WidgetContract
{
    use ProvidesWidgetDefaults;

    public static function type(): string
    {
        return 'content_style_1';
    }

    public static function label(): string
    {
        return 'Content Style 1 (Image Left + Text)';
    }

    public static function icon(): string
    {
        return 'PanelLeft';
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
            'image_side' => 'left',
            'marker_style' => 'check',
            'card' => false,
            'heading_style' => 'italic',
            'bg' => 'none',
        ];
    }

    public static function defaultData(): array
    {
        return [
            'eyebrow' => '',
            'heading' => 'Warum Berliner Kunden ihren Umzug uns anvertrauen',
            'body' => "Vertrauen entsteht durch saubere Arbeit. Ob Privatumzug, Gewerbeumzug, Fernumzug oder Spezialtransport – unsere geschulten Teams in Berlin packen, transportieren und montieren sorgfältig, schnell und ohne Beschädigungen.\n\nMit Moovato wissen Sie genau, woran Sie sind: feste Ansprechpartner, klare Absprachen und ein Ergebnis, das hält. So wird Ihr Umzug in Berlin zur Nebensache.",
            'image_alt' => 'Moovato Umzugsteam bei einem Umzug in einer Berliner Wohnung',
            'points' => [],
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
