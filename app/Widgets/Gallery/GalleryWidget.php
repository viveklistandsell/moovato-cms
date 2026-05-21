<?php

declare(strict_types=1);

namespace App\Widgets\Gallery;

use App\Widgets\Concerns\ProvidesWidgetDefaults;
use App\Widgets\Contracts\WidgetContract;

final class GalleryWidget implements WidgetContract
{
    use ProvidesWidgetDefaults;

    public static function type(): string
    {
        return 'gallery';
    }

    public static function label(): string
    {
        return 'Gallery';
    }

    public static function icon(): string
    {
        return 'Images';
    }

    public static function category(): string
    {
        return 'media';
    }

    public static function defaultSettings(): array
    {
        return [
            'layout' => 'grid', // grid | masonry | carousel
            'columns' => 3,     // 2 | 3 | 4
            'gap' => 'md',      // sm | md | lg
            'images' => [],     // array of {path, url}
        ];
    }

    public static function defaultData(): array
    {
        return [
            'heading' => '',
            // Per-image captions keyed by index. Settings store the image
            // references (same for all locales); only captions translate.
            'captions' => [],
        ];
    }

    public static function settingsRules(): array
    {
        return [
            'layout' => ['required', 'in:grid,masonry,carousel'],
            'columns' => ['required', 'integer', 'in:2,3,4'],
            'gap' => ['required', 'in:sm,md,lg'],
            'images' => ['nullable', 'array', 'max:50'],
            'images.*.path' => ['nullable', 'string', 'max:1000'],
            'images.*.url' => ['nullable', 'string', 'max:2000'],
        ];
    }

    public static function dataRules(): array
    {
        return [
            'heading' => ['nullable', 'string', 'max:255'],
            'captions' => ['nullable', 'array'],
            'captions.*' => ['nullable', 'string', 'max:500'],
        ];
    }
}
