<?php

declare(strict_types=1);

namespace App\Widgets\DashboardPromo;

use App\Widgets\Concerns\ProvidesWidgetDefaults;
use App\Widgets\Contracts\WidgetContract;

/**
 * Moovato dashboard promo: a wide promo card (headline, copy, CTA + product
 * mockup) above two feature cards — reviews and additional services — on soft
 * token-tinted panels. Images are settings; copy is translatable data.
 */
final class DashboardPromoWidget implements WidgetContract
{
    use ProvidesWidgetDefaults;

    public static function type(): string
    {
        return 'dashboard_promo';
    }

    public static function label(): string
    {
        return 'Dashboard Promo';
    }

    public static function icon(): string
    {
        return 'LayoutDashboard';
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
        return [
            'dashboard_image_path' => null,
            'dashboard_image_url' => null,
            'reviews_image_path' => null,
            'reviews_image_url' => null,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public static function defaultData(): array
    {
        return [
            'promo_title' => 'Ihr persönliches Dashboard',
            'promo_description' => '',
            'promo_text' => 'Personalisieren Sie Ihre Umzugsdaten, empfohlenen Artikel und Umzugsdienste.',
            'promo_cta_label' => 'Jetzt kostenlos loslegen',
            'promo_cta_url' => '#',
            'dashboard_image_alt' => 'Moovato Dashboard',
            'reviews_title' => 'Authentische Bewertungen',
            'reviews_text' => 'Der Ruf lügt nicht. Schauen Sie sich das Moovato-Profil einer Umzugsfirma an, um die Erfahrungen anderer Kunden zu lesen.',
            'reviews_image_alt' => 'Kundenbewertungen',
            'services_title' => 'Umzugsservices',
            'services_text' => 'Ein Umzug geht über das Finden einer Umzugsfirma hinaus. Finden Sie in Sekundenschnelle weitere Dienstleister.',
            'services_link_label' => 'Alle anzeigen',
            'services_link_url' => '#',
            'services' => [
                ['icon' => 'ShieldCheck', 'label' => 'Umzugsversicherung'],
                ['icon' => 'Boxes', 'label' => 'Möbeleinlagerung'],
                ['icon' => 'FileText', 'label' => 'Ummeldeservice'],
            ],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public static function settingsRules(): array
    {
        return [
            'dashboard_image_path' => ['nullable', 'string'],
            'dashboard_image_url' => ['nullable', 'string'],
            'reviews_image_path' => ['nullable', 'string'],
            'reviews_image_url' => ['nullable', 'string'],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public static function dataRules(): array
    {
        return [
            'promo_title' => ['nullable', 'string'],
            'promo_description' => ['nullable', 'string'],
            'promo_text' => ['nullable', 'string'],
            'promo_cta_label' => ['nullable', 'string'],
            'promo_cta_url' => ['nullable', 'string'],
            'dashboard_image_alt' => ['nullable', 'string'],
            'reviews_title' => ['nullable', 'string'],
            'reviews_text' => ['nullable', 'string'],
            'reviews_image_alt' => ['nullable', 'string'],
            'services_title' => ['nullable', 'string'],
            'services_text' => ['nullable', 'string'],
            'services_link_label' => ['nullable', 'string'],
            'services_link_url' => ['nullable', 'string'],
            'services' => ['nullable', 'array'],
            'services.*.icon' => ['nullable', 'string'],
            'services.*.label' => ['nullable', 'string'],
        ];
    }
}
