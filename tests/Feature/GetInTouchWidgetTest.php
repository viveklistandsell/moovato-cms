<?php

declare(strict_types=1);

use App\Widgets\GetInTouch\GetInTouchWidget;
use App\Widgets\Registry\WidgetRegistry;

test('get in touch widget is registered under the get_in_touch slug', function (): void {
    expect(app(WidgetRegistry::class)->resolve('get_in_touch'))->toBe(GetInTouchWidget::class);
});

test('get in touch widget exposes heading copy and two buttons', function (): void {
    $data = GetInTouchWidget::defaultData();

    expect($data)->toHaveKeys([
        'eyebrow',
        'heading',
        'lead',
        'primary_label',
        'primary_url',
        'secondary_label',
        'secondary_url',
    ])
        ->and($data['heading'])->toBe('Lassen Sie uns sprechen.')
        ->and(GetInTouchWidget::defaultSettings())->toBe([]);
});

test('get in touch widget exposes the picker metadata shape', function (): void {
    $meta = GetInTouchWidget::toArray();

    expect($meta)->toHaveKeys(['type', 'label', 'icon', 'category', 'default_settings', 'default_data'])
        ->and($meta['type'])->toBe('get_in_touch')
        ->and($meta['label'])->toBe('Get In Touch')
        ->and($meta['category'])->toBe('marketing');
});
