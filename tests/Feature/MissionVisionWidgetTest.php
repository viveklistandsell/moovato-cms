<?php

declare(strict_types=1);

use App\Widgets\MissionVision\MissionVisionWidget;
use App\Widgets\Registry\WidgetRegistry;

test('mission vision widget is registered under its type slug', function (): void {
    $registry = app(WidgetRegistry::class);

    expect($registry->has('mission_vision'))->toBeTrue()
        ->and($registry->resolve('mission_vision'))->toBe(MissionVisionWidget::class);
});

test('mission vision ships German hero copy with an image slot', function (): void {
    $settings = MissionVisionWidget::defaultSettings();
    $data = MissionVisionWidget::defaultData();

    expect($settings)->toHaveKeys(['image_path', 'image_url'])
        ->and($data)->toHaveKeys(['image_alt', 'heading', 'body', 'button_label', 'button_url'])
        ->and($data['body'])->toBeString()->not->toBeEmpty();
});

test('mission vision exposes the picker metadata shape', function (): void {
    $meta = MissionVisionWidget::toArray();

    expect($meta)->toHaveKeys(['type', 'label', 'icon', 'category', 'default_settings', 'default_data'])
        ->and($meta['type'])->toBe('mission_vision')
        ->and($meta['label'])->toBe('Mission & Vision')
        ->and($meta['category'])->toBe('content');
});
