<?php

declare(strict_types=1);

use App\Widgets\ContentStyle1\ContentStyle1Widget;
use App\Widgets\ContentStyle2\ContentStyle2Widget;
use App\Widgets\Registry\WidgetRegistry;

test('the content style widgets are registered under their slugs', function (): void {
    $registry = app(WidgetRegistry::class);

    expect($registry->resolve('content_style_1'))->toBe(ContentStyle1Widget::class)
        ->and($registry->resolve('content_style_2'))->toBe(ContentStyle2Widget::class);
});

test('content style 1 places the image on the left with paragraph body', function (): void {
    $settings = ContentStyle1Widget::defaultSettings();
    $data = ContentStyle1Widget::defaultData();

    expect($settings['image_side'])->toBe('left')
        ->and($data['body'])->toContain("\n\n");
});

test('content style 2 places the image on the right', function (): void {
    expect(ContentStyle2Widget::defaultSettings()['image_side'])->toBe('right');
});

test('the content style widgets expose the picker metadata shape', function (): void {
    $widgets = [
        ContentStyle1Widget::class => 'content_style_1',
        ContentStyle2Widget::class => 'content_style_2',
    ];

    foreach ($widgets as $class => $type) {
        $meta = $class::toArray();

        expect($meta)->toHaveKeys(['type', 'label', 'icon', 'category', 'default_settings', 'default_data'])
            ->and($meta['type'])->toBe($type)
            ->and($meta['category'])->toBe('content');
    }
});
