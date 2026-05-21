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
            'eyebrow' => '',
            'title' => '',
            'subtitle' => '',
            'primary_label' => '',
            'primary_url' => '',
            'secondary_label' => '',
            'secondary_url' => '',
        ];
    }

    public static function settingsRules(): array
    {
        return [
            'image_path' => ['nullable', 'string', 'max:1000'],
            'image_url' => ['nullable', 'string', 'max:2000'],
            'alignment' => ['required', 'in:left,center,right'],
            'overlay' => ['boolean'],
            'height' => ['required', 'in:sm,md,lg,xl'],
        ];
    }

    public static function dataRules(): array
    {
        return [
            'eyebrow' => ['nullable', 'string', 'max:120'],
            'title' => ['nullable', 'string', 'max:255'],
            'subtitle' => ['nullable', 'string', 'max:500'],
            'primary_label' => ['nullable', 'string', 'max:80'],
            'primary_url' => ['nullable', 'string', 'max:2000'],
            'secondary_label' => ['nullable', 'string', 'max:80'],
            'secondary_url' => ['nullable', 'string', 'max:2000'],
        ];
    }
}
