<?php

declare(strict_types=1);

use App\Widgets\CtaWork\CtaWorkWidget;
use App\Widgets\Registry\WidgetRegistry;

test('cta work widget is registered under its type slug', function (): void {
    $registry = app(WidgetRegistry::class);

    expect($registry->has('cta_work'))->toBeTrue()
        ->and($registry->resolve('cta_work'))->toBe(CtaWorkWidget::class);
});

test('cta work ships a background + inline image slot and a german cta', function (): void {
    $settings = CtaWorkWidget::defaultSettings();
    $data = CtaWorkWidget::defaultData();

    expect($settings)->toHaveKeys([
        'bg_image_path',
        'bg_image_url',
        'inline_image_path',
        'inline_image_url',
    ])
        ->and($data)->toHaveKeys(['title_before', 'title_after', 'button_label', 'button_url'])
        ->and($data['button_label'])->toBe('Kontakt aufnehmen')
        ->and(CtaWorkWidget::category())->toBe('marketing');
});

test('cta work exposes the picker metadata shape', function (): void {
    $meta = CtaWorkWidget::toArray();

    expect($meta)->toHaveKeys(['type', 'label', 'icon', 'category', 'default_settings', 'default_data'])
        ->and($meta['type'])->toBe('cta_work')
        ->and($meta['label'])->toBe('Work CTA');
});
