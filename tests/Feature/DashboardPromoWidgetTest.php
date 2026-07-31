<?php

declare(strict_types=1);

use App\Widgets\DashboardPromo\DashboardPromoWidget;
use App\Widgets\Registry\WidgetRegistry;

test('dashboard promo widget is registered under the dashboard_promo slug', function (): void {
    $registry = app(WidgetRegistry::class);

    expect($registry->has('dashboard_promo'))->toBeTrue()
        ->and($registry->resolve('dashboard_promo'))->toBe(DashboardPromoWidget::class);
});

test('dashboard promo ships two image slots and german card copy', function (): void {
    $settings = DashboardPromoWidget::defaultSettings();
    $data = DashboardPromoWidget::defaultData();

    expect($settings)->toHaveKeys([
        'dashboard_image_path',
        'dashboard_image_url',
        'reviews_image_path',
        'reviews_image_url',
    ])
        ->and($data)->toHaveKeys([
            'promo_title',
            'promo_cta_label',
            'reviews_title',
            'services_title',
            'services',
        ])
        ->and($data['services'][0])->toHaveKeys(['icon', 'label'])
        ->and($data['promo_title'])->toBe('Ihr persönliches Dashboard');
});

test('dashboard promo validates its settings and data fields', function (): void {
    expect(DashboardPromoWidget::settingsRules())->toHaveKeys([
        'dashboard_image_path',
        'dashboard_image_url',
        'reviews_image_path',
        'reviews_image_url',
    ])
        ->and(DashboardPromoWidget::dataRules())->toHaveKeys([
            'promo_title',
            'promo_text',
            'promo_cta_label',
            'promo_cta_url',
            'reviews_title',
            'reviews_text',
            'services_title',
            'services_text',
            'services_link_label',
            'services',
            'services.*.icon',
            'services.*.label',
        ]);
});

test('dashboard promo exposes the picker metadata shape', function (): void {
    $meta = DashboardPromoWidget::toArray();

    expect($meta)->toHaveKeys(['type', 'label', 'icon', 'category', 'default_settings', 'default_data'])
        ->and($meta['type'])->toBe('dashboard_promo')
        ->and($meta['label'])->toBe('Dashboard Promo')
        ->and($meta['category'])->toBe('marketing');
});
