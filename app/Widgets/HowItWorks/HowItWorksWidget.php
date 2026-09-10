<?php

declare(strict_types=1);

namespace App\Widgets\HowItWorks;

use App\Widgets\Concerns\ProvidesWidgetDefaults;
use App\Widgets\Contracts\WidgetContract;

final class HowItWorksWidget implements WidgetContract
{
    use ProvidesWidgetDefaults;

    public static function type(): string
    {
        return 'how_it_works';
    }

    public static function label(): string
    {
        return 'How It Works';
    }

    public static function icon(): string
    {
        return 'Route';
    }

    public static function category(): string
    {
        return 'content';
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
            'eyebrow' => 'In 4 Schritten',
            'heading' => 'So einfach läuft Ihr Umzug',
            'steps' => [
                ['icon' => 'ClipboardList', 'title' => 'Formular ausfüllen', 'description' => 'Teilen Sie uns in 60 Sekunden die wichtigsten Eckdaten zu Ihrem Umzug mit.'],
                ['icon' => 'Tag', 'title' => 'Angebot erhalten', 'description' => 'Sie bekommen innerhalb von 24 Stunden Ihren transparenten Festpreis.'],
                ['icon' => 'CalendarCheck', 'title' => 'Termin buchen', 'description' => 'Wählen Sie Ihren Wunschtermin – den Rest übernehmen wir für Sie.'],
                ['icon' => 'Truck', 'title' => 'Umzug genießen', 'description' => 'Unser Team packt, transportiert und baut auf. Sie lehnen sich entspannt zurück.'],
            ],
            'button_label' => 'Kostenloses Angebot anfordern',
            'button_url' => '#angebot',
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
            'eyebrow' => ['nullable', 'string'],
            'heading' => ['nullable', 'string'],
            'steps' => ['nullable', 'array'],
            'steps.*.icon' => ['nullable', 'string'],
            'steps.*.title' => ['nullable', 'string'],
            'steps.*.description' => ['nullable', 'string'],
            'button_label' => ['nullable', 'string'],
            'button_url' => ['nullable', 'string'],
        ];
    }
}
