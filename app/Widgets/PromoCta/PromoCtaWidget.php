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
            'heading' => ['nullable', 'string', 'max:160'],
            'features' => ['nullable', 'array', 'max:8'],
            'features.*' => ['nullable', 'string', 'max:120'],
            'button_label' => ['nullable', 'string', 'max:80'],
            'button_url' => ['nullable', 'string', 'max:2000'],
            'badge_number' => ['nullable', 'string', 'max:8'],
            'badge_unit' => ['nullable', 'string', 'max:40'],
            'badge_label' => ['nullable', 'string', 'max:40'],
            'call_label' => ['nullable', 'string', 'max:80'],
            'phone' => ['nullable', 'string', 'max:60'],
        ];
    }
}
