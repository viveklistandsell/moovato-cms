<?php

declare(strict_types=1);

namespace App\Widgets\ServiceCards;

use App\Widgets\Concerns\ProvidesWidgetDefaults;
use App\Widgets\Contracts\WidgetContract;

final class ServiceCardsWidget implements WidgetContract
{
    use ProvidesWidgetDefaults;

    public static function type(): string
    {
        return 'service_cards';
    }

    public static function label(): string
    {
        return 'Service Cards';
    }

    public static function icon(): string
    {
        return 'LayoutGrid';
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
        return [
            'columns' => 3,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public static function defaultData(): array
    {
        return [
            'eyebrow' => 'Warum Moovato',
            'heading' => 'Ihre Vorteile auf einen Blick',
            'items' => [
                ['icon' => 'Tag', 'title' => 'Festpreisgarantie', 'description' => 'Ihr Preis steht fest – keine versteckten Kosten und keine bösen Überraschungen.'],
                ['icon' => 'ShieldCheck', 'title' => 'Versichert & geprüft', 'description' => 'Alle Transporte sind versichert und werden von geprüften Profis durchgeführt.'],
                ['icon' => 'Clock', 'title' => 'Pünktlich & zuverlässig', 'description' => 'Wir kommen zum vereinbarten Termin und halten unsere Zusagen ein.'],
                ['icon' => 'Users', 'title' => 'Erfahrenes Team', 'description' => 'Eingespielte Umzugsprofis mit über 1.200 erfolgreichen Umzügen in Berlin.'],
            ],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public static function settingsRules(): array
    {
        return [
            'columns' => ['required', 'integer', 'in:2,3,4'],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public static function dataRules(): array
    {
        return [
            'eyebrow' => ['nullable', 'string', 'max:60'],
            'heading' => ['nullable', 'string', 'max:160'],
            'items' => ['nullable', 'array', 'max:12'],
            'items.*.icon' => ['nullable', 'string', 'max:64'],
            'items.*.title' => ['nullable', 'string', 'max:120'],
            'items.*.description' => ['nullable', 'string', 'max:500'],
        ];
    }
}
