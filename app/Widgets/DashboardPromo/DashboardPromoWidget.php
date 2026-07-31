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
            'dashboard_image_path' => ['nullable', 'string', 'max:1000'],
            'dashboard_image_url' => ['nullable', 'string', 'max:2000'],
            'reviews_image_path' => ['nullable', 'string', 'max:1000'],
            'reviews_image_url' => ['nullable', 'string', 'max:2000'],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public static function dataRules(): array
    {
        return [
            'promo_title' => ['nullable', 'string', 'max:160'],
            'promo_text' => ['nullable', 'string', 'max:400'],
            'promo_cta_label' => ['nullable', 'string', 'max:80'],
            'promo_cta_url' => ['nullable', 'string', 'max:2000'],
            'dashboard_image_alt' => ['nullable', 'string', 'max:160'],
            'reviews_title' => ['nullable', 'string', 'max:160'],
            'reviews_text' => ['nullable', 'string', 'max:500'],
            'reviews_image_alt' => ['nullable', 'string', 'max:160'],
            'services_title' => ['nullable', 'string', 'max:160'],
            'services_text' => ['nullable', 'string', 'max:500'],
            'services_link_label' => ['nullable', 'string', 'max:80'],
            'services_link_url' => ['nullable', 'string', 'max:2000'],
            'services' => ['nullable', 'array', 'max:8'],
            'services.*.icon' => ['nullable', 'string', 'max:64'],
            'services.*.label' => ['nullable', 'string', 'max:120'],
        ];
    }
}
