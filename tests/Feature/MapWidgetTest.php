<?php

declare(strict_types=1);

use App\Widgets\Map\MapWidget;
use App\Widgets\Registry\WidgetRegistry;

test('map widget is registered under the map slug', function (): void {
    $registry = app(WidgetRegistry::class);

    expect($registry->has('map'))->toBeTrue()
        ->and($registry->resolve('map'))->toBe(MapWidget::class);
});

test('map ships an image slot and positioned german city pins', function (): void {
    $settings = MapWidget::defaultSettings();
    $data = MapWidget::defaultData();

    expect($settings)->toHaveKeys(['image_path', 'image_url', 'pins'])
        ->and($settings['image_url'])->toBe('/images/berlin-map.webp')
        ->and($settings['pins'])->not->toBeEmpty()
        ->and($settings['pins'][0])->toHaveKeys(['x', 'y', 'city', 'label', 'flag_path', 'flag_url'])
        ->and($settings['pins'][0]['city'])->toBe('Mitte')
        ->and($data['heading'])->toBe('In jedem Berliner Bezirk für Sie da');
});

test('map validates pin coordinates within 0 to 100', function (): void {
    $rules = MapWidget::settingsRules();

    expect($rules['pins.*.x'])->toContain('numeric', 'min:0', 'max:100')
        ->and($rules['pins.*.y'])->toContain('numeric', 'min:0', 'max:100');
});

test('map exposes the picker metadata shape', function (): void {
    $meta = MapWidget::toArray();

    expect($meta)->toHaveKeys(['type', 'label', 'icon', 'category', 'default_settings', 'default_data'])
        ->and($meta['type'])->toBe('map')
        ->and($meta['label'])->toBe('Service Map');
});
