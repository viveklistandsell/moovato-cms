<?php

declare(strict_types=1);

use App\Widgets\Registry\WidgetRegistry;
use App\Widgets\SplitMedia\SplitMediaWidget;

test('split media is registered under the split_media slug', function (): void {
    expect(app(WidgetRegistry::class)->resolve('split_media'))->toBe(SplitMediaWidget::class);
});

test('split media defaults to a full-bleed image on the right with a checklist', function (): void {
    $settings = SplitMediaWidget::defaultSettings();
    $data = SplitMediaWidget::defaultData();

    expect($settings)->toHaveKeys(['image_path', 'image_url', 'image_side', 'marker_style', 'heading_style'])
        ->and($settings['image_side'])->toBe('right')
        ->and($data['points'])->toHaveCount(5);
});

test('split media exposes the picker metadata shape', function (): void {
    $meta = SplitMediaWidget::toArray();

    expect($meta)->toHaveKeys(['type', 'label', 'icon', 'category', 'default_settings', 'default_data'])
        ->and($meta['type'])->toBe('split_media')
        ->and($meta['category'])->toBe('content');
});
