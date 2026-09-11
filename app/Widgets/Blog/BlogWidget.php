<?php

declare(strict_types=1);

namespace App\Widgets\Blog;

use App\Widgets\Concerns\ProvidesWidgetDefaults;
use App\Widgets\Contracts\WidgetContract;

final class BlogWidget implements WidgetContract
{
    use ProvidesWidgetDefaults;

    public static function type(): string
    {
        return 'blog';
    }

    public static function label(): string
    {
        return 'Latest Posts';
    }

    public static function icon(): string
    {
        return 'Newspaper';
    }

    public static function category(): string
    {
        return 'content';
    }

    public static function defaultSettings(): array
    {
        return [
            'count' => 3,
            'columns' => 3,
            'category' => '',
        ];
    }

    public static function defaultData(): array
    {
        return [
            'eyebrow' => 'News & Blog',
            'heading' => 'Aktuelles & Tipps rund um Ihren Umzug',
            'subheading' => '',
            'description' => '',
            'read_more_label' => 'Mehr lesen',
            'cta_label' => 'Alle Beiträge ansehen',
            'cta_url' => '/blog',
        ];
    }

    public static function settingsRules(): array
    {
        return [
            'count' => ['required', 'integer', 'min:1', 'max:12'],
            'columns' => ['required', 'integer', 'in:2,3,4'],
            'category' => ['nullable', 'string'],
        ];
    }

    public static function dataRules(): array
    {
        return [
            'eyebrow' => ['nullable', 'string'],
            'heading' => ['nullable', 'string'],
            'subheading' => ['nullable', 'string'],
            'description' => ['nullable', 'string'],
            'read_more_label' => ['nullable', 'string'],
            'cta_label' => ['nullable', 'string'],
            'cta_url' => ['nullable', 'string'],
        ];
    }
}
