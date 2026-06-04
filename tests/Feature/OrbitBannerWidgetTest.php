<?php

declare(strict_types=1);

use App\Widgets\OrbitBanner\OrbitBannerWidget;
use App\Widgets\Registry\WidgetRegistry;

test('orbit banner widget is registered under the orbit_banner slug', function (): void {
    $registry = app(WidgetRegistry::class);

    expect($registry->has('orbit_banner'))->toBeTrue()
        ->and($registry->resolve('orbit_banner'))->toBe(OrbitBannerWidget::class);
});

test('orbit banner ships two image slots and german hero copy', function (): void {
    $settings = OrbitBannerWidget::defaultSettings();
    $data = OrbitBannerWidget::defaultData();

    expect($settings)->toHaveKeys([
        'image_path',
        'image_url',
        'inline_image_path',
        'inline_image_url',
    ])
        ->and($data)->toHaveKeys([
            'crumbs',
            'highlight',
            'heading',
            'description',
            'primary_label',
            'primary_url',
            'image_alt',
            'inline_image_alt',
        ])
        ->and($data)->not->toHaveKey('eyebrow')
        ->and($data['crumbs'][0])->toHaveKeys(['label', 'url'])
        ->and($data['heading'])->toContain('Berlin');
});

test('orbit banner validates image slots and copy fields', function (): void {
    expect(OrbitBannerWidget::settingsRules())->toHaveKeys([
        'image_path',
        'image_url',
        'inline_image_path',
        'inline_image_url',
    ])
        ->and(OrbitBannerWidget::dataRules())->toHaveKeys([
            'crumbs',
            'crumbs.*.label',
            'crumbs.*.url',
            'highlight',
            'heading',
            'description',
            'primary_label',
            'primary_url',
            'image_alt',
            'inline_image_alt',
        ]);
});

test('orbit banner exposes the picker metadata shape', function (): void {
    $meta = OrbitBannerWidget::toArray();

    expect($meta)->toHaveKeys(['type', 'label', 'icon', 'category', 'default_settings', 'default_data'])
        ->and($meta['type'])->toBe('orbit_banner')
        ->and($meta['label'])->toBe('Orbit Banner')
        ->and($meta['category'])->toBe('layout');
});
