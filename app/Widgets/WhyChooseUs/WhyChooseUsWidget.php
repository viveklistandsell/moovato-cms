<?php

declare(strict_types=1);

namespace App\Widgets\WhyChooseUs;

use App\Widgets\Concerns\ProvidesWidgetDefaults;
use App\Widgets\Contracts\WidgetContract;

final class WhyChooseUsWidget implements WidgetContract
{
    use ProvidesWidgetDefaults;

    public static function type(): string
    {
        return 'why_choose_us';
    }

    public static function label(): string
    {
        return 'Why Choose Us';
    }

    public static function icon(): string
    {
        return 'BadgeCheck';
    }

    public static function category(): string
    {
        return 'content';
    }

    public static function defaultSettings(): array
    {
        return [
            'image_path' => null,
            'image_url' => null,
        ];
    }

    public static function defaultData(): array
    {
        return [
            'eyebrow' => 'Warum Moovato',
            'heading' => 'Darum vertraut Berlin auf Moovato',
            'subheading' => 'Tausende zufriedene Kundinnen und Kunden setzen bei ihrem Umzug auf unseren Service – aus gutem Grund.',
            'image_alt' => 'Moovato Umzugsteam bei der Arbeit in Berlin',
            'button_label' => 'Kostenloses Angebot anfordern',
            'button_url' => '#angebot',
            'cards' => [
                ['icon' => 'ShieldCheck', 'title' => 'Festpreisgarantie', 'description' => 'Transparente Festpreise ohne versteckte Kosten – Sie wissen vorab genau, was Ihr Umzug in Berlin kostet.'],
                ['icon' => 'BadgeCheck', 'title' => 'Versichert & geprüft', 'description' => 'Ihr Hab und Gut ist bei uns umfassend versichert. Geschultes Personal und geprüfte Ausrüstung sorgen für Sicherheit.'],
                ['icon' => 'Users', 'title' => 'Erfahrenes Team', 'description' => 'Unsere Umzugsprofis bringen jahrelange Erfahrung mit und behandeln Ihr Inventar mit größter Sorgfalt.'],
                ['icon' => 'Clock', 'title' => 'Pünktlich & zuverlässig', 'description' => 'Wir halten Termine ein und arbeiten effizient – damit Ihr Umzugstag in Berlin reibungslos verläuft.'],
                ['icon' => 'Sparkles', 'title' => 'Rundum-sorglos-Service', 'description' => 'Von der Planung über das Verpacken bis zur Möbelmontage – Ihren kompletten Umzug erhalten Sie aus einer Hand.'],
            ],
        ];
    }

    public static function settingsRules(): array
    {
        return [
            'image_path' => ['nullable', 'string'],
            'image_url' => ['nullable', 'string'],
        ];
    }

    public static function dataRules(): array
    {
        return [
            'eyebrow' => ['nullable', 'string'],
            'heading' => ['nullable', 'string'],
            'subheading' => ['nullable', 'string'],
            'image_alt' => ['nullable', 'string'],
            'button_label' => ['nullable', 'string'],
            'button_url' => ['nullable', 'string'],
            'cards' => ['nullable', 'array'],
            'cards.*.icon' => ['nullable', 'string'],
            'cards.*.title' => ['nullable', 'string'],
            'cards.*.description' => ['nullable', 'string'],
        ];
    }
}
