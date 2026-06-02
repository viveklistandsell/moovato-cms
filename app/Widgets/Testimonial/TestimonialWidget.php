<?php

declare(strict_types=1);

namespace App\Widgets\Testimonial;

use App\Widgets\Concerns\ProvidesWidgetDefaults;
use App\Widgets\Contracts\WidgetContract;

final class TestimonialWidget implements WidgetContract
{
    use ProvidesWidgetDefaults;

    public static function type(): string
    {
        return 'testimonial';
    }

    public static function label(): string
    {
        return 'Testimonials';
    }

    public static function icon(): string
    {
        return 'Quote';
    }

    public static function category(): string
    {
        return 'marketing';
    }

    public static function defaultSettings(): array
    {
        return [
            'layout' => 'grid', // grid | carousel | single
            'columns' => 2,     // 1 | 2 | 3
        ];
    }

    public static function defaultData(): array
    {
        return [
            'heading' => 'Das sagen unsere Kunden',
            'items' => [
                [
                    'quote' => 'Pünktlich, freundlich und top organisiert. Unser Wohnungsumzug in Berlin lief völlig stressfrei – absolute Empfehlung!',
                    'author' => 'Sarah M.',
                    'role' => 'Privatumzug, Berlin-Mitte',
                    'avatar_path' => null,
                    'avatar_url' => null,
                ],
                [
                    'quote' => 'Moovato hat unseren Büroumzug am Wochenende erledigt, sodass montags alles bereit war. Eingespieltes Team!',
                    'author' => 'Daniel K.',
                    'role' => 'Büroumzug, Berlin-Kreuzberg',
                    'avatar_path' => null,
                    'avatar_url' => null,
                ],
            ],
        ];
    }

    public static function settingsRules(): array
    {
        return [
            'layout' => ['required', 'in:grid,carousel,single'],
            'columns' => ['required', 'integer', 'in:1,2,3'],
        ];
    }

    public static function dataRules(): array
    {
        return [
            'heading' => ['nullable', 'string', 'max:255'],
            'items' => ['nullable', 'array', 'max:20'],
            'items.*.quote' => ['nullable', 'string', 'max:1000'],
            'items.*.author' => ['nullable', 'string', 'max:120'],
            'items.*.role' => ['nullable', 'string', 'max:120'],
            'items.*.avatar_path' => ['nullable', 'string', 'max:1000'],
            'items.*.avatar_url' => ['nullable', 'string', 'max:2000'],
        ];
    }
}
