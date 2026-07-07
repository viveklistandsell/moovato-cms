<?php

declare(strict_types=1);

namespace App\Widgets\TestimonialsShowcase;

use App\Widgets\Concerns\ProvidesWidgetDefaults;
use App\Widgets\Contracts\WidgetContract;

/**
 * Testimonials Showcase: a dark section pairing a circular avatar selector with
 * an interactive review card (rating, quote, identity). Clicking a thumbnail or
 * the prev/next arrows switches the active testimonial.
 */
final class TestimonialsShowcaseWidget implements WidgetContract
{
    use ProvidesWidgetDefaults;

    public static function type(): string
    {
        return 'testimonials_showcase';
    }

    public static function label(): string
    {
        return 'Testimonials Showcase';
    }

    public static function icon(): string
    {
        return 'MessageSquareQuote';
    }

    public static function category(): string
    {
        return 'marketing';
    }

    public static function defaultSettings(): array
    {
        return [];
    }

    public static function defaultData(): array
    {
        return [
            'eyebrow' => 'Kundenstimmen',
            'heading' => 'Echte Bewertungen von Umzügen in Berlin.',
            'button_label' => 'Alle Bewertungen',
            'button_url' => '/bewertungen',
            'items' => [
                [
                    'rating' => 5,
                    'rating_text' => '5,0 Bewertung',
                    'quote' => 'Pünktlich, freundlich und top organisiert. Unser Wohnungsumzug in Berlin-Mitte lief völlig stressfrei – das Team hat mitgedacht und alles sicher transportiert. Absolute Empfehlung!',
                    'name' => 'Sarah M.',
                    'role' => 'Privatumzug, Berlin-Mitte',
                    'image_path' => null,
                    'image_url' => null,
                ],
                [
                    'rating' => 5,
                    'rating_text' => '5,0 Bewertung',
                    'quote' => 'Moovato hat unseren Büroumzug übers Wochenende erledigt, sodass montags alles einsatzbereit war. Faire Festpreise und ein eingespieltes Team – so muss ein Gewerbeumzug laufen.',
                    'name' => 'Daniel K.',
                    'role' => 'Gewerbeumzug, Berlin-Kreuzberg',
                    'image_path' => null,
                    'image_url' => null,
                ],
                [
                    'rating' => 5,
                    'rating_text' => '5,0 Bewertung',
                    'quote' => 'Der Fernumzug nach München war perfekt geplant. Klare Kommunikation, pünktliche Lieferung und keine versteckten Kosten. Ich habe mich zu jedem Zeitpunkt gut aufgehoben gefühlt.',
                    'name' => 'Aylin T.',
                    'role' => 'Fernumzug, Berlin → München',
                    'image_path' => null,
                    'image_url' => null,
                ],
                [
                    'rating' => 5,
                    'rating_text' => '5,0 Bewertung',
                    'quote' => 'Ein Klavier und empfindliche Kunst mussten transportiert werden – das Spezialtransport-Team von Moovato hat das absolut souverän gemeistert. Sehr professionell und sorgfältig.',
                    'name' => 'Robert H.',
                    'role' => 'Spezialtransport, Berlin-Charlottenburg',
                    'image_path' => null,
                    'image_url' => null,
                ],
                [
                    'rating' => 5,
                    'rating_text' => '5,0 Bewertung',
                    'quote' => 'Inklusive Möbelmontage und Einlagerung – alles aus einer Hand. Das Team war schnell, freundlich und hat jede Kiste beschriftet wieder aufgebaut. Danke für den reibungslosen Umzug!',
                    'name' => 'Nina & Jonas',
                    'role' => 'Privatumzug, Berlin-Prenzlauer Berg',
                    'image_path' => null,
                    'image_url' => null,
                ],
            ],
        ];
    }

    public static function settingsRules(): array
    {
        return [];
    }

    public static function dataRules(): array
    {
        return [
            'eyebrow' => ['nullable', 'string', 'max:120'],
            'heading' => ['nullable', 'string', 'max:255'],
            'button_label' => ['nullable', 'string', 'max:120'],
            'button_url' => ['nullable', 'string', 'max:2048'],
            'items' => ['nullable', 'array', 'max:12'],
            'items.*.rating' => ['nullable', 'integer', 'min:1', 'max:5'],
            'items.*.rating_text' => ['nullable', 'string', 'max:60'],
            'items.*.quote' => ['nullable', 'string', 'max:1000'],
            'items.*.name' => ['nullable', 'string', 'max:120'],
            'items.*.role' => ['nullable', 'string', 'max:160'],
            'items.*.image_path' => ['nullable', 'string', 'max:1000'],
            'items.*.image_url' => ['nullable', 'string', 'max:2000'],
        ];
    }
}
