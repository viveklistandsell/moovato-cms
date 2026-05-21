<?php

declare(strict_types=1);

namespace App\Widgets\Banner;

use App\Widgets\Concerns\ProvidesWidgetDefaults;
use App\Widgets\Contracts\WidgetContract;

final class BannerWidget implements WidgetContract
{
    use ProvidesWidgetDefaults;

    public static function type(): string
    {
        return 'banner';
    }

    public static function label(): string
    {
        return 'Banner';
    }

    public static function icon(): string
    {
        return 'Flag';
    }

    public static function category(): string
    {
        return 'marketing';
    }

    public static function defaultSettings(): array
    {
        return [
            'variant' => 'info', // info | success | warning | danger | brand
            'dismissible' => false,
            'icon' => 'Megaphone',
        ];
    }

    public static function defaultData(): array
    {
        return [
            'message' => '',
            'cta_label' => '',
            'cta_url' => '',
        ];
    }

    public static function settingsRules(): array
    {
        return [
            'variant' => ['required', 'in:info,success,warning,danger,brand'],
            'dismissible' => ['boolean'],
            'icon' => ['nullable', 'string', 'max:64'],
        ];
    }

    public static function dataRules(): array
    {
        return [
            'message' => ['nullable', 'string', 'max:500'],
            'cta_label' => ['nullable', 'string', 'max:80'],
            'cta_url' => ['nullable', 'string', 'max:2000'],
        ];
    }
}
