<?php

declare(strict_types=1);

use App\Widgets\PageBanner\PageBannerWidget;
use App\Widgets\Registry\WidgetRegistry;

test('page banner widget is registered under the page_banner slug', function (): void {
    $registry = app(WidgetRegistry::class);

    expect($registry->has('page_banner'))->toBeTrue()
        ->and($registry->resolve('page_banner'))->toBe(PageBannerWidget::class);
});

test('page banner ships a background image slot, breadcrumbs, a button and points', function (): void {
    $settings = PageBannerWidget::defaultSettings();
    $data = PageBannerWidget::defaultData();

    expect($settings)->toHaveKeys(['image_path', 'image_url'])
        ->and($data)->toHaveKeys([
            'crumbs',
            'heading',
            'primary_label',
            'primary_url',
            'points',
            'image_alt',
        ])
        ->and($data)->not->toHaveKey('secondary_label')
        ->and($data['crumbs'][0])->toHaveKeys(['label', 'url'])
        ->and($data['points'])->toHaveCount(4);
});

test('page banner exposes the picker metadata shape', function (): void {
    $meta = PageBannerWidget::toArray();

    expect($meta)->toHaveKeys(['type', 'label', 'icon', 'category', 'default_settings', 'default_data'])
        ->and($meta['type'])->toBe('page_banner')
        ->and($meta['label'])->toBe('Page Banner')
        ->and($meta['category'])->toBe('layout');
});
