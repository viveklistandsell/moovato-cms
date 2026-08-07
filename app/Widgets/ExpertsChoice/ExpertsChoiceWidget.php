<?php

declare(strict_types=1);

namespace App\Widgets\ExpertsChoice;

use App\Widgets\Concerns\ProvidesWidgetDefaults;
use App\Widgets\Contracts\WidgetContract;

final class ExpertsChoiceWidget implements WidgetContract
{
    use ProvidesWidgetDefaults;

    public static function type(): string
    {
        return 'experts_choice';
    }

    public static function label(): string
    {
        return 'Experts Choice';
    }

    public static function icon(): string
    {
        return 'Award';
    }

    public static function category(): string
    {
        return 'content';
    }

    public static function defaultSettings(): array
    {
        return [];
    }

    public static function defaultData(): array
    {
        return [
            'eyebrow' => 'Warum Moovato',
            'heading' => 'Die Wahl der Profis',
            'body' => 'Tausende Berlinerinnen und Berliner vertrauen bei ihrem Umzug auf unser eingespieltes Team – aus gutem Grund.',
            'points' => [
                'Saubere, beschriftete Umzugsfahrzeuge',
                'Professionelles, einheitlich gekleidetes Personal',
                'Garantiert pünktliche Reaktion',
                'Täglich gewartete, zuverlässige Ausrüstung',
            ],
            'button_label' => 'So arbeiten wir',
            'button_url' => '#ablauf',
            'since_label' => 'Seit 2014',
            'cards' => [
                ['icon' => 'Award', 'title' => 'Erfahren', 'description' => 'Jahrelange Erfahrung bei Umzügen aller Art in ganz Berlin.'],
                ['icon' => 'ShieldCheck', 'title' => 'Voll versichert', 'description' => 'Alle Kundenanliegen werden über unsere Versicherungspartner abgesichert.'],
                ['icon' => 'Heart', 'title' => 'Sicher für Kind & Tier', 'description' => 'Rücksichtsvolles Arbeiten – sicher für Ihre Familie und Haustiere.'],
                ['icon' => 'Users', 'title' => 'Professionelles Team', 'description' => 'Geschultes Personal, das Ihr Inventar mit größter Sorgfalt behandelt.'],
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
            'eyebrow' => ['nullable', 'string'],
            'heading' => ['nullable', 'string'],
            'body' => ['nullable', 'string'],
            'points' => ['nullable', 'array'],
            'points.*' => ['nullable', 'string'],
            'button_label' => ['nullable', 'string'],
            'button_url' => ['nullable', 'string'],
            'since_label' => ['nullable', 'string'],
            'cards' => ['nullable', 'array'],
            'cards.*.icon' => ['nullable', 'string'],
            'cards.*.title' => ['nullable', 'string'],
            'cards.*.description' => ['nullable', 'string'],
        ];
    }
}
