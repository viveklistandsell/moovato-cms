<?php

declare(strict_types=1);

namespace App\Widgets\Hero;

use App\Widgets\Concerns\ProvidesWidgetDefaults;
use App\Widgets\Contracts\WidgetContract;

final class HeroWidget implements WidgetContract
{
    use ProvidesWidgetDefaults;

    public static function type(): string
    {
        return 'hero';
    }

    public static function label(): string
    {
        return 'Hero Section';
    }

    public static function icon(): string
    {
        return 'Sparkles';
    }

    public static function category(): string
    {
        return 'layout';
    }

    public static function defaultSettings(): array
    {
        return [
            'image_path' => null,
            'image_url' => null,
            'alignment' => 'center', // left | center | right
            'overlay' => true,
            'height' => 'lg',        // sm | md | lg | xl
        ];
    }

    public static function defaultData(): array
    {
        return [
            'eyebrow' => 'Umzugsunternehmen in Berlin',
            'title' => 'Stressfrei umziehen mit Moovato',
            'subtitle' => 'Ihr moderner Umzugspartner in Berlin – versichert, pünktlich und zum Festpreis. Privatumzug, Gewerbeumzug, Fernumzug und Spezialtransport.',
            'image_alt' => 'Moovato Umzugswagen und Umzugsteam in Berlin',
            'primary_label' => 'Kostenloses Angebot',
            'primary_url' => '#angebot',
            'secondary_label' => 'Unsere Leistungen',
            'secondary_url' => '#leistungen',
        ];
    }

    public static function settingsRules(): array
    {
        return [
            'image_path' => ['nullable', 'string'],
            'image_url' => ['nullable', 'string'],
            'alignment' => ['required', 'in:left,center,right'],
            'overlay' => ['boolean'],
            'height' => ['required', 'in:sm,md,lg,xl'],
        ];
    }

    public static function dataRules(): array
    {
        return [
            'eyebrow' => ['nullable', 'string'],
            'title' => ['nullable', 'string'],
            'subtitle' => ['nullable', 'string'],
            'image_alt' => ['nullable', 'string'],
            'primary_label' => ['nullable', 'string'],
            'primary_url' => ['nullable', 'string'],
            'secondary_label' => ['nullable', 'string'],
            'secondary_url' => ['nullable', 'string'],
        ];
    }
}
