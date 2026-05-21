<?php

declare(strict_types=1);

namespace App\Widgets\Faq;

use App\Widgets\Concerns\ProvidesWidgetDefaults;
use App\Widgets\Contracts\WidgetContract;

final class FaqWidget implements WidgetContract
{
    use ProvidesWidgetDefaults;

    public static function type(): string
    {
        return 'faq';
    }

    public static function label(): string
    {
        return 'FAQ';
    }

    public static function icon(): string
    {
        return 'HelpCircle';
    }

    public static function category(): string
    {
        return 'content';
    }

    public static function defaultSettings(): array
    {
        return [
            'layout' => 'accordion', // accordion | grid
            'first_open' => true,
        ];
    }

    public static function defaultData(): array
    {
        return [
            'heading' => '',
            'items' => [
                ['question' => '', 'answer' => ''],
            ],
        ];
    }

    public static function settingsRules(): array
    {
        return [
            'layout' => ['required', 'in:accordion,grid'],
            'first_open' => ['boolean'],
        ];
    }

    public static function dataRules(): array
    {
        return [
            'heading' => ['nullable', 'string', 'max:255'],
            'items' => ['nullable', 'array', 'max:30'],
            'items.*.question' => ['nullable', 'string', 'max:500'],
            'items.*.answer' => ['nullable', 'string'],
        ];
    }
}
