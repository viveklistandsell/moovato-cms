<?php

declare(strict_types=1);

namespace App\Widgets\Reviews;

use App\Widgets\Concerns\ProvidesWidgetDefaults;
use App\Widgets\Contracts\WidgetContract;

final class ReviewsWidget implements WidgetContract
{
    use ProvidesWidgetDefaults;

    public static function type(): string
    {
        return 'reviews';
    }

    public static function label(): string
    {
        return 'Reviews';
    }

    public static function icon(): string
    {
        return 'Star';
    }

    public static function category(): string
    {
        return 'marketing';
    }

    /**
     * @return array<string, mixed>
     */
    public static function defaultSettings(): array
    {
        return [];
    }

    /**
     * @return array<string, mixed>
     */
    public static function defaultData(): array
    {
        return [
            'heading' => 'Erfolgsgeschichten: Was unsere Kunden sagen',
            'badge' => 'Moovato ist mit 4,9 / 5 aus über 1.200 Google-Bewertungen ausgezeichnet',
            'rating_value' => '4,9',
            'rating_label' => 'Sternebewertung auf Google',
            'rating_sub' => '1.200+ erfolgreiche Umzüge in Berlin & Umgebung',
            'cta_label' => 'Jetzt bewerten',
            'cta_url' => '#',
            'testimonials' => [
                [
                    'quote' => 'Der Umzug lief absolut reibungslos – pünktlich, freundlich und sorgfältig. Nichts ist zu Bruch gegangen.',
                    'author' => 'Sarah M.',
                    'role' => 'Privatumzug · Berlin-Mitte',
                    'avatar_url' => null,
                    'rating' => 5,
                ],
                [
                    'quote' => 'Unser Büroumzug am Wochenende war perfekt organisiert. Am Montag konnten wir direkt weiterarbeiten.',
                    'author' => 'Daniel K.',
                    'role' => 'Gewerbeumzug · Berlin-Kreuzberg',
                    'avatar_url' => null,
                    'rating' => 5,
                ],
                [
                    'quote' => 'Faires Festpreis-Angebot und ein eingespieltes Team. Vom Erstkontakt bis zum Aufbau alles top. Klare Empfehlung!',
                    'author' => 'Mehmet Y.',
                    'role' => 'Fernumzug · Berlin → München',
                    'avatar_url' => null,
                    'rating' => 5,
                ],
            ],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public static function settingsRules(): array
    {
        return [];
    }

    /**
     * @return array<string, mixed>
     */
    public static function dataRules(): array
    {
        return [
            'heading' => ['nullable', 'string', 'max:200'],
            'badge' => ['nullable', 'string', 'max:200'],
            'rating_value' => ['nullable', 'string', 'max:10'],
            'rating_label' => ['nullable', 'string', 'max:80'],
            'rating_sub' => ['nullable', 'string', 'max:160'],
            'cta_label' => ['nullable', 'string', 'max:80'],
            'cta_url' => ['nullable', 'string', 'max:2000'],
            'testimonials' => ['nullable', 'array', 'max:12'],
            'testimonials.*.quote' => ['nullable', 'string', 'max:600'],
            'testimonials.*.author' => ['nullable', 'string', 'max:120'],
            'testimonials.*.role' => ['nullable', 'string', 'max:120'],
            'testimonials.*.avatar_url' => ['nullable', 'string', 'max:2000'],
            'testimonials.*.rating' => ['nullable', 'integer', 'min:1', 'max:5'],
        ];
    }
}
