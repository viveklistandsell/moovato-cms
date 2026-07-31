<?php

declare(strict_types=1);

namespace App\Widgets\Image;

use App\Widgets\Concerns\ProvidesWidgetDefaults;
use App\Widgets\Contracts\WidgetContract;

final class ImageWidget implements WidgetContract
{
    use ProvidesWidgetDefaults;

    public static function type(): string
    {
        return 'image';
    }

    public static function label(): string
    {
        return 'Image Section';
    }

    public static function icon(): string
    {
        return 'Image';
    }

    public static function category(): string
    {
        return 'media';
    }

    public static function defaultSettings(): array
    {
        return [
            'image_path' => null,
            'image_url' => null,
            'aspect' => '16/9', // 16/9 | 4/3 | 1/1 | auto
            'width' => 'wide',  // narrow | wide | full
            'rounded' => true,
            'link_url' => null,
        ];
    }

    public static function defaultData(): array
    {
        return [
            'alt' => 'Moovato Umzugsteam bei der Arbeit in Berlin',
            'caption' => 'Unser erfahrenes Team sorgt für einen reibungslosen Umzug.',
        ];
    }

    public static function settingsRules(): array
    {
        return [
            'image_path' => ['nullable', 'string', 'max:1000'],
            'image_url' => ['nullable', 'string', 'max:2000'],
            'aspect' => ['required', 'in:16/9,4/3,1/1,auto'],
            'width' => ['required', 'in:narrow,wide,full'],
            'rounded' => ['boolean'],
            'link_url' => ['nullable', 'string', 'max:2000'],
        ];
    }

    public static function dataRules(): array
    {
        return [
            'alt' => ['nullable', 'string', 'max:255'],
            'caption' => ['nullable', 'string', 'max:500'],
        ];
    }
}
