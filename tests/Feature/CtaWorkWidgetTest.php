<?php

declare(strict_types=1);

use App\Widgets\CtaWork\CtaWorkWidget;
use App\Widgets\Registry\WidgetRegistry;

test('cta work widget is registered under its type slug', function (): void {
    $registry = app(WidgetRegistry::class);

    expect($registry->has('cta_work'))->toBeTrue()
        ->and($registry->resolve('cta_work'))->toBe(CtaWorkWidget::class);
});

test('cta work ships a default decorative image, heading, subtext, and a german cta', function (): void {
    $settings = CtaWorkWidget::defaultSettings();
    $data = CtaWorkWidget::defaultData();

    expect($settings)->toHaveKeys(['media_image_path', 'media_image_url'])
        ->and($settings['media_image_url'])->toBe('/images/boxes-dolly.png')
        ->and($data)->toHaveKeys(['heading', 'subtext', 'media_image_alt', 'button_label', 'button_url'])
        ->and($data['button_label'])->toBe('Jetzt Angebot anfordern')
        ->and(CtaWorkWidget::category())->toBe('marketing');
});

test('cta work exposes the picker metadata shape', function (): void {
    $meta = CtaWorkWidget::toArray();

    expect($meta)->toHaveKeys(['type', 'label', 'icon', 'category', 'default_settings', 'default_data'])
        ->and($meta['type'])->toBe('cta_work')
        ->and($meta['label'])->toBe('Work CTA');
});
