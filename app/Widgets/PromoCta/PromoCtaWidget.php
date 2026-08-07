<?php

declare(strict_types=1);

namespace App\Widgets\PromoCta;

use App\Widgets\Concerns\ProvidesWidgetDefaults;
use App\Widgets\Contracts\WidgetContract;

final class PromoCtaWidget implements WidgetContract
{
    use ProvidesWidgetDefaults;

    public static function type(): string
    {
        return 'promo_cta';
    }

    public static function label(): string
    {
        return 'Promo CTA';
    }

    public static function icon(): string
    {
        return 'PhoneCall';
    }

    public static function category(): string
    {
        return 'marketing';
    }

    public static function defaultData(): array
    {
        return [
            'heading' => 'Warum Moovato?',
            'features' => [
                'Festpreisgarantie',
                'Kostenlose Beratung',
                'Versichert & geprüft',
                'Erfahrene Umzugsprofis',
            ],
            'button_label' => 'Mehr erfahren',
            'button_url' => '#leistungen',
            'badge_number' => '24',
            'badge_unit' => 'Stunden',
            'badge_label' => 'Service',
            'call_label' => 'Rufen Sie an',
            'phone' => '030 1234 5678',
        ];
    }

    public static function dataRules(): array
    {
        return [
            'heading' => ['nullable', 'string'],
            'features' => ['nullable', 'array'],
            'features.*' => ['nullable', 'string'],
            'button_label' => ['nullable', 'string'],
            'button_url' => ['nullable', 'string'],
            'badge_number' => ['nullable', 'string'],
            'badge_unit' => ['nullable', 'string'],
            'badge_label' => ['nullable', 'string'],
            'call_label' => ['nullable', 'string'],
            'phone' => ['nullable', 'string'],
        ];
    }
}
