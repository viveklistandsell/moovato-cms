<?php

declare(strict_types=1);

use App\Widgets\Registry\WidgetRegistry;
use App\Widgets\StatsBand\StatsBandWidget;

test('stats band is registered under the stats_band slug', function (): void {
    $registry = app(WidgetRegistry::class);

    expect($registry->resolve('stats_band'))->toBe(StatsBandWidget::class);
});

test('stats band exposes a variant and stat list', function (): void {
    $settings = StatsBandWidget::defaultSettings();
    $data = StatsBandWidget::defaultData();

    expect($settings)->toHaveKey('variant')
        ->and($settings['variant'])->toBe('dark')
        ->and($data['stats'])->toHaveCount(4)
        ->and($data['stats'][0])->toHaveKeys(['value', 'label']);
});

test('stats band exposes the picker metadata shape', function (): void {
    $meta = StatsBandWidget::toArray();

    expect($meta)->toHaveKeys(['type', 'label', 'icon', 'category', 'default_settings', 'default_data'])
        ->and($meta['type'])->toBe('stats_band')
        ->and($meta['category'])->toBe('content');
});
