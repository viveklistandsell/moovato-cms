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
            'eyebrow' => ['nullable', 'string', 'max:120'],
            'heading' => ['nullable', 'string', 'max:255'],
            'body' => ['nullable', 'string', 'max:500'],
            'points' => ['nullable', 'array', 'max:8'],
            'points.*' => ['nullable', 'string', 'max:160'],
            'button_label' => ['nullable', 'string', 'max:80'],
            'button_url' => ['nullable', 'string', 'max:2000'],
            'since_label' => ['nullable', 'string', 'max:40'],
            'cards' => ['nullable', 'array', 'max:6'],
            'cards.*.icon' => ['nullable', 'string', 'max:64'],
            'cards.*.title' => ['nullable', 'string', 'max:120'],
            'cards.*.description' => ['nullable', 'string', 'max:300'],
        ];
    }
}
