<?php

declare(strict_types=1);

use App\Widgets\About\AboutWidget;
use App\Widgets\Registry\WidgetRegistry;

test('about widget is registered under the about slug', function (): void {
    $registry = app(WidgetRegistry::class);

    expect($registry->has('about'))->toBeTrue()
        ->and($registry->resolve('about'))->toBe(AboutWidget::class);
});

test('about ships an image slot, badge, checklist and trusted avatars', function (): void {
    $settings = AboutWidget::defaultSettings();
    $data = AboutWidget::defaultData();

    expect($settings)->toHaveKeys(['image_path', 'image_url'])
        ->and($data)->toHaveKeys([
            'eyebrow',
            'heading',
            'body',
            'badge',
            'features',
            'trusted_label',
            'avatars',
            'button_label',
            'button_url',
        ])
        ->and($data['features'])->toHaveCount(4)
        ->and($data['avatars'])->not->toBeEmpty()
        ->and($data['eyebrow'])->toBe('Über uns');
});

test('about exposes the picker metadata shape', function (): void {
    $meta = AboutWidget::toArray();

    expect($meta)->toHaveKeys(['type', 'label', 'icon', 'category', 'default_settings', 'default_data'])
        ->and($meta['type'])->toBe('about')
        ->and($meta['label'])->toBe('About Us')
        ->and($meta['category'])->toBe('content');
});
