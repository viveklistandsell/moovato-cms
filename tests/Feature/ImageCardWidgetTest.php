<?php

declare(strict_types=1);

use App\Widgets\ImageCard\ImageCardWidget;
use App\Widgets\Registry\WidgetRegistry;

test('image card widget is registered under its type slug', function (): void {
    $registry = app(WidgetRegistry::class);

    expect($registry->has('image_card'))->toBeTrue()
        ->and($registry->resolve('image_card'))->toBe(ImageCardWidget::class);
});

test('image card ships German heading, body and an image slot', function (): void {
    $settings = ImageCardWidget::defaultSettings();
    $data = ImageCardWidget::defaultData();

    expect($settings)->toHaveKeys(['image_path', 'image_url', 'read_more_enabled'])
        ->and($settings['read_more_enabled'])->toBeFalse()
        ->and($data)->toHaveKeys(['heading', 'body', 'image_alt'])
        ->and($data['heading'])->not->toBeEmpty()
        ->and($data['body'])->not->toBeEmpty();
});

test('image card exposes the picker metadata shape', function (): void {
    $meta = ImageCardWidget::toArray();

    expect($meta)->toHaveKeys(['type', 'label', 'icon', 'category', 'default_settings', 'default_data'])
        ->and($meta['type'])->toBe('image_card')
        ->and($meta['label'])->toBe('Image Card (Read More)')
        ->and($meta['category'])->toBe('content');
});
