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
            'description' => '<ul><li><strong>Festpreisgarantie</strong> – Der vereinbarte Preis gilt – auch wenn der Umzugstag einmal länger dauert.</li><li><strong>Kostenlose Beratung</strong> – Wir besprechen Ihren Umzug unverbindlich und finden die passende Lösung.</li><li><strong>Versichert &amp; geprüft</strong> – Ihr Hab und Gut ist während des gesamten Transports abgesichert.</li><li><strong>Erfahrene Umzugsprofis</strong> – Unser Team packt, trägt und montiert mit jahrelanger Praxis.</li></ul>',
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
            'description' => ['nullable', 'string'],
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
