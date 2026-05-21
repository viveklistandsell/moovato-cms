<?php

declare(strict_types=1);

namespace App\Widgets\Cta;

use App\Widgets\Concerns\ProvidesWidgetDefaults;
use App\Widgets\Contracts\WidgetContract;

final class CtaWidget implements WidgetContract
{
    use ProvidesWidgetDefaults;

    public static function type(): string
    {
        return 'cta';
    }

    public static function label(): string
    {
        return 'Call to Action';
    }

    public static function icon(): string
    {
        return 'Megaphone';
    }

    public static function category(): string
    {
        return 'marketing';
    }

    public static function defaultSettings(): array
    {
        return [
            'variant' => 'brand', // brand | muted | dark
            'alignment' => 'center', // left | center
        ];
    }

    public static function defaultData(): array
    {
        return [
            'eyebrow' => '',
            'title' => '',
            'description' => '',
            'primary_label' => '',
            'primary_url' => '',
            'secondary_label' => '',
            'secondary_url' => '',
        ];
    }

    public static function settingsRules(): array
    {
        return [
            'variant' => ['required', 'in:brand,muted,dark'],
            'alignment' => ['required', 'in:left,center'],
        ];
    }

    public static function dataRules(): array
    {
        return [
            'eyebrow' => ['nullable', 'string', 'max:120'],
            'title' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:500'],
            'primary_label' => ['nullable', 'string', 'max:80'],
            'primary_url' => ['nullable', 'string', 'max:2000'],
            'secondary_label' => ['nullable', 'string', 'max:80'],
            'secondary_url' => ['nullable', 'string', 'max:2000'],
        ];
    }
}
