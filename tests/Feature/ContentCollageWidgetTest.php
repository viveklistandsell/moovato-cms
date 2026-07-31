<?php

declare(strict_types=1);

use App\Widgets\ContentCollage\ContentCollageWidget;
use App\Widgets\Registry\WidgetRegistry;

test('content collage is registered under the content_collage slug', function (): void {
    expect(app(WidgetRegistry::class)->resolve('content_collage'))->toBe(ContentCollageWidget::class);
});

test('content collage exposes a collage image array, checklist and button', function (): void {
    $settings = ContentCollageWidget::defaultSettings();
    $data = ContentCollageWidget::defaultData();

    expect($settings)->toHaveKeys(['images', 'image_side', 'marker_style'])
        ->and($data)->toHaveKeys(['heading', 'body', 'points', 'alts', 'button_label', 'button_url'])
        ->and($data['points'])->toHaveCount(4);
});

test('content collage exposes the picker metadata shape', function (): void {
    $meta = ContentCollageWidget::toArray();

    expect($meta)->toHaveKeys(['type', 'label', 'icon', 'category', 'default_settings', 'default_data'])
        ->and($meta['type'])->toBe('content_collage')
        ->and($meta['category'])->toBe('content');
});
