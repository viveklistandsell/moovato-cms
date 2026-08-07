<?php

declare(strict_types=1);

use App\Widgets\AboutStats\AboutStatsWidget;
use App\Widgets\Registry\WidgetRegistry;

test('about stats widget is registered under the about_stats slug', function (): void {
    $registry = app(WidgetRegistry::class);

    expect($registry->has('about_stats'))->toBeTrue()
        ->and($registry->resolve('about_stats'))->toBe(AboutStatsWidget::class);
});

test('about stats ships two image slots, avatars and stat blocks', function (): void {
    $settings = AboutStatsWidget::defaultSettings();
    $data = AboutStatsWidget::defaultData();

    expect($settings)->toHaveKeys([
        'image_path',
        'image_url',
        'image_2_path',
        'image_2_url',
    ])
        ->and($data)->toHaveKeys([
            'eyebrow',
            'heading',
            'heading_accent',
            'body',
            'reviews_label',
            'avatars',
            'stats',
            'button_label',
            'button_url',
        ])
        ->and($data['stats'])->toHaveCount(2)
        ->and($data['stats'][0])->toHaveKeys(['description'])
        ->and($data['eyebrow'])->toBe('Über uns');
});

test('about stats exposes the picker metadata shape', function (): void {
    $meta = AboutStatsWidget::toArray();

    expect($meta)->toHaveKeys(['type', 'label', 'icon', 'category', 'default_settings', 'default_data'])
        ->and($meta['type'])->toBe('about_stats')
        ->and($meta['label'])->toBe('About + Stats')
        ->and($meta['category'])->toBe('content');
});
