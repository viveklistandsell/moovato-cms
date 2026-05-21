<?php

declare(strict_types=1);

namespace App\Widgets\Features;

use App\Widgets\Concerns\ProvidesWidgetDefaults;
use App\Widgets\Contracts\WidgetContract;

final class FeaturesWidget implements WidgetContract
{
    use ProvidesWidgetDefaults;

    public static function type(): string
    {
        return 'features';
    }

    public static function label(): string
    {
        return 'Features Grid';
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
            'columns' => 3, // 2 | 3 | 4
            'icon_style' => 'badge', // badge | outline | plain
        ];
    }

    public static function defaultData(): array
    {
        return [
            'heading' => '',
            'subheading' => '',
            'items' => [
                ['icon' => 'Zap', 'title' => '', 'description' => ''],
            ],
        ];
    }

    public static function settingsRules(): array
    {
        return [
            'columns' => ['required', 'integer', 'in:2,3,4'],
            'icon_style' => ['required', 'in:badge,outline,plain'],
        ];
    }

    public static function dataRules(): array
    {
        return [
            'heading' => ['nullable', 'string', 'max:255'],
            'subheading' => ['nullable', 'string', 'max:500'],
            'items' => ['nullable', 'array', 'max:12'],
            'items.*.icon' => ['nullable', 'string', 'max:64'],
            'items.*.title' => ['nullable', 'string', 'max:120'],
            'items.*.description' => ['nullable', 'string', 'max:500'],
        ];
    }
}
